# Module 10: Compliance & Standards
## Implementation Plan

**Priority:** 🟡 Medium | **Complexity:** Medium | **Duration:** 2-3 weeks  
**Requirements:** REQ-COMP-001 to REQ-COMP-010

---

## 1. Overview

Industry standards compliance (ISO 19650, BIM), regional regulations (GDPR, CCPA), and security certifications (SOC 2, ISO 27001).

---

## 2. Requirements Checklist

### Industry Standards (REQ-COMP-001 to 004)
- [ ] ISO 19650 information management
- [ ] BIM Level 2 and Level 3 workflows
- [ ] CSI MasterFormat cost coding
- [ ] AIA document standards

### Regional Regulations (REQ-COMP-005 to 007)
- [ ] GDPR compliance (EU)
- [ ] CCPA compliance (California)
- [ ] PIPEDA compliance (Canada)
- [ ] Data residency support
- [ ] Localized formats

### Security Standards (REQ-COMP-008 to 010)
- [ ] SOC 2 Type II controls
- [ ] ISO 27001 compliance
- [ ] Annual security audits

---

## 3. Database Schema

```sql
-- Compliance Settings
CREATE TABLE compliance_settings (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT NOT NULL,
    gdpr_enabled BOOLEAN DEFAULT FALSE,
    ccpa_enabled BOOLEAN DEFAULT FALSE,
    data_residency_region VARCHAR(50),
    retention_policy_days INT DEFAULT 2555, -- 7 years
    iso19650_enabled BOOLEAN DEFAULT FALSE,
    bim_level ENUM('none', 'level2', 'level3') DEFAULT 'none',
    cost_code_standard ENUM('csi', 'uniformat', 'custom') DEFAULT 'csi',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Data Subject Requests (GDPR)
CREATE TABLE data_subject_requests (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    request_type ENUM('access', 'rectification', 'erasure', 'portability', 'restriction') NOT NULL,
    subject_email VARCHAR(255) NOT NULL,
    subject_name VARCHAR(255),
    description TEXT,
    status ENUM('pending', 'processing', 'completed', 'rejected') DEFAULT 'pending',
    response TEXT,
    completed_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (subject_email)
);

-- Consent Records
CREATE TABLE consent_records (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    consent_type VARCHAR(100) NOT NULL,
    granted BOOLEAN NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id)
);

-- Compliance Audits
CREATE TABLE compliance_audits (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    audit_type VARCHAR(100) NOT NULL,
    auditor VARCHAR(255),
    audit_date DATE NOT NULL,
    scope TEXT,
    findings TEXT,
    recommendations TEXT,
    status ENUM('scheduled', 'in_progress', 'completed') DEFAULT 'scheduled',
    report_path VARCHAR(1000),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Security Controls
CREATE TABLE security_controls (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    control_id VARCHAR(50) NOT NULL,
    framework VARCHAR(50) NOT NULL, -- SOC2, ISO27001
    name VARCHAR(255) NOT NULL,
    description TEXT,
    implementation_status ENUM('not_implemented', 'partial', 'implemented') DEFAULT 'not_implemented',
    evidence_path VARCHAR(1000),
    last_reviewed_at TIMESTAMP,
    next_review_at TIMESTAMP,
    INDEX idx_framework (framework)
);
```

---

## 4. API Endpoints

```
# Compliance Settings
GET    /api/v1/compliance/settings
PUT    /api/v1/compliance/settings

# GDPR
POST   /api/v1/gdpr/data-request
GET    /api/v1/gdpr/data-requests
PUT    /api/v1/gdpr/data-requests/{id}
GET    /api/v1/gdpr/export-data/{userId}
DELETE /api/v1/gdpr/erase-data/{userId}
POST   /api/v1/gdpr/consent
GET    /api/v1/gdpr/consent/{userId}

# Compliance Reports
GET    /api/v1/compliance/iso19650-status
GET    /api/v1/compliance/audit-trail
GET    /api/v1/compliance/security-controls

# Localization
GET    /api/v1/locale/formats
PUT    /api/v1/users/{id}/locale
```

---

## 5. Implementation Tasks

### Phase 1: ISO 19650 (Week 1)
- [ ] CDE container workflows
- [ ] Suitability codes
- [ ] Naming conventions
- [ ] BIM compliance checks
- [ ] Compliance dashboard

### Phase 2: GDPR/Privacy (Week 1-2)
- [ ] Consent management
- [ ] Data subject requests
- [ ] Right to erasure
- [ ] Data export
- [ ] Privacy policy integration

### Phase 3: Localization (Week 2)
- [ ] Date/time formats
- [ ] Number/currency formats
- [ ] Measurement units
- [ ] Timezone handling

### Phase 4: Security Framework (Week 2-3)
- [ ] SOC 2 control mapping
- [ ] ISO 27001 control mapping
- [ ] Evidence collection
- [ ] Audit scheduling
- [ ] Compliance reporting

---

## 6. ISO 19650 Implementation

### CDE Containers
| Container | Purpose | Transition Rules |
|-----------|---------|------------------|
| WIP | Work in Progress | Author only |
| Shared | Team review | Requires check |
| Published | Approved for use | Approval workflow |
| Archive | Superseded versions | Auto on new version |

### Suitability Codes
| Code | Meaning |
|------|---------|
| S0 | WIP |
| S1 | Fit for coordination |
| S2 | Fit for information |
| S3 | Fit for review/comment |
| S4 | Fit for stage approval |
| S5 | Fit for costing |
| S6 | Fit for manufacture |
| S7 | Fit for construction |

---

## 7. GDPR Features

| Right | Implementation |
|-------|----------------|
| Access | User data export (JSON) |
| Rectification | Profile edit + admin tools |
| Erasure | Anonymization + deletion |
| Portability | Machine-readable export |
| Restriction | Processing flags |

---

## 8. Key Services

```php
// ComplianceService
class ComplianceService {
    public function checkISO19650(Project $project): ComplianceReport;
    public function getComplianceScore(Organization $org): Score;
    public function generateAuditReport(string $framework): Report;
}

// GDPRService
class GDPRService {
    public function handleDataRequest(DataSubjectRequest $request): void;
    public function exportUserData(User $user): string;
    public function eraseUserData(User $user): void;
    public function recordConsent(User $user, string $type, bool $granted): void;
}

// LocaleService
class LocaleService {
    public function formatDate(Carbon $date, User $user): string;
    public function formatCurrency(float $amount, User $user): string;
    public function formatMeasurement(float $value, string $unit, User $user): string;
}
```

---

## 9. Acceptance Criteria

- [ ] ISO 19650 containers functioning
- [ ] Suitability codes enforced
- [ ] BIM workflows available
- [ ] CSI cost codes supported
- [ ] GDPR data requests processed
- [ ] User data exportable
- [ ] User data erasable
- [ ] Consent records maintained
- [ ] Date/number formats localized
- [ ] Security controls tracked
