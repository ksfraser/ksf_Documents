<?php

declare(strict_types=1);

namespace Ksfraser\Documents\Service;

use Ksfraser\Documents\Entity\Document;

class DocumentService implements DocumentServiceInterface
{
    private $db;
    private string $tablePrefix;
    
    public function __construct($db = null, string $tablePrefix = 'fa_')
    {
        $this->db = $db;
        $this->tablePrefix = $tablePrefix;
    }
    
    private function getTable(string $table): string
    {
        return $this->tablePrefix . $table;
    }
    
    private function escape($value): string
    {
        if ($this->db && method_exists($this->db, 'escape')) {
            return $this->db->escape($value);
        }
        return "'" . addslashes($value) . "'";
    }
    
    private function escapeInt($value): int
    {
        return (int)$value;
    }
    
    public function createDocument(Document $doc): int
    {
        if (!$this->db) {
            throw new \RuntimeException('Database connection not set');
        }
        
        $sql = "INSERT INTO " . $this->getTable('documents') . " 
            (title, type, status, created_by, created_at, expires_at, ack_required, ack_deadline, entity_type, entity_id)
            VALUES (
                " . $this->escape($doc->getTitle()) . ",
                " . $this->escape($doc->getType()) . ",
                " . $this->escape($doc->getStatus()) . ",
                " . $this->escapeInt($doc->getCreatedBy()) . ",
                " . $this->escape($doc->getCreatedAt()) . ",
                " . $this->escape($doc->getExpiresAt() ?? '') . ",
                " . $this->escape($doc->getAckRequired()) . ",
                " . $this->escape($doc->getAckDeadline() ?? '') . ",
                " . $this->escape($doc->getEntityType()) . ",
                " . $this->escapeInt($doc->getEntityId()) . "
            )";
        
        $this->db->query($sql);
        return $this->db->insert_id;
    }
    
    public function getDocument(int $id): ?Document
    {
        if (!$this->db) {
            throw new \RuntimeException('Database connection not set');
        }
        
        $sql = "SELECT * FROM " . $this->getTable('documents') . " WHERE id = " . $this->escapeInt($id);
        $result = $this->db->query($sql);
        
        if (!$result || $this->db->num_rows($result) == 0) {
            return null;
        }
        
        $row = $this->db->fetch($result);
        $doc = Document::fromArray($row);
        
        $doc->setAttachments($this->getAttachments($id));
        
        return $doc;
    }
    
    public function updateDocument(Document $doc): bool
    {
        if (!$this->db || !$doc->getId()) {
            return false;
        }
        
        $sql = "UPDATE " . $this->getTable('documents') . " SET
            title = " . $this->escape($doc->getTitle()) . ",
            type = " . $this->escape($doc->getType()) . ",
            status = " . $this->escape($doc->getStatus()) . ",
            expires_at = " . $this->escape($doc->getExpiresAt() ?? '') . ",
            ack_required = " . $this->escape($doc->getAckRequired()) . ",
            ack_deadline = " . $this->escape($doc->getAckDeadline() ?? '') . "
            WHERE id = " . $this->escapeInt($doc->getId());
        
        return $this->db->query($sql);
    }
    
    public function deleteDocument(int $id): bool
    {
        if (!$this->db) {
            return false;
        }
        
        $sql = "DELETE FROM " . $this->getTable('documents') . " WHERE id = " . $this->escapeInt($id);
        return $this->db->query($sql);
    }
    
    public function listDocuments(array $filters = []): array
    {
        if (!$this->db) {
            throw new \RuntimeException('Database connection not set');
        }
        
        $sql = "SELECT * FROM " . $this->getTable('documents') . " WHERE 1=1";
        
        if (!empty($filters['type'])) {
            $sql .= " AND type = " . $this->escape($filters['type']);
        }
        if (!empty($filters['status'])) {
            $sql .= " AND status = " . $this->escape($filters['status']);
        }
        if (!empty($filters['entity_type'])) {
            $sql .= " AND entity_type = " . $this->escape($filters['entity_type']);
        }
        if (!empty($filters['entity_id'])) {
            $sql .= " AND entity_id = " . $this->escapeInt($filters['entity_id']);
        }
        if (!empty($filters['ack_required'])) {
            $sql .= " AND ack_required = " . $this->escape($filters['ack_required']);
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT " . $this->escapeInt($filters['limit']);
        }
        
        $result = $this->db->query($sql);
        $documents = [];
        
        while ($row = $this->db->fetch($result)) {
            $documents[] = Document::fromArray($row);
        }
        
        return $documents;
    }
    
    public function addAttachment(int $docId, string $filePath, string $fileName): bool
    {
        if (!$this->db) {
            return false;
        }
        
        $sql = "INSERT INTO " . $this->getTable('document_attachments') . "
            (doc_id, file_path, file_name, uploaded_at)
            VALUES (
                " . $this->escapeInt($docId) . ",
                " . $this->escape($filePath) . ",
                " . $this->escape($fileName) . ",
                " . $this->escape(date('Y-m-d H:i:s')) . "
            )";
        
        return $this->db->query($sql);
    }
    
    public function getAttachments(int $docId): array
    {
        if (!$this->db) {
            return [];
        }
        
        $sql = "SELECT * FROM " . $this->getTable('document_attachments') . " 
            WHERE doc_id = " . $this->escapeInt($docId) . " AND active = 1
            ORDER BY uploaded_at DESC";
        
        $result = $this->db->query($sql);
        $attachments = [];
        
        while ($row = $this->db->fetch($result)) {
            $attachments[] = $row;
        }
        
        return $attachments;
    }
    
    public function removeAttachment(int $docId, int $attachmentId): bool
    {
        if (!$this->db) {
            return false;
        }
        
        $sql = "UPDATE " . $this->getTable('document_attachments') . " SET active = 0
            WHERE id = " . $this->escapeInt($attachmentId) . " AND doc_id = " . $this->escapeInt($docId);
        
        return $this->db->query($sql);
    }
    
    public function getExpiringDocuments(int $days = 30): array
    {
        if (!$this->db) {
            return [];
        }
        
        $futureDate = date('Y-m-d', strtotime("+{$days} days"));
        
        $sql = "SELECT * FROM " . $this->getTable('documents') . "
            WHERE expires_at IS NOT NULL 
            AND expires_at <= " . $this->escape($futureDate) . "
            AND status = 'Active'
            ORDER BY expires_at ASC";
        
        $result = $this->db->query($sql);
        $documents = [];
        
        while ($row = $this->db->fetch($result)) {
            $documents[] = Document::fromArray($row);
        }
        
        return $documents;
    }
    
    public function createAcknowledgment(int $docId, int $userId, int $employeeId): bool
    {
        if (!$this->db) {
            return false;
        }
        
        $sql = "INSERT INTO " . $this->getTable('document_acknowledgments') . "
            (doc_id, user_id, employee_id, acknowledged_at)
            VALUES (
                " . $this->escapeInt($docId) . ",
                " . $this->escapeInt($userId) . ",
                " . $this->escapeInt($employeeId) . ",
                " . $this->escape(date('Y-m-d H:i:s')) . "
            )";
        
        return $this->db->query($sql);
    }
    
    public function getAcknowledgedDocuments(int $employeeId): array
    {
        if (!$this->db) {
            return [];
        }
        
        $sql = "SELECT d.* FROM " . $this->getTable('documents') . " d
            INNER JOIN " . $this->getTable('document_acknowledgments') . " a ON d.id = a.doc_id
            WHERE a.employee_id = " . $this->escapeInt($employeeId) . "
            ORDER BY a.acknowledged_at DESC";
        
        $result = $this->db->query($sql);
        $documents = [];
        
        while ($row = $this->db->fetch($result)) {
            $documents[] = Document::fromArray($row);
        }
        
        return $document;
    }
    
    public function getPendingAcknowledgments(int $employeeId): array
    {
        if (!$this->db) {
            return [];
        }
        
        $now = date('Y-m-d');
        
        $sql = "SELECT d.* FROM " . $this->getTable('documents') . " d
            WHERE d.ack_required = 'yes'
            AND d.status = 'Active'
            AND (d.ack_deadline IS NULL OR d.ack_deadline >= " . $this->escape($now) . ")
            AND d.id NOT IN (
                SELECT doc_id FROM " . $this->getTable('document_acknowledgments') . "
                WHERE employee_id = " . $this->escapeInt($employeeId) . "
            )
            ORDER BY d.ack_deadline ASC";
        
        $result = $this->db->query($sql);
        $documents = [];
        
        while ($row = $this->db->fetch($result)) {
            $documents[] = Document::fromArray($row);
        }
        
        return $documents;
    }
}