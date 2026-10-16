# Functional Requirements - ksf_Documents

## Document Information
- **Module**: ksf_Documents
- **Version**: 1.0.0
- **Date**: 2026-05-11
- **Status**: Implemented
- **Author**: KSFII Development Team

---

## 1. Overview

### 1.1 Purpose
ksf_Documents provides document management for employee records including contracts, tax forms, policies, and performance reviews.

### 1.2 Scope
- Document upload and storage
- Version control
- Electronic signatures
- Expiry tracking and reminders
- Integration with HRM

---

## 2. Core Entity

### 2.1 Document

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| id | string | Yes | UUID |
| employee_id | string | Yes | FK to Employee |
| type | enum | Yes | DocumentType |
| name | string | Yes | Document name |
| file_path | string | Yes | Storage path |
| mime_type | string | Yes | File type |
| version | int | Yes | Version number |
| expiry_date | Date | No | Expiration date |
| status | enum | Yes | DocumentStatus |
| signature_data | text | No | Electronic signature |
| signed_at | DateTime | No | Signature timestamp |
| acknowledged | bool | Yes | Default false |
| acknowledged_at | DateTime | No | Ack timestamp |
| created_at | DateTime | Yes | Auto |
| updated_at | DateTime | Yes | Auto |

---

## 3. Functional Requirements

### FR-DOC-001: Document Upload
**Requirement**: System shall upload and store employee documents.

**Features**:
- Upload documents (PDF, DOC, DOCX, images)
- Assign to employee
- Set document type
- Generate storage path
- Track upload timestamp

### FR-DOC-002: Version Control
**Requirement**: System shall maintain document versions.

**Features**:
- Auto-increment version on update
- Store previous versions
- View version history
- Restore previous version
- Compare versions

### FR-DOC-003: Electronic Signatures
**Requirement**: System shall support electronic signatures.

**Features**:
- Capture signature data
- Record signing timestamp
- Verify signature integrity
- Signature required for contract types

### FR-DOC-004: Acknowledgment Tracking
**Requirement**: System shall track document acknowledgment.

**Features**:
- Mark document as acknowledged
- Record acknowledgment timestamp
- Require acknowledgment for policies
- Auto-reminder for pending acknowledgments

### FR-DOC-005: Expiry Tracking
**Requirement**: System shall track document expiry.

**Features**:
- Set expiry date for applicable documents
- Identify expired documents
- Send expiry reminders
- Trigger renewal workflow
- Archive expired documents

### FR-DOC-006: Document Retrieval
**Requirement**: System shall retrieve documents efficiently.

**Features**:
- Get by employee
- Filter by type
- Filter by status
- Search by name
- Get expiring documents

### FR-DOC-007: RBAC Integration
**Requirement**: All document and folder queries MUST be filtered through RBAC.

**Features**:
- JOIN against 0_rbac_record_access for all document-fetching queries
- JOIN against 0_rbac_team_members for current user resolution
- Support PUBLIC and FULL projections per entity
- Enforce team-based access at query level

### FR-DOC-008: DTO Projections
**Requirement**: Document entity shall define PUBLIC and FULL data projections.

**Features**:
- PUBLIC: id, name, type, status, version, expiry_date, created_at, employee_id
- FULL: all fields including file_path, mime_type, signature_data, signed_at, internal_notes, audit_trail
- Folder: PUBLIC (id, name, parent_id) vs FULL (description, owner_id, access_control_list)
- DocumentVersion: PUBLIC (version_number, created_by) vs FULL (file_path, file_hash, file_size)

### FR-DOC-009: Soft Delete
**Requirement**: Documents shall support soft delete.

**Features**:
- `deleted = 1` flag, `deleted_by`, `deleted_at` fields
- Hard delete restricted to super-admin
- Deleted records gated by `can_view_deleted` type-level permission

### FR-DOC-010: Access Inheritance
**Requirement**: Document access shall cascade to child entities.

**Features**:
- Team access to document inherits to document_versions
- Soft-deleted document restricts all child version visibility
- Folder-level access resolved separately via query-level rule

### FR-DOC-011: Audit Logging
**Requirement**: All document access and lifecycle events shall be audited.

**Features**:
- Log document uploads, signatures, acknowledgments, deletions, restorations
- Log RBAC grant/revoke operations
- Append-only audit trail with no application-level UPDATE/DELETE

---

## 4. Document Types

| Type | Signature Required | Expiry | Ack Required |
|------|-------------------|--------|--------------|
| Employment Contract | Yes | 1 year | Yes |
| Tax Form | No | Annual | No |
| Benefits Enrollment | Yes | Annual | Yes |
| Policy Acknowledgment | Yes | None | Yes |
| Performance Review | Yes | None | Yes |
| Training Certification | Yes | 1-3 years | No |
| Work Permit | Yes | Varies | No |

---

## 5. Events

| Event | Trigger |
|-------|---------|
| document.uploaded | New document uploaded |
| document.signed | Document signed |
| document.acknowledged | Document acknowledged |
| document.expiring | Document within 30 days of expiry |
| document.expired | Document past expiry date |

---

*Document Version: 1.0.0*
*Last Updated: 2026-05-11*