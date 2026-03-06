# InfraHub Module Improvements Plan
## Modules Selected: 3, 4, 6, 7, 8, 9, 11, 12

---

## Module 3: Reporting (Priority 🥇)
**Current:** 182 lines PHP, 630 lines blade. Has task breakdown, milestone overview, module summary, activity logs, financial summary.
**Gaps:** No visual charts, no executive dashboard, no export.

### Improvements:
1. **Executive RAG Dashboard** — Add a visual project health strip (Red/Amber/Green) at the top summarizing schedule, cost, quality
2. **Visual Bar Charts** — Replace plain text stats with CSS bar charts for task breakdown
3. **Monthly Trend Mini-Chart** — Document uploads / activities per month (last 6 months)
4. **Quick Export Button** — One-click PDF/CSV export of the dashboard summary
5. **Cross-Module Health Cards** — Richer module summary cards with sparkline-style indicators

---

## Module 4: Inventory (Priority 🥈)
**Current:** 2117 lines PHP (largest!), 3397 lines blade. Has PO, GRN, Transfers, Adjustments, Requisitions, Assets.
**Gaps:** No low-stock alerts, no visual PO pipeline, heavy blade.

### Improvements:
1. **Low Stock Alert Strip** — Warning banner at top showing items below reorder level
2. **PO Pipeline Widget** — Visual pipeline showing PO status distribution (Draft → Sent → Received → Completed)
3. **Stock Value Summary** — Total inventory value card with category breakdown
4. **Requisition Status Badges** — Visual status flow for requisition approvals
5. **Quick Stats Enhancement** — Add "Pending GRNs", "Items Below Reorder" as stat cards

---

## Module 6: SHEQ (Priority 🥉)
**Current:** 762 lines PHP, 274 lines blade. Has Incidents, Inspections, Snags/Defects, Social tabs.
**Gaps:** No safety KPI dashboard, no severity heatmap, no CAPA tracking.

### Improvements:
1. **Safety KPI Strip** — LTIFR, TRIFR, Safe Days counter at the top
2. **Incident Severity Heatmap** — Visual grid showing incident distribution by severity × status
3. **Overdue Inspection Alert** — Banner for inspections past their scheduled date
4. **Toolbox Talk Quick-Log** — Simple button to log daily safety briefings
5. **Resolution Rate Gauge** — Percentage of snags resolved within target time

---

## Module 7: Financials
**Current:** 760 lines PHP, 294 lines blade. Has Invoices, Receipts, Expenses tables with analytics.
**Gaps:** Aging analysis exists but could be more visual, no budget vs actual.

### Improvements:
1. **Cash Flow Visualization** — CSS bar chart showing monthly income vs expenses
2. **Invoice Aging Visual Bars** — Color-coded horizontal bars for 30/60/90/120+ day buckets
3. **Overdue Invoice Alert Strip** — Red banner showing overdue unpaid invoices
4. **Payment Waterfall** — Visual flow: Contract → Certified → Invoiced → Paid → Balance
5. **Expense Category Donut** — Mini category breakdown visual

---

## Module 8: Field Management
**Current:** 598 lines PHP, 4 lines blade (!). Has daily site logs with task progress sync.
**Gaps:** Blade is minimal (just stat cards + table), no site diary view, no photo log.

### Improvements:
1. **Weekly Summary Strip** — Show this week's total labor hours, equipment hours, activities
2. **Weather Log Badge** — Visual weather strip for recent logs
3. **Task Progress Tracker** — Mini progress bars for tasks updated through daily logs
4. **Daily Log Timeline View** — Visual timeline of recent daily logs
5. **Quick Log Statistics** — Average labor count, equipment hours, entries per week

---

## Module 9: Cost & Contracts
**Current:** 664 lines PHP, 67 lines blade. Has contracts table with rich detail modal, certificates.
**Gaps:** No contract timeline visual, no variation tracking, no payment progress.

### Improvements:
1. **Contract Duration Bar** — Visual horizontal bar showing contract timeline with % elapsed
2. **Payment Progress Gauge** — Circle or bar showing paid vs total contract value
3. **Certificate Expiry Calendar** — Visual mini-calendar of upcoming certificate expirations
4. **Variation Summary Strip** — Count of approved vs pending variations
5. **Subcontractor Performance Cards** — Quick performance indicators

---

## Module 11: RFI & Submittals
**Current:** 546 lines PHP, 54 lines blade. Has RFI and Submittal tables with tab switcher.
**Gaps:** No response time analytics, no status pipeline, basic tab design.

### Improvements:
1. **RFI Response Time Analytics** — Average response time badge + overdue count
2. **Submittal Status Pipeline** — Visual pipeline (Pending → Under Review → Approved/Rejected)
3. **Overdue RFI Alert** — Red banner for RFIs past their due date
4. **Response Time KPI Cards** — Show average days to respond, longest open RFI
5. **Discipline Distribution** — Mini chart showing RFIs by discipline/trade

---

## Module 12: CDE (Document Management)
**Current:** Just redesigned with OneDrive styling.
**Gaps:** No document preview, no version timeline.

### Improvements:
1. **Document Preview Pane** — Right-side slide-in panel with metadata + sharing info
2. **Revision Timeline** — Visual version history per document
3. **Activity Feed** — Recent document actions (uploaded, shared, revised)
4. **Storage Usage Indicator** — Visual storage used vs available
5. **Quick Search** — Search documents across all folders

---

## Execution Order:
1. Module 8 (Field) — Quick win, blade is empty
2. Module 9 (Costs) — Small blade, high impact
3. Module 11 (RFI) — Small blade, analytics addition
4. Module 6 (SHEQ) — Add safety KPIs
5. Module 7 (Financials) — Visual enhancements
6. Module 3 (Reporting) — Executive dashboard
7. Module 4 (Inventory) — Low stock alerts
8. Module 12 (CDE) — Polish pass
