# UAT Plan - ksf_Documents

## Document Information
- **Module**: ksf_Documents
- **Version**: 1.0.0
- **Date**: 2026-05-11
- **Status**: Implemented
- **Author**: KSFII Development Team

---

## 1. UAT Scenarios

### UAT-DOC-001: HR Admin Uploads Contract

**Steps**:
1. Login as HR Admin
2. Navigate to Employee > Documents
3. Select employee
4. Click "Upload Document"
5. Select type "Employment Contract"
6. Upload PDF file
7. Submit

**Expected**:
- Document uploaded successfully
- Status = AwaitingSignature
- Document appears in list

---

### UAT-DOC-002: Employee Signs Contract

**Steps**:
1. Login as Employee
2. Navigate to My Documents
3. View pending document
4. Review contract content
5. Apply digital signature
6. Submit signature

**Expected**:
- Signature recorded
- Signed timestamp recorded
- Status = Signed

---

### UAT-DOC-003: Employee Acknowledges Policy

**Steps**:
1. Login as Employee
2. Navigate to My Documents
3. View signed policy
4. Read policy content
5. Click "Acknowledge"
6. Confirm acknowledgment

**Expected**:
- Acknowledgment recorded
- Status = Acknowledged

---

### UAT-DOC-004: Expiry Reminder

**Precondition**: Document expires in 30 days

**Steps**:
1. System runs nightly cron
2. Identify expiring documents
3. Send email notification
4. Generate task for HR

**Expected**:
- Email sent to HR Admin
- Task created for renewal

---

*Document Version: 1.0.0*
*Last Updated: 2026-05-11*