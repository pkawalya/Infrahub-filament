# Module 4: Task & Workflow Engine
## Implementation Plan

**Priority:** 🟠 High | **Complexity:** Medium | **Duration:** 4-6 weeks  
**Requirements:** REQ-TASK-001 to REQ-TASK-024

---

## 1. Overview

Comprehensive task management system with role-based assignment, escalation rules, and multi-channel notifications. Serves as the workflow backbone for all other modules.

---

## 2. Requirements Checklist

### Task Management (REQ-TASK-001 to 008)
- [ ] Comprehensive task creation with all metadata
- [ ] File attachments and document links
- [ ] Task dependencies (predecessor/successor)
- [ ] Parent-child hierarchies (subtasks)
- [ ] Recurring tasks
- [ ] Multiple views (list, kanban, calendar, gantt, map)

### Role-Based Assignment (REQ-TASK-009 to 013)
- [ ] Assign to users, roles, teams, organizations
- [ ] Team notification on assignment
- [ ] Self-assignment from team queue
- [ ] Task reassignment with notifications
- [ ] Workload tracking

### Escalation Rules (REQ-TASK-014 to 018)
- [ ] Configurable escalation triggers
- [ ] Auto-reassignment or notification
- [ ] Pre-escalation warnings
- [ ] Escalation logging

### Notifications (REQ-TASK-019 to 024)
- [ ] Multi-channel delivery (email, in-app, push, SMS)
- [ ] User notification preferences
- [ ] @mentions in comments
- [ ] Notification center

---

## 3. Database Schema

```sql
-- Tasks
CREATE TABLE tasks (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    parent_id BIGINT NULL,
    task_number VARCHAR(50),
    title VARCHAR(500) NOT NULL,
    description TEXT,
    priority ENUM('low', 'normal', 'high', 'critical') DEFAULT 'normal',
    status ENUM('not_started', 'in_progress', 'completed', 'on_hold', 'cancelled') DEFAULT 'not_started',
    percent_complete TINYINT DEFAULT 0,
    due_date TIMESTAMP,
    start_date TIMESTAMP,
    actual_start TIMESTAMP,
    actual_finish TIMESTAMP,
    estimated_hours DECIMAL(8,2),
    actual_hours DECIMAL(8,2),
    location VARCHAR(255),
    cost_code VARCHAR(50),
    tags JSON,
    is_recurring BOOLEAN DEFAULT FALSE,
    recurrence_pattern JSON,
    created_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_project (project_id),
    INDEX idx_parent (parent_id),
    INDEX idx_status (status),
    INDEX idx_due_date (due_date),
    FULLTEXT idx_search (title, description)
);

-- Task Assignments
CREATE TABLE task_assignments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    task_id BIGINT NOT NULL,
    assignee_type ENUM('user', 'role', 'team', 'organization') NOT NULL,
    assignee_id BIGINT NOT NULL,
    assigned_by BIGINT,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    accepted_at TIMESTAMP,
    is_primary BOOLEAN DEFAULT FALSE,
    INDEX idx_task (task_id),
    INDEX idx_assignee (assignee_type, assignee_id)
);

-- Task Dependencies
CREATE TABLE task_dependencies (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    task_id BIGINT NOT NULL,
    depends_on_task_id BIGINT NOT NULL,
    dependency_type ENUM('finish_to_start', 'start_to_start', 'finish_to_finish', 'start_to_finish') DEFAULT 'finish_to_start',
    lag_days INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_dependency (task_id, depends_on_task_id)
);

-- Task Links (to other entities)
CREATE TABLE task_links (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    task_id BIGINT NOT NULL,
    linkable_type VARCHAR(100) NOT NULL,
    linkable_id BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_task (task_id),
    INDEX idx_linkable (linkable_type, linkable_id)
);

-- Task Comments
CREATE TABLE task_comments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    task_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    content TEXT NOT NULL,
    mentions JSON,
    attachments JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_task (task_id)
);

-- Task Time Logs
CREATE TABLE task_time_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    task_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    hours DECIMAL(5,2) NOT NULL,
    work_date DATE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Escalation Rules
CREATE TABLE escalation_rules (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT,
    organization_id BIGINT,
    name VARCHAR(255) NOT NULL,
    trigger_type ENUM('time_overdue', 'priority', 'task_type') NOT NULL,
    trigger_config JSON,
    action_type ENUM('reassign', 'notify', 'both') NOT NULL,
    action_config JSON,
    warning_hours INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Escalation Log
CREATE TABLE escalation_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    task_id BIGINT NOT NULL,
    rule_id BIGINT NOT NULL,
    escalated_from BIGINT,
    escalated_to BIGINT,
    reason TEXT,
    escalated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Notifications
CREATE TABLE notifications (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    type VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    body TEXT,
    data JSON,
    channels JSON,
    read_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_read (user_id, read_at)
);

-- Notification Preferences
CREATE TABLE notification_preferences (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    event_type VARCHAR(100) NOT NULL,
    email_enabled BOOLEAN DEFAULT TRUE,
    push_enabled BOOLEAN DEFAULT TRUE,
    sms_enabled BOOLEAN DEFAULT FALSE,
    frequency ENUM('immediate', 'daily_digest', 'weekly_digest') DEFAULT 'immediate',
    UNIQUE KEY unique_preference (user_id, event_type)
);
```

---

## 4. API Endpoints

```
# Tasks
GET    /api/v1/projects/{id}/tasks
POST   /api/v1/projects/{id}/tasks
GET    /api/v1/tasks/{id}
PUT    /api/v1/tasks/{id}
DELETE /api/v1/tasks/{id}
POST   /api/v1/tasks/{id}/complete
POST   /api/v1/tasks/{id}/reopen
GET    /api/v1/tasks/{id}/subtasks
POST   /api/v1/tasks/{id}/subtasks

# Assignments
POST   /api/v1/tasks/{id}/assign
DELETE /api/v1/tasks/{id}/assignments/{assignmentId}
POST   /api/v1/tasks/{id}/reassign
GET    /api/v1/my-tasks
GET    /api/v1/team-queue

# Dependencies
POST   /api/v1/tasks/{id}/dependencies
DELETE /api/v1/tasks/{id}/dependencies/{depId}

# Comments
GET    /api/v1/tasks/{id}/comments
POST   /api/v1/tasks/{id}/comments

# Time Tracking
POST   /api/v1/tasks/{id}/time-logs
GET    /api/v1/tasks/{id}/time-logs

# Views
GET    /api/v1/projects/{id}/tasks/kanban
GET    /api/v1/projects/{id}/tasks/calendar
GET    /api/v1/projects/{id}/tasks/gantt

# Escalation
GET    /api/v1/escalation-rules
POST   /api/v1/escalation-rules
PUT    /api/v1/escalation-rules/{id}

# Notifications
GET    /api/v1/notifications
POST   /api/v1/notifications/mark-read
POST   /api/v1/notifications/mark-all-read
GET    /api/v1/notification-preferences
PUT    /api/v1/notification-preferences
```

---

## 5. Implementation Tasks

### Phase 1: Core Task Management (Week 1-2)
- [ ] Task CRUD operations
- [ ] Assignment system
- [ ] Status transitions
- [ ] File attachments
- [ ] Comments with @mentions

### Phase 2: Task Relationships (Week 2-3)
- [ ] Parent-child hierarchies
- [ ] Dependencies with lag
- [ ] Document/entity linking
- [ ] Recurring task scheduler

### Phase 3: Views (Week 3-4)
- [ ] List view with sorting/filtering
- [ ] Kanban board with drag-drop
- [ ] Calendar view
- [ ] Gantt chart
- [ ] Map view (location-based)

### Phase 4: Escalation Engine (Week 4-5)
- [ ] Rule configuration UI
- [ ] Escalation scheduler (cron)
- [ ] Warning notifications
- [ ] Auto-reassignment
- [ ] Escalation history

### Phase 5: Notification System (Week 5-6)
- [ ] Email notifications
- [ ] In-app notifications
- [ ] Mobile push (FCM/APNs)
- [ ] SMS integration (Twilio)
- [ ] User preferences
- [ ] @mention detection
- [ ] Notification center UI

---

## 6. Key Services

```php
// TaskService
class TaskService {
    public function create(Project $project, array $data): Task;
    public function assign(Task $task, string $type, int $id): TaskAssignment;
    public function complete(Task $task, User $user): void;
    public function getWorkload(User $user): WorkloadSummary;
    public function getTeamQueue(Team $team): Collection;
}

// EscalationService
class EscalationService {
    public function checkAndEscalate(): void; // Called by scheduler
    public function sendWarning(Task $task, EscalationRule $rule): void;
    public function escalate(Task $task, EscalationRule $rule): void;
}

// NotificationService
class NotificationService {
    public function send(User $user, string $type, array $data): void;
    public function sendToAssignees(Task $task, string $type, array $data): void;
    public function parseMentions(string $content): array;
    public function getUserPreference(User $user, string $eventType): NotificationPreference;
}
```

---

## 7. Frontend Components

```
src/modules/tasks/
├── components/
│   ├── TaskForm.vue
│   ├── TaskCard.vue
│   ├── TaskList.vue
│   ├── KanbanBoard.vue
│   ├── GanttChart.vue
│   ├── CalendarView.vue
│   ├── TaskComments.vue
│   ├── AssigneeSelector.vue
│   ├── DependencyManager.vue
│   └── NotificationCenter.vue
├── pages/
│   ├── TaskDashboard.vue
│   ├── MyTasks.vue
│   ├── TeamQueue.vue
│   └── EscalationRules.vue
└── stores/
    ├── tasks.ts
    └── notifications.ts
```

---

## 8. Acceptance Criteria

- [ ] Tasks support all required fields
- [ ] Multi-assignee support working
- [ ] Dependencies block task completion correctly
- [ ] Subtasks roll up to parent
- [ ] Recurring tasks auto-create
- [ ] All 5 views functioning
- [ ] Escalations trigger on schedule
- [ ] Pre-warnings sent before escalation
- [ ] Notifications delivered via all channels
- [ ] @mentions notify mentioned users
- [ ] Notification preferences respected
