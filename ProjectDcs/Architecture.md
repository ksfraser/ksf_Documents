# Architecture - ksf_Documents

## Document Information
- **Module**: ksf_Documents
- **Version**: 1.0.0
- **Date**: 2026-05-11
- **Status**: Implemented
- **Author**: KSFII Development Team

---

## 1. Module Overview

ksf_Documents provides document management for employee records including contracts, tax forms, policies, and performance reviews with versioning and expiry tracking.

### 1.1 Namespace
```php
Ksfraser\Documents\
```

### 1.2 Layer Pattern
```
ksf_Documents/           → Business Logic
    ├── Entity/          → Domain entities
    ├── Service/         → Business services
    ├── Contract/        → Interfaces for adapters
    └── Exception/        → Domain exceptions
```

---

## 2. Core Entity

### 2.1 Document

```php
class Document {
    private string $id;
    private string $employeeId;
    private DocumentType $type;
    private string $name;
    private string $filePath;
    private string $mimeType;
    private int $version;
    private ?\DateTime $expiryDate;
    private DocumentStatus $status;
    private ?string $signatureData;
    private ?\DateTime $signedAt;
    private bool $acknowledged;
    private ?\DateTime $acknowledgedAt;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;
    
    // Methods
    public function upload(string $filePath): self;
    public function sign(string $signatureData): self;
    public function acknowledge(): self;
    public function isExpired(): bool;
    public function needsRenewal(): bool;
    public function getVersion(): int;
    public function createNewVersion(string $filePath): self;
}
```

### 2.2 DocumentType Enum

```php
enum DocumentType: string {
    case EmploymentContract = 'employment_contract';
    case TaxForm = 'tax_form';
    case BenefitsEnrollment = 'benefits_enrollment';
    case PolicyAcknowledgment = 'policy_acknowledgment';
    case PerformanceReview = 'performance_review';
    case TrainingCertification = 'training_certification';
    case WorkPermit = 'work_permit';
    case Other = 'other';
}
```

### 2.3 DocumentStatus Enum

```php
enum DocumentStatus: string {
    case Pending = 'pending';
    case AwaitingSignature = 'awaiting_signature';
    case Signed = 'signed';
    case Acknowledged = 'acknowledged';
    case Expired = 'expired';
    case Archived = 'archived';
}
```

---

## 3. Service Layer

### 3.1 DocumentService

| Method | Description |
|--------|-------------|
| `uploadDocument(string $employeeId, DocumentType $type, string $filePath): Document` | Upload new document |
| `getDocument(string $id): ?Document` | Retrieve document |
| `getEmployeeDocuments(string $employeeId): array` | Get all for employee |
| `getDocumentsByType(string $employeeId, DocumentType $type): array` | Filter by type |
| `signDocument(string $id, string $signatureData): Document` | Apply signature |
| `acknowledgeDocument(string $id): Document` | Mark acknowledged |
| `createNewVersion(string $id, string $filePath): Document` | New version |
| `getExpiringDocuments(int $days = 30): array` | Documents expiring soon |
| `archiveDocument(string $id): bool` | Move to archive |

### 3.2 DocumentServiceInterface

```php
interface DocumentServiceInterface {
    public function uploadDocument(string $employeeId, string $type, string $filePath): Document;
    public function getDocument(string $id): ?Document;
    public function getEmployeeDocuments(string $employeeId): array;
    public function signDocument(string $id, string $signatureData): Document;
    public function acknowledgeDocument(string $id): Document;
}
```

---

## 4. State Machine

### 4.1 Document Lifecycle

```
Pending ──> AwaitingSignature ──> Signed ──> Acknowledged
    │                                │
    └──> Archived                    └──> Expired
                                         │
                                         └──> (renewal) ──> AwaitingSignature
```

---

## 5. Integration Architecture

### 5.1 Provided Services

| Consumer | Interface | Data |
|----------|-----------|------|
| ksf_HRM | DocumentServiceInterface | Employee documents |
| ksf_FA_Documents | DocumentServiceInterface | Document sync |
| ksf_Performance | DocumentServiceInterface | Review documents |

### 5.2 Consumed Services

| Provider | Interface | Data |
|---------|-----------|------|
| ksf_HRM | EmployeeServiceInterface | Employee records |
| ksf_Timesheets | TimeEntryServiceInterface | Document task time |

---

## 6. Error Handling

### 6.1 Exception Hierarchy

```
\Exception
└── KsfDocumentsException (base)
    ├── DocumentNotFoundException
    ├── DocumentExpiredException
    ├── InvalidSignatureException
    └── DocumentUploadException
```

---

## 7. File Structure

```
ksf_Documents/
├── composer.json
├── AGENTS.md
├── ProjectDcs/
│   ├── Business Requirements.md
│   ├── Architecture.md           ← THIS FILE
│   ├── Functional Requirements.md
│   ├── Use Case.md
│   ├── Test Plan.md
│   ├── UAT Plan.md
│   └── RTM.md
└── src/Ksfraser/Documents/
    ├── Entity/
    │   └── Document.php
    ├── Service/
    │   ├── DocumentService.php
    │   └── DocumentServiceInterface.php
    └── Exception/
        └── DocumentException.php
```

---

*Document Version: 1.0.0*
*Last Updated: 2026-05-11*