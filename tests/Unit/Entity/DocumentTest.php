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

    public function testRequiresAcknowledgment(): void
    {
        $doc = new Document();
        $doc->setType(Document::TYPE_POLICY);
        $this->assertTrue($doc->requiresAcknowledgment());
    }
}