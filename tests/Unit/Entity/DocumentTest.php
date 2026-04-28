<?php

declare(strict_types=1);

namespace Ksfraser\Documents\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Ksfraser\Documents\Entity\Document;

class DocumentTest extends TestCase
{
    public function testCanCreateDocument(): void
    {
        $doc = new Document();
        $this->assertInstanceOf(Document::class, $doc);
    }

    public function testCanSetAndGetTitle(): void
    {
        $doc = new Document();
        $doc->setTitle('Employee Handbook');
        $this->assertEquals('Employee Handbook', $doc->getTitle());
    }

    public function testCanSetAndGetType(): void
    {
        $doc = new Document();
        $doc->setType(Document::TYPE_POLICY);
        $this->assertEquals('Policy', $doc->getType());
    }

    public function testCanSetAckRequired(): void
    {
        $doc = new Document();
        $doc->setAckRequired('yes');
        $this->assertTrue($doc->requiresAcknowledgment());
    }

    public function testCanAddAttachment(): void
    {
        $doc = new Document();
        $doc->addAttachment(['file_path' => '/uploads/test.pdf', 'file_name' => 'test.pdf']);
        $this->assertCount(1, $doc->getAttachments());
    }

    public function testIsExpired(): void
    {
        $doc = new Document();
        $doc->setExpiresAt('2020-01-01');
        $this->assertTrue($doc->isExpired());
    }

    public function testFromArray(): void
    {
        $data = [
            'id' => 1,
            'title' => 'Test Policy',
            'type' => 'Policy',
            'status' => 'Active',
            'created_by' => 1,
            'created_at' => '2024-01-01 10:00:00',
            'expires_at' => '2025-01-01',
            'ack_required' => 'yes',
            'ack_deadline' => '2024-12-31',
            'entity_type' => 'employee',
            'entity_id' => 5,
            'attachments' => json_encode([['file_name' => 'test.pdf']]),
        ];
        
        $doc = Document::fromArray($data);
        
        $this->assertEquals(1, $doc->getId());
        $this->assertEquals('Test Policy', $doc->getTitle());
        $this->assertEquals('Policy', $doc->getType());
        $this->assertTrue($doc->requiresAcknowledgment());
        $this->assertCount(1, $doc->getAttachments());
    }

    public function testToArray(): void
    {
        $doc = new Document();
        $doc->setId(1);
        $doc->setTitle('Test');
        $doc->setType(Document::TYPE_CONTRACT);
        $doc->setAckRequired('yes');
        $doc->setEntityType('employee');
        $doc->setEntityId(10);
        
        $arr = $doc->toArray();
        
        $this->assertEquals(1, $arr['id']);
        $this->assertEquals('Test', $arr['title']);
        $this->assertEquals('employee', $arr['entity_type']);
        $this->assertEquals(10, $arr['entity_id']);
    }
}