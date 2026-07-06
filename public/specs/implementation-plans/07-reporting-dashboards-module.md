# Module 7: Reporting & Dashboards
## Implementation Plan

**Priority:** 🟡 Medium | **Complexity:** Medium | **Duration:** 4-6 weeks  
**Requirements:** REQ-DASH-001 to REQ-DASH-013, REQ-REP-001 to REQ-REP-017

---

## 1. Overview

Real-time dashboards with role-based widgets and a custom report builder supporting scheduled reports and compliance templates.

---

## 2. Requirements Checklist

### Dashboards (REQ-DASH-001 to 013)
- [ ] Role-based customizable dashboards
- [ ] KPIs, charts, tables, maps, feeds
- [ ] Project health overview
- [ ] Cost performance metrics
- [ ] Task status tracking
- [ ] Document activity
- [ ] Compliance indicators
- [ ] Drill-down capability

### Report Builder (REQ-REP-001 to 008)
- [ ] Drag-and-drop interface
- [ ] Multiple data sources
- [ ] Filtering, grouping, formulas
- [ ] Charts and visualizations
- [ ] Template saving and sharing
- [ ] Export to PDF, Excel, CSV, Word

### Scheduled Reports (REQ-REP-009 to 013)
- [ ] Recurring schedules
- [ ] Email distribution
- [ ] Format preferences
- [ ] Failure alerts

### Government/Client Templates (REQ-REP-014 to 017)
- [ ] Custom template upload
- [ ] Field mapping
- [ ] AIA, OSHA forms
- [ ] Validation checks

---

## 3. Database Schema

```sql
-- Dashboards
CREATE TABLE dashboards (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT,
    project_id BIGINT,
    name VARCHAR(255) NOT NULL,
    is_default BOOLEAN DEFAULT FALSE,
    is_shared BOOLEAN DEFAULT FALSE,
    layout JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_project (project_id)
);

-- Dashboard Widgets
CREATE TABLE dashboard_widgets (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    dashboard_id BIGINT NOT NULL,
    widget_type VARCHAR(100) NOT NULL,
    title VARCHAR(255),
    config JSON,
    position_x INT DEFAULT 0,
    position_y INT DEFAULT 0,
    width INT DEFAULT 4,
    height INT DEFAULT 3,
    refresh_interval INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Report Templates
CREATE TABLE report_templates (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT,
    project_id BIGINT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    report_type ENUM('custom', 'system', 'external') DEFAULT 'custom',
    data_sources JSON,
    fields JSON,
    filters JSON,
    groupings JSON,
    charts JSON,
    layout JSON,
    branding JSON,
    is_shared BOOLEAN DEFAULT FALSE,
    created_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Scheduled Reports
CREATE TABLE scheduled_reports (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    report_template_id BIGINT NOT NULL,
    project_id BIGINT,
    name VARCHAR(255) NOT NULL,
    frequency ENUM('daily', 'weekly', 'monthly', 'quarterly') NOT NULL,
    day_of_week INT,
    day_of_month INT,
    time_of_day TIME DEFAULT '08:00:00',
    timezone VARCHAR(50) DEFAULT 'UTC',
    recipients JSON,
    format ENUM('pdf', 'excel', 'csv') DEFAULT 'pdf',
    filters JSON,
    is_active BOOLEAN DEFAULT TRUE,
    last_run_at TIMESTAMP,
    last_run_status ENUM('success', 'failed'),
    last_error TEXT,
    created_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Report Runs
CREATE TABLE report_runs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    scheduled_report_id BIGINT,
    report_template_id BIGINT NOT NULL,
    status ENUM('pending', 'running', 'completed', 'failed') DEFAULT 'pending',
    started_at TIMESTAMP,
    completed_at TIMESTAMP,
    file_path VARCHAR(1000),
    file_size BIGINT,
    error_message TEXT,
    run_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_scheduled (scheduled_report_id),
    INDEX idx_status (status)
);

-- External Templates
CREATE TABLE external_templates (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    template_type VARCHAR(100),
    file_path VARCHAR(1000),
    field_mappings JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 4. API Endpoints

```
# Dashboards
GET    /api/v1/dashboards
POST   /api/v1/dashboards
GET    /api/v1/dashboards/{id}
PUT    /api/v1/dashboards/{id}
DELETE /api/v1/dashboards/{id}
POST   /api/v1/dashboards/{id}/widgets
PUT    /api/v1/widgets/{id}
DELETE /api/v1/widgets/{id}

# Widget Data
GET    /api/v1/widgets/project-health
GET    /api/v1/widgets/cost-summary
GET    /api/v1/widgets/task-status
GET    /api/v1/widgets/document-activity
GET    /api/v1/widgets/compliance-status
GET    /api/v1/widgets/recent-activity

# Report Templates
GET    /api/v1/report-templates
POST   /api/v1/report-templates
GET    /api/v1/report-templates/{id}
PUT    /api/v1/report-templates/{id}
DELETE /api/v1/report-templates/{id}
POST   /api/v1/report-templates/{id}/preview
POST   /api/v1/report-templates/{id}/run

# Scheduled Reports
GET    /api/v1/scheduled-reports
POST   /api/v1/scheduled-reports
PUT    /api/v1/scheduled-reports/{id}
DELETE /api/v1/scheduled-reports/{id}
POST   /api/v1/scheduled-reports/{id}/run-now

# Report Runs
GET    /api/v1/report-runs
GET    /api/v1/report-runs/{id}
GET    /api/v1/report-runs/{id}/download

# External Templates
GET    /api/v1/external-templates
POST   /api/v1/external-templates
GET    /api/v1/external-templates/{id}/generate
```

---

## 5. Implementation Tasks

### Phase 1: Dashboard Framework (Week 1-2)
- [ ] Dashboard CRUD
- [ ] Widget grid system
- [ ] Drag-and-drop layout
- [ ] Widget configuration

### Phase 2: Widget Library (Week 2-3)
- [ ] KPI cards
- [ ] Line/bar/pie charts (Chart.js)
- [ ] Data tables
- [ ] Activity feeds
- [ ] Map widgets
- [ ] Alert widgets

### Phase 3: Data Aggregation (Week 3-4)
- [ ] Project health calculations
- [ ] Cost aggregation
- [ ] Task status counts
- [ ] Document metrics
- [ ] Compliance scoring
- [ ] Caching for performance

### Phase 4: Report Builder (Week 4-5)
- [ ] Drag-and-drop UI
- [ ] Data source selection
- [ ] Field picker
- [ ] Filter builder
- [ ] Chart builder
- [ ] Preview mode

### Phase 5: Export & Scheduling (Week 5-6)
- [ ] PDF generation (DomPDF/Snappy)
- [ ] Excel export (PhpSpreadsheet)
- [ ] Scheduler (Laravel Task Scheduling)
- [ ] Email distribution
- [ ] Run history

### Phase 6: External Templates (Week 6)
- [ ] Template upload
- [ ] Field mapping UI
- [ ] AIA form generation
- [ ] Validation rules

---

## 6. Key Services

```php
// DashboardService
class DashboardService {
    public function getDefaultDashboard(User $user, ?Project $project): Dashboard;
    public function getWidgetData(string $widgetType, array $config): array;
    public function calculateProjectHealth(Project $project): HealthScore;
}

// ReportService
class ReportService {
    public function preview(ReportTemplate $template, array $filters): array;
    public function generate(ReportTemplate $template, array $filters, string $format): string;
    public function schedule(ReportTemplate $template, array $scheduleConfig): ScheduledReport;
}

// ReportScheduler (Artisan Command)
class RunScheduledReports extends Command {
    public function handle(): void {
        $reports = ScheduledReport::where('is_active', true)->due()->get();
        foreach ($reports as $report) {
            dispatch(new GenerateReportJob($report));
        }
    }
}
```

---

## 7. Widget Types

| Widget Type | Data Source | Refresh |
|-------------|-------------|---------|
| project_health | Projects, Tasks, Costs | 5 min |
| cost_summary | Budgets, Actuals | 15 min |
| cost_chart | Cost data over time | 15 min |
| task_kanban | Tasks | Real-time |
| task_status | Tasks | 5 min |
| document_activity | Documents, Audit logs | 5 min |
| rfi_status | RFIs | 10 min |
| milestone_tracker | Milestones | 10 min |
| compliance_score | Multiple sources | 30 min |
| recent_activity | Audit logs | Real-time |
| weather | Weather API | 1 hour |
| map | Projects, Media | 30 min |

---

## 8. Report Types

| Type | Description | Formats |
|------|-------------|---------|
| Cost Report | Budget vs Actual | PDF, Excel |
| Progress Report | Schedule status | PDF |
| Quality Report | Inspections, NCRs | PDF |
| Safety Report | Observations, Incidents | PDF |
| RFI Log | RFI register | Excel |
| Submittal Log | Submittal register | Excel |
| AIA G702 | Payment application | PDF |
| AIA G703 | Schedule of values | PDF |

---

## 9. Acceptance Criteria

- [ ] Dashboards customizable per user
- [ ] Widgets refresh without page reload
- [ ] Drill-down from summary to detail works
- [ ] Report builder creates working reports
- [ ] All export formats working
- [ ] Scheduled reports send on time
- [ ] Failed report alerts sent
- [ ] AIA forms generate correctly
- [ ] External template mapping works
