# InfraHub — Construction Project Management Platform

> A comprehensive, multi-tenant construction project management platform built with **Laravel 12** and **Filament 4**, featuring a **Client Portal**, **Mobile PWA**, and **REST API**.

---

## Overview

InfraHub is an all-in-one project management solution designed for construction and infrastructure companies. It supports multi-company tenancy, modular project workflows, role-based access control, and mobile field access — all from a single unified platform.

### Key Highlights

- 🏗️ **Multi-project dashboard** with real-time stat cards and interactive project timeline
- 🏢 **Multi-company tenancy** — each company manages its own users, roles, and projects
- 📁 **15+ integrated modules** per project (Documents, Tasks, SHEQ, BOQ, Inventory, Contracts, Equipment, and more)
- 🔐 **Three-panel architecture** — Admin panel, Company App panel, and Client Portal
- 📱 **Mobile PWA** at `/mobile` — offline-first, bottom nav, works on 3G
- 🌐 **REST API v1** — 60+ endpoints with Sanctum auth and module-based permissions
- 🎨 **Customizable UI** — per-user navigation style and color theme settings
- 📊 **Dashboard widgets** — stats overview, project Gantt timeline with milestones
- ⚡ **Compact, data-dense UI** — optimized tables, sticky actions, narrow sidebars
- 📦 **Background jobs** — BOQ variance sync, compliance alerts, queue processing
- 🔒 **Security hardened** — 2FA, rate limiting, session management, security headers

---

## Architecture

### Panels

| Panel | URL | Access | Purpose |
|-------|-----|--------|---------|
| **Admin** | `/admin` | Super Admins only | Platform management: companies, subscriptions, all users & roles |
| **App** | `/app` | All active users | Day-to-day work: projects, modules, settings |
| **Client** | `/client` | Client users | Client Portal: view projects, invoices, documents |
| **Mobile** | `/mobile` | All users (API auth) | Mobile PWA: tasks, forms, offline field work |

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
| **Site** | Field Logs, Inventory, SHEQ, Equipment |
| **Commercial** | Financials, Contracts, BOQ, Subcontractors |
| **Information** | Documents (CDE), RFIs & Submittals, Reports, Suggestion Box |

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

### Inventory & Requisition Workflow

- **Products, Stores, Assets** — full inventory management
- **Material Requisitions** — create, approve, and issue against BOQ items
- **Issuance enforcement** — materials can only be issued against approved requisitions
- **Purchase Orders & GRN** — procurement lifecycle tracking
- **Asset lifecycle** — checkout, check-in, maintenance, transfer, dispose

### BOQ (Bill of Quantities)

- **Multi-BOQ per project** linked to contracts
- **Section headers** — grouped by category (A. Preliminaries, B. Substructure, etc.) with subtotals
- **Excel/CSV bulk import** — paste from Excel or upload CSV files
- **Variance tracking** — automated overrun/underrun alerts synced via background jobs
- **Revision snapshots** — track changes over time
- **Progress tracking** — quantity completed per line item with visual progress bars

### SHEQ (Safety, Health, Environment, Quality & Social)

- **Incidents, Inspections, Snags, Social records** — tabbed interface
- **Social records** — grievances, stakeholder engagement, labour welfare, CSR activities
- **Bulk actions** — resolve, delete across multiple records

### Client Portal

- **Auto-provisioning** — toggle "Create Portal Account" when creating a client to auto-create a User with login credentials
- **Grant access later** — "Grant Portal Access" action on any existing client
- **Portal status** — icon column in clients table shows active/inactive portal access
- **Client panel features** — Projects, Invoices, Documents, Change Password, Help & Docs
- **Security** — 2FA via email, must_change_password on first login, rate-limited login

### Mobile PWA (`/mobile`)

- **Bottom tab navigation** — Home, Projects, Tasks, Forms, Profile
- **Dashboard** — Stat cards, quick action grid, my tasks, recent projects
- **Projects** — Searchable list with status filter pills, project detail with module shortcuts
- **Tasks** — Aggregated from all projects, Open/Done/Overdue filters
- **Offline Forms** — Site diary, attendance, safety incident — saves to IndexedDB when offline
- **Dark theme** — Matches InfraHub brand (#020617 background, #6366f1 accent)
- **API-first** — All data loaded via REST API with localStorage caching
- **<50KB CSS** — No JS framework, vanilla JS with `fetch()`, fast on 3G
- **Installable** — manifest.json configured, add to home screen

### REST API v1

| Endpoint Group | Routes | Auth |
|---|---|---|
| **Auth** | login, register, logout, me, tokens | Public / Sanctum |
| **Projects** | CRUD + stats | `module:projects.*` |
| **Documents** | CRUD + review workflow (project-scoped) | `module:documents.*` |
| **Tasks** | CRUD + progress updates (project-scoped) | `module:tasks.*` |
| **RFIs** | CRUD + answer/close (project-scoped) | `module:documents.*` |
| **Submittals** | CRUD + review/resubmit (project-scoped) | `module:documents.*` |
| **Work Orders** | CRUD (project-scoped) | `module:work_orders.*` |
| **Site Diaries** | CRUD + approval | `module:field_logs.*` |
| **Attendance** | CRUD + today view | `module:crew.*` |
| **Equipment** | Allocations + fuel logs | `module:equipment.*` |
| **Safety** | CRUD | `module:safety.*` |
| **Offline Sync** | Generic queue + per-module sync | Sanctum |
| **Health** | `GET /api/health` | Public |

### Project Invitations

- **Invite by email** — "Invite People" action on project view
- **One email, multiple projects** — Same email can be invited to different projects
- **Token-based acceptance** — Secure unique tokens with expiration
- **Team management** — "Team" action shows members + pending invitations

### Suggestion Box

- **Priority levels** — Urgent, High, Normal, Low with emoji indicators
- **Upvoting** — All users can upvote suggestions
- **Engagement stats** — Total upvotes + in-progress count
- **Priority filter** — Filter by priority level

### Security & Hardening

- **Two-Factor Authentication** — Email OTP (10-minute expiry) on all panels
- **Rate limiting** — 5 attempts per email + 15 per IP per minute
- **Password policy** — 90-day expiry with 14-day warning, forced change on first login
- **Session security** — 30-minute timeout, concurrent session management
- **Security headers** — X-Frame-Options, CSP, HSTS, X-Content-Type-Options
- **IP blocking** — AdminPanelProvider-level blocked IP management

### UI Personalization

- **Navigation layout** — sidebar or top navigation
- **Color theme** — customizable primary color per user
- **Collapsible sidebar** on desktop
- **SPA mode** for snappy navigation
- **Fullscreen toggle** — global fullscreen mode across all pages

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
| **Auth & Permissions** | Spatie Permission + Filament Shield + Sanctum |
| **Database** | MySQL 8+ |
| **Frontend** | Blade, Alpine.js, Livewire 3 |
| **Mobile** | PWA (Vanilla JS, Service Worker, IndexedDB) |
| **API** | REST API v1 with Sanctum token auth |
| **Styling** | Filament Design System + Custom CSS |
| **Build** | Vite |
| **Fonts** | Inter (Google Fonts) |
| **Queue** | Database driver (Redis-ready) |
| **Cache/Session** | Database driver |
| **Offline** | Service Worker v3 + IndexedDB + Background Sync |

---

## Requirements

- PHP ≥ 8.2
- MySQL ≥ 8.0
- Composer ≥ 2.x
- Node.js ≥ 18.x & npm
- Git

---

## Development Guide

### Quick Start (One Command)

```bash
# Starts server, queue worker, log tail, and Vite — all in one terminal
composer dev
```

This runs concurrently:
- `php artisan serve` — Laravel dev server (http://localhost:8000)
- `php artisan queue:listen --tries=1` — Queue worker for background jobs
- `php artisan pail --timeout=0` — Real-time log streamer
- `npm run dev` — Vite hot-reload for CSS/JS

### Manual Start (Separate Terminals)

If you prefer running services individually:

**Terminal 1 — Web Server:**
```bash
php artisan serve
```

**Terminal 2 — Queue Worker:**
```bash
# Development (restarts on code changes, 1 try)
php artisan queue:listen --tries=1

# Production-like (persistent worker, 3 retries, 90s timeout)
php artisan queue:work --tries=3 --timeout=90
```

**Terminal 3 — Vite (hot reload for CSS/JS):**
```bash
npm run dev
```

**Terminal 4 — Log Tail (optional):**
```bash
php artisan pail
```

### Common Development Commands

```bash
# ── Database ──
php artisan migrate                          # Run pending migrations
php artisan migrate:fresh --seed             # Reset DB + seed demo data
php artisan migrate:status                   # Check migration status
php artisan tinker                           # Interactive REPL

# ── Cache & Views ──
php artisan optimize:clear                   # Clear ALL caches (config, route, view, app)
php artisan view:clear                       # Clear compiled Blade views
php artisan cache:clear                      # Clear application cache
php artisan config:clear                     # Clear config cache
php artisan route:clear                      # Clear route cache

# ── Permissions & Shield ──
php artisan shield:generate --all            # Regenerate all permissions
php artisan shield:super-admin               # Assign super_admin role
php artisan shield:install                   # Install Shield (first time only)

# ── Queue ──
php artisan queue:work --tries=3 --timeout=90     # Process queue jobs
php artisan queue:retry all                       # Retry all failed jobs
php artisan queue:failed                          # List failed jobs
php artisan queue:flush                           # Delete all failed jobs
php artisan queue:prune-batches --hours=72        # Prune old batch records

# ── Scheduled Jobs (Manual Trigger) ──
php artisan boq:sync-variance --all               # Sync BOQ variance (all projects)
php artisan boq:sync-variance --sync              # Run synchronously (for debugging)
php artisan loan:process-alerts                   # Process loan alerts

# ── Scheduler ──
php artisan schedule:list                         # View scheduled jobs
php artisan schedule:work                         # Run scheduler locally (dev)

# ── Assets ──
npm run build                                # Production build (CSS + JS)
npm run dev                                  # Vite dev server with HMR
php artisan storage:link                     # Create storage symlink

# ── Code Quality ──
php artisan test                             # Run test suite
./vendor/bin/pint                            # Fix code style (Laravel Pint)
php -l path/to/file.php                      # PHP syntax check
```

### Background Jobs & Scheduler

InfraHub uses background jobs for heavy processing:

| Job | Schedule | Description |
|-----|----------|-------------|
| `boq:sync-variance --all` | Daily 2:00 AM | Syncs BOQ variance across all projects |
| `loan:process-alerts` | Daily 7:00 AM | Sends loan payment & overdue alerts |
| `queue:prune-batches` | Sunday 3:00 AM | Cleans up old queue batch records |
| `auth:clear-resets` | Daily | Removes expired password reset tokens |

To run the scheduler in development:
```bash
php artisan schedule:work
```

### Project Structure

```
app/
├── Console/Commands/               # Artisan commands
│   ├── SyncBoqVariance.php         # BOQ variance sync command
│   ├── ProcessLoanAlerts.php       # Loan alerts command
│   └── SendComplianceAlerts.php    # Compliance alert command
├── Filament/
│   ├── Admin/                      # Admin panel (super admin)
│   │   └── Resources/
│   │       ├── CompanyResource
│   │       ├── UserResource
│   │       ├── RoleResource
│   │       ├── EmailTemplateResource
│   │       └── SubscriptionResource
│   ├── App/                        # App panel (all users)
│   │   ├── Concerns/               # Shared traits (ExportsTableCsv, SecureLogin)
│   │   ├── Pages/
│   │   │   ├── Dashboard.php       # Registers widgets (Stats + Timeline)
│   │   │   └── OfflineForms.php    # Offline-capable field forms
│   │   ├── Resources/
│   │   │   ├── CdeProjectResource/
│   │   │   │   └── Pages/
│   │   │   │       ├── ViewCdeProject      # Overview + Invite/Team actions
│   │   │   │       ├── BaseModulePage      # Abstract base for modules
│   │   │   │       └── Modules/            # Project module pages
│   │   │   │           ├── CoreFsmPage           # Work Orders (FSM)
│   │   │   │           ├── TaskWorkflowPage      # Tasks & Gantt
│   │   │   │           ├── PlanningProgressPage   # Milestones & EVM
│   │   │   │           ├── FieldManagementPage    # Daily Logs
│   │   │   │           ├── InventoryPage          # Stock, POs, GRN
│   │   │   │           ├── SheqPage               # Safety, Inspections
│   │   │   │           ├── FinancialsPage          # Cost tracking
│   │   │   │           ├── CostContractsPage       # Contracts
│   │   │   │           ├── BoqPage                 # Bill of Quantities
│   │   │   │           ├── CdePage                 # Documents (ISO 19650)
│   │   │   │           ├── EquipmentPage           # Plant & Equipment
│   │   │   │           ├── SubcontractorPage       # Subcontractors
│   │   │   │           ├── SuggestionBoxPage       # Suggestions + Upvotes
│   │   │   │           └── ReportingPage           # Reports & Analytics
│   │   │   └── ... (Asset, Client, Invoice, Task, WorkOrder resources)
│   │   └── Widgets/                # Dashboard widgets
│   ├── Client/                     # Client portal
│   │   ├── Pages/
│   │   │   ├── Dashboard.php
│   │   │   ├── ChangePassword.php
│   │   │   ├── ClientHelp.php      # Help & Documentation
│   │   │   └── Auth/Login.php
│   │   ├── Resources/
│   │   │   ├── ClientProjectResource
│   │   │   ├── ClientInvoiceResource
│   │   │   └── ClientDocumentResource
│   │   └── Widgets/ClientOverview.php
│   └── Pages/
│       └── SystemSettings          # Global UI settings
├── Http/
│   ├── Controllers/
│   │   ├── Api/                    # REST API v1 controllers
│   │   │   ├── AuthController.php
│   │   │   ├── ProjectController.php
│   │   │   ├── DocumentController.php
│   │   │   ├── TaskController.php
│   │   │   ├── RfiController.php
│   │   │   ├── SafetyController.php
│   │   │   ├── EquipmentController.php
│   │   │   ├── OfflineSyncController.php
│   │   │   └── HealthController.php
│   │   ├── MobileController.php     # Mobile PWA views
│   │   ├── ProjectInvitationController.php
│   │   └── InvitationController.php
│   └── Middleware/
│       ├── SecurityHeaders.php
│       ├── SessionSecurity.php
│       └── ForcePasswordChange.php
├── Models/                         # 70+ Eloquent models
│   ├── User.php, Company.php, CdeProject.php
│   ├── ProjectInvitation.php, ProjectSuggestion.php
│   ├── Boq.php, BoqItem.php, BoqRevision.php
│   ├── Contract.php, Invoice.php, PurchaseOrder.php
│   ├── SafetyIncident.php, EquipmentAllocation.php
│   ├── Tender.php, SubcontractorPackage.php
│   └── ...
├── Services/                       # Business logic services
│   ├── EmailService.php            # Templated email dispatch
│   ├── BoqVarianceService.php
│   └── ModuleNotificationService.php
└── Providers/Filament/
    ├── AdminPanelProvider.php
    ├── AppPanelProvider.php
    └── ClientPanelProvider.php

resources/views/
├── filament/app/pages/modules/     # Module Blade views
├── filament/client/pages/          # Client portal views
├── mobile/                         # Mobile PWA views
│   ├── layout.blade.php            # App shell + bottom nav
│   ├── login.blade.php
│   ├── home.blade.php              # Dashboard
│   ├── projects/                   # List + detail
│   ├── tasks.blade.php
│   ├── forms.blade.php             # Offline field forms
│   └── profile.blade.php
└── invitations/                    # Invitation acceptance pages

public/
├── css/mobile.css                  # Mobile PWA styles
├── sw.js                           # Service Worker v3
├── manifest.json                   # PWA manifest
└── js/offline-*.js                 # Offline capabilities

routes/
├── web.php                         # Web + mobile routes
├── api.php                         # REST API v1 (60+ endpoints)
└── console.php                     # Scheduler & Artisan commands

database/
├── migrations/                     # Database migrations (120+)
└── seeders/
    ├── DatabaseSeeder.php
    ├── SaasFoundationSeeder.php    # Full demo data
    └── RolesAndPermissionsSeeder.php
```

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

QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database
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
composer dev     # Start all services (recommended)
```

Or manually:
```bash
php artisan serve                        # Terminal 1
php artisan queue:work --tries=3 --timeout=90   # Terminal 2
npm run dev                              # Terminal 3
```

Access the application:
- **App panel**: [http://localhost:8000/app](http://localhost:8000/app)
- **Admin panel**: [http://localhost:8000/admin](http://localhost:8000/admin)
- **Client portal**: [http://localhost:8000/client](http://localhost:8000/client)
- **Mobile PWA**: [http://localhost:8000/mobile](http://localhost:8000/mobile)
- **API docs**: `GET /api/health` for health check, all endpoints under `/api/v1/`

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
| **Client** | Client Portal | View projects, invoices, and documents at `/client` |

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

### Queue Configuration

```env
QUEUE_CONNECTION=database
```

InfraHub uses the `database` queue driver by default. For production, consider switching to Redis:

```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Mail Configuration

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Google Login Integration

```env
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
```

1. Visit [Google Cloud Console](https://console.cloud.google.com/)
2. Create/select a project → Enable Google+ API
3. Create OAuth 2.0 credentials (Web application)
4. Add redirect URI: `http://localhost:8000/auth/google/callback`

---

## Ubuntu Server Deployment Guide

### Prerequisites

- Ubuntu 22.04 or 24.04 LTS
- Root or sudo access
- Domain name pointed to server IP (e.g., `infrahub.yourdomain.com`)

### Step 1 — Install System Dependencies

```bash
sudo apt update && sudo apt upgrade -y

# Add PHP 8.3 repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.3 + required extensions
sudo apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-common \
  php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip \
  php8.3-gd php8.3-intl php8.3-bcmath php8.3-redis php8.3-tokenizer

# Install MySQL, Nginx, Node.js, and utilities
sudo apt install -y mysql-server nginx git unzip curl supervisor

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js 20 LTS
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

### Step 2 — Configure MySQL

```bash
sudo mysql_secure_installation

sudo mysql -u root -p
```

```sql
CREATE DATABASE infrahub_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'infrahub'@'localhost' IDENTIFIED BY 'YOUR_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON infrahub_production.* TO 'infrahub'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3 — Deploy Application

```bash
# Create web directory
sudo mkdir -p /var/www/infrahub
sudo chown $USER:$USER /var/www/infrahub

# Clone repository
cd /var/www
git clone <repository-url> infrahub
cd infrahub

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Environment setup
cp .env.example .env
php artisan key:generate
```

Edit `.env` for production:

```env
APP_NAME="InfraHub"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://infrahub.yourdomain.com
APP_TIMEZONE=Africa/Kampala

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=infrahub_production
DB_USERNAME=infrahub
DB_PASSWORD=YOUR_STRONG_PASSWORD

QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="InfraHub"
```

```bash
# Run migrations & seed
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link

# Set permissions
sudo chown -R www-data:www-data /var/www/infrahub
sudo chmod -R 775 /var/www/infrahub/storage
sudo chmod -R 775 /var/www/infrahub/bootstrap/cache

# Optimize for production
php artisan optimize
php artisan filament:optimize
php artisan view:cache
php artisan icons:cache
```

### Step 4 — Configure Nginx

```bash
sudo nano /etc/nginx/sites-available/infrahub
```

```nginx
server {
    listen 80;
    server_name infrahub.yourdomain.com;
    root /var/www/infrahub/public;
    index index.php;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    # Max upload size (for CSV/Excel imports)
    client_max_body_size 50M;

    # Gzip compression
    gzip on;
    gzip_types text/css application/javascript application/json image/svg+xml;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/infrahub /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Step 5 — Configure SSL (Let's Encrypt)

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d infrahub.yourdomain.com
```

### Step 6 — Setup Queue Worker (Supervisor)

```bash
sudo nano /etc/supervisor/conf.d/infrahub-worker.conf
```

```ini
[program:infrahub-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/infrahub/artisan queue:work database --sleep=3 --tries=3 --timeout=90 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/infrahub/storage/logs/worker.log
stdout_logfile_maxbytes=10MB
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start infrahub-worker:*

# Verify workers are running
sudo supervisorctl status
```

### Step 7 — Setup Cron for Scheduler

```bash
sudo crontab -e -u www-data
```

Add this line:

```cron
* * * * * cd /var/www/infrahub && php artisan schedule:run >> /dev/null 2>&1
```

This triggers the Laravel scheduler every minute, which runs:
- **BOQ Variance Sync** at 2:00 AM daily
- **Loan Alerts** at 7:00 AM daily
- **Queue Batch Pruning** on Sundays at 3:00 AM
- **Auth Token Cleanup** daily

### Step 8 — PHP-FPM Tuning (Optional)

```bash
sudo nano /etc/php/8.3/fpm/pool.d/www.conf
```

Recommended settings for a 2GB+ RAM server:

```ini
pm = dynamic
pm.max_children = 20
pm.start_servers = 5
pm.min_spare_servers = 3
pm.max_spare_servers = 10
pm.max_requests = 500

; Upload & execution limits
php_admin_value[upload_max_filesize] = 50M
php_admin_value[post_max_size] = 50M
php_admin_value[max_execution_time] = 300
php_admin_value[memory_limit] = 256M
```

```bash
sudo systemctl restart php8.3-fpm
```

---

## Production Maintenance

### Deploying Updates

```bash
cd /var/www/infrahub

# Pull latest code
git pull origin main

# Install/update dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Run migrations
php artisan migrate --force

# Clear and rebuild caches
php artisan optimize:clear
php artisan optimize
php artisan filament:optimize
php artisan view:cache
php artisan icons:cache

# Restart queue workers (picks up new code)
sudo supervisorctl restart infrahub-worker:*

# Restart PHP-FPM
sudo systemctl restart php8.3-fpm
```

### Quick Deploy Script

Create `/var/www/infrahub/deploy.sh`:

```bash
#!/bin/bash
set -e

echo "🚀 Deploying InfraHub..."

cd /var/www/infrahub

# Maintenance mode
php artisan down --retry=60

# Pull & install
git pull origin main
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Database
php artisan migrate --force

# Caches
php artisan optimize:clear
php artisan optimize
php artisan filament:optimize
php artisan view:cache
php artisan icons:cache

# Restart services
sudo supervisorctl restart infrahub-worker:*
sudo systemctl restart php8.3-fpm

# Go live
php artisan up

echo "✅ Deployment complete!"
```

```bash
chmod +x /var/www/infrahub/deploy.sh
```

### Monitoring & Logs

```bash
# Application logs
tail -f /var/www/infrahub/storage/logs/laravel.log

# Queue worker logs
tail -f /var/www/infrahub/storage/logs/worker.log

# BOQ variance sync logs
tail -f /var/www/infrahub/storage/logs/boq-variance.log

# Nginx logs
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log

# Supervisor status
sudo supervisorctl status

# Failed queue jobs
php artisan queue:failed
php artisan queue:retry all

# Disk usage
du -sh /var/www/infrahub/storage/
```

### Backup Strategy

```bash
# Database backup
mysqldump -u infrahub -p infrahub_production | gzip > ~/backups/infrahub_$(date +%Y%m%d_%H%M).sql.gz

# Full backup (code + storage + DB)
tar -czf ~/backups/infrahub_full_$(date +%Y%m%d).tar.gz \
  /var/www/infrahub/storage/app \
  /var/www/infrahub/.env

# Automate daily backups via cron
echo "0 4 * * * mysqldump -u infrahub -p'PASSWORD' infrahub_production | gzip > /home/ubuntu/backups/infrahub_\$(date +\%Y\%m\%d).sql.gz" | sudo tee -a /var/spool/cron/crontabs/root
```

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
| Queue jobs not processing | Ensure `php artisan queue:work` is running; check `QUEUE_CONNECTION=database` in `.env` |
| Supervisor workers stopped | Run `sudo supervisorctl restart infrahub-worker:*` |
| 502 Bad Gateway (Nginx) | Check `php8.3-fpm` is running: `sudo systemctl status php8.3-fpm` |
| Upload fails (large files) | Increase `client_max_body_size` in Nginx and `upload_max_filesize` in PHP |
| Scheduler not running | Verify cron entry: `crontab -l -u www-data` |
| Client portal 404 | Ensure `ClientPanelProvider` is registered in `bootstrap/providers.php` |
| Mobile PWA not loading | Check routes: `php artisan route:list --path=mobile` |
| API returns 401 | Token expired or invalid — re-authenticate via `POST /api/v1/auth/login` |
| Offline forms not syncing | Check Service Worker: `sw.js` must be served from root, ensure HTTPS in production |

---

## License

This project is licensed under the [GPL-3.0 License](LICENSE).