<?php

declare(strict_types=1);

namespace Ksfraser\Documents\Service;

use Ksfraser\Documents\Entity\Document;

interface DocumentServiceInterface
{
    public function createDocument(Document $doc): int;
    public function getDocument(int $id): ?Document;
    public function updateDocument(Document $doc): bool;
    public function deleteDocument(int $id): bool;
    public function listDocuments(array $filters = []): array;
    public function addAttachment(int $docId, string $filePath, string $fileName): bool;
    public function getAttachments(int $docId): array;
    public function removeAttachment(int $docId, int $attachmentId): bool;
    public function getExpiringDocuments(int $days = 30): array;
    public function createAcknowledgment(int $docId, int $userId, int $employeeId): bool;
    public function getAcknowledgedDocuments(int $employeeId): array;
    public function getPendingAcknowledgments(int $employeeId): array;
}