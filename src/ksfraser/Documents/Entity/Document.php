<?php

declare(strict_types=1);

namespace Ksfraser\Documents\Entity;

class Document
{
    public const TYPE_POLICY = 'Policy';
    public const TYPE_CONTRACT = 'Contract';
    public const TYPE_FORM = 'Form';
    public const TYPE_TRAINING = 'Training';
    public const TYPE_HANDBOOK = 'Handbook';
    public const TYPE_OTHER = 'Other';

    private ?int $id = null;
    private string $title = '';
    private string $type = self::TYPE_OTHER;
    private string $status = 'Active';
    private ?int $createdBy = null;
    private string $createdAt = '';
    private ?string $expiresAt = null;
    private string $ackRequired = 'no';
    private ?string $ackDeadline = null;
    
    private string $entityType = '';
    private ?int $entityId = null;
    private array $attachments = [];

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }
    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }
    public function getType(): string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
    public function getCreatedBy(): ?int { return $this->createdBy; }
    public function setCreatedBy(?int $createdBy): self { $this->createdBy = $createdBy; return $this; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function setCreatedAt(string $createdAt): self { $this->createdAt = $createdAt; return $this; }
    public function getExpiresAt(): ?string { return $this->expiresAt; }
    public function setExpiresAt(?string $expiresAt): self { $this->expiresAt = $expiresAt; return $this; }
    public function getAckRequired(): string { return $this->ackRequired; }
    public function setAckRequired(string $ackRequired): self { $this->ackRequired = $ackRequired; return $this; }
    public function getAckDeadline(): ?string { return $this->ackDeadline; }
    public function setAckDeadline(?string $ackDeadline): self { $this->ackDeadline = $ackDeadline; return $this; }
    
    public function getEntityType(): string { return $this->entityType; }
    public function setEntityType(string $entityType): self { $this->entityType = $entityType; return $this; }
    public function getEntityId(): ?int { return $this->entityId; }
    public function setEntityId(?int $entityId): self { $this->entityId = $entityId; return $this; }
    public function getAttachments(): array { return $this->attachments; }
    public function setAttachments(array $attachments): self { $this->attachments = $attachments; return $this; }
    public function addAttachment(array $attachment): self { 
        $this->attachments[] = $attachment; 
        return $this; 
    }

    public function isActive(): bool { return $this->status === 'Active'; }
    public function isExpired(): bool { 
        if (!$this->expiresAt) return false;
        return strtotime($this->expiresAt) < time();
    }
    public function requiresAcknowledgment(): bool { return $this->ackRequired === 'yes'; }
    public function isAcked(): bool { return $this->ackRequired === 'yes' && !!$this->ackDeadline; }
    
    public static function fromArray(array $data): self
    {
        $doc = new self();
        $doc->setId($data['id'] ?? null);
        $doc->setTitle($data['title'] ?? '');
        $doc->setType($data['type'] ?? self::TYPE_OTHER);
        $doc->setStatus($data['status'] ?? 'Active');
        $doc->setCreatedBy($data['created_by'] ?? null);
        $doc->setCreatedAt($data['created_at'] ?? date('Y-m-d H:i:s'));
        $doc->setExpiresAt($data['expires_at'] ?? null);
        $doc->setAckRequired($data['ack_required'] ?? 'no');
        $doc->setAckDeadline($data['ack_deadline'] ?? null);
        $doc->setEntityType($data['entity_type'] ?? '');
        $doc->setEntityId($data['entity_id'] ?? null);
        
        if (isset($data['attachments'])) {
            $doc->setAttachments(is_array($data['attachments']) ? $data['attachments'] : json_decode($data['attachments'], true) ?? []);
        }
        
        return $doc;
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'status' => $this->status,
            'created_by' => $this->createdBy,
            'created_at' => $this->createdAt,
            'expires_at' => $this->expiresAt,
            'ack_required' => $this->ackRequired,
            'ack_deadline' => $this->ackDeadline,
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'attachments' => json_encode($this->attachments),
        ];
    }
}