<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Str;

class UserManualHelper
{
    public static function getDefaultSections(): array
    {
        return [
            [
                'group' => 'Introduction',
                'title' => '1. System Navigation & Layout',
                'icon' => '🧭',
                'image_path' => 'images/image-1a.png',
                'content' => "When logging into the `/app` panel, the interface opens directly to your selected **Project Workspace**. This secure structure guarantees that your project data is protected and visible only to authorized team members.\n\n### Layout & Navigation Tools\n- **Project Selector**: Switch between different projects instantly using the selector in the sidebar or top header. Changing the project updates all metrics and views to reflect your selected project.\n- **Key Metrics Summary Cards**: Located at the top of most pages, these cards summarize totals, active items, urgent alerts, and costs for quick review.\n- **Sub-navigation Tabs**: Several complex dashboards use tabs (e.g., Inventory, Safety Board, Schedule) to help you switch between different views and sub-sections quickly.",
            ],
            [
                'group' => 'Operations',
                'title' => 'Project Schedule & Tasks',
                'icon' => '📅',
                'image_path' => 'images/image-2.jpeg',
                'content' => "The Schedule dashboard is a unified center merging tasks, schedule visualizers, and project milestones.\n\n#### Key Workflows & Features\n1. **WBS Code Hierarchy**: Tasks are organized into Work Breakdown Structure (WBS) levels (e.g., 1.0, 1.1, 1.1.1). Parent tasks (Summary Tasks) act as containers.\n2. **WBS Code Regeneration**: If tasks are reordered or updated, click the **Regenerate WBS Codes** utility to recursively recalculate all hierarchy numbers.\n3. **MS Project XML Import/Export**:\n   - **Import**: Click the \"Import MS Project XML\" action, upload your XML, choose whether to clear existing tasks, and import. This parses tasks, durations, dates, milestones, and predecessor links. WBS levels are rebuilt automatically.\n   - **Export**: Streams out a native XML format matching Microsoft Project schemas.\n4. **Excel Schedule Import**: Import spreadsheets directly. The system maps `Title`, `Duration`, `Predecessors`, and `Start Date` columns.\n5. **Interactive Gantt Chart**: The GANTT visualizer displays durations, progress bars, and relationships (Finish-to-Start, Start-to-Start, etc.).\n6. **Task Attributes**:\n   - *Constraint Types*: ASAP (As Soon As Possible), ALAP, MSO (Must Start On), MFO (Must Finish On), SNET, SNLT, FNET, FNLT.\n   - *Predecessors*: Repeater allows defining dependent tasks with lag or lead days (negative lag).\n   - *Commissioning Phase*: Tag tasks with OHL or Energy construction stages for specialized tracking.",
            ],
            [
                'group' => 'Operations',
                'title' => 'Work Order Management (Core FSM)',
                'icon' => '🔧',
                'image_path' => null,
                'content' => "Field Service Management (FSM) is integrated into task scheduling. Work Orders represent distinct operations requiring labor, material, or checklists.\n\n#### Key Workflows & Features\n1. **Work Order Checklist**: Create operational checklists (to-do check-items) that field operators tick off on-site.\n2. **Cost Estimation**: Track both estimated and actual costs for Parts (materials) and Services (subcontractors/labor).\n3. **Progress Sync**: When a work order's checklists are fully ticked, its progress updates the corresponding project task.",
            ],
            [
                'group' => 'Operations',
                'title' => 'Planning & Progress (Milestones)',
                'icon' => '🚩',
                'image_path' => 'images/image-3.jpeg',
                'content' => "Milestones represent key deadlines and target handovers (e.g., \"Foundations Complete\", \"Grid Connection\").\n\n#### Key Workflows & Features\n1. **Stats Cards**:\n   - **Total Milestones** vs. **Completed** (%)\n   - **Delayed Milestones** (Target Date in the past without completion)\n   - **Upcoming Milestones** (Next 30 days)\n2. **Completion Action**: Click **Complete** on any pending milestone, enter the `Actual Date`, and the system locks it as completed.\n3. **Reschedule Action**:\n   - When rescheduling, select a new target date and specify a mandatory **Reason for Reschedule**.\n   - The system automatically logs the date change and reason directly into the description field history: `[Rescheduled Month Day: Old Date → New Date] Reason`.\n4. **Variance Calculations**:\n   - *For Completed Milestones*: Variance calculates early or late delivery (e.g., `3d early` or `5d late`).\n   - *For Pending Milestones*: Calculates days remaining or days overdue.",
            ],
            [
                'group' => 'Site & Resources',
                'title' => 'Inventory & Store Management',
                'icon' => '📦',
                'image_path' => 'images/image-4.jpeg',
                'content' => "This module tracks physical stock from procurement request through to material usage on site.\n\n#### Sub-Context Tabs\n- **Products**: Item catalog showing SKUs, Brands, unit of measure, unit cost/selling prices, reorder levels, and total stock.\n- **Stores**: Configured warehouse locations, yards, or containers scoping stock level tracking.\n- **Material Requisitions**: Field requests for stock. Track status (Pending, Approved, Issued).\n- **Purchase Orders (POs)**:\n  - High-fidelity invoice-style builder.\n  - Choose a supplier, define shipping cost, tax rate, and order items.\n  - Status updates: `Draft` → `Ordered` → `Closed`.\n- **Goods Received Notes (GRNs)**:\n  - Link to POs. Check off actual quantities received against ordered quantities to record initial warehouse stock.\n- **Material Issuances**:\n  - Log physical material usage. Link to site workers or subcontractors, tracking quantities and returnable status.\n- **Stock Transfers & Adjustments**: Log internal stock movements between store yards or correct inventory count discrepancies.\n- **Assets Registry**: Fixed assets tracker. Track asset assignments, check-out dates, expected returns, and log maintenance events (e.g., inspections, breakdowns, maintenance costs).",
            ],
            [
                'group' => 'Site & Resources',
                'title' => 'SHEQ (Safety, Health, Environment, Quality & Social)',
                'icon' => '⚠️',
                'image_path' => 'images/image-5.jpeg',
                'content' => "Ensures site compliance, tracks occurrences, and measures community relationship parameters.\n\n#### Operational Pillars\n1. **Safety Incidents**:\n   - Record incident type (injury, near miss, property damage, chemical spill, etc.) and severity (minor, moderate, major, critical, fatal).\n   - **AI-Assisted Incident Analysis**: Sends incident data to the integrated AI model to recommend preventative measures and evaluate root causes.\n   - Calculates **Days Since Last Incident** to show real-time safety stats.\n2. **Safety Inspections**:\n   - Build checklists using custom templates. Field safety officers conduct inspections and record PASS/FAIL results per item.\n3. **Snag Tracking (Defects & Quality Issues)**:\n   - Identify issues, assign to team members, set deadlines, and track resolution statuses.\n   - **Snags Registry**: Central snags list to log construction snags.\n4. **Social & Community Records**:\n   - Record complaints or local grievances. Monitor local hiring targets and community project allocations.",
            ],
            [
                'group' => 'Site & Resources',
                'title' => 'Plant & Equipment Management',
                'icon' => '🚜',
                'image_path' => 'images/image-6.jpeg',
                'content' => "Controls company-owned fleet, machinery, and rented equipment.\n\n#### Key Workflows & Features\n1. **Active Equipment Allocations**: Assign a machine (excavator, crane, truck) to the project, logging a daily rate.\n2. **Fuel Logs Register**:\n   - Log fuel date, liters filled, cost per liter, pump receipt images, and odometer/hour readings.\n   - The dashboard aggregates **Month-To-Date (MTD) Fuel Costs** and total liters consumed.\n3. **Preventative Maintenance Logs**: Log scheduled servicing or odometer triggers.",
            ],
            [
                'group' => 'Site & Resources',
                'title' => 'Field Operations (Daily Site Logs)',
                'icon' => '👷',
                'image_path' => 'images/image-7.jpeg',
                'content' => "The Daily Log is the primary mechanism for recording progress, site conditions, and manpower on a day-to-day basis.\n\n#### Key Workflows & Features\n1. **Diary Fields**: Weather (rain hours, temperature), work stoppages, labor force on-site, subcontractor counts, visitors, and general notes.\n2. **Task Progress Updates**:\n   - Within each daily log, list which schedule tasks were worked on.\n   - Enter hours worked, status update (in progress, completed, blocked), and cumulative progress percentage.\n3. **Progress Synchronization (`syncTaskProgress`)**:\n   - When a daily log is approved, the system updates the main Project Schedule.\n   - If progress increases, it logs the `actual_start` automatically.\n   - If a task hits 100% or is marked completed in the log, its status changes to `done`, and the system logs the completion date (`actual_finish`).\n   - actual hours are added to the task's historical records.\n   - Summary parent tasks automatically run child rollups (`rollUpFromChildren`) to update parent phase percentages.",
            ],
            [
                'group' => 'Commercial & Cost',
                'title' => 'Bill of Quantities (BOQ)',
                'icon' => '📊',
                'image_path' => 'images/image-8.jpeg',
                'content' => "The BOQ stores the project's pricing breakdown by sections (Preliminaries, Substructure, finishes, etc.).\n\n#### Key Workflows & Features\n1. **Excel Paste-to-Import**:\n   - Click **Excel Paste Import**, copy columns straight from Excel (Code, Description, Unit, Quantity, Rate, Category), paste into the raw text area, and click submit. The system parses and populates the items.\n2. **Bulk CSV Upload**:\n   - Upload a CSV file matching standard templates (`Code, Description, Unit, Qty, Rate`). The system skips headers and imports the lines.\n3. **Variations**: Toggle the `is_variation` option on any import to record items as contract variations.\n4. **Variance Alerts**: Automatically monitors quantity discrepancies (e.g. actual quantities completed on site exceeding the original BOQ limit) and raises flags.",
            ],
            [
                'group' => 'Commercial & Cost',
                'title' => 'Financial Tracking (Invoicing & Expenses)',
                'icon' => '💳',
                'image_path' => null,
                'content' => "Monitors cash flow, incoming payments, and outgoing operational expenses.\n\n#### Key Workflows & Features\n1. **Client Invoicing**: Draft invoices, add invoice line items, calculate taxes, track due dates, and monitor collection rate percentage.\n2. **Invoice Payments**: Record payment amount, receipt dates, reference numbers (cheques/electronic transfers), and payment methods.\n3. **Expenses Tracking**: Categorize expenses (material, labor, permit, equipment, overhead). Routes requests through an approval workflow (Draft → Submitted → Approved/Rejected).\n4. **Financial Health Stats**: Aggregates Outstanding receivables, Collection rate, and Cash balances.",
            ],
            [
                'group' => 'Commercial & Cost',
                'title' => 'Cost & Contract Management',
                'icon' => '📝',
                'image_path' => 'images/image-9.jpeg',
                'content' => "Handles formal contracts with subcontractors, suppliers, or the client.\n\n#### Key Workflows & Features\n1. **Stats Cards**:\n   - **Original Sum** vs. **Revised Contract Sum**.\n   - **Total Payments** certified.\n   - **Retainage Held** (amount withheld for safety/defect liabilities).\n2. **Record Payment Certificate**:\n   - Log a certified payment with details (Certificate #, date, reference).\n   - If `retainage_percent` is configured (e.g., 10%), the system automatically calculates the retainage amount from the payment, updating the `retainage_held` aggregate.\n   - **Retainage Release**: Record a release payment to subtract from `retainage_held` and add to `retainage_released`.\n3. **Variation Action**:\n   - Apply additions or deductions to the contract.\n   - Automatically recalculates the `revised_value` and appends a structured log entry to the contract description: `[Variation Date — +Amount] Reason`.",
            ],
            [
                'group' => 'Commercial & Cost',
                'title' => 'Subcontractor Management',
                'icon' => '🤝',
                'image_path' => null,
                'content' => "Dedicated section for managing subcontractor work packages.\n\n#### Key Workflows & Features\n1. **Subcontractor Registry**: Store subcontractor contacts, emails, and active statuses.\n2. **Work Packages**: Allocate specific BOQ items to a subcontractor, track progress, contract value, and paid-to-date figures.",
            ],
            [
                'group' => 'Commercial & Cost',
                'title' => 'Tenders & Bids',
                'icon' => '💸',
                'image_path' => null,
                'content' => "Establishes and tracks the company's business pipeline, estimating workflow, and competitive bids.\n\n#### Key Workflows & Features\n1. **Tender Pipeline & Status Tracking**:\n   - Track bid progression through formal stages: `Identified` (identifying a new RFP/RFQ opportunity), `Preparing` (compiling estimate rates), `Submitted` (formal bid submitted), `Shortlisted`, `Awarded` (won!), `Lost`, or `Withdrawn`.\n2. **Lead Estimator Assignment**: \n   - Assign tenders to a Lead Estimator to coordinate rate compilation and review due dates.\n3. **Financial Estimation**:\n   - Log `Estimated Value` (expected contract size) vs. `Our Bid Amount` (actual price submitted to client) alongside `Win Probability (%)` to feed pipeline forecasts.\n4. **AI-Powered Tender Analysis**:\n   - Click the **AI** sparkles button on a tender card to automatically trigger the `AiAssistantService`. The assistant parses descriptions to extract key technical requirements, highlights bid risks, and produces a structured \"Bid/No-Bid\" recommendation.\n5. **Bid Strategy & Lessons Learned**:\n   - Record competitors, strategy notes, and document the specific `Loss Reason` if the tender is lost, supporting historical analysis for future bids.\n6. **Company-wide Stages Customization**:\n   - Set up custom estimation gates (e.g. pre-qualification, site visits, management sign-off) under **Settings -> Tender Stages** or **Settings -> Bid Stages**.",
            ],
            [
                'group' => 'Collaboration',
                'title' => 'CDE (Common Data Environment) Document Management',
                'icon' => '📁',
                'image_path' => null,
                'content' => "ISO 19650 compliant document manager for blueprint revisions, manuals, and correspondence.\n\n#### Key Workflows & Features\n1. **ISO 19650 Gateways**:\n   - Folders and documents can be tagged with standardized suitability codes:\n     - **S0**: Work in Progress (WIP)\n     - **S1**: For Coordination\n     - **S2**: For Information\n     - **S3**: For Review & Comment\n     - **S4**: For Stage Approval\n     - **S6**: For PIM Handover (Project Information Model)\n     - **S7**: For AIM Handover (Asset Information Model)\n2. **Discipline Tags**: Tag documents by field (Architecture, Structural, Mechanical, Electrical, Civil, etc.).\n3. **Revision Management**: System supports major/minor revision tracking (`P01`, `P02`, `Rev A`, `Rev B`).\n4. **Document Sharing Links**: Generate external download links with configured expiration dates.\n5. **Transmittals**: Compile multiple documents, select recipients, write package details, and send formal transmittal packages.",
            ],
            [
                'group' => 'Collaboration',
                'title' => 'RFIs & Submittals',
                'icon' => '❓',
                'image_path' => null,
                'content' => "Technical query and material approval pipelines.\n\n#### Key Workflows & Features\n1. **RFIs (Requests for Information)**:\n   - Create technical questions, assign to engineering teams, set due dates, and track answer turnarounds.\n   - Metrics automatically track **Average RFI Response Time** in days.\n2. **Submittals**:\n   - Workflow for shop drawings, material samples, and specifications.\n   - Review cycles: `Pending` → `Under Review` → `Approved / Approved as Noted / Revise & Resubmit / Rejected`.",
            ],
            [
                'group' => 'Collaboration',
                'title' => 'Reporting, AI Analytics & Exports',
                'icon' => '📈',
                'image_path' => null,
                'content' => "Provides operational analytics and compliance records.\n\n#### Key Workflows & Features\n1. **AI Project Pulse**:\n   - Employs an LLM service (`AiAssistantService`) to scan all project data (tasks, milestones, safety incidents, contract cost variance, expenses, and document suitabilities).\n   - Generates a concise text-based analysis of project health and highlights critical issues.\n2. **Ask AI Assistant**:\n   - Chat box inside the reporting module that answers queries using the current project snapshot context.\n3. **Filament CSV Exports**:\n   - Instantly export CSV sheets formatted for local compliance audits (including URA tax audits) for:\n     - Financials\n     - Tasks\n     - CDE Documents\n     - Safety/SHEQ logs\n     - Cost Contracts\n     - Inventory Audit / Stock valuation\n     - Slow moving stock & material expirations",
            ],
            [
                'group' => 'Collaboration',
                'title' => 'Suggestion Box',
                'icon' => '📥',
                'image_path' => null,
                'content' => "An anonymous channel for workers and field crews to submit feedback.\n\n#### Key Workflows & Features\n1. **Submissions**: Click **Submit Suggestion**, select a category (Safety, process, equipment, communication, work_conditions, general), choose priority, and type feedback. Submissions are anonymous.\n2. **Voting**: Team members can upvote suggestions.\n3. **Admin Actions**: Project managers can view suggestions, post official responses, and update feedback statuses (`Reviewed`, `In Progress`, `Implemented`, `Dismissed`).",
            ],
        ];
    }

    public static function getSections(): array
    {
        $sections = Setting::getValue('user_manual_sections');
        if (empty($sections)) {
            return self::getDefaultSections();
        }

        $decoded = is_string($sections) ? json_decode($sections, true) : $sections;
        return is_array($decoded) ? $decoded : self::getDefaultSections();
    }

    public static function getGroupedSections(): array
    {
        $sections = self::getSections();
        $grouped = [];

        foreach ($sections as $section) {
            $group = $section['group'] ?? 'General';
            $section['slug'] = self::slugify($section['title']);
            $grouped[$group][] = $section;
        }

        return $grouped;
    }

    public static function slugify(string $text): string
    {
        $text = strtolower($text);
        $text = str_replace('&', '', $text);
        $text = preg_replace('/[^\w\s-]/', '', $text);
        $text = trim($text);
        $text = preg_replace('/\s/', '-', $text);
        return $text;
    }

    public static function getImageUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        return asset('storage/' . $path);
    }
}
