<?php

declare(strict_types=1);

namespace Ksfraser\Documents\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Ksfraser\Documents\Service\DocumentService;
use Ksfraser\Documents\Entity\Document;

class DocumentServiceTest extends TestCase
{
    private $mockDb;
    private DocumentService $service;

    protected function setUp(): void
    {
        $this->mockDb = $this->createMock(\stdClass::class);
        $this->service = new DocumentService($this->mockDb, 'fa_');
    }

    public function testServiceCanBeCreated(): void
    {
        $this->assertInstanceOf(DocumentService::class, $this->service);
    }

    public function testCreateDocumentThrowsWhenNoDb(): void
    {
        $service = new DocumentService(null, 'fa_');
        $doc = new Document();
        $doc->setTitle('Test');
        
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Database connection not set');
        $service->createDocument($doc);
    }

    public function testGetExpiringDocumentsReturnsEmptyArrayWhenNoDb(): void
    {
        $service = new DocumentService(null, 'fa_');
        
        $result = $service->getExpiringDocuments(30);
        
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testAddAttachmentReturnsFalseWhenNoDb(): void
    {
        $service = new DocumentService(null, 'fa_');
        
        $result = $service->addAttachment(1, '/path/to/file.pdf', 'file.pdf');
        $this->assertFalse($result);
    }

    public function testDeleteDocumentReturnsFalseWhenNoDb(): void
    {
        $service = new DocumentService(null, 'fa_');
        
        $result = $service->deleteDocument(1);
        $this->assertFalse($result);
    }
}