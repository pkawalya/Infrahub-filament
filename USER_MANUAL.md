# InfraHub Modular Project Management System
## End-User & Site Operations Manual

Welcome to the **InfraHub Modular Project Management System User Manual**. This document is a comprehensive, functional guide for Project Managers, Site Engineers, Commercial Officers, Document Controllers, and Company Administrators operating within the InfraHub platform. 

The InfraHub application panel (`/app`) is your central workspace for managing construction and infrastructure projects. To keep your information safe and secure, the system organizes data and modules around your specific company and selected project, ensuring you only view information relevant to your active workspace.

---

## Table of Contents
1. [System Navigation & Layout](#1-system-navigation--layout)
2. [Operations Management](#2-operations-management)
   - [Project Schedule & Tasks](#project-schedule--tasks)
   - [Work Order Management (Core FSM)](#work-order-management-core-fsm)
   - [Planning & Progress (Milestones)](#planning--progress-milestones)
3. [Site & Resource Management](#3-site--resource-management)
   - [Inventory & Store Management](#inventory--store-management)
   - [SHEQ (Safety, Health, Environment, Quality & Social)](#sheq-safety-health-environment-quality--social)
   - [Plant & Equipment Management](#plant--equipment-management)
   - [Field Operations (Daily Site Logs)](#field-operations-daily-site-logs)
4. [Commercial & Cost Management](#4-commercial--cost-management)
   - [Bill of Quantities (BOQ)](#bill-of-quantities-boq)
   - [Financial Tracking (Invoicing & Expenses)](#financial-tracking-invoicing--expenses)
   - [Cost & Contract Management](#cost--contract-management)
   - [Subcontractor Management](#subcontractor-management)
   - [Tenders & Bids](#tenders--bids)
5. [Information & Collaboration](#5-information--collaboration)
   - [CDE (Common Data Environment) Document Management](#cde-common-data-environment-document-management)
   - [RFIs & Submittals](#rfis--submittals)
   - [Reporting, AI Analytics & Exports](#reporting-ai-analytics--exports)
   - [Suggestion Box](#suggestion-box)

---

## 1. System Navigation & Layout

When logging into the `/app` panel, the interface opens directly to your selected **Project Workspace**. This secure structure guarantees that your project data is protected and visible only to authorized team members.

![Project Dashboard Landing Page](/images/image-1a.png)
*Figure 1: Project Workspace Landing Dashboard featuring real-time key metrics and active module navigation.*

### Layout & Navigation Tools
- **Project Selector**: Switch between different projects instantly using the selector in the sidebar or top header. Changing the project updates all metrics and views to reflect your selected project.
- **Key Metrics Summary Cards**: Located at the top of most pages, these cards summarize totals, active items, urgent alerts, and costs for quick review.
- **Sub-navigation Tabs**: Several complex dashboards use tabs (e.g., Inventory, Safety Board, Schedule) to help you switch between different views and sub-sections quickly.

---

## 2. Operations Management

Operations Management aggregates scheduling, task execution, site works, and milestone delivery.

### Project Schedule & Tasks
The Schedule dashboard is a unified center merging tasks, schedule visualizers, and project milestones.

![Project Schedule & Tasks view](/images/image-2.png)
*Figure 2: Project Schedule management with WBS structure, Gantt visualizer, and task lists.*

#### Key Workflows & Features
1. **WBS Code Hierarchy**: Tasks are organized into Work Breakdown Structure (WBS) levels (e.g., 1.0, 1.1, 1.1.1). Parent tasks (Summary Tasks) act as containers.
2. **WBS Code Regeneration**: If tasks are reordered or updated, click the **Regenerate WBS Codes** utility to recursively recalculate all hierarchy numbers.
3. **MS Project XML Import/Export**:
   - **Import**: Click the "Import MS Project XML" action, upload your XML, choose whether to clear existing tasks, and import. This parses tasks, durations, dates, milestones, and predecessor links. WBS levels are rebuilt automatically.
   - **Export**: Streams out a native XML format matching Microsoft Project schemas.
4. **Excel Schedule Import**: Import spreadsheets directly. The system maps `Title`, `Duration`, `Predecessors`, and `Start Date` columns.
5. **Interactive Gantt Chart**: The GANTT visualizer displays durations, progress bars, and relationships (Finish-to-Start, Start-to-Start, etc.).
6. **Task Attributes**:
   - *Constraint Types*: ASAP (As Soon As Possible), ALAP, MSO (Must Start On), MFO (Must Finish On), SNET, SNLT, FNET, FNLT.
   - *Predecessors*: Repeater allows defining dependent tasks with lag or lead days (negative lag).
   - *Commissioning Phase*: Tag tasks with OHL or Energy construction stages for specialized tracking.

---

### Work Order Management (Core FSM)
Field Service Management (FSM) is integrated into task scheduling. Work Orders represent distinct operations requiring labor, material, or checklists.

#### Key Workflows & Features
1. **Work Order Checklist**: Create operational checklists (to-do check-items) that field operators tick off on-site.
2. **Cost Estimation**: Track both estimated and actual costs for Parts (materials) and Services (subcontractors/labor).
3. **Progress Sync**: When a work order's checklists are fully ticked, its progress updates the corresponding project task.

---

### Planning & Progress (Milestones)
Milestones represent key deadlines and target handovers (e.g., "Foundations Complete", "Grid Connection").

![Milestone Planning & Progress](/images/image-3.png)
*Figure 3: Milestone tracker highlighting target dates, actual completions, and variance alerts.*

#### Key Workflows & Features
1. **Stats Cards**:
   - **Total Milestones** vs. **Completed** (%)
   - **Delayed Milestones** (Target Date in the past without completion)
   - **Upcoming Milestones** (Next 30 days)
2. **Completion Action**: Click **Complete** on any pending milestone, enter the `Actual Date`, and the system locks it as completed.
3. **Reschedule Action**:
   - When rescheduling, select a new target date and specify a mandatory **Reason for Reschedule**.
   - The system automatically logs the date change and reason directly into the description field history: `[Rescheduled Month Day: Old Date → New Date] Reason`.
4. **Variance Calculations**:
   - *For Completed Milestones*: Variance calculates early or late delivery (e.g., `3d early` or `5d late`).
   - *For Pending Milestones*: Calculates days remaining or days overdue.

---

## 3. Site & Resource Management

Resource management controls materials, safety compliance, fleet utilization, and daily diaries.

### Inventory & Store Management
This module tracks physical stock from procurement request through to material usage on site.

![Inventory & Store Management Overview](/images/image-4.png)
*Figure 4: Inventory tracking page showing active product records, store locations, and pending POs.*

#### Sub-Context Tabs
- **Products**: Item catalog showing SKUs, Brands, unit of measure, unit cost/selling prices, reorder levels, and total stock.
- **Stores**: Configured warehouse locations, yards, or containers scoping stock level tracking.
- **Material Requisitions**: Field requests for stock. Track status (Pending, Approved, Issued).
- **Purchase Orders (POs)**:
  - High-fidelity invoice-style builder.
  - Choose a supplier, define shipping cost, tax rate, and order items.
  - Status updates: `Draft` → `Ordered` → `Closed`.
- **Goods Received Notes (GRNs)**:
  - Link to POs. Check off actual quantities received against ordered quantities to record initial warehouse stock.
- **Material Issuances**:
  - Log physical material usage. Link to site workers or subcontractors, tracking quantities and returnable status.
- **Stock Transfers & Adjustments**: Log internal stock movements between store yards or correct inventory count discrepancies.
- **Assets Registry**: Fixed assets tracker. Track asset assignments, check-out dates, expected returns, and log maintenance events (e.g., inspections, breakdowns, maintenance costs).

---

### SHEQ (Safety, Health, Environment, Quality & Social)
Ensures site compliance, tracks occurrences, and measures community relationship parameters.

![SHEQ Incident & Inspections Panel](/images/image-5.png)
*Figure 5: SHEQ board showing incident levels, active inspections, and snag resolutions.*

#### Operational Pillars
1. **Safety Incidents**:
   - Record incident type (injury, near miss, property damage, chemical spill, etc.) and severity (minor, moderate, major, critical, fatal).
   - **AI-Assisted Incident Analysis**: Sends incident data to the integrated AI model to recommend preventative measures and evaluate root causes.
   - Calculates **Days Since Last Incident** to show real-time safety stats.
2. **Safety Inspections**:
   - Build checklists using custom templates. Field safety officers conduct inspections and record PASS/FAIL results per item.
3. **Snag Tracking (Defects & Quality Issues)**:
   - Identify issues, assign to team members, set deadlines, and track resolution statuses.
4. **Social & Community Records**:
   - Record complaints or local grievances. Monitor local hiring targets and community project allocations.

---

### Plant & Equipment Management
Controls company-owned fleet, machinery, and rented equipment.

![Plant & Equipment Management Dashboard](/images/image-6.png)
*Figure 6: Equipment fleet dashboard detailing active machinery allocations and fuel consumption.*

#### Key Workflows & Features
1. **Active Equipment Allocations**: Assign a machine (excavator, crane, truck) to the project, logging a daily rate.
2. **Fuel Logs Register**:
   - Log fuel date, liters filled, cost per liter, pump receipt images, and odometer/hour readings.
   - The dashboard aggregates **Month-To-Date (MTD) Fuel Costs** and total liters consumed.
3. **Preventative Maintenance Logs**: Log scheduled servicing or odometer triggers.

---

### Field Operations (Daily Site Logs)
The Daily Log is the primary mechanism for recording progress, site conditions, and manpower on a day-to-day basis.

![Daily Site Log Editor](/images/image-7.png)
*Figure 7: Daily site log form showing weather conditions, subcontractor counts, and scheduled task progress updates.*

#### Key Workflows & Features
1. **Diary Fields**: Weather (rain hours, temperature), work stoppages, labor force on-site, subcontractor counts, visitors, and general notes.
2. **Task Progress Updates**:
   - Within each daily log, list which schedule tasks were worked on.
   - Enter hours worked, status update (in progress, completed, blocked), and cumulative progress percentage.
3. **Progress Synchronization (`syncTaskProgress`)**:
   - When a daily log is approved, the system updates the main Project Schedule.
   - If progress increases, it logs the `actual_start` automatically.
   - If a task hits 100% or is marked completed in the log, its status changes to `done`, and the system logs the completion date (`actual_finish`).
   - actual hours are added to the task's historical records.
   - Summary parent tasks automatically run child rollups (`rollUpFromChildren`) to update parent phase percentages.

---

## 4. Commercial & Cost Management

This category handles commercial contracts, variations, financial health, and subcontractor progress.

### Bill of Quantities (BOQ)
The BOQ stores the project's pricing breakdown by sections (Preliminaries, Substructure, finishes, etc.).

![BOQ Sections and Items View](/images/image-8.png)
*Figure 8: BOQ detail showing item categories, variation items, and variance alerts.*

#### Key Workflows & Features
1. **Excel Paste-to-Import**:
   - Click **Excel Paste Import**, copy columns straight from Excel (Code, Description, Unit, Quantity, Rate, Category), paste into the raw text area, and click submit. The system parses and populates the items.
2. **Bulk CSV Upload**:
   - Upload a CSV file matching standard templates (`Code, Description, Unit, Qty, Rate`). The system skips headers and imports the lines.
3. **Variations**: Toggle the `is_variation` option on any import to record items as contract variations.
4. **Variance Alerts**: Automatically monitors quantity discrepancies (e.g. actual quantities completed on site exceeding the original BOQ limit) and raises flags.

---

### Financial Tracking (Invoicing & Expenses)
Monitors cash flow, incoming payments, and outgoing operational expenses.

#### Key Workflows & Features
1. **Client Invoicing**: Draft invoices, add invoice line items, calculate taxes, track due dates, and monitor collection rate percentage.
2. **Invoice Payments**: Record payment amount, receipt dates, reference numbers (cheques/electronic transfers), and payment methods.
3. **Expenses Tracking**: Categorize expenses (material, labor, permit, equipment, overhead). Routes requests through an approval workflow (Draft → Submitted → Approved/Rejected).
4. **Financial Health Stats**: Aggregates Outstanding receivables, Collection rate, and Cash balances.

---

### Cost & Contract Management
Handles formal contracts with subcontractors, suppliers, or the client.

![Contracts and Certified Payments panel](/images/image-9.png)
*Figure 9: Contracts manager displaying original values, revised contract sums, variation logs, and retainage.*

#### Key Workflows & Features
1. **Stats Cards**:
   - **Original Sum** vs. **Revised Contract Sum**.
   - **Total Payments** certified.
   - **Retainage Held** (amount withheld for safety/defect liabilities).
2. **Record Payment Certificate**:
   - Log a certified payment with details (Certificate #, date, reference).
   - If `retainage_percent` is configured (e.g., 10%), the system automatically calculates the retainage amount from the payment, updating the `retainage_held` aggregate.
   - **Retainage Release**: Record a release payment to subtract from `retainage_held` and add to `retainage_released`.
3. **Variation Action**:
   - Apply additions or deductions to the contract.
   - Automatically recalculates the `revised_value` and appends a structured log entry to the contract description: `[Variation Date — +Amount] Reason`.

---

### Subcontractor Management
Dedicated section for managing subcontractor work packages.

#### Key Workflows & Features
1. **Subcontractor Registry**: Store subcontractor contacts, emails, and active statuses.
2. **Work Packages**: Allocate specific BOQ items to a subcontractor, track progress, contract value, and paid-to-date figures.

---

### Tenders & Bids
Establishes and tracks the company's business pipeline, estimating workflow, and competitive bids.

#### Key Workflows & Features
1. **Tender Pipeline & Status Tracking**:
   - Track bid progression through formal stages: `Identified` (identifying a new RFP/RFQ opportunity), `Preparing` (compiling estimate rates), `Submitted` (formal bid submitted), `Shortlisted`, `Awarded` (won!), `Lost`, or `Withdrawn`.
2. **Lead Estimator Assignment**:
   - Assign tenders to a Lead Estimator to coordinate rate compilation and review due dates.
3. **Financial Estimation**:
   - Log `Estimated Value` (expected contract size) vs. `Our Bid Amount` (actual price submitted to client) alongside `Win Probability (%)` to feed pipeline forecasts.
4. **AI-Powered Tender Analysis**:
   - Click the **AI** sparkles button on a tender card to automatically trigger the `AiAssistantService`. The assistant parses descriptions to extract key technical requirements, highlights bid risks, and produces a structured "Bid/No-Bid" recommendation.
5. **Bid Strategy & Lessons Learned**:
   - Record competitors, strategy notes, and document the specific `Loss Reason` if the tender is lost, supporting historical analysis for future bids.
6. **Company-wide Stages Customization**:
   - Set up custom estimation gates (e.g. pre-qualification, site visits, management sign-off) under **Settings -> Tender Stages** or **Settings -> Bid Stages**.

---

## 5. Information & Collaboration

Documents, communications, and reporting tools.

### CDE (Common Data Environment) Document Management
ISO 19650 compliant document manager for blueprint revisions, manuals, and correspondence.

#### Key Workflows & Features
1. **ISO 19650 Gateways**:
   - Folders and documents can be tagged with standardized suitability codes:
     - **S0**: Work in Progress (WIP)
     - **S1**: For Coordination
     - **S2**: For Information
     - **S3**: For Review & Comment
     - **S4**: For Stage Approval
     - **S6**: For PIM Handover (Project Information Model)
     - **S7**: For AIM Handover (Asset Information Model)
2. **Discipline Tags**: Tag documents by field (Architecture, Structural, Mechanical, Electrical, Civil, etc.).
3. **Revision Management**: System supports major/minor revision tracking (`P01`, `P02`, `Rev A`, `Rev B`).
4. **Document Sharing Links**: Generate external download links with configured expiration dates.
5. **Transmittals**: Compile multiple documents, select recipients, write package details, and send formal transmittal packages.

---

### RFIs & Submittals
Technical query and material approval pipelines.

#### Key Workflows & Features
1. **RFIs (Requests for Information)**:
   - Create technical questions, assign to engineering teams, set due dates, and track answer turnarounds.
   - Metrics automatically track **Average RFI Response Time** in days.
2. **Submittals**:
   - Workflow for shop drawings, material samples, and specifications.
   - Review cycles: `Pending` → `Under Review` → `Approved / Approved as Noted / Revise & Resubmit / Rejected`.

---

### Reporting, AI Analytics & Exports
Provides operational analytics and compliance records.

#### Key Workflows & Features
1. **AI Project Pulse**:
   - Employs an LLM service (`AiAssistantService`) to scan all project data (tasks, milestones, safety incidents, contract cost variance, expenses, and document suitabilities).
   - Generates a concise text-based analysis of project health and highlights critical issues.
2. **Ask AI Assistant**:
   - Chat box inside the reporting module that answers queries using the current project snapshot context.
3. **Filament CSV Exports**:
   - Instantly export CSV sheets formatted for local compliance audits (including URA tax audits) for:
     - Financials
     - Tasks
     - CDE Documents
     - Safety/SHEQ logs
     - Cost Contracts
     - Inventory Audit / Stock valuation
     - Slow moving stock & material expirations

---

### Suggestion Box
An anonymous channel for workers and field crews to submit feedback.

#### Key Workflows & Features
1. **Submissions**: Click **Submit Suggestion**, select a category (Safety, process, equipment, communication, work_conditions, general), choose priority, and type feedback. Submissions are anonymous.
2. **Voting**: Team members can upvote suggestions.
3. **Admin Actions**: Project managers can view suggestions, post official responses, and update feedback statuses (`Reviewed`, `In Progress`, `Implemented`, `Dismissed`).
