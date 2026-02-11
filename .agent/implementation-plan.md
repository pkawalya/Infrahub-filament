---
description: Implementation plan for rebuilding the FSM product as a multi-tenant SaaS using Filament 4.
---

# InfraHub SaaS — Implementation Plan

## Architecture

| Component | Technology |
|---|---|
| Framework | Laravel 12 |
| Admin UI | Filament 4 |
| RBAC | Filament Shield (Spatie Permissions) |
| Database | MySQL |
| Tenancy | `company_id` column scoping via global scopes |

## Panels

| Panel | Path | Purpose |
|---|---|---|
| **Admin** | `/admin` | Super Admin — manage companies, subscriptions, platform |
| **App** | `/app` | Tenant — all FSM modules, scoped to company |

## Module Registry

| Code | Module Name | Migration | Model(s) | Resource |
|---|---|---|---|---|
| `core` | Core FSM | ✅ `100100` | ✅ WorkOrder, Client, Asset, Invoice, etc. | ✅ WorkOrderResource, ClientResource, AssetResource, InvoiceResource |
| `cde` | Common Data Environment | ✅ `100300` | ✅ CdeProject, CdeFolder, CdeDocument, Rfi | ✅ CdeProjectResource |
| `field_management` | Field Management | ✅ `100400` | ⬜ ServiceLocation, TechTracking, Route | ⬜ Pending |
| `task_workflow` | Task & Workflow | ✅ `100500` | ✅ Task | ✅ TaskResource |
| `inventory` | Inventory & Procurement | ✅ `100600` | ✅ Product, Warehouse, Supplier, PurchaseOrder | ⬜ Pending |
| `cost_contracts` | Cost & Contracts | ✅ `100700` | ✅ Vendor, Contract | ⬜ Pending |
| `planning_progress` | Planning & Progress | ✅ `100800` | ⬜ Schedule, Milestone, Timesheet | ⬜ Pending |
| `boq_management` | BOQ Management | ✅ `100900` | ✅ Boq, BoqItem, BoqRevision | ⬜ Pending |
| `sheq` | SHEQ Management | ✅ `101000` | ✅ SafetyIncident, SafetyInspection | ✅ SafetyIncidentResource |
| `reporting` | Reporting & Dashboards | ⬜ Pending | ⬜ Pending | ⬜ Pending |

## Multi-Tenancy

- **Trait**: `App\Models\Concerns\BelongsToCompany`
  - Auto-sets `company_id` on creation from `auth()->user()->company_id`
  - Global scope filters all queries by current user's company
  - Super admins bypass the global scope
- **User Types**: `super_admin`, `company_admin`, `manager`, `member`, `technician`, `client`
- **Panel Access**: `super_admin` → `/admin`; all active users → `/app`

## Completed Work

### Phase 1: Foundation ✅
- [x] SaaS foundation migration (subscriptions, companies, modules, company_module_access)
- [x] User model with multi-tenant support
- [x] Company model with module management & billing
- [x] Subscription model
- [x] Module registry with all 10 modules defined
- [x] BelongsToCompany trait (auto company_id, global scope)
- [x] Admin panel provider (super admin only)
- [x] App panel provider (tenant users, Shield RBAC)
- [x] Admin dashboard with PlatformOverview widget
- [x] App dashboard with TenantDashboardOverview + WorkOrdersByStatusChart widgets

### Phase 2: Core Migrations ✅
- [x] Core FSM tables (100100)
- [x] HR/Operations tables (100200)
- [x] CDE tables (100300)
- [x] Field Management tables (100400)
- [x] Task & Workflow tables (100500)
- [x] Inventory tables (100600)
- [x] Cost & Contracts tables (100700)
- [x] Planning & Progress tables (100800)
- [x] BOQ tables (100900)
- [x] SHEQ tables (101000)

### Phase 3: Models ✅
- [x] Foundation: Subscription, Company, CompanyModuleAccess, Module, User
- [x] Core FSM: Client, Asset, ServicePart, WorkOrderType, WorkOrder, WorkOrderRequest, WorkOrderItem, WorkOrderTask, WorkOrderAppointment, Invoice, InvoicePayment, Estimation, EstimationItem, Feedback
- [x] CDE: CdeProject, CdeFolder, CdeDocument, Rfi
- [x] Task: Task
- [x] Inventory: Product, ProductCategory, Warehouse, StockLevel, Supplier, PurchaseOrder, PurchaseOrderItem
- [x] Cost & Contracts: Vendor, Contract
- [x] HR: Employee, Attendance, Leave
- [x] SHEQ: SafetyIncident, SafetyInspection
- [x] BOQ: Boq, BoqItem, BoqRevision

### Phase 4: Filament Resources ✅
- [x] Admin: CompanyResource, SubscriptionResource
- [x] App: WorkOrderResource, ClientResource, AssetResource, InvoiceResource
- [x] App: CdeProjectResource, TaskResource, SafetyIncidentResource

### Phase 5: Seeders ✅
- [x] SaasFoundationSeeder (plans, modules, super admin, demo company)

## Remaining Work

### Phase 6: Additional Resources
- [ ] EmployeeResource (HR)
- [ ] ProductResource, WarehouseResource, PurchaseOrderResource (Inventory)
- [ ] ContractResource, VendorResource (Cost & Contracts)
- [ ] BoqResource (BOQ)
- [ ] ScheduleResource, TimesheetResource (Planning)
- [ ] RfiResource, CdeDocumentResource (CDE)
- [ ] SafetyInspectionResource (SHEQ)
- [ ] Field Management resources (ServiceLocation, Route, SiteCheckin)

### Phase 7: Feature Flagging (Module Gating)
- [ ] Middleware to check module access per navigation group
- [ ] Hide nav items for disabled modules
- [ ] Gate resource access based on company module subscriptions

### Phase 8: Advanced Features
- [ ] Reporting module (custom report builder, export to PDF/Excel)
- [ ] Client Portal panel
- [ ] Real-time notifications (work order assignments, approvals)
- [ ] Activity logging (audit trail)
- [ ] API endpoints for mobile app
- [ ] Stripe/Paddle integration for subscription billing

## How To Run

```bash
# From the /home/pkawalya/Desktop/projects/infrahub directory:

# 1. Run migrations (fresh)
php artisan migrate:fresh --force

# 2. Seed foundation data
php artisan db:seed

# 3. Build assets
npm run build

# 4. Start dev server
php artisan serve

# Login credentials:
# Super Admin: admin@infrahub.io / password  → /admin
# Company Admin: admin@acme-fs.com / password  → /app
```
