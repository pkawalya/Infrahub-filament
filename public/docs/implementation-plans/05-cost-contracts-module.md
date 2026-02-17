# Module 5: Cost, Contracts & Commercials
## Implementation Plan

**Priority:** 🟠 High | **Complexity:** Very High | **Duration:** 8-10 weeks  
**Requirements:** REQ-COST-001 to REQ-COST-035, REQ-CONT-001 to REQ-CONT-010, REQ-PAY-001 to REQ-PAY-012, REQ-CERT-001 to REQ-CERT-005, REQ-CLAIM-001 to REQ-CLAIM-006

---

## 1. Overview

Complete financial management system including budgets, cost tracking, contracts, change orders, payment applications, and claims management.

---

## 2. Sub-Modules

| Sub-Module | Requirements | Priority |
|------------|--------------|----------|
| 5.1 Project Budgets | REQ-COST-001 to 007 | Critical |
| 5.2 Cost Codes | REQ-COST-008 to 012 | Critical |
| 5.3 Commitments | REQ-COST-013 to 017 | High |
| 5.4 Change Orders | REQ-COST-018 to 024 | High |
| 5.5 Forecasting | REQ-COST-025 to 030 | High |
| 5.6 Actual vs Budget | REQ-COST-031 to 035 | High |
| 5.7 Main Contracts | REQ-CONT-001 to 005 | Critical |
| 5.8 Subcontracts | REQ-CONT-006 to 010 | High |
| 5.9 Payment Applications | REQ-PAY-001 to 007 | Critical |
| 5.10 Retainage | REQ-PAY-008 to 012 | High |
| 5.11 Certificates | REQ-CERT-001 to 005 | Medium |
| 5.12 Claims | REQ-CLAIM-001 to 006 | Medium |

---

## 3. Database Schema

```sql
-- Cost Codes
CREATE TABLE cost_codes (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT,
    project_id BIGINT,
    parent_id BIGINT,
    code VARCHAR(50) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    level INT DEFAULT 1,
    is_summary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_org (organization_id),
    INDEX idx_project (project_id),
    INDEX idx_parent (parent_id)
);

-- Budgets
CREATE TABLE budgets (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    version_name VARCHAR(100) NOT NULL,
    version_type ENUM('original', 'revised', 'approved') DEFAULT 'original',
    is_current BOOLEAN DEFAULT FALSE,
    total_amount DECIMAL(15,2) DEFAULT 0,
    contingency_amount DECIMAL(15,2) DEFAULT 0,
    approved_by BIGINT,
    approved_at TIMESTAMP,
    notes TEXT,
    created_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_project (project_id)
);

-- Budget Line Items
CREATE TABLE budget_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    budget_id BIGINT NOT NULL,
    cost_code_id BIGINT NOT NULL,
    description TEXT,
    quantity DECIMAL(15,4),
    unit VARCHAR(50),
    unit_cost DECIMAL(15,4),
    total_amount DECIMAL(15,2) NOT NULL,
    contingency DECIMAL(15,2) DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_budget (budget_id),
    INDEX idx_cost_code (cost_code_id)
);

-- Contracts
CREATE TABLE contracts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    contract_number VARCHAR(100) NOT NULL,
    title VARCHAR(500) NOT NULL,
    contract_type ENUM('main', 'subcontract', 'purchase_order') NOT NULL,
    pricing_type ENUM('lump_sum', 'unit_price', 'cost_plus', 'gmp') DEFAULT 'lump_sum',
    vendor_id BIGINT,
    client_id BIGINT,
    original_value DECIMAL(15,2) NOT NULL,
    approved_changes DECIMAL(15,2) DEFAULT 0,
    pending_changes DECIMAL(15,2) DEFAULT 0,
    current_value DECIMAL(15,2) GENERATED ALWAYS AS (original_value + approved_changes) STORED,
    start_date DATE,
    end_date DATE,
    retainage_percent DECIMAL(5,2) DEFAULT 0,
    payment_terms VARCHAR(255),
    scope_of_work TEXT,
    status ENUM('draft', 'executed', 'active', 'completed', 'closed', 'terminated') DEFAULT 'draft',
    created_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_project (project_id),
    INDEX idx_vendor (vendor_id)
);

-- Schedule of Values
CREATE TABLE schedule_of_values (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    contract_id BIGINT NOT NULL,
    line_number INT NOT NULL,
    cost_code_id BIGINT,
    description VARCHAR(500) NOT NULL,
    scheduled_value DECIMAL(15,2) NOT NULL,
    completed_previous DECIMAL(15,2) DEFAULT 0,
    completed_this_period DECIMAL(15,2) DEFAULT 0,
    stored_materials DECIMAL(15,2) DEFAULT 0,
    total_completed DECIMAL(15,2) GENERATED ALWAYS AS (completed_previous + completed_this_period + stored_materials) STORED,
    percent_complete DECIMAL(5,2) GENERATED ALWAYS AS (IF(scheduled_value > 0, total_completed / scheduled_value * 100, 0)) STORED,
    balance_to_finish DECIMAL(15,2) GENERATED ALWAYS AS (scheduled_value - total_completed) STORED,
    retainage DECIMAL(15,2) DEFAULT 0,
    INDEX idx_contract (contract_id)
);

-- Change Orders
CREATE TABLE change_orders (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    contract_id BIGINT NOT NULL,
    co_number VARCHAR(50) NOT NULL,
    title VARCHAR(500) NOT NULL,
    description TEXT,
    reason_code VARCHAR(50),
    amount DECIMAL(15,2) NOT NULL,
    schedule_impact_days INT DEFAULT 0,
    status ENUM('draft', 'pending', 'approved', 'rejected') DEFAULT 'draft',
    submitted_at TIMESTAMP,
    approved_by BIGINT,
    approved_at TIMESTAMP,
    created_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_project (project_id),
    INDEX idx_contract (contract_id)
);

-- Change Order Items
CREATE TABLE change_order_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    change_order_id BIGINT NOT NULL,
    cost_code_id BIGINT,
    description TEXT,
    quantity DECIMAL(15,4),
    unit VARCHAR(50),
    unit_cost DECIMAL(15,4),
    amount DECIMAL(15,2) NOT NULL
);

-- Payment Applications
CREATE TABLE payment_applications (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    contract_id BIGINT NOT NULL,
    application_number INT NOT NULL,
    period_from DATE,
    period_to DATE,
    original_contract_sum DECIMAL(15,2),
    change_orders_amount DECIMAL(15,2) DEFAULT 0,
    current_contract_sum DECIMAL(15,2),
    total_completed_stored DECIMAL(15,2) DEFAULT 0,
    retainage_held DECIMAL(15,2) DEFAULT 0,
    total_earned_less_retainage DECIMAL(15,2) DEFAULT 0,
    previous_certificates DECIMAL(15,2) DEFAULT 0,
    current_payment_due DECIMAL(15,2) DEFAULT 0,
    balance_to_finish DECIMAL(15,2) DEFAULT 0,
    status ENUM('draft', 'submitted', 'under_review', 'approved', 'paid') DEFAULT 'draft',
    submitted_at TIMESTAMP,
    approved_by BIGINT,
    approved_at TIMESTAMP,
    paid_at TIMESTAMP,
    notes TEXT,
    created_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_contract (contract_id)
);

-- Retainage Releases
CREATE TABLE retainage_releases (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    contract_id BIGINT NOT NULL,
    release_type ENUM('partial', 'full') NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    milestone VARCHAR(255),
    release_date DATE,
    status ENUM('pending', 'approved', 'released') DEFAULT 'pending',
    approved_by BIGINT,
    approved_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Certificates
CREATE TABLE certificates (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    certifiable_type VARCHAR(100), -- contract, vendor
    certifiable_id BIGINT,
    certificate_type ENUM('insurance', 'bond', 'license', 'safety', 'quality') NOT NULL,
    certificate_name VARCHAR(255),
    issuer VARCHAR(255),
    certificate_number VARCHAR(100),
    coverage_amount DECIMAL(15,2),
    issue_date DATE,
    expiry_date DATE,
    file_path VARCHAR(1000),
    status ENUM('active', 'expiring_soon', 'expired') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_expiry (expiry_date)
);

-- Claims
CREATE TABLE claims (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    contract_id BIGINT NOT NULL,
    claim_number VARCHAR(50) NOT NULL,
    title VARCHAR(500) NOT NULL,
    claim_basis ENUM('delay', 'disruption', 'extra_work', 'differing_conditions', 'other') NOT NULL,
    claimant_type ENUM('contractor', 'owner') NOT NULL,
    amount_claimed DECIMAL(15,2),
    time_extension_days INT,
    description TEXT,
    status ENUM('draft', 'submitted', 'under_review', 'negotiating', 'settled', 'disputed') DEFAULT 'draft',
    resolution TEXT,
    settled_amount DECIMAL(15,2),
    settled_days INT,
    submitted_at TIMESTAMP,
    resolved_at TIMESTAMP,
    created_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_project (project_id)
);

-- Cost Actuals
CREATE TABLE cost_actuals (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    cost_code_id BIGINT NOT NULL,
    transaction_date DATE NOT NULL,
    source_type VARCHAR(50), -- invoice, timesheet, equipment
    source_id BIGINT,
    description TEXT,
    amount DECIMAL(15,2) NOT NULL,
    vendor_id BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_project_code (project_id, cost_code_id),
    INDEX idx_date (transaction_date)
);

-- Cost Forecasts
CREATE TABLE cost_forecasts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    cost_code_id BIGINT NOT NULL,
    forecast_date DATE NOT NULL,
    scenario ENUM('optimistic', 'expected', 'pessimistic') DEFAULT 'expected',
    committed_costs DECIMAL(15,2) DEFAULT 0,
    pending_costs DECIMAL(15,2) DEFAULT 0,
    estimate_to_complete DECIMAL(15,2) DEFAULT 0,
    forecast_at_completion DECIMAL(15,2) DEFAULT 0,
    variance_to_budget DECIMAL(15,2) DEFAULT 0,
    assumptions TEXT,
    created_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 4. API Endpoints

```
# Cost Codes
GET    /api/v1/cost-codes
POST   /api/v1/cost-codes
PUT    /api/v1/cost-codes/{id}
POST   /api/v1/cost-codes/import

# Budgets
GET    /api/v1/projects/{id}/budgets
POST   /api/v1/projects/{id}/budgets
GET    /api/v1/budgets/{id}
PUT    /api/v1/budgets/{id}
POST   /api/v1/budgets/{id}/approve
POST   /api/v1/budgets/{id}/import

# Contracts
GET    /api/v1/projects/{id}/contracts
POST   /api/v1/projects/{id}/contracts
GET    /api/v1/contracts/{id}
PUT    /api/v1/contracts/{id}
GET    /api/v1/contracts/{id}/sov

# Change Orders
GET    /api/v1/contracts/{id}/change-orders
POST   /api/v1/contracts/{id}/change-orders
GET    /api/v1/change-orders/{id}
PUT    /api/v1/change-orders/{id}
POST   /api/v1/change-orders/{id}/approve

# Payment Applications
GET    /api/v1/contracts/{id}/payment-applications
POST   /api/v1/contracts/{id}/payment-applications
GET    /api/v1/payment-applications/{id}
PUT    /api/v1/payment-applications/{id}
POST   /api/v1/payment-applications/{id}/submit
POST   /api/v1/payment-applications/{id}/approve
GET    /api/v1/payment-applications/{id}/g702

# Retainage
GET    /api/v1/contracts/{id}/retainage
POST   /api/v1/contracts/{id}/retainage-release

# Certificates
GET    /api/v1/contracts/{id}/certificates
POST   /api/v1/contracts/{id}/certificates
GET    /api/v1/certificates/expiring

# Claims
GET    /api/v1/projects/{id}/claims
POST   /api/v1/projects/{id}/claims
GET    /api/v1/claims/{id}
PUT    /api/v1/claims/{id}

# Cost Reports
GET    /api/v1/projects/{id}/cost-summary
GET    /api/v1/projects/{id}/cost-by-code
GET    /api/v1/projects/{id}/earned-value
GET    /api/v1/projects/{id}/cash-flow
GET    /api/v1/projects/{id}/forecasts
```

---

## 5. Implementation Tasks

### Phase 1: Cost Codes & Budgets (Week 1-2)
- [ ] Cost code hierarchy management
- [ ] CSI MasterFormat import
- [ ] Budget versions
- [ ] Budget line items
- [ ] Budget approval workflow

### Phase 2: Contracts (Week 2-4)
- [ ] Contract CRUD
- [ ] Schedule of Values
- [ ] Contract amendments
- [ ] Vendor/client linking

### Phase 3: Change Orders (Week 4-5)
- [ ] Change order creation
- [ ] Multi-level approval
- [ ] Auto-update contract values
- [ ] Reason code analytics

### Phase 4: Payment Applications (Week 5-7)
- [ ] Payment app workflow
- [ ] SOV updates
- [ ] AIA G702/G703 generation
- [ ] Payment tracking

### Phase 5: Retainage & Certificates (Week 7-8)
- [ ] Retainage calculations
- [ ] Release workflow
- [ ] Certificate tracking
- [ ] Expiry alerts

### Phase 6: Forecasting & Reporting (Week 8-10)
- [ ] Cost actuals import
- [ ] EVM calculations
- [ ] Cash flow projections
- [ ] Variance analysis
- [ ] Claims management

---

## 6. Key Services

```php
// BudgetService
class BudgetService {
    public function createVersion(Project $project, array $data): Budget;
    public function importFromExcel(Budget $budget, UploadedFile $file): ImportResult;
    public function compareVersions(Budget $v1, Budget $v2): ComparisonResult;
    public function approve(Budget $budget, User $user): void;
}

// ContractService
class ContractService {
    public function calculateCurrentValue(Contract $contract): Decimal;
    public function updateSOV(Contract $contract, array $sov): void;
    public function getPaymentSummary(Contract $contract): PaymentSummary;
}

// CostReportService
class CostReportService {
    public function getSummary(Project $project): CostSummary;
    public function getEarnedValue(Project $project): EVMMetrics;
    public function getCashFlow(Project $project, Carbon $from, Carbon $to): CashFlowData;
    public function getVarianceByCode(Project $project): Collection;
}
```

---

## 7. Acceptance Criteria

- [ ] Budgets support multiple versions
- [ ] Cost codes hierarchical to unlimited levels
- [ ] Contracts track original + change orders
- [ ] Change orders auto-update contract values
- [ ] Payment apps generate AIA forms
- [ ] Retainage calculated automatically
- [ ] Certificate expiry alerts working
- [ ] EVM metrics calculated correctly
- [ ] Cash flow projections generated
- [ ] Claims linked to supporting documents
