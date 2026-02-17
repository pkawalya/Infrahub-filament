# Module 8: User & Access Management
## Implementation Plan

**Priority:** 🔴 Critical | **Complexity:** Medium | **Duration:** 3-4 weeks  
**Requirements:** REQ-USER-001 to REQ-USER-031

---

## 1. Overview

Multi-project, multi-organization user management with role-based access control, two-factor authentication, and comprehensive activity logging.

---

## 2. Requirements Checklist

### Multi-Project Support (REQ-USER-001 to 006)
- [ ] Unlimited projects per organization
- [ ] Multi-project user access
- [ ] Project switching without logout
- [ ] Project selector in UI
- [ ] Default project per user
- [ ] Project-specific settings

### Role-Based Permissions (REQ-USER-007 to 013)
- [ ] Standard roles (13 predefined)
- [ ] Custom role creation
- [ ] Granular permissions
- [ ] Multi-role assignment
- [ ] Permission inheritance
- [ ] Immediate effect changes

### Organization Access (REQ-USER-014 to 019)
- [ ] Hierarchical organizations
- [ ] User inheritance from org
- [ ] Org-level admin management
- [ ] Inter-org sharing controls
- [ ] Org templates and standards

### Two-Factor Auth (REQ-USER-020 to 024)
- [ ] Authenticator app support
- [ ] SMS/Email codes
- [ ] Mandatory 2FA option
- [ ] Trusted devices
- [ ] Backup codes

### Activity Logs (REQ-USER-025 to 031)
- [ ] Comprehensive action logging
- [ ] Search and filter
- [ ] Export capability
- [ ] Tamper-proof storage
- [ ] Suspicious activity alerts

---

## 3. Database Schema

```sql
-- Organizations
CREATE TABLE organizations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    parent_id BIGINT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE,
    type ENUM('company', 'division', 'contractor', 'consultant') DEFAULT 'company',
    settings JSON,
    logo_path VARCHAR(1000),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_parent (parent_id)
);

-- Users (extended)
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    job_title VARCHAR(255),
    avatar_path VARCHAR(1000),
    timezone VARCHAR(50) DEFAULT 'UTC',
    locale VARCHAR(10) DEFAULT 'en',
    default_project_id BIGINT,
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    two_factor_secret VARCHAR(255),
    two_factor_recovery_codes JSON,
    email_verified_at TIMESTAMP,
    last_login_at TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_org (organization_id),
    INDEX idx_email (email)
);

-- Roles
CREATE TABLE roles (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT,
    is_system BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_role (organization_id, slug)
);

-- Permissions
CREATE TABLE permissions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    module VARCHAR(100),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Role Permissions
CREATE TABLE role_permissions (
    role_id BIGINT NOT NULL,
    permission_id BIGINT NOT NULL,
    PRIMARY KEY (role_id, permission_id)
);

-- User Roles (per project)
CREATE TABLE user_roles (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    role_id BIGINT NOT NULL,
    project_id BIGINT,
    assigned_by BIGINT,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_role (user_id, role_id, project_id)
);

-- Project Users
CREATE TABLE project_users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    access_level ENUM('full', 'limited', 'read_only') DEFAULT 'full',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_project_user (project_id, user_id)
);

-- Trusted Devices (2FA)
CREATE TABLE trusted_devices (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    device_token VARCHAR(255) NOT NULL,
    device_name VARCHAR(255),
    ip_address VARCHAR(45),
    user_agent TEXT,
    trusted_until TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id)
);

-- Teams
CREATE TABLE teams (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT NOT NULL,
    project_id BIGINT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Team Members
CREATE TABLE team_members (
    team_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    role ENUM('member', 'lead') DEFAULT 'member',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (team_id, user_id)
);
```

---

## 4. API Endpoints

```
# Authentication
POST   /api/v1/auth/login
POST   /api/v1/auth/logout
POST   /api/v1/auth/refresh
POST   /api/v1/auth/2fa/enable
POST   /api/v1/auth/2fa/verify
POST   /api/v1/auth/2fa/disable
GET    /api/v1/auth/2fa/backup-codes
POST   /api/v1/auth/trust-device

# Users
GET    /api/v1/users
POST   /api/v1/users
GET    /api/v1/users/{id}
PUT    /api/v1/users/{id}
DELETE /api/v1/users/{id}
GET    /api/v1/me
PUT    /api/v1/me

# Organizations
GET    /api/v1/organizations
POST   /api/v1/organizations
GET    /api/v1/organizations/{id}
PUT    /api/v1/organizations/{id}
GET    /api/v1/organizations/{id}/users

# Projects
GET    /api/v1/projects/{id}/users
POST   /api/v1/projects/{id}/users
DELETE /api/v1/projects/{id}/users/{userId}
PUT    /api/v1/me/default-project

# Roles & Permissions
GET    /api/v1/roles
POST   /api/v1/roles
PUT    /api/v1/roles/{id}
DELETE /api/v1/roles/{id}
GET    /api/v1/permissions
POST   /api/v1/users/{id}/roles
DELETE /api/v1/users/{id}/roles/{roleId}

# Teams
GET    /api/v1/teams
POST   /api/v1/teams
PUT    /api/v1/teams/{id}
POST   /api/v1/teams/{id}/members
DELETE /api/v1/teams/{id}/members/{userId}

# Activity Logs
GET    /api/v1/activity-logs
GET    /api/v1/activity-logs/export
GET    /api/v1/users/{id}/activity
```

---

## 5. Implementation Tasks

### Phase 1: Core User Management (Week 1)
- [ ] User CRUD
- [ ] Organization structure
- [ ] Project membership
- [ ] Project switching

### Phase 2: RBAC (Week 1-2)
- [ ] Role management
- [ ] Permission system
- [ ] Role assignment
- [ ] Permission checking middleware
- [ ] Custom roles

### Phase 3: Two-Factor Auth (Week 2-3)
- [ ] TOTP setup (Google Authenticator)
- [ ] SMS integration (Twilio)
- [ ] Email code fallback
- [ ] Trusted devices
- [ ] Backup codes
- [ ] Mandatory 2FA setting

### Phase 4: Activity Logging (Week 3-4)
- [ ] Automatic action logging
- [ ] Search and filter UI
- [ ] Export functionality
- [ ] Suspicious activity detection
- [ ] Alert notifications

---

## 6. Default Roles

| Role | Key Permissions |
|------|-----------------|
| System Administrator | All permissions |
| Organization Admin | Org users, settings, all projects |
| Project Administrator | Project settings, all project data |
| Project Manager | Full project access |
| Site Manager | Field ops, daily logs, inspections |
| Engineer | Technical docs, RFIs, submittals |
| QA/QC Manager | Quality, inspections, NCRs |
| Safety Manager | Safety, incidents |
| Document Controller | Documents, transmittals |
| Contractor | Limited project access |
| Consultant | Review and comment |
| Client Representative | View and approve |
| Read-Only User | View only |

---

## 7. Key Services

```php
// AuthService
class AuthService {
    public function login(array $credentials): AuthResult;
    public function enable2FA(User $user): TwoFactorSetup;
    public function verify2FA(User $user, string $code): bool;
    public function trustDevice(User $user, Request $request): void;
}

// PermissionService
class PermissionService {
    public function can(User $user, string $permission, ?Project $project = null): bool;
    public function getRoles(User $user, ?Project $project = null): Collection;
    public function assignRole(User $user, Role $role, ?Project $project = null): void;
}

// ActivityLogService
class ActivityLogService {
    public function log(string $action, ?Model $subject = null): void;
    public function search(array $filters): LengthAwarePaginator;
    public function detectSuspicious(User $user): array;
}
```

---

## 8. Acceptance Criteria

- [ ] Users can access multiple projects
- [ ] Project switching is seamless
- [ ] RBAC restricts access correctly
- [ ] Custom roles can be created
- [ ] 2FA with authenticator works
- [ ] SMS/Email codes work as fallback
- [ ] Trusted devices skip 2FA
- [ ] Activity logs capture all actions
- [ ] Suspicious activity triggers alerts
- [ ] Logs are exportable
