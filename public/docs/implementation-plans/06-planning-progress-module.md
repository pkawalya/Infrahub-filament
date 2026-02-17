# Module 6: Planning & Progress Control
## Implementation Plan

**Priority:** 🟠 High | **Complexity:** High | **Duration:** 6-8 weeks  
**Requirements:** REQ-PLAN-001 to REQ-PLAN-027

---

## 1. Overview

Schedule management and progress tracking with Gantt chart visualization, milestone tracking, look-ahead planning, and integration with cost modules for earned value analysis.

---

## 2. Requirements Checklist

### Program of Works (REQ-PLAN-001 to 008)
- [ ] Gantt chart visualization
- [ ] Import from MS Project/Primavera P6
- [ ] Activity management with all fields
- [ ] Critical path calculation
- [ ] Multiple baselines
- [ ] Resource leveling
- [ ] Export to standard formats

### Milestones (REQ-PLAN-009 to 013)
- [ ] Milestone creation and tracking
- [ ] Achievement notifications
- [ ] Dashboard display
- [ ] Performance metrics

### Look-Ahead Planning (REQ-PLAN-014 to 018)
- [ ] Short-term planning (2-6 weeks)
- [ ] Resource requirements
- [ ] Constraint tracking
- [ ] Reliability metrics

### Progress Tracking (REQ-PLAN-019 to 023)
- [ ] Multiple tracking methods
- [ ] Mobile progress updates
- [ ] Roll-up calculations
- [ ] S-curve visualization

### Integration (REQ-PLAN-024 to 027)
- [ ] Link to cost codes
- [ ] Link to tasks and documents
- [ ] Earned value comparison
- [ ] Delay impact analysis

---

## 3. Database Schema

```sql
-- Schedules
CREATE TABLE schedules (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    schedule_type ENUM('master', 'baseline', 'current', 'lookahead') DEFAULT 'current',
    baseline_number INT,
    start_date DATE,
    end_date DATE,
    working_days_per_week INT DEFAULT 5,
    calendar_settings JSON,
    is_active BOOLEAN DEFAULT TRUE,
    imported_from VARCHAR(50),
    imported_file_path VARCHAR(1000),
    created_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_project (project_id)
);

-- Schedule Activities
CREATE TABLE schedule_activities (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    schedule_id BIGINT NOT NULL,
    parent_id BIGINT,
    activity_id VARCHAR(50) NOT NULL,
    wbs_code VARCHAR(100),
    name VARCHAR(500) NOT NULL,
    description TEXT,
    activity_type ENUM('task', 'milestone', 'summary', 'hammock') DEFAULT 'task',
    duration_days INT DEFAULT 0,
    planned_start DATE,
    planned_finish DATE,
    actual_start DATE,
    actual_finish DATE,
    forecast_start DATE,
    forecast_finish DATE,
    percent_complete DECIMAL(5,2) DEFAULT 0,
    remaining_duration INT,
    total_float INT DEFAULT 0,
    free_float INT DEFAULT 0,
    is_critical BOOLEAN DEFAULT FALSE,
    calendar_id BIGINT,
    cost_code_id BIGINT,
    location VARCHAR(255),
    notes TEXT,
    sort_order INT,
    level INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_schedule (schedule_id),
    INDEX idx_parent (parent_id),
    INDEX idx_dates (planned_start, planned_finish)
);

-- Activity Dependencies
CREATE TABLE activity_dependencies (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    predecessor_id BIGINT NOT NULL,
    successor_id BIGINT NOT NULL,
    dependency_type ENUM('FS', 'SS', 'FF', 'SF') DEFAULT 'FS',
    lag_days INT DEFAULT 0,
    UNIQUE KEY unique_dep (predecessor_id, successor_id)
);

-- Activity Resources
CREATE TABLE activity_resources (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    activity_id BIGINT NOT NULL,
    resource_type ENUM('labor', 'equipment', 'material') NOT NULL,
    resource_name VARCHAR(255),
    quantity DECIMAL(10,2),
    unit VARCHAR(50),
    hours_per_day DECIMAL(5,2),
    cost_per_unit DECIMAL(15,4),
    total_cost DECIMAL(15,2)
);

-- Milestones
CREATE TABLE milestones (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    schedule_id BIGINT,
    activity_id BIGINT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    target_date DATE NOT NULL,
    actual_date DATE,
    status ENUM('not_started', 'in_progress', 'achieved', 'missed') DEFAULT 'not_started',
    responsible_id BIGINT,
    is_key_milestone BOOLEAN DEFAULT FALSE,
    notify_on_achieve BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_project (project_id),
    INDEX idx_target (target_date)
);

-- Look-Ahead Plans
CREATE TABLE lookahead_plans (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    plan_date DATE NOT NULL,
    weeks_ahead INT DEFAULT 3,
    status ENUM('draft', 'issued', 'closed') DEFAULT 'draft',
    notes TEXT,
    created_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_project_date (project_id, plan_date)
);

-- Look-Ahead Items
CREATE TABLE lookahead_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    lookahead_id BIGINT NOT NULL,
    activity_id BIGINT,
    week_number INT,
    planned_work TEXT,
    resource_requirements TEXT,
    material_requirements TEXT,
    prerequisites TEXT,
    constraints TEXT,
    committed BOOLEAN DEFAULT FALSE,
    actual_completion DECIMAL(5,2),
    reason_not_completed TEXT
);

-- Progress Updates
CREATE TABLE progress_updates (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    activity_id BIGINT NOT NULL,
    update_date DATE NOT NULL,
    percent_complete DECIMAL(5,2),
    units_completed DECIMAL(15,2),
    units_planned DECIMAL(15,2),
    actual_start DATE,
    actual_finish DATE,
    remaining_duration INT,
    notes TEXT,
    photos JSON,
    updated_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_activity_date (activity_id, update_date)
);

-- Schedule Baselines
CREATE TABLE schedule_baselines (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    schedule_id BIGINT NOT NULL,
    baseline_number INT NOT NULL,
    baseline_name VARCHAR(255),
    baseline_date DATE NOT NULL,
    activities_snapshot JSON,
    created_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_baseline (schedule_id, baseline_number)
);
```

---

## 4. API Endpoints

```
# Schedules
GET    /api/v1/projects/{id}/schedules
POST   /api/v1/projects/{id}/schedules
GET    /api/v1/schedules/{id}
PUT    /api/v1/schedules/{id}
POST   /api/v1/schedules/{id}/import
GET    /api/v1/schedules/{id}/export
POST   /api/v1/schedules/{id}/calculate-cpm

# Activities
GET    /api/v1/schedules/{id}/activities
POST   /api/v1/schedules/{id}/activities
GET    /api/v1/activities/{id}
PUT    /api/v1/activities/{id}
DELETE /api/v1/activities/{id}
POST   /api/v1/activities/{id}/update-progress
GET    /api/v1/schedules/{id}/gantt
GET    /api/v1/schedules/{id}/critical-path

# Dependencies
POST   /api/v1/activities/{id}/dependencies
DELETE /api/v1/dependencies/{id}

# Milestones
GET    /api/v1/projects/{id}/milestones
POST   /api/v1/projects/{id}/milestones
PUT    /api/v1/milestones/{id}
POST   /api/v1/milestones/{id}/achieve

# Look-Ahead
GET    /api/v1/projects/{id}/lookahead
POST   /api/v1/projects/{id}/lookahead
GET    /api/v1/lookahead/{id}
PUT    /api/v1/lookahead/{id}
GET    /api/v1/lookahead/{id}/reliability

# Baselines
GET    /api/v1/schedules/{id}/baselines
POST   /api/v1/schedules/{id}/baselines
GET    /api/v1/schedules/{id}/compare-baseline/{baselineId}

# Progress Reports
GET    /api/v1/projects/{id}/progress-summary
GET    /api/v1/projects/{id}/s-curve
GET    /api/v1/projects/{id}/schedule-variance
```

---

## 5. Implementation Tasks

### Phase 1: Schedule Management (Week 1-2)
- [ ] Schedule CRUD
- [ ] Activity management
- [ ] Dependency tracking
- [ ] Resource assignments

### Phase 2: Import/Export (Week 2-3)
- [ ] MS Project (.mpp) import
- [ ] Primavera P6 (.xer) import
- [ ] Excel import
- [ ] Export to MPP, XER, PDF, Excel

### Phase 3: CPM & Visualization (Week 3-4)
- [ ] Critical path calculation
- [ ] Float calculation
- [ ] Interactive Gantt chart
- [ ] Drag-and-drop scheduling

### Phase 4: Baselines & Comparison (Week 4-5)
- [ ] Baseline creation
- [ ] Baseline comparison
- [ ] Variance tracking
- [ ] Trend analysis

### Phase 5: Milestones & Look-Ahead (Week 5-6)
- [ ] Milestone tracking
- [ ] Achievement notifications
- [ ] Look-ahead planning
- [ ] Reliability metrics (PPC)

### Phase 6: Progress Tracking (Week 6-7)
- [ ] Mobile progress updates
- [ ] Multiple tracking methods
- [ ] Photo attachments
- [ ] Roll-up calculations

### Phase 7: Integration & Reporting (Week 7-8)
- [ ] Link to cost codes
- [ ] S-curve generation
- [ ] Earned value integration
- [ ] Delay analysis

---

## 6. Key Services

```php
// ScheduleService
class ScheduleService {
    public function importMSProject(Schedule $schedule, UploadedFile $file): ImportResult;
    public function importP6(Schedule $schedule, UploadedFile $file): ImportResult;
    public function calculateCriticalPath(Schedule $schedule): array;
    public function createBaseline(Schedule $schedule, string $name): ScheduleBaseline;
    public function compareToBaseline(Schedule $schedule, int $baselineNumber): ComparisonResult;
}

// ProgressService
class ProgressService {
    public function updateActivity(Activity $activity, array $data): Activity;
    public function getSCurve(Project $project): SCurveData;
    public function getScheduleVariance(Project $project): VarianceReport;
    public function rollUpProgress(Activity $parent): void;
}

// LookaheadService
class LookaheadService {
    public function create(Project $project, int $weeks): LookaheadPlan;
    public function calculatePPC(LookaheadPlan $plan): float;
    public function getReliabilityTrend(Project $project): array;
}
```

---

## 7. Frontend Components

```
src/modules/planning/
├── components/
│   ├── GanttChart.vue (using DHTMLX or similar)
│   ├── ActivityForm.vue
│   ├── DependencyEditor.vue
│   ├── MilestoneTracker.vue
│   ├── SCurveChart.vue
│   ├── LookaheadGrid.vue
│   ├── ProgressSlider.vue
│   └── BaselineComparison.vue
├── pages/
│   ├── ScheduleDashboard.vue
│   ├── GanttView.vue
│   ├── MilestoneDashboard.vue
│   ├── LookaheadPlanning.vue
│   └── ProgressReports.vue
└── stores/
    ├── schedule.ts
    └── progress.ts
```

---

## 8. Acceptance Criteria

- [ ] Import from MS Project/Primavera works
- [ ] Critical path calculated correctly
- [ ] Gantt chart interactive with drag-drop
- [ ] Float values display correctly
- [ ] Baselines tracked separately
- [ ] Milestone achievement triggers notifications
- [ ] Look-ahead PPC calculated
- [ ] Progress updates from mobile work
- [ ] S-curves generated accurately
- [ ] Schedule-cost integration works for EVM
