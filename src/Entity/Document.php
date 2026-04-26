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
    private string $filename = '';
    private ?string $content = null;
    private ?string $version = null;
    private string $status = 'Active';
    private ?int $createdBy = null;
    private string $createdAt = '';

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }
    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }
    public function getType(): string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }
    public function getFilename(): string { return $this->filename; }
    public function setFilename(string $filename): self { $this->filename = $filename; return $this; }
    public function getContent(): ?string { return $this->content; }
    public function setContent(?string $content): self { $this->content = $content; return $this; }
    public function getVersion(): ?string { return $this->version; }
    public function setVersion(?string $version): self { $this->version = $version; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
    public function getCreatedBy(): ?int { return $this->createdBy; }
    public function setCreatedBy(?int $createdBy): self { $this->createdBy = $createdBy; return $this; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function setCreatedAt(string $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function isActive(): bool { return $this->status === 'Active'; }
    public function requiresAcknowledgment(): bool { return $this->type === self::TYPE_POLICY || $this->type === self::TYPE_CONTRACT; }
}