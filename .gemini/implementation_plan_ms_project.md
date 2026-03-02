# MS Project-Grade Task Management — Implementation Plan

## Phase 1: Database Enhancement (Migration)
- Add `wbs_code` (WBS number like 1.2.3), `duration_days`, `constraint_type`, `constraint_date`
- Add `baseline_start`, `baseline_finish`, `baseline_duration`, `baseline_cost`
- Add `is_milestone` flag, `calendar_id`, `lag_days` to dependencies
- Add `cost_rate`, `resource_type` fields
- Add `level` (indent level), `is_summary` (auto-computed for tasks with children)

## Phase 2: Model Enhancement
- TaskDependency model with FS/SS/FF/SF support
- Critical Path calculation method
- Auto WBS code generation
- Duration ↔ Start/End date auto-calculation
- Summary task roll-up (dates, progress, hours, cost)

## Phase 3: Gantt Chart View (JavaScript)
- Split-pane layout: task list (left) + Gantt bars (right)
- Interactive bars with drag-to-resize dates
- Dependency arrows between bars
- Critical path highlighting in red
- Today line marker
- Zoom controls (day/week/month/quarter)
- Milestone diamonds ◆
- Summary task brackets ▬

## Phase 4: Multiple Views
- Gantt Chart (default, MS Project style)
- Task Table (spreadsheet-like, inline editing)
- Board / Kanban
- Calendar
- Resource Sheet

## Phase 5: Features
- Baseline save/compare
- Resource leveling alerts
- % Complete auto-roll-up
- Indent/Outdent tasks
- Import from MS Project XML/CSV
- Export to PDF/CSV
