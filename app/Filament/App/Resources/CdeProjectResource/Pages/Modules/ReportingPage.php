<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\CdeActivityLog;
use App\Models\Contract;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Task;
use App\Support\CurrencyHelper;
use Carbon\Carbon;

class ReportingPage extends BaseModulePage
{
    protected static string $moduleCode = 'reporting';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Reports';
    protected static ?string $title = 'Reporting & Dashboards';
    protected string $view = 'filament.app.pages.modules.reporting';

    // ── Date Range Filter ──
    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    public string $activeReport = 'summary';

    public function mount(int|string $record): void
    {
        parent::mount($record);
        $this->dateFrom = now()->startOfYear()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updatedDateFrom(): void
    { /* triggers Livewire re-render */
    }
    public function updatedDateTo(): void
    { /* triggers Livewire re-render */
    }
    public function updatedActiveReport(): void
    { /* triggers Livewire re-render */
    }

    private function from(): Carbon
    {
        return Carbon::parse($this->dateFrom ?? now()->startOfYear());
    }
    private function to(): Carbon
    {
        return Carbon::parse($this->dateTo ?? now())->endOfDay();
    }

    // ═══════════════════════════════════════════
    // Stats Cards
    // ═══════════════════════════════════════════
    public function getStats(): array
    {
        $r = $this->record;
        $totalTasks = $r->tasks()->count();
        $doneTasks = $r->tasks()->where('status', 'done')->count();
        $progress = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;
        $boqValue = $r->boqs()->sum('total_value');
        $contractValue = $r->contracts()->sum('original_value');

        return [
            ['label' => 'Progress', 'value' => $progress . '%', 'sub' => $doneTasks . '/' . $totalTasks . ' tasks', 'sub_type' => $progress >= 75 ? 'success' : ($progress >= 40 ? 'warning' : 'neutral'), 'primary' => true, 'icon' => 'heroicon-o-chart-bar'],
            ['label' => 'BOQ Value', 'value' => CurrencyHelper::formatCompact($boqValue), 'full_value' => CurrencyHelper::format($boqValue, 0), 'sub' => $r->boqs()->count() . ' BOQs', 'icon' => 'heroicon-o-currency-dollar', 'icon_color' => '#059669', 'icon_bg' => '#ecfdf5'],
            ['label' => 'Contract Value', 'value' => CurrencyHelper::formatCompact($contractValue), 'full_value' => CurrencyHelper::format($contractValue, 0), 'sub' => $r->contracts()->where('status', 'active')->count() . ' active', 'icon' => 'heroicon-o-document-text', 'icon_color' => '#2563eb', 'icon_bg' => '#eff6ff'],
            ['label' => 'Safety Incidents', 'value' => $r->safetyIncidents()->count(), 'sub' => $r->safetyIncidents()->whereNotIn('status', ['closed', 'resolved'])->count() . ' open', 'sub_type' => $r->safetyIncidents()->whereNotIn('status', ['closed', 'resolved'])->count() > 0 ? 'danger' : 'success', 'icon' => 'heroicon-o-shield-exclamation', 'icon_color' => '#d97706', 'icon_bg' => '#fffbeb'],
        ];
    }

    // ═══════════════════════════════════════════
    // Report: Project Summary
    // ═══════════════════════════════════════════
    public function getProjectSummaryReport(): array
    {
        $r = $this->record;
        $from = $this->from();
        $to = $this->to();

        $taskBreakdown = ['todo' => 0, 'in_progress' => 0, 'review' => 0, 'done' => 0, 'blocked' => 0];
        $tasks = $r->tasks()->selectRaw('status, count(*) as cnt')->groupBy('status')->pluck('cnt', 'status');
        foreach ($tasks as $s => $c) {
            $taskBreakdown[$s] = $c;
        }

        $health = $this->getProjectHealth();
        $milestones = $r->milestones()->orderBy('target_date')->limit(20)->get(['name', 'target_date', 'actual_date', 'status']);
        $moduleSummary = $this->getModuleSummary();

        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();
            $trend[] = [
                'label' => $month->format('M'),
                'tasks_completed' => $r->tasks()->where('status', 'done')->whereBetween('completed_at', [$start, $end])->count(),
                'logs' => $r->dailySiteLogs()->whereBetween('log_date', [$start, $end])->count(),
            ];
        }

        return [
            'task_breakdown' => $taskBreakdown,
            'total_tasks' => array_sum($taskBreakdown),
            'health' => $health,
            'milestones' => $milestones->map(fn($m) => [
                'title' => $m->name,
                'target' => $m->target_date?->format('M d'),
                'actual' => $m->actual_date?->format('M d'),
                'status' => $m->status,
                'is_late' => $m->target_date && !$m->actual_date && $m->target_date->isPast(),
            ])->toArray(),
            'module_summary' => $moduleSummary,
            'trend' => $trend,
        ];
    }

    // ═══════════════════════════════════════════
    // Report: Financial
    // ═══════════════════════════════════════════
    public function getFinancialReport(): array
    {
        $r = $this->record;
        $pid = $r->id;
        $from = $this->from();
        $to = $this->to();

        $invoiced = (float) Invoice::where('cde_project_id', $pid)->whereBetween('issue_date', [$from, $to])->sum('total_amount');
        $received = (float) InvoicePayment::where('cde_project_id', $pid)->whereBetween('payment_date', [$from, $to])->sum('amount');
        $expenses = (float) Expense::where('cde_project_id', $pid)->where('status', '!=', 'rejected')->whereBetween('expense_date', [$from, $to])->sum('amount');
        $outstanding = (float) Invoice::where('cde_project_id', $pid)->whereNotIn('status', ['paid', 'cancelled'])->selectRaw('SUM(total_amount - amount_paid) as total')->value('total');
        $overdue = Invoice::where('cde_project_id', $pid)->whereNotIn('status', ['paid', 'cancelled'])->whereNotNull('due_date')->where('due_date', '<', now())->count();

        // Invoices by status
        $byStatus = Invoice::where('cde_project_id', $pid)->whereBetween('issue_date', [$from, $to])
            ->selectRaw('status, count(*) as cnt, sum(total_amount) as total')
            ->groupBy('status')->get()->map(fn($r) => ['status' => $r->status, 'count' => $r->cnt, 'total' => (float) $r->total])->toArray();

        // Monthly cash flow
        $cashFlow = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $cashFlow[] = [
                'label' => $m->format('M'),
                'invoiced' => (float) Invoice::where('cde_project_id', $pid)->whereYear('issue_date', $m->year)->whereMonth('issue_date', $m->month)->sum('total_amount'),
                'received' => (float) InvoicePayment::where('cde_project_id', $pid)->whereYear('payment_date', $m->year)->whereMonth('payment_date', $m->month)->sum('amount'),
                'expenses' => (float) Expense::where('cde_project_id', $pid)->where('status', '!=', 'rejected')->whereYear('expense_date', $m->year)->whereMonth('expense_date', $m->month)->sum('amount'),
            ];
        }

        // Expense breakdown
        $expenseBreakdown = Expense::where('cde_project_id', $pid)->where('status', '!=', 'rejected')
            ->whereBetween('expense_date', [$from, $to])
            ->selectRaw('category, SUM(amount) as total, COUNT(*) as cnt')
            ->groupBy('category')->orderByDesc('total')->get()
            ->map(fn($r) => ['category' => ucfirst(str_replace('_', ' ', $r->category)), 'total' => (float) $r->total, 'total_fmt' => CurrencyHelper::format($r->total, 0), 'count' => $r->cnt])->toArray();

        return compact('invoiced', 'received', 'expenses', 'outstanding', 'overdue', 'byStatus', 'cashFlow', 'expenseBreakdown');
    }

    // ═══════════════════════════════════════════
    // Report: Tasks
    // ═══════════════════════════════════════════
    public function getTaskReport(): array
    {
        $r = $this->record;
        $from = $this->from();
        $to = $this->to();

        $tasks = $r->tasks()->whereBetween('created_at', [$from, $to])->with('assignee')->get();
        $byStatus = $tasks->groupBy('status')->map->count()->toArray();
        $byPriority = $tasks->groupBy('priority')->map->count()->toArray();
        $byAssignee = $tasks->groupBy(fn($t) => $t->assignee?->name ?? 'Unassigned')
            ->map(fn($group) => ['total' => $group->count(), 'done' => $group->where('status', 'done')->count()])
            ->sortByDesc('total')->take(15)->toArray();

        $overdue = $r->tasks()->where('status', '!=', 'done')
            ->whereNotNull('due_date')->where('due_date', '<', now())
            ->with('assignee')->limit(20)->get()
            ->map(fn($t) => [
                'title' => $t->title,
                'assignee' => $t->assignee?->name ?? '—',
                'due' => $t->due_date?->format('M d, Y'),
                'days_overdue' => $t->due_date ? (int) now()->diffInDays($t->due_date) : 0,
                'priority' => $t->priority ?? 'medium',
            ])->toArray();

        $completedInRange = $r->tasks()->where('status', 'done')
            ->whereBetween('completed_at', [$from, $to])->count();
        $createdInRange = $tasks->count();

        return compact('byStatus', 'byPriority', 'byAssignee', 'overdue', 'completedInRange', 'createdInRange');
    }

    // ═══════════════════════════════════════════
    // Report: Documents
    // ═══════════════════════════════════════════
    public function getDocumentReport(): array
    {
        $r = $this->record;
        $from = $this->from();
        $to = $this->to();

        $docs = $r->documents()->whereBetween('created_at', [$from, $to])->get();
        $byStatus = $docs->groupBy('status')->map->count()->toArray();
        $byDiscipline = $docs->groupBy('discipline')->map->count()->sortDesc()->toArray();
        $bySuitability = $docs->groupBy('suitability_code')->map->count()->toArray();

        $recentUploads = $r->documents()->whereBetween('created_at', [$from, $to])
            ->with('uploader')->latest()->limit(15)->get()
            ->map(fn($d) => [
                'title' => $d->title,
                'doc_number' => $d->document_number,
                'status' => $d->status,
                'suitability' => $d->suitability_code,
                'uploaded_by' => $d->uploader?->name ?? '—',
                'date' => $d->created_at->format('M d, Y'),
                'revision' => $d->revision,
            ])->toArray();

        return [
            'total' => $docs->count(),
            'by_status' => $byStatus,
            'by_discipline' => $byDiscipline,
            'by_suitability' => $bySuitability,
            'recent_uploads' => $recentUploads,
        ];
    }

    // ═══════════════════════════════════════════
    // Report: Contracts
    // ═══════════════════════════════════════════
    public function getContractReport(): array
    {
        $r = $this->record;
        $contracts = $r->contracts()->with('vendor')->get();

        $byStatus = $contracts->groupBy('status')->map->count()->toArray();
        $byType = $contracts->groupBy('type')->map->count()->toArray();
        $totalOriginal = $contracts->sum('original_value');
        $totalRevised = $contracts->sum('revised_value');
        $totalPaid = $contracts->sum('amount_paid');
        $variance = $totalRevised - $totalOriginal;

        $details = $contracts->map(fn($c) => [
            'number' => $c->contract_number,
            'title' => $c->title,
            'vendor' => $c->vendor?->name ?? '—',
            'type' => $c->type,
            'status' => $c->status,
            'original' => CurrencyHelper::format($c->original_value, 0),
            'revised' => CurrencyHelper::format($c->revised_value, 0),
            'paid' => CurrencyHelper::format($c->amount_paid, 0),
            'pct' => $c->revised_value > 0 ? round(($c->amount_paid / $c->revised_value) * 100) : 0,
        ])->toArray();

        return compact('byStatus', 'byType', 'totalOriginal', 'totalRevised', 'totalPaid', 'variance', 'details');
    }

    // ═══════════════════════════════════════════
    // Report: Safety
    // ═══════════════════════════════════════════
    public function getSafetyReport(): array
    {
        $r = $this->record;
        $from = $this->from();
        $to = $this->to();

        $incidents = $r->safetyIncidents()->whereBetween('created_at', [$from, $to])->get();
        $bySeverity = $incidents->groupBy('severity')->map->count()->toArray();
        $byStatus = $incidents->groupBy('status')->map->count()->toArray();
        $byType = $incidents->groupBy('type')->map->count()->toArray();

        $details = $incidents->sortByDesc('created_at')->take(20)->map(fn($i) => [
            'title' => $i->title,
            'type' => $i->type,
            'severity' => $i->severity,
            'status' => $i->status,
            'date' => $i->incident_date?->format('M d, Y') ?? $i->created_at->format('M d, Y'),
            'location' => $i->location ?? '—',
        ])->values()->toArray();

        // Monthly trend
        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $trend[] = [
                'label' => $m->format('M'),
                'count' => $r->safetyIncidents()->whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count(),
            ];
        }

        return compact('bySeverity', 'byStatus', 'byType', 'details', 'trend');
    }

    // ═══════════════════════════════════════════
    // Report: RFIs
    // ═══════════════════════════════════════════
    public function getRfiReport(): array
    {
        $r = $this->record;
        $from = $this->from();
        $to = $this->to();

        $rfis = $r->rfis()->whereBetween('created_at', [$from, $to])->with('assignee')->get();
        $byStatus = $rfis->groupBy('status')->map->count()->toArray();
        $avgResponseDays = $rfis->filter(fn($r) => $r->responded_at)->avg(fn($r) => $r->created_at->diffInDays($r->responded_at));

        $details = $rfis->sortByDesc('created_at')->take(20)->map(fn($r) => [
            'number' => $r->rfi_number ?? '—',
            'subject' => $r->subject,
            'status' => $r->status,
            'assignee' => $r->assignee?->name ?? '—',
            'date' => $r->created_at->format('M d, Y'),
            'response_days' => $r->responded_at ? $r->created_at->diffInDays($r->responded_at) : null,
        ])->values()->toArray();

        return ['total' => $rfis->count(), 'by_status' => $byStatus, 'avg_response_days' => round($avgResponseDays ?? 0, 1), 'details' => $details];
    }

    // ═══════════════════════════════════════════
    // CSV Export
    // ═══════════════════════════════════════════
    public function exportReport(string $reportType): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filename = $this->record->name . '_' . $reportType . '_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($reportType) {
            $handle = fopen('php://output', 'w');

            match ($reportType) {
                'financial' => $this->exportFinancialCsv($handle),
                'tasks' => $this->exportTasksCsv($handle),
                'documents' => $this->exportDocumentsCsv($handle),
                'contracts' => $this->exportContractsCsv($handle),
                'safety' => $this->exportSafetyCsv($handle),
                'rfis' => $this->exportRfisCsv($handle),
                default => $this->exportSummaryCsv($handle),
            };

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function exportFinancialCsv($handle): void
    {
        $r = $this->record;
        $pid = $r->id;
        $from = $this->from();
        $to = $this->to();
        fputcsv($handle, ['Financial Report - ' . $r->name, 'From: ' . $from->format('M d, Y'), 'To: ' . $to->format('M d, Y')]);
        fputcsv($handle, []);

        // Invoices
        fputcsv($handle, ['INVOICES']);
        fputcsv($handle, ['Invoice #', 'Client', 'Status', 'Issue Date', 'Due Date', 'Total', 'Paid', 'Balance']);
        $invoices = Invoice::where('cde_project_id', $pid)->whereBetween('issue_date', [$from, $to])->with('client')->get();
        foreach ($invoices as $inv) {
            fputcsv($handle, [$inv->invoice_number, $inv->client?->name, $inv->status, $inv->issue_date?->format('Y-m-d'), $inv->due_date?->format('Y-m-d'), $inv->total_amount, $inv->amount_paid, $inv->total_amount - $inv->amount_paid]);
        }
        fputcsv($handle, []);

        // Expenses
        fputcsv($handle, ['EXPENSES']);
        fputcsv($handle, ['Title', 'Category', 'Amount', 'Date', 'Status']);
        $expenses = Expense::where('cde_project_id', $pid)->whereBetween('expense_date', [$from, $to])->get();
        foreach ($expenses as $exp) {
            fputcsv($handle, [$exp->title, $exp->category, $exp->amount, $exp->expense_date?->format('Y-m-d'), $exp->status]);
        }
    }

    private function exportTasksCsv($handle): void
    {
        $r = $this->record;
        $from = $this->from();
        $to = $this->to();
        fputcsv($handle, ['Task Report - ' . $r->name, 'From: ' . $from->format('M d, Y'), 'To: ' . $to->format('M d, Y')]);
        fputcsv($handle, []);
        fputcsv($handle, ['Title', 'Status', 'Priority', 'Assignee', 'Due Date', 'Created', 'Completed']);
        $tasks = $r->tasks()->whereBetween('created_at', [$from, $to])->with('assignee')->get();
        foreach ($tasks as $t) {
            fputcsv($handle, [$t->title, $t->status, $t->priority, $t->assignee?->name, $t->due_date?->format('Y-m-d'), $t->created_at->format('Y-m-d'), $t->completed_at?->format('Y-m-d')]);
        }
    }

    private function exportDocumentsCsv($handle): void
    {
        $r = $this->record;
        $from = $this->from();
        $to = $this->to();
        fputcsv($handle, ['Document Report - ' . $r->name, 'From: ' . $from->format('M d, Y'), 'To: ' . $to->format('M d, Y')]);
        fputcsv($handle, []);
        fputcsv($handle, ['Doc #', 'Title', 'Discipline', 'Status', 'Suitability', 'Revision', 'Uploaded By', 'Date']);
        $docs = $r->documents()->whereBetween('created_at', [$from, $to])->with('uploader')->get();
        foreach ($docs as $d) {
            fputcsv($handle, [$d->document_number, $d->title, $d->discipline, $d->status, $d->suitability_code, $d->revision, $d->uploader?->name, $d->created_at->format('Y-m-d')]);
        }
    }

    private function exportContractsCsv($handle): void
    {
        $r = $this->record;
        fputcsv($handle, ['Contract Report - ' . $r->name]);
        fputcsv($handle, []);
        fputcsv($handle, ['Contract #', 'Title', 'Vendor', 'Type', 'Status', 'Original Value', 'Revised Value', 'Paid', '% Paid']);
        $contracts = $r->contracts()->with('vendor')->get();
        foreach ($contracts as $c) {
            fputcsv($handle, [$c->contract_number, $c->title, $c->vendor?->name, $c->type, $c->status, $c->original_value, $c->revised_value, $c->amount_paid, $c->revised_value > 0 ? round(($c->amount_paid / $c->revised_value) * 100) . '%' : '0%']);
        }
    }

    private function exportSafetyCsv($handle): void
    {
        $r = $this->record;
        $from = $this->from();
        $to = $this->to();
        fputcsv($handle, ['Safety Report - ' . $r->name, 'From: ' . $from->format('M d, Y'), 'To: ' . $to->format('M d, Y')]);
        fputcsv($handle, []);
        fputcsv($handle, ['Title', 'Type', 'Severity', 'Status', 'Date', 'Location']);
        $incidents = $r->safetyIncidents()->whereBetween('created_at', [$from, $to])->get();
        foreach ($incidents as $i) {
            fputcsv($handle, [$i->title, $i->type, $i->severity, $i->status, $i->incident_date?->format('Y-m-d') ?? $i->created_at->format('Y-m-d'), $i->location]);
        }
    }

    private function exportRfisCsv($handle): void
    {
        $r = $this->record;
        $from = $this->from();
        $to = $this->to();
        fputcsv($handle, ['RFI Report - ' . $r->name, 'From: ' . $from->format('M d, Y'), 'To: ' . $to->format('M d, Y')]);
        fputcsv($handle, []);
        fputcsv($handle, ['RFI #', 'Subject', 'Status', 'Assignee', 'Created', 'Response Days']);
        $rfis = $r->rfis()->whereBetween('created_at', [$from, $to])->with('assignee')->get();
        foreach ($rfis as $rfi) {
            fputcsv($handle, [$rfi->rfi_number, $rfi->subject, $rfi->status, $rfi->assignee?->name, $rfi->created_at->format('Y-m-d'), $rfi->responded_at ? $rfi->created_at->diffInDays($rfi->responded_at) : '—']);
        }
    }

    private function exportSummaryCsv($handle): void
    {
        $r = $this->record;
        fputcsv($handle, ['Project Summary - ' . $r->name]);
        fputcsv($handle, []);
        $summary = $this->getModuleSummary();
        fputcsv($handle, ['Module', 'Total', 'Open/Active']);
        foreach ($summary as $m) {
            fputcsv($handle, [$m['module'], $m['total'], $m['open'] ?? '—']);
        }
    }

    // ═══════════════════════════════════════════
    // Helpers (kept from original)
    // ═══════════════════════════════════════════
    public function getProjectHealth(): array
    {
        $r = $this->record;
        $totalTasks = $r->tasks()->count();
        $doneTasks = $r->tasks()->where('status', 'done')->count();
        $overdueTasks = $r->tasks()->where('status', '!=', 'done')->whereNotNull('due_date')->where('due_date', '<', now())->count();
        $taskPct = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;

        $overdueRatio = $totalTasks > 0 ? $overdueTasks / $totalTasks : 0;
        $scheduleStatus = $overdueRatio > 0.2 ? 'red' : ($overdueRatio > 0.05 ? 'amber' : 'green');

        $contractOriginal = $r->contracts()->sum('original_value');
        $contractRevised = $r->contracts()->sum('revised_value');
        $costVariance = $contractOriginal > 0 ? (($contractRevised - $contractOriginal) / $contractOriginal) * 100 : 0;
        $costStatus = $costVariance > 10 ? 'red' : ($costVariance > 3 ? 'amber' : 'green');

        $openSnags = $r->snagItems()->whereIn('status', ['open', 'in_progress'])->count();
        $qualityStatus = $openSnags > 20 ? 'red' : ($openSnags > 5 ? 'amber' : 'green');

        $openIncidents = $r->safetyIncidents()->whereNotIn('status', ['closed', 'resolved'])->count();
        $criticalIncidents = $r->safetyIncidents()->whereIn('severity', ['critical', 'fatal'])->whereNotIn('status', ['closed', 'resolved'])->count();
        $safetyStatus = $criticalIncidents > 0 ? 'red' : ($openIncidents > 3 ? 'amber' : 'green');

        return [
            ['dimension' => 'Schedule', 'status' => $scheduleStatus, 'detail' => "$overdueTasks overdue / $totalTasks tasks ({$taskPct}% done)"],
            ['dimension' => 'Cost', 'status' => $costStatus, 'detail' => 'Variance: ' . ($costVariance >= 0 ? '+' : '') . round($costVariance, 1) . '%'],
            ['dimension' => 'Quality', 'status' => $qualityStatus, 'detail' => "$openSnags open snags/defects"],
            ['dimension' => 'Safety', 'status' => $safetyStatus, 'detail' => "$openIncidents open incidents" . ($criticalIncidents > 0 ? " ($criticalIncidents critical)" : '')],
        ];
    }

    public function getModuleSummary(): array
    {
        $r = $this->record;
        return [
            ['module' => 'Tasks', 'icon' => '📋', 'total' => $r->tasks()->count(), 'open' => $r->tasks()->whereNotIn('status', ['done'])->count()],
            ['module' => 'Documents', 'icon' => '📁', 'total' => $r->documents()->count(), 'open' => $r->documents()->where('status', 'draft')->count()],
            ['module' => 'RFIs', 'icon' => '❓', 'total' => $r->rfis()->count(), 'open' => $r->rfis()->whereIn('status', ['open', 'submitted'])->count()],
            ['module' => 'Contracts', 'icon' => '📝', 'total' => $r->contracts()->count(), 'open' => $r->contracts()->where('status', 'active')->count()],
            ['module' => 'Work Orders', 'icon' => '🔧', 'total' => $r->workOrders()->count(), 'open' => $r->workOrders()->whereNotIn('status', ['completed', 'closed', 'cancelled'])->count()],
            ['module' => 'Safety', 'icon' => '🛡️', 'total' => $r->safetyIncidents()->count(), 'open' => $r->safetyIncidents()->whereNotIn('status', ['closed', 'resolved'])->count()],
            ['module' => 'Daily Logs', 'icon' => '📊', 'total' => $r->dailySiteLogs()->count(), 'open' => null],
            ['module' => 'Milestones', 'icon' => '🎯', 'total' => $r->milestones()->count(), 'open' => $r->milestones()->whereIn('status', ['pending', 'in_progress'])->count()],
        ];
    }
}
