# Module 3: Project Execution & Field Management
## Implementation Plan

**Priority:** 🔴 Critical | **Complexity:** High | **Duration:** 6-8 weeks  
**Requirements:** REQ-FIELD-001 to REQ-FIELD-028, REQ-QS-001 to REQ-QS-028, REQ-RFI-001 to REQ-RFI-015, REQ-SUB-001 to REQ-SUB-012

---

## 1. Overview

Mobile-first field operations module covering daily logs, quality & safety management, inspections, RFIs, and submittals with full offline support.

---

## 2. Sub-Modules

| Sub-Module | Requirements | Priority |
|------------|--------------|----------|
| 3.1 Daily Logs | REQ-FIELD-001 to 007 | Critical |
| 3.2 Weather Tracking | REQ-FIELD-008 to 011 | High |
| 3.3 Manpower & Equipment | REQ-FIELD-012 to 015 | High |
| 3.4 Site Photos/Videos | REQ-FIELD-016 to 022 | High |
| 3.5 Offline Functionality | REQ-FIELD-023 to 028 | Critical |
| 3.6 Inspections & Checklists | REQ-QS-001 to 008 | Critical |
| 3.7 NCRs & Defects | REQ-QS-009 to 016 | High |
| 3.8 Safety & Incidents | REQ-QS-017 to 028 | Critical |
| 3.9 RFI Management | REQ-RFI-001 to 015 | High |
| 3.10 Submittals | REQ-SUB-001 to 012 | High |

---

## 3. Database Schema

```sql
-- Daily Logs
CREATE TABLE daily_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    log_date DATE NOT NULL,
    supervisor_id BIGINT,
    area VARCHAR(255),
    trade VARCHAR(100),
    weather_conditions JSON,
    work_activities TEXT,
    progress_notes TEXT,
    issues_delays TEXT,
    visitors TEXT,
    deliveries TEXT,
    safety_observations TEXT,
    status ENUM('draft', 'submitted', 'approved') DEFAULT 'draft',
    submitted_at TIMESTAMP,
    locked_at TIMESTAMP,
    created_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_log (project_id, log_date, supervisor_id, area),
    INDEX idx_project_date (project_id, log_date)
);

-- Weather Records
CREATE TABLE weather_records (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    daily_log_id BIGINT NOT NULL,
    recorded_at TIMESTAMP NOT NULL,
    temp_high DECIMAL(5,2),
    temp_low DECIMAL(5,2),
    precipitation_type VARCHAR(50),
    precipitation_amount DECIMAL(5,2),
    wind_speed DECIMAL(5,2),
    wind_direction VARCHAR(10),
    conditions VARCHAR(100),
    source ENUM('manual', 'api') DEFAULT 'manual',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Manpower Logs
CREATE TABLE manpower_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    daily_log_id BIGINT NOT NULL,
    trade VARCHAR(100) NOT NULL,
    worker_count INT NOT NULL,
    hours_regular DECIMAL(5,2),
    hours_overtime DECIMAL(5,2),
    tasks_performed TEXT,
    cost_code VARCHAR(50),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Equipment Logs
CREATE TABLE equipment_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    daily_log_id BIGINT NOT NULL,
    equipment_type VARCHAR(100),
    equipment_id VARCHAR(100),
    operator_name VARCHAR(255),
    hours_operated DECIMAL(5,2),
    tasks_performed TEXT,
    fuel_consumption DECIMAL(8,2),
    maintenance_notes TEXT,
    cost_code VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Site Media
CREATE TABLE site_media (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    daily_log_id BIGINT,
    media_type ENUM('photo', 'video') NOT NULL,
    file_path VARCHAR(1000) NOT NULL,
    file_name VARCHAR(255),
    file_size BIGINT,
    mime_type VARCHAR(100),
    thumbnail_path VARCHAR(1000),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    captured_at TIMESTAMP,
    description TEXT,
    location_reference VARCHAR(255),
    tags JSON,
    markups JSON,
    linked_type VARCHAR(100),
    linked_id BIGINT,
    captured_by BIGINT,
    device_info JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_project (project_id),
    INDEX idx_date (captured_at),
    INDEX idx_linked (linked_type, linked_id)
);

-- Inspection Templates
CREATE TABLE inspection_templates (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT,
    organization_id BIGINT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    questions JSON,
    scoring_method ENUM('pass_fail', 'percentage', 'points'),
    pass_threshold DECIMAL(5,2),
    photo_required BOOLEAN DEFAULT FALSE,
    signature_required BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inspections
CREATE TABLE inspections (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    template_id BIGINT NOT NULL,
    inspection_number VARCHAR(50),
    location VARCHAR(255),
    area VARCHAR(255),
    scheduled_date DATE,
    performed_date DATE,
    inspector_id BIGINT,
    responses JSON,
    score DECIMAL(5,2),
    result ENUM('pass', 'fail', 'pending') DEFAULT 'pending',
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled') DEFAULT 'scheduled',
    notes TEXT,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    signature_path VARCHAR(1000),
    linked_contract_id BIGINT,
    completed_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_project (project_id),
    INDEX idx_date (performed_date)
);

-- NCRs / Defects
CREATE TABLE ncr_defects (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    ncr_number VARCHAR(50) NOT NULL,
    type ENUM('ncr', 'defect', 'snag') NOT NULL,
    title VARCHAR(500) NOT NULL,
    description TEXT,
    location VARCHAR(255),
    severity ENUM('critical', 'major', 'minor') DEFAULT 'minor',
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    responsible_party_id BIGINT,
    due_date DATE,
    estimated_cost DECIMAL(15,2),
    actual_cost DECIMAL(15,2),
    root_cause TEXT,
    corrective_action TEXT,
    preventive_action TEXT,
    status ENUM('open', 'in_progress', 'resolved', 'verified', 'closed') DEFAULT 'open',
    linked_inspection_id BIGINT,
    linked_document_id BIGINT,
    created_by BIGINT,
    verified_by BIGINT,
    verified_at TIMESTAMP,
    closed_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_project (project_id),
    INDEX idx_status (status)
);

-- Safety Observations
CREATE TABLE safety_observations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    observation_number VARCHAR(50),
    observation_type ENUM('safe_behavior', 'unsafe_condition', 'unsafe_act', 'near_miss', 'hazard') NOT NULL,
    category VARCHAR(100),
    location VARCHAR(255),
    description TEXT,
    personnel_involved TEXT,
    immediate_action TEXT,
    follow_up_required BOOLEAN DEFAULT FALSE,
    status ENUM('open', 'closed') DEFAULT 'open',
    observed_by BIGINT,
    observed_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Incidents
CREATE TABLE incidents (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    incident_number VARCHAR(50),
    incident_type ENUM('injury', 'property_damage', 'environmental', 'security') NOT NULL,
    injury_classification VARCHAR(50),
    incident_date DATE NOT NULL,
    incident_time TIME,
    location VARCHAR(255),
    description TEXT,
    personnel_involved JSON,
    witnesses JSON,
    injuries_description TEXT,
    treatment_provided TEXT,
    property_damage_description TEXT,
    immediate_response TEXT,
    root_cause TEXT,
    corrective_actions TEXT,
    status ENUM('reported', 'investigating', 'closed') DEFAULT 'reported',
    reported_by BIGINT,
    reported_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_project (project_id),
    INDEX idx_date (incident_date)
);

-- RFIs
CREATE TABLE rfis (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    rfi_number VARCHAR(50) NOT NULL,
    subject VARCHAR(500) NOT NULL,
    description TEXT,
    drawing_references JSON,
    specification_references JSON,
    cost_impact ENUM('yes', 'no', 'unknown') DEFAULT 'unknown',
    schedule_impact ENUM('yes', 'no', 'unknown') DEFAULT 'unknown',
    priority ENUM('low', 'normal', 'high', 'critical') DEFAULT 'normal',
    requested_by BIGINT,
    assigned_to BIGINT,
    required_response_date DATE,
    response_description TEXT,
    response_attachments JSON,
    cost_implications TEXT,
    schedule_implications TEXT,
    responded_by BIGINT,
    responded_at TIMESTAMP,
    status ENUM('draft', 'submitted', 'under_review', 'answered', 'closed') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_project (project_id),
    INDEX idx_status (status),
    INDEX idx_assigned (assigned_to)
);

-- Submittals
CREATE TABLE submittals (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    submittal_number VARCHAR(50) NOT NULL,
    specification_section VARCHAR(100),
    submittal_type ENUM('shop_drawings', 'product_data', 'samples', 'mock_ups', 'other') DEFAULT 'other',
    title VARCHAR(500) NOT NULL,
    description TEXT,
    submitted_by BIGINT,
    assigned_to BIGINT,
    required_action VARCHAR(100),
    required_response_date DATE,
    response_action ENUM('approved', 'approved_as_noted', 'revise_resubmit', 'rejected'),
    response_comments TEXT,
    response_markups JSON,
    responded_by BIGINT,
    responded_at TIMESTAMP,
    revision_number INT DEFAULT 1,
    status ENUM('draft', 'submitted', 'under_review', 'responded', 'closed') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_project (project_id),
    INDEX idx_status (status)
);
```

---

## 4. API Endpoints

```
# Daily Logs
GET    /api/v1/projects/{id}/daily-logs
POST   /api/v1/projects/{id}/daily-logs
GET    /api/v1/daily-logs/{id}
PUT    /api/v1/daily-logs/{id}
POST   /api/v1/daily-logs/{id}/submit
GET    /api/v1/daily-logs/{id}/report

# Weather
POST   /api/v1/daily-logs/{id}/weather
GET    /api/v1/projects/{id}/weather-history

# Manpower & Equipment
POST   /api/v1/daily-logs/{id}/manpower
POST   /api/v1/daily-logs/{id}/equipment

# Site Media
POST   /api/v1/projects/{id}/media
GET    /api/v1/projects/{id}/media
GET    /api/v1/media/{id}
PUT    /api/v1/media/{id}
DELETE /api/v1/media/{id}
POST   /api/v1/media/{id}/markup

# Inspections
GET    /api/v1/inspection-templates
POST   /api/v1/inspection-templates
GET    /api/v1/projects/{id}/inspections
POST   /api/v1/projects/{id}/inspections
GET    /api/v1/inspections/{id}
PUT    /api/v1/inspections/{id}
POST   /api/v1/inspections/{id}/complete

# NCRs/Defects
GET    /api/v1/projects/{id}/ncrs
POST   /api/v1/projects/{id}/ncrs
GET    /api/v1/ncrs/{id}
PUT    /api/v1/ncrs/{id}
POST   /api/v1/ncrs/{id}/resolve
POST   /api/v1/ncrs/{id}/verify
GET    /api/v1/projects/{id}/snag-list

# Safety
GET    /api/v1/projects/{id}/safety-observations
POST   /api/v1/projects/{id}/safety-observations
GET    /api/v1/projects/{id}/incidents
POST   /api/v1/projects/{id}/incidents
GET    /api/v1/incidents/{id}
PUT    /api/v1/incidents/{id}

# RFIs
GET    /api/v1/projects/{id}/rfis
POST   /api/v1/projects/{id}/rfis
GET    /api/v1/rfis/{id}
PUT    /api/v1/rfis/{id}
POST   /api/v1/rfis/{id}/respond

# Submittals
GET    /api/v1/projects/{id}/submittals
POST   /api/v1/projects/{id}/submittals
GET    /api/v1/submittals/{id}
PUT    /api/v1/submittals/{id}
POST   /api/v1/submittals/{id}/respond
```

---

## 5. Implementation Tasks

### Phase 1: Daily Logs (Week 1-2)
- [ ] Daily log form with all fields
- [ ] Weather entry (manual + API integration)
- [ ] Manpower logging
- [ ] Equipment logging
- [ ] Log submission and locking
- [ ] Summary reports

### Phase 2: Site Media (Week 2-3)
- [ ] Camera integration (mobile)
- [ ] GPS metadata capture
- [ ] Photo organization and tagging
- [ ] Markup/annotation tools
- [ ] Time-lapse sequences

### Phase 3: Offline Functionality (Week 3-4)
- [ ] IndexedDB storage
- [ ] Background sync
- [ ] Conflict detection/resolution
- [ ] Selective sync priorities

### Phase 4: Inspections (Week 4-5)
- [ ] Template builder with question types
- [ ] Mobile inspection form
- [ ] Signature capture
- [ ] Auto-scoring
- [ ] Report generation

### Phase 5: NCRs & Defects (Week 5-6)
- [ ] NCR/Defect logging
- [ ] Workflow routing
- [ ] Root cause analysis
- [ ] Snag list generation
- [ ] Metrics dashboard

### Phase 6: Safety (Week 6-7)
- [ ] Safety observation forms
- [ ] Incident reporting
- [ ] OSHA classification
- [ ] Corrective action tracking

### Phase 7: RFIs & Submittals (Week 7-8)
- [ ] RFI creation and response
- [ ] Submittal workflow
- [ ] Drawing/spec linking
- [ ] Response time tracking
- [ ] Registers and exports

---

## 6. Mobile App Features

```
React Native / Flutter Components:
├── DailyLogForm - Multi-step form with sections
├── CameraCapture - Photo/video with GPS
├── OfflineQueue - Pending sync indicator
├── InspectionForm - Dynamic questions
├── SignatureCapture - Touch signature
├── RFIDetail - View/respond to RFIs
└── IncidentReport - Step-by-step incident form
```

---

## 7. Acceptance Criteria

- [ ] Daily logs fillable offline, sync when online
- [ ] Photos capture GPS and timestamp automatically
- [ ] Inspections auto-calculate pass/fail
- [ ] Failed inspections generate NCRs automatically
- [ ] RFI response time tracked and reported
- [ ] Submittals support multiple revision cycles
- [ ] Safety incidents notify designated personnel
- [ ] All data syncs within 5 minutes when online
