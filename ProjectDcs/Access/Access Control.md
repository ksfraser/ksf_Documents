# Documents Module - Access Control Specification

## Document Information

| Field | Value |
|-------|-------|
| Document Title | Access Control Specification |
| Module | ksf_Documents |
| Version | 1.0.0 |
| Author | KSF Development Team |
| Last Updated | May 2026 |

---

## 1. Access Control Overview

### 1.1 Purpose

Access control for ksf_Documents:
- **Document Owners** fully manage their documents
- **Team Members** access shared documents
- **Contract Users** access contract-linked documents
- **System Admin** manages all documents

### 1.2 Key Principles

| Principle | Description |
|-----------|-------------|
| Ownership | Creators control their documents |
| Sharing | Documents can be explicitly shared |
| Inheritance | Folder access propagates to contents |
| Contract Linkage | Contract access affects document visibility |

---

## 2. Role Definitions

| Role | Access Level |
|------|--------------|
| Document Owner | Own documents full access |
| Folder Owner | Folder + contents management |
| Team Member | Shared documents |
| Contract User | Contract-linked documents |
| Public | Documents marked public |
| System Admin | All documents |

---

## 3. Record-Level Access

### 3.1 Document Metadata

| Field | Owner | Team | Contract User | Public |
|-------|-------|------|---------------|--------|
| File Name | Read/Write | Read | Read | Read |
| Description | Read/Write | Read | Read | Read |
| Content | Read/Write | Read/Download | Read/Download | Read/Download |
| Tags | Read/Write | Read/Write | Read | Read/Write |
| Permissions | Read/Write | Hidden | Hidden | Hidden |
| Version History | Read/Write | Read | Read | Hidden |

### 3.2 Folder Structure

| Field | Owner | Team Member | Admin |
|-------|-------|-------------|-------|
| Create Folder | Yes | No | Yes |
| Delete Folder | Yes | No | Yes |
| Add Documents | Yes | Yes (within shared) | Yes |
| Set Permissions | Yes | No | Yes |
| View Contents | Yes | Yes (if shared) | Yes |

---

## 4. Contract-Document Linkage

### 4.1 Document Types

| Type | Access Rule |
|------|-------------|
| Contract Documents | Per contract access |
| Invoices | Customer sees own |
| Employee Documents | HR access + self |
| Project Documents | Per project access |
| Shared Documents | Per sharing settings |

### 4.2 Visibility Matrix

| Document Type | Employee | Customer | Manager | Admin |
|---------------|----------|----------|---------|-------|
| Own Documents | Full | Full | Full | Full |
| Shared Team | Read | N/A | Read | Full |
| Project Docs | Read (assigned) | N/A | Read (team) | Full |
| Contract Docs | Hidden | Per contract | Read | Full |
| Public Docs | Read | Read | Read | Full |

---

## 5. Sharing Mechanisms

### 5.1 Direct Sharing

```
Document → Share → Select Users/Folders → Set Permission Level
```

| Permission | Actions |
|------------|---------|
| View | Read + Download |
| Edit | View + Upload new version |
| Manage | View + Edit + Delete + Share |

### 5.2 Folder Sharing

Shared folders inherit permissions to contents:
- Subfolders can override parent permissions
- Most restrictive permission wins
- Exception: Admin override

---

## 6. Sensitive Documents

### 6.1 Classification Levels

| Level | Access Required |
|-------|-----------------|
| Public | Any authenticated user |
| Internal | Team members |
| Confidential | Explicit access + NDA |
| Restricted | Owner + Admin only |

### 6.2 Restricted Fields

| Field | Access |
|-------|--------|
| Salary Documents | HR Admin + Self |
| Legal Documents | Legal + Admin |
| M&A Documents | Executive + Admin |
| Personal Documents | Self + HR Admin |

---

## 7. Family Company Considerations

### 7.1 Parent-Child Visibility

- Parent company users may see child company documents if shared
- Separate legal entities = separate document spaces
- Gift flag not typically applicable to documents

### 7.2 Document Privacy

Documents containing family information:
- Normal access rules apply
- Sensitive family data: HR Admin可见 only

---

## 8. FA Integration

### 8.1 FrontAccounting Documents

Access to ksf_FA_Documents:
- Invoice PDFs: Customer + Finance roles
- Tax Documents: Finance + Admin
- Audit Reports: Admin + Auditors

### 8.2 Document Categories

| Category | Visibility |
|----------|------------|
| Sales Invoices | Customer + Finance |
| Purchase Invoices | Finance only |
| Payments | Finance + Admin |
| Statements | Customer (own) + Finance |

---

## 9. Revision History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0.0 | May 2026 | KSF Development Team | Initial specification |