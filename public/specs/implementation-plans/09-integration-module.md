# Module 9: Integration & Interoperability
## Implementation Plan

**Priority:** 🟡 Medium | **Complexity:** High | **Duration:** 4-6 weeks  
**Requirements:** REQ-INT-001 to REQ-INT-013

---

## 1. Overview

API access for external integrations, pre-built connectors for common systems, and bulk import/export capabilities.

---

## 2. Requirements Checklist

### API Access (REQ-INT-001 to 005)
- [ ] RESTful API for all major functions
- [ ] OAuth 2.0 + API key authentication
- [ ] Comprehensive documentation
- [ ] Configurable rate limits

### Integration Points (REQ-INT-006 to 008)
- [ ] Accounting systems (QuickBooks, Sage, SAP)
- [ ] ERP systems
- [ ] BIM/CAD software (Autodesk, Revit)
- [ ] Scheduling tools (MS Project, P6)
- [ ] Document management systems
- [ ] Email systems (Outlook, Gmail)
- [ ] SSO providers (SAML, OIDC)
- [ ] Bi-directional sync
- [ ] Error logging and alerts

### Data Import/Export (REQ-INT-009 to 013)
- [ ] Excel import
- [ ] CSV import
- [ ] XML/JSON import
- [ ] Import templates
- [ ] Data validation
- [ ] Bulk export with metadata

---

## 3. Database Schema

```sql
-- API Keys
CREATE TABLE api_keys (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    key_hash VARCHAR(255) NOT NULL,
    key_prefix VARCHAR(10) NOT NULL,
    scopes JSON,
    rate_limit INT DEFAULT 1000,
    last_used_at TIMESTAMP,
    expires_at TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    created_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_prefix (key_prefix)
);

-- OAuth Clients
CREATE TABLE oauth_clients (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT,
    name VARCHAR(255) NOT NULL,
    client_id VARCHAR(100) UNIQUE NOT NULL,
    client_secret VARCHAR(255) NOT NULL,
    redirect_uris JSON,
    scopes JSON,
    is_confidential BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Integration Connections
CREATE TABLE integration_connections (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT NOT NULL,
    project_id BIGINT,
    integration_type VARCHAR(100) NOT NULL,
    name VARCHAR(255) NOT NULL,
    config JSON,
    credentials JSON,
    sync_settings JSON,
    last_sync_at TIMESTAMP,
    last_sync_status ENUM('success', 'failed', 'partial'),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Sync Logs
CREATE TABLE integration_sync_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    connection_id BIGINT NOT NULL,
    sync_type ENUM('full', 'incremental', 'push', 'pull') NOT NULL,
    started_at TIMESTAMP NOT NULL,
    completed_at TIMESTAMP,
    status ENUM('running', 'completed', 'failed') DEFAULT 'running',
    records_processed INT DEFAULT 0,
    records_created INT DEFAULT 0,
    records_updated INT DEFAULT 0,
    records_failed INT DEFAULT 0,
    error_log JSON,
    INDEX idx_connection (connection_id)
);

-- Import Jobs
CREATE TABLE import_jobs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    import_type VARCHAR(100) NOT NULL,
    file_path VARCHAR(1000) NOT NULL,
    file_name VARCHAR(255),
    mapping JSON,
    status ENUM('pending', 'validating', 'importing', 'completed', 'failed') DEFAULT 'pending',
    total_rows INT,
    processed_rows INT DEFAULT 0,
    success_rows INT DEFAULT 0,
    failed_rows INT DEFAULT 0,
    errors JSON,
    started_at TIMESTAMP,
    completed_at TIMESTAMP,
    created_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Webhooks
CREATE TABLE webhooks (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT NOT NULL,
    project_id BIGINT,
    name VARCHAR(255) NOT NULL,
    url VARCHAR(1000) NOT NULL,
    events JSON,
    secret VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    last_triggered_at TIMESTAMP,
    failure_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Webhook Logs
CREATE TABLE webhook_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    webhook_id BIGINT NOT NULL,
    event VARCHAR(100) NOT NULL,
    payload JSON,
    response_status INT,
    response_body TEXT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_webhook (webhook_id)
);
```

---

## 4. API Endpoints

```
# API Keys
GET    /api/v1/api-keys
POST   /api/v1/api-keys
DELETE /api/v1/api-keys/{id}
POST   /api/v1/api-keys/{id}/regenerate

# OAuth
POST   /oauth/token
POST   /oauth/revoke
GET    /oauth/authorize

# Integrations
GET    /api/v1/integrations
POST   /api/v1/integrations
GET    /api/v1/integrations/{id}
PUT    /api/v1/integrations/{id}
DELETE /api/v1/integrations/{id}
POST   /api/v1/integrations/{id}/sync
GET    /api/v1/integrations/{id}/logs

# Import/Export
POST   /api/v1/import/upload
POST   /api/v1/import/validate
POST   /api/v1/import/execute
GET    /api/v1/import/{id}/status
GET    /api/v1/import/templates/{type}
POST   /api/v1/export
GET    /api/v1/export/{id}/download

# Webhooks
GET    /api/v1/webhooks
POST   /api/v1/webhooks
PUT    /api/v1/webhooks/{id}
DELETE /api/v1/webhooks/{id}
POST   /api/v1/webhooks/{id}/test
GET    /api/v1/webhooks/{id}/logs
```

---

## 5. Implementation Tasks

### Phase 1: API Foundation (Week 1-2)
- [ ] API versioning
- [ ] API key management
- [ ] OAuth 2.0 implementation
- [ ] Rate limiting
- [ ] OpenAPI/Swagger docs

### Phase 2: Webhooks (Week 2-3)
- [ ] Webhook management
- [ ] Event dispatching
- [ ] Retry logic
- [ ] Signature verification

### Phase 3: Import System (Week 3-4)
- [ ] File upload handling
- [ ] Template generation
- [ ] Mapping interface
- [ ] Validation engine
- [ ] Background processing
- [ ] Error reporting

### Phase 4: Pre-built Integrations (Week 4-6)
- [ ] QuickBooks connector
- [ ] SSO (SAML 2.0)
- [ ] MS Project sync
- [ ] Autodesk integration
- [ ] Email integrations

---

## 6. Integration Types

| System | Sync Type | Data |
|--------|-----------|------|
| QuickBooks | Bi-directional | Invoices, Payments, Vendors |
| Sage | Bi-directional | Costs, Invoices |
| SAP | Bi-directional | Full accounting |
| MS Project | Import/Export | Schedules |
| Primavera P6 | Import/Export | Schedules |
| Autodesk BIM 360 | Sync | Documents, Models |
| Outlook | Push | Emails, Calendar |
| Azure AD / Okta | SSO | Users |

---

## 7. Key Services

```php
// ApiKeyService
class ApiKeyService {
    public function generate(Organization $org, array $scopes): ApiKey;
    public function validate(string $key): ?ApiKey;
    public function checkRateLimit(ApiKey $key): bool;
}

// IntegrationService
class IntegrationService {
    public function connect(string $type, array $config): IntegrationConnection;
    public function sync(IntegrationConnection $conn, string $direction): SyncResult;
    public function getConnector(string $type): IntegrationConnector;
}

// ImportService
class ImportService {
    public function validate(UploadedFile $file, string $type, array $mapping): ValidationResult;
    public function import(ImportJob $job): void;
    public function getTemplate(string $type): string;
}
```

---

## 8. Acceptance Criteria

- [ ] API keys authenticate requests
- [ ] OAuth 2.0 flow complete
- [ ] Rate limits enforced
- [ ] Swagger docs accessible
- [ ] Webhooks deliver reliably
- [ ] Excel/CSV imports work
- [ ] Import validation catches errors
- [ ] At least 2 integrations working
- [ ] SSO login functional
