# InfraHub — Construction Project Management Platform

> A comprehensive, multi-tenant construction project management platform built with **Laravel 12** and **Filament 4**.

---

## Overview

InfraHub is an all-in-one project management solution designed for construction and infrastructure companies. It supports multi-company tenancy, modular project workflows, and role-based access control — all from a single unified interface.

### Key Highlights

- 🏗️ **Multi-project dashboard** with real-time stat cards
- 🏢 **Multi-company tenancy** — each company manages its own users, roles, and projects
- 📁 **10 integrated modules** per project (Documents, Tasks, SHEQ, BOQ, Contracts, and more)
- 🔐 **Dual-panel architecture** — Admin panel for super admins, App panel for company teams
- 🎨 **Customizable UI** — per-user navigation style and color theme settings
- 📊 **Dashboard widgets** — charts, activity feeds, and module-level statistics

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
Settings (Admin-only)
  ├── Users          — company-scoped user management
  ├── Roles          — company-scoped role & permission management
  └── UI Settings    — navigation layout & color theme
```

### Project Module Tabs

When viewing a project, tabbed navigation provides access to:

| Module | Description |
|--------|-------------|
| **Overview** | Dashboard with stats, recent tasks, documents, progress |
| **Core FSM** | Work orders, assets, invoices |
| **Documents (CDE)** | Document management, RFIs, submittals |
| **Tasks & Workflow** | Task management, assignments, time tracking |
| **SHEQ** | Safety incidents, inspections, compliance |
| **BOQ** | Bills of quantities & cost estimation |
| **Contracts** | Contract management, values, active tracking |
| **Field Management** | Daily logs, inspections, workforce |
| **Inventory** | Stock items, purchase orders, deliveries |
| **Planning & Progress** | Milestones, schedule health, S-curve |
| **Reporting** | Custom reports, dashboards, data export |

---

## Features

### Multi-Tenancy & Access Control

- **Company isolation** — users only see their own company's data
- **Company-scoped roles** — each company defines their own roles and permissions
- **User types**: Super Admin, Company Admin, Manager, Team Member, Technician, Client
- **Filament Shield** integration with Gate-level bypass for super admins
- **Policy-based authorization** with `before()` hooks for admin access

### Project Management

- Project creation with module toggles (enable/disable per project)
- Project status tracking (Planning, Active, On Hold, Completed)
- Dashboard stat cards with real-time counts
- Header-based project selector with search and status indicators

### Module Features

Each module page includes:
- **Stat cards** — gradient primary card + metric cards with icons
- **Data tables** — searchable, sortable, filterable via Filament tables
- **Contextual actions** — create, view, edit, delete with proper authorization

### UI Personalization

- **Navigation layout** — sidebar or top navigation
- **Color theme** — customizable primary color per user
- **Collapsible sidebar** on desktop
- **SPA mode** for snappy navigation

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
npm run dev
php artisan serve
```

Access the application:
- **App panel**: [http://localhost:8000/app](http://localhost:8000/app)
- **Admin panel**: [http://localhost:8000/admin](http://localhost:8000/admin)

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
│   │       └── SubscriptionResource
│   ├── App/                      # App panel (all users)
│   │   ├── Pages/
│   │   │   └── Dashboard
│   │   ├── Resources/
│   │   │   ├── CdeProjectResource/
│   │   │   │   └── Pages/
│   │   │   │       ├── ViewCdeProject    # Project overview
│   │   │   │       └── Modules/          # 10 module tab pages
│   │   │   ├── CompanyUserResource       # Company-scoped users
│   │   │   ├── CompanyRoleResource       # Company-scoped roles
│   │   │   ├── ClientResource
│   │   │   └── AssetResource
│   │   └── Widgets/
│   └── Pages/
│       └── SystemSettings                # UI settings (shared)
├── Models/
│   ├── User.php                  # With company scoping
│   ├── Role.php                  # Custom Spatie Role with company_id
│   ├── Company.php
│   ├── CdeProject.php
│   └── Setting.php               # Per-user settings storage
├── Policies/
│   ├── UserPolicy.php            # before() for admin bypass
│   └── RolePolicy.php            # before() for admin bypass
└── Providers/
    └── Filament/
        ├── AdminPanelProvider.php
        └── AppPanelProvider.php
```

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

---

## License

This project is licensed under the [MIT License](LICENSE).