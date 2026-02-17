# InfraHub — Construction Project Management Platform

> A comprehensive, multi-tenant construction project management platform built with **Laravel 12** and **Filament 4**.

---

## Overview

InfraHub is an all-in-one project management solution designed for construction and infrastructure companies. It supports multi-company tenancy, modular project workflows, and role-based access control — all from a single unified interface.

### Key Highlights

- 🏗️ **Multi-project dashboard** with real-time stat cards and interactive project timeline
- 🏢 **Multi-company tenancy** — each company manages its own users, roles, and projects
- 📁 **10 integrated modules** per project (Documents, Tasks, SHEQ, BOQ, Contracts, and more)
- 🔐 **Dual-panel architecture** — Admin panel for super admins, App panel for company teams
- 🎨 **Customizable UI** — per-user navigation style and color theme settings
- 📊 **Dashboard widgets** — stats overview, project Gantt timeline with milestones
- ⚡ **Compact, data-dense UI** — optimized tables, sticky actions, narrow sidebars

---

## Architecture

### Panels

| Panel | URL | Access | Purpose |
|-------|-----|--------|---------|
| **Admin** | `/admin` | Super Admins only | Platform management: companies, subscriptions, all users & roles |
| **App** | `/app` | All active users | Day-to-day work: projects, modules, settings |

### Navigation Structure (App Panel Sidebar)

```
Dashboard
Projects
  └── Projects (CRUD + overview)
Company
  ├── Clients
  └── Assets
Work Orders
  ├── Work Orders
  └── Invoices
Task & Workflow
  └── Tasks
SHEQ
  └── Safety Incidents
Settings (Admin-only)
  ├── Users          — company-scoped user management
  ├── Roles          — company-scoped role & permission management
  ├── Email Templates
  └── UI Settings    — navigation layout & color theme
```

### Project Sub-Navigation (Sidebar Groups)

When viewing a project record, a collapsible sidebar (`SubNavigationPosition::Start`) provides grouped module access:

| Group | Modules |
|-------|---------|
| **Operations** | Core FSM (Work Orders), Tasks & Workflow, Planning & Progress |
| **Site** | Field Logs, Inventory, SHEQ |
| **Commercial** | Contracts, BOQ |
| **Information** | Documents (CDE), Reports |

---

## Features

### Dashboard Widgets

1. **Tenant Dashboard Overview** — stat cards for Active Projects, Open Tasks, Work Orders, Revenue
2. **Project Timeline** — interactive Gantt-style timeline showing all company projects with:
   - Horizontal bars (start → end date) color-coded by status
   - Milestone markers (✓ completed, ● in-progress, ◇ pending, ! overdue)
   - Red "TODAY" marker line
   - Progress fill showing elapsed time percentage
   - **Clickable** — click any project bar to navigate to its detail page

### Multi-Tenancy & Access Control

- **Company isolation** — users only see their own company's data
- **Company-scoped roles** — each company defines their own roles and permissions
- **User types**: Super Admin, Company Admin, Manager, Team Member, Technician, Client
- **Filament Shield** integration with Gate-level bypass for super admins
- **Policy-based authorization** with `before()` hooks for admin access

### Project Management

- Project creation with module toggles (enable/disable per project)
- Project status tracking (Planning, Active, On Hold, Completed, Cancelled)
- Dashboard stat cards with real-time counts
- Header-based project selector with search and status indicators
- **Clickable table rows** — click any project row to navigate to its view page

### Module Features

Each module page includes:
- **Stat cards** — gradient primary card + metric cards with icons
- **Data tables** — searchable, sortable, filterable via Filament tables
- **Grouped actions** — row actions (View, Edit, Approve, Duplicate, Delete) collapsed into ActionGroup dropdowns
- **Sticky action column** — actions column stays pinned to the right when scrolling horizontally
- **Compact layout** — reduced row padding, smaller text, narrower sidebars for data-dense views

### UI Personalization

- **Navigation layout** — sidebar or top navigation
- **Color theme** — customizable primary color per user
- **Collapsible sidebar** on desktop
- **SPA mode** for snappy navigation

### UI Customizations (Custom CSS Theme)

The app uses a custom CSS theme (`resources/css/filament/app/theme.css`) with:

| Customization | Details |
|---------------|---------|
| **Main sidebar width** | Narrowed to `13rem` (default ~16rem) |
| **Sub-nav sidebar width** | Narrowed to `11rem` |
| **Main content padding** | Reduced to `1rem` |
| **Sub-nav ↔ content gap** | Reduced to `1rem` |
| **Table text** | `0.8125rem` font, `1.375rem` line-height |
| **Table row padding** | `0.5rem` vertical (default `1rem`) |
| **Table badges** | Smaller font and tighter padding |
| **Action buttons** | `1.75rem × 1.75rem` compact icons |
| **Actions column** | `position: sticky; right: 0` — frozen on right |

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Framework** | Laravel 12 |
| **Admin UI** | Filament 4 |
| **Auth & Permissions** | Spatie Permission + Filament Shield |
| **Database** | MySQL / SQLite |
| **Frontend** | Blade, Alpine.js, Livewire |
| **Styling** | Filament Design System + Custom CSS |
| **Build** | Vite |
| **Fonts** | Inter (Google Fonts) |

---

## Requirements

- PHP ≥ 8.2
- Laravel 12
- Composer
- Node.js & npm

---

## Installation

### 1. Clone & Install

```bash
git clone <repository-url>
cd infrahub
composer install
npm install
```

### 2. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Configuration

Configure your database in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=infrahub
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrate & Seed

```bash
php artisan migrate
php artisan storage:link
php artisan db:seed
```

### 5. Create Admin User

```bash
php artisan make:filament-user
```

### 6. Setup Roles & Permissions

```bash
php artisan shield:setup
php artisan shield:install
php artisan shield:super-admin
```

> **Important:** Ensure the super admin user has the `super_admin` Spatie role assigned and `user_type = 'super_admin'` in the database.

### 7. Build & Run

```bash
npm run build
php artisan serve
```

Access the application:
- **App panel**: [http://localhost:8000/app](http://localhost:8000/app)
- **Admin panel**: [http://localhost:8000/admin](http://localhost:8000/admin)

---

## Demo / Test Users

After running `php artisan db:seed`, the following users are available for testing.
**Default password for all users:** `password`

### Super Admin

| Email | Panel | Role | Capabilities |
|-------|-------|------|-------------|
| `admin@infrahub.io` | `/admin` | Super Admin | Full platform access. Manage companies, subscriptions, all users, email templates, UI settings. Can also access `/app`. |

### ACME Facility Services (Sample Company)

| Email | Panel | Role | Capabilities |
|-------|-------|------|-------------|
| `admin@acme-fs.com` | `/app` | Company Admin | Manage company users, roles, projects, email templates, UI settings |
| `sarah@acme-fs.com` | `/app` | Manager | Project management, task oversight, reporting |
| `john@acme-fs.com` | `/app` | Technician | Field service, work order execution |
| `mike@acme-fs.com` | `/app` | Member | Day-to-day project work, task completion |

### Quick Start

```bash
# Seed the database with demo data
php artisan db:seed

# Or run the specific seeder
php artisan db:seed --class=SaasFoundationSeeder
```

> **Tip:** Log in as `admin@infrahub.io` on `/admin` to explore platform management, then switch to `admin@acme-fs.com` on `/app` to see the company experience.

---

## User Roles & Permissions

### Role Hierarchy

| Role | Scope | Capabilities |
|------|-------|-------------|
| **Super Admin** | Platform-wide | Full access to everything. Manages companies, all users, subscriptions. Bypasses all Gate/policy checks. |
| **Company Admin** | Company-scoped | Manages company users, defines custom roles, configures projects. Cannot create super admins. |
| **Manager** | Company-scoped | Project management, task oversight, reporting |
| **Team Member** | Company-scoped | Day-to-day project work, task completion |
| **Technician** | Company-scoped | Field service operations, work order execution |
| **Client** | Limited | Client portal access, project viewing |

### Company-Scoped Roles

Company admins can create custom roles under **Settings > Roles**. These roles:
- Belong to the creating company (`company_id` is auto-set)
- Are only visible to users within that company
- Can have any combination of permissions assigned
- Global/system roles (`super_admin`, `panel_user`) are visible but not editable

---

## Key Configuration

### Filament Shield (`config/filament-shield.php`)

```php
'super_admin' => [
    'enabled' => true,
    'name' => 'super_admin',
    'define_via_gate' => true,     // Enables Gate::before bypass
    'intercept_gate' => 'before',
],
```

### Navigation Groups (`AppPanelProvider.php`)

```php
->navigationGroups([
    'Dashboard',
    'Projects',
    'Company',
    'Settings',
])
```

---

## Project Structure

```
app/
├── Filament/
│   ├── Admin/                    # Admin panel (super admin)
│   │   └── Resources/
│   │       ├── CompanyResource
│   │       ├── UserResource
│   │       ├── RoleResource
│   │       ├── EmailTemplateResource
│   │       └── SubscriptionResource
│   ├── App/                      # App panel (all users)
│   │   ├── Pages/
│   │   │   └── Dashboard.php     # Registers widgets (Stats + Timeline)
│   │   ├── Resources/
│   │   │   ├── CdeProjectResource/
│   │   │   │   └── Pages/
│   │   │   │       ├── ViewCdeProject    # Project overview
│   │   │   │       ├── CreateCdeProject
│   │   │   │       ├── EditCdeProject
│   │   │   │       ├── ListCdeProjects
│   │   │   │       ├── BaseModulePage    # Abstract base for modules
│   │   │   │       └── Modules/          # 10 module tab pages
│   │   │   │           ├── CoreFsmPage
│   │   │   │           ├── TaskWorkflowPage
│   │   │   │           ├── PlanningProgressPage
│   │   │   │           ├── FieldManagementPage
│   │   │   │           ├── InventoryPage
│   │   │   │           ├── SheqPage
│   │   │   │           ├── CostContractsPage
│   │   │   │           ├── BoqPage
│   │   │   │           ├── CdePage
│   │   │   │           └── ReportingPage
│   │   │   ├── AssetResource
│   │   │   ├── ClientResource
│   │   │   ├── CompanyUserResource       # Company-scoped users
│   │   │   ├── CompanyRoleResource       # Company-scoped roles
│   │   │   ├── CompanyEmailTemplateResource
│   │   │   ├── InvoiceResource
│   │   │   ├── SafetyIncidentResource
│   │   │   ├── TaskResource
│   │   │   └── WorkOrderResource
│   │   └── Widgets/
│   │       ├── TenantDashboardOverview.php  # Stats cards
│   │       └── ProjectTimelineWidget.php    # Gantt timeline
│   └── Pages/
│       └── SystemSettings                # UI settings (shared)
├── Models/
│   ├── User.php                  # With company scoping
│   ├── Role.php                  # Custom Spatie Role with company_id
│   ├── Company.php
│   ├── CdeProject.php
│   ├── Milestone.php             # Project milestones (timeline)
│   ├── WorkOrder.php
│   ├── Task.php
│   ├── Contract.php
│   ├── Boq.php / BoqItem.php
│   ├── SafetyIncident.php / SafetyInspection.php
│   ├── DailySiteLog.php
│   ├── Invoice.php
│   ├── PurchaseOrder.php
│   ├── CdeDocument.php / CdeFolder.php
│   ├── Module.php                # Module registry
│   ├── Setting.php               # Per-user settings storage
│   └── ... (60+ models total)
├── Policies/                     # 17 policy files
│   ├── UserPolicy.php            # before() for admin bypass
│   ├── RolePolicy.php            # before() for admin bypass
│   ├── CdeProjectPolicy.php
│   └── ... (WorkOrder, Task, Invoice, etc.)
└── Providers/
    └── Filament/
        ├── AdminPanelProvider.php
        └── AppPanelProvider.php

resources/
├── css/filament/app/
│   └── theme.css                 # Custom compact UI overrides
├── views/filament/app/
│   ├── widgets/
│   │   └── project-timeline.blade.php   # Gantt timeline view
│   ├── pages/modules/         # Module Blade views
│   └── components/            # Custom Blade components
└── ...

public/
└── docs/
    ├── requirements.txt          # Full requirements specification
    ├── index.html                # Requirements overview page
    ├── Construction Management System - Requirements Document.pdf
    ├── Construction Management System - Requirements Document.docx
    └── implementation-plans/     # 11 module implementation plans
        ├── 00-overview.md
        ├── 01-system-wide-requirements.md
        ├── 02-cde-module.md
        ├── 03-field-management-module.md
        ├── 04-task-workflow-module.md
        ├── 05-cost-contracts-module.md
        ├── 06-planning-progress-module.md
        ├── 07-reporting-dashboards-module.md
        ├── 08-user-access-module.md
        ├── 09-integration-module.md
        └── 10-compliance-module.md

database/
└── seeders/
    ├── DatabaseSeeder.php
    ├── SaasFoundationSeeder.php   # Full demo data seeder
    ├── RoleSeeder.php
    └── RolesAndPermissionsSeeder.php
```

---

## Requirements Documentation

Full project requirements are available in `public/docs/`:

| Document | Description |
|----------|-------------|
| **requirements.txt** | 974-line comprehensive requirements specification |
| **Implementation Plans** (11 files) | Module-by-module implementation plans |
| **Requirements PDF/DOCX** | Formatted requirements document |

### Requirement Coverage by Module

| Module | Req IDs | Key Areas |
|--------|---------|-----------|
| **System-Wide** | REQ-SYS-001 to 015 | Architecture, Performance, Security |
| **CDE (Documents)** | REQ-CDE-001 to 076 | Repository, Versioning, Workflows, ISO 19650 |
| **Field Management** | REQ-FIELD-001 to 028 | Daily Logs, Weather, Manpower, Offline |
| **Quality & Safety** | REQ-QS-001 to 028 | Inspections, NCRs, Incidents, CAPA |
| **RFIs & Submittals** | REQ-RFI/SUB-001 to 012 | RFI Management, Submittals, Response Tracking |
| **Tasks & Workflow** | REQ-TASK-001 to 024 | Task Management, Assignments, Escalations |
| **Cost & Contracts** | REQ-COST/CONT/PAY-001 to 035+ | Budgets, Commitments, Change Orders, Payments |
| **Planning & Progress** | REQ-PLAN-001 to 027 | Scheduling, Milestones, Progress, S-Curves |
| **Reporting** | REQ-DASH/REP-001 to 017 | Dashboards, Custom Reports, Exports |
| **User & Access** | REQ-USER-001 to 031 | RBAC, Multi-project, 2FA, Audit Logs |
| **Integration** | REQ-INT-001 to 013 | API, Import/Export, Third-party |
| **Compliance** | REQ-COMP-001 to 010 | ISO 19650, GDPR, Security Standards |

---

## Google Login Integration

### Getting OAuth Credentials

1. Visit [Google Cloud Console](https://console.cloud.google.com/)
2. Create/select a project → Enable Google+ API
3. Create OAuth 2.0 credentials (Web application)
4. Add redirect URI: `http://localhost:8000/auth/google/callback`

### Environment Configuration

```env
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
```

---

## Queue & Email Notifications

### Configuration

```env
QUEUE_CONNECTION=database
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

### Running the Queue Worker

```bash
# Development
php artisan queue:work

# Production (with Supervisor recommended)
php artisan queue:work --daemon --tries=3 --timeout=60
```

### Notification Types

- **Project Assignment** — when added to a project
- **Comment Notifications** — new comments on tickets
- **Ticket Updates** — status changes

---

## Troubleshooting

| Issue | Solution |
|-------|---------|
| Settings not showing in sidebar | Run `php artisan optimize:clear` and hard refresh (Ctrl+Shift+R) |
| Super admin can't access resources | Ensure `super_admin` Spatie role is assigned AND `define_via_gate` is `true` in Shield config |
| 403 on Users/Roles pages | Check `UserPolicy` and `RolePolicy` have `before()` methods for admin bypass |
| Module stats showing 0 | Verify the `getStats()` method queries the correct tables for the project |
| SPA navigation stale | Hard refresh or open in incognito to clear Livewire cache |
| CSS changes not visible | Run `npm run build` then `php artisan view:clear` and hard refresh |
| Table actions cut off | Actions are grouped into ActionGroup dropdowns; the column is sticky on the right |
| `Cannot redeclare non static $view` | Widget `$view` property must be non-static in Filament 4: `protected string $view = ...` |

---

## License

This project is licensed under the [MIT License](LICENSE).