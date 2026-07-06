# Module 2: Common Data Environment (CDE)
## Implementation Plan

**Priority:** 🔴 Critical | **Complexity:** Very High | **Duration:** 8-12 weeks  
**Requirements:** REQ-CDE-001 to REQ-CDE-076

---

## 1. Overview

The CDE module is the document management backbone of Infrahub, providing centralized document storage, version control, workflows, and ISO 19650 compliance.

---

## 2. Sub-Modules

| Sub-Module | Requirements | Priority |
|------------|--------------|----------|
| 2.1 Document Repository | REQ-CDE-001 to 004 | Critical |
| 2.2 Folder Structure | REQ-CDE-005 to 008 | Critical |
| 2.3 Drawing Registers | REQ-CDE-009 to 012 | High |
| 2.4 Version Control | REQ-CDE-013 to 020 | Critical |
| 2.5 Markups & Annotations | REQ-CDE-021 to 027 | High |
| 2.6 Transmittals | REQ-CDE-028 to 034 | High |
| 2.7 Access Control | REQ-CDE-035 to 040 | Critical |
| 2.8 ISO 19650 Compliance | REQ-CDE-041 to 046 | High |
| 2.9 Audit Trail | REQ-CDE-047 to 051 | Critical |
| 2.10 Document Workflows | REQ-CDE-052 to 076 | High |

---

## 3. Database Schema

```sql
-- Projects
CREATE TABLE projects (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT NOT NULL,
    code VARCHAR(50) UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('planning', 'active', 'on_hold', 'completed', 'archived') DEFAULT 'planning',
    start_date DATE,
    end_date DATE,
    settings JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Folders
CREATE TABLE folders (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    parent_id BIGINT NULL,
    name VARCHAR(255) NOT NULL,
    path VARCHAR(1000),
    discipline VARCHAR(100),
    folder_type VARCHAR(100),
    iso_container ENUM('wip', 'shared', 'published', 'archive'),
    settings JSON,
    created_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_project (project_id),
    INDEX idx_parent (parent_id),
    INDEX idx_path (path(255))
);

-- Documents
CREATE TABLE documents (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    folder_id BIGINT NOT NULL,
    document_number VARCHAR(255),
    title VARCHAR(500) NOT NULL,
    description TEXT,
    discipline VARCHAR(100),
    document_type VARCHAR(100),
    current_version_id BIGINT,
    status ENUM('wip', 'shared', 'published', 'archived') DEFAULT 'wip',
    suitability_code VARCHAR(20),
    file_format VARCHAR(20),
    is_locked BOOLEAN DEFAULT FALSE,
    locked_by BIGINT,
    locked_at TIMESTAMP,
    created_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_project (project_id),
    INDEX idx_folder (folder_id),
    INDEX idx_number (document_number),
    FULLTEXT idx_search (title, description)
);

-- Document Versions
CREATE TABLE document_versions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    document_id BIGINT NOT NULL,
    version_number VARCHAR(20) NOT NULL,
    revision_number VARCHAR(20),
    file_path VARCHAR(1000) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_size BIGINT,
    mime_type VARCHAR(100),
    checksum VARCHAR(64),
    change_description TEXT,
    uploaded_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_document (document_id)
);

-- Drawing Register
CREATE TABLE drawing_register (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    document_id BIGINT NOT NULL UNIQUE,
    drawing_number VARCHAR(100),
    drawing_title VARCHAR(500),
    discipline VARCHAR(100),
    scale VARCHAR(50),
    sheet_size VARCHAR(20),
    author VARCHAR(255),
    issue_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Markups
CREATE TABLE document_markups (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    document_version_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    markup_type ENUM('text', 'shape', 'arrow', 'highlight', 'stamp', 'freehand'),
    content JSON,
    page_number INT,
    position JSON,
    is_resolved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Transmittals
CREATE TABLE transmittals (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    transmittal_number VARCHAR(50) NOT NULL,
    from_organization_id BIGINT,
    to_organization_id BIGINT,
    subject VARCHAR(500),
    description TEXT,
    purpose_code VARCHAR(50),
    required_action VARCHAR(100),
    response_due_date DATE,
    status ENUM('draft', 'sent', 'received', 'acknowledged', 'responded') DEFAULT 'draft',
    sent_at TIMESTAMP,
    created_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Transmittal Items
CREATE TABLE transmittal_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    transmittal_id BIGINT NOT NULL,
    document_version_id BIGINT NOT NULL,
    purpose_code VARCHAR(50),
    notes TEXT
);

-- Document Permissions
CREATE TABLE document_permissions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    permissible_type VARCHAR(100), -- folder, document
    permissible_id BIGINT,
    grantee_type VARCHAR(50), -- user, role, organization
    grantee_id BIGINT,
    permission ENUM('none', 'view', 'download', 'comment', 'upload', 'edit', 'delete', 'manage'),
    expires_at TIMESTAMP NULL,
    granted_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Document Workflows
CREATE TABLE workflow_templates (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT,
    organization_id BIGINT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    workflow_type ENUM('sequential', 'parallel', 'conditional'),
    steps JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE document_workflows (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    document_id BIGINT NOT NULL,
    workflow_template_id BIGINT,
    current_step INT DEFAULT 1,
    status ENUM('pending', 'in_progress', 'completed', 'rejected', 'cancelled') DEFAULT 'pending',
    started_at TIMESTAMP,
    completed_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE workflow_tasks (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    workflow_id BIGINT NOT NULL,
    step_number INT NOT NULL,
    assigned_to BIGINT,
    action_type ENUM('review', 'approve', 'reject', 'revise', 'info_only'),
    status ENUM('pending', 'in_progress', 'completed', 'skipped') DEFAULT 'pending',
    comments TEXT,
    attachments JSON,
    due_date TIMESTAMP,
    sla_target_hours INT,
    completed_at TIMESTAMP,
    completed_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 4. API Endpoints

```
# Projects
GET    /api/v1/projects
POST   /api/v1/projects
GET    /api/v1/projects/{id}
PUT    /api/v1/projects/{id}
DELETE /api/v1/projects/{id}

# Folders
GET    /api/v1/projects/{id}/folders
POST   /api/v1/projects/{id}/folders
PUT    /api/v1/folders/{id}
DELETE /api/v1/folders/{id}
POST   /api/v1/folders/{id}/copy
POST   /api/v1/folders/{id}/move

# Documents
GET    /api/v1/folders/{id}/documents
POST   /api/v1/folders/{id}/documents
GET    /api/v1/documents/{id}
PUT    /api/v1/documents/{id}
DELETE /api/v1/documents/{id}
POST   /api/v1/documents/{id}/upload-version
GET    /api/v1/documents/{id}/versions
GET    /api/v1/documents/{id}/download
POST   /api/v1/documents/{id}/checkout
POST   /api/v1/documents/{id}/checkin
GET    /api/v1/documents/{id}/compare/{version1}/{version2}

# Drawing Register
GET    /api/v1/projects/{id}/drawing-register
GET    /api/v1/projects/{id}/drawing-register/export

# Markups
GET    /api/v1/documents/{id}/markups
POST   /api/v1/documents/{id}/markups
PUT    /api/v1/markups/{id}
DELETE /api/v1/markups/{id}

# Transmittals
GET    /api/v1/projects/{id}/transmittals
POST   /api/v1/projects/{id}/transmittals
GET    /api/v1/transmittals/{id}
PUT    /api/v1/transmittals/{id}
POST   /api/v1/transmittals/{id}/send
POST   /api/v1/transmittals/{id}/respond

# Permissions
GET    /api/v1/documents/{id}/permissions
POST   /api/v1/documents/{id}/permissions
DELETE /api/v1/permissions/{id}

# Workflows
GET    /api/v1/workflow-templates
POST   /api/v1/workflow-templates
POST   /api/v1/documents/{id}/start-workflow
GET    /api/v1/documents/{id}/workflow
POST   /api/v1/workflow-tasks/{id}/complete
```

---

## 5. Implementation Tasks

### Phase 1: Document Repository (Week 1-3)
- [ ] File storage service (S3/local)
- [ ] Document CRUD operations
- [ ] Version control system
- [ ] File upload with chunking (2GB support)
- [ ] Checksum verification

### Phase 2: Folder Management (Week 3-4)
- [ ] Hierarchical folder structure
- [ ] Folder templates
- [ ] Bulk operations (copy, move)
- [ ] Path management

### Phase 3: Drawing Register (Week 4-5)
- [ ] Auto-extract metadata from files
- [ ] Drawing numbering conventions
- [ ] Register views and exports

### Phase 4: Markups & Annotations (Week 5-7)
- [ ] PDF.js integration for viewing
- [ ] Markup tools UI
- [ ] Real-time collaboration
- [ ] Markup export

### Phase 5: Transmittals (Week 7-8)
- [ ] Transmittal creation wizard
- [ ] Email notifications
- [ ] Response tracking
- [ ] Transmittal register

### Phase 6: Access Control (Week 8-9)
- [ ] Permission management UI
- [ ] Inheritance system
- [ ] Temporary access grants
- [ ] Permission audit

### Phase 7: ISO 19650 (Week 9-10)
- [ ] Container workflows (WIP → Shared → Published)
- [ ] Suitability codes
- [ ] Naming conventions
- [ ] Compliance reporting

### Phase 8: Document Workflows (Week 10-12)
- [ ] Workflow template builder
- [ ] Sequential/parallel routing
- [ ] SLA tracking
- [ ] Automated reminders
- [ ] Escalation rules

---

## 6. Key Services

```php
// DocumentService
class DocumentService {
    public function upload(UploadedFile $file, Folder $folder, array $metadata): Document;
    public function createVersion(Document $doc, UploadedFile $file, string $changeDesc): DocumentVersion;
    public function checkout(Document $doc, User $user): bool;
    public function checkin(Document $doc, User $user, ?UploadedFile $newVersion): bool;
    public function compareVersions(DocumentVersion $v1, DocumentVersion $v2): ComparisonResult;
}

// TransmittalService
class TransmittalService {
    public function create(Project $project, array $data): Transmittal;
    public function addDocuments(Transmittal $t, array $documentVersionIds): void;
    public function send(Transmittal $t): void;
    public function respond(Transmittal $t, array $response): void;
}

// WorkflowService
class WorkflowService {
    public function startWorkflow(Document $doc, WorkflowTemplate $template): DocumentWorkflow;
    public function completeTask(WorkflowTask $task, string $action, ?string $comments): void;
    public function checkSLABreaches(): Collection;
    public function sendReminders(): void;
}
```

---

## 7. Frontend Components

```
src/modules/cde/
├── components/
│   ├── FolderTree.vue
│   ├── DocumentList.vue
│   ├── DocumentViewer.vue
│   ├── MarkupTools.vue
│   ├── VersionHistory.vue
│   ├── TransmittalForm.vue
│   ├── WorkflowStatus.vue
│   └── PermissionManager.vue
├── pages/
│   ├── ProjectDocuments.vue
│   ├── DrawingRegister.vue
│   ├── TransmittalList.vue
│   └── WorkflowDashboard.vue
└── stores/
    ├── documents.ts
    ├── folders.ts
    └── workflows.ts
```

---

## 8. Acceptance Criteria

- [ ] Upload files up to 2GB with progress indicator
- [ ] All versions retained and accessible
- [ ] Side-by-side version comparison works
- [ ] Markups saved per user with timestamps
- [ ] Transmittals send email notifications
- [ ] Permissions restrict access correctly
- [ ] Workflows route documents to assignees
- [ ] SLA breaches trigger alerts
- [ ] Audit logs capture all document activities
- [ ] ISO 19650 containers function correctly
