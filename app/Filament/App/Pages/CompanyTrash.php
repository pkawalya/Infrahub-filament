<?php

namespace App\Filament\App\Pages;

use App\Models\Asset;
use App\Models\ChangeOrder;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Drawing;
use App\Models\GoodsReceivedNote;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\Quotation;
use App\Models\StockTransfer;
use App\Models\Subcontractor;
use App\Models\Supplier;
use App\Models\Task;
use App\Models\WorkOrder;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class CompanyTrash extends Page
{
    protected static string $view = 'filament.app.pages.company-trash';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-trash';
    protected static ?string $navigationLabel = 'Trash';
    protected static string|\UnitEnum|null $navigationGroup = 'Company';
    protected static ?int $navigationSort = 99;
    protected static ?string $title = 'Company Trash';

    /** Active type filter tab */
    public string $activeType = 'all';

    /** Search query */
    public string $search = '';

    // ─── Model Registry ──────────────────────────────────────────

    /**
     * All model classes that support SoftDeletes and belong to a company.
     * Each entry: [ 'label' => string, 'model' => FQCN, 'labelField' => string, 'icon' => string ]
     */
    public static function modelRegistry(): array
    {
        return [
            'assets'          => ['label' => 'Assets',           'model' => Asset::class,            'labelField' => 'name',          'icon' => 'heroicon-o-cube'],
            'change_orders'   => ['label' => 'Change Orders',    'model' => ChangeOrder::class,      'labelField' => 'title',         'icon' => 'heroicon-o-document-plus'],
            'clients'         => ['label' => 'Clients',          'model' => Client::class,           'labelField' => 'name',          'icon' => 'heroicon-o-user-group'],
            'contracts'       => ['label' => 'Contracts',        'model' => Contract::class,         'labelField' => 'title',         'icon' => 'heroicon-o-document-text'],
            'drawings'        => ['label' => 'Drawings',         'model' => Drawing::class,          'labelField' => 'title',         'icon' => 'heroicon-o-map'],
            'grns'            => ['label' => 'Goods Received',   'model' => GoodsReceivedNote::class,'labelField' => 'grn_number',    'icon' => 'heroicon-o-inbox-arrow-down'],
            'invoices'        => ['label' => 'Invoices',         'model' => Invoice::class,          'labelField' => 'invoice_number','icon' => 'heroicon-o-receipt-percent'],
            'purchase_orders' => ['label' => 'Purchase Orders',  'model' => PurchaseOrder::class,    'labelField' => 'po_number',     'icon' => 'heroicon-o-shopping-cart'],
            'quotations'      => ['label' => 'Quotations',       'model' => Quotation::class,        'labelField' => 'quote_number',  'icon' => 'heroicon-o-clipboard-document-list'],
            'stock_transfers' => ['label' => 'Stock Transfers',  'model' => StockTransfer::class,    'labelField' => 'reference',     'icon' => 'heroicon-o-arrows-right-left'],
            'subcontractors'  => ['label' => 'Subcontractors',   'model' => Subcontractor::class,    'labelField' => 'name',          'icon' => 'heroicon-o-building-storefront'],
            'suppliers'       => ['label' => 'Suppliers',        'model' => Supplier::class,         'labelField' => 'name',          'icon' => 'heroicon-o-truck'],
            'tasks'           => ['label' => 'Tasks',            'model' => Task::class,             'labelField' => 'title',         'icon' => 'heroicon-o-check-square'],
            'work_orders'     => ['label' => 'Work Orders',      'model' => WorkOrder::class,        'labelField' => 'title',         'icon' => 'heroicon-o-wrench-screwdriver'],
        ];
    }

    // ─── Data Loading ─────────────────────────────────────────────

    private function cid(): int
    {
        return auth()->user()->company_id;
    }

    /**
     * Load all (or filtered) trashed records, returning a flat collection of items.
     * Each item has: type, type_key, label, deleted_at, id, icon
     */
    public function getTrashedItems(): Collection
    {
        $cid      = $this->cid();
        $registry = static::modelRegistry();
        $results  = collect();

        foreach ($registry as $typeKey => $meta) {
            if ($this->activeType !== 'all' && $this->activeType !== $typeKey) {
                continue;
            }

            /** @var \Illuminate\Database\Eloquent\Model $model */
            $model      = $meta['model'];
            $labelField = $meta['labelField'];

            $query = $model::onlyTrashed()->where('company_id', $cid);

            if ($this->search !== '') {
                $query->where($labelField, 'like', '%' . $this->search . '%');
            }

            $records = $query->select(['id', $labelField, 'deleted_at'])->get();

            foreach ($records as $record) {
                $results->push([
                    'id'         => $record->id,
                    'type_key'   => $typeKey,
                    'type_label' => $meta['label'],
                    'icon'       => $meta['icon'],
                    'label'      => $record->{$labelField} ?? "#{$record->id}",
                    'deleted_at' => $record->deleted_at,
                ]);
            }
        }

        return $results->sortByDesc('deleted_at')->values();
    }

    /**
     * Get counts per type for the tab badges.
     */
    public function getTypeCounts(): array
    {
        $cid     = $this->cid();
        $counts  = ['all' => 0];

        foreach (static::modelRegistry() as $typeKey => $meta) {
            $count              = $meta['model']::onlyTrashed()->where('company_id', $cid)->count();
            $counts[$typeKey]   = $count;
            $counts['all']     += $count;
        }

        return $counts;
    }

    // ─── Actions ──────────────────────────────────────────────────

    /** Restore a single record */
    public function restore(string $typeKey, int $id): void
    {
        $meta   = static::modelRegistry()[$typeKey] ?? null;
        if (!$meta) return;

        $record = $meta['model']::onlyTrashed()->where('company_id', $this->cid())->find($id);
        if (!$record) return;

        $record->restore();

        Notification::make()
            ->title("{$meta['label']} restored successfully")
            ->success()
            ->send();
    }

    /** Permanently delete a single record */
    public function forceDelete(string $typeKey, int $id): void
    {
        $meta   = static::modelRegistry()[$typeKey] ?? null;
        if (!$meta) return;

        $record = $meta['model']::onlyTrashed()->where('company_id', $this->cid())->find($id);
        if (!$record) return;

        $record->forceDelete();

        Notification::make()
            ->title("{$meta['label']} permanently deleted")
            ->warning()
            ->send();
    }

    /** Restore ALL trashed records for the current company */
    public function restoreAll(): void
    {
        $cid   = $this->cid();
        $total = 0;

        foreach (static::modelRegistry() as $typeKey => $meta) {
            if ($this->activeType !== 'all' && $this->activeType !== $typeKey) {
                continue;
            }
            $count  = $meta['model']::onlyTrashed()->where('company_id', $cid)->count();
            $meta['model']::onlyTrashed()->where('company_id', $cid)->restore();
            $total += $count;
        }

        Notification::make()
            ->title("{$total} record(s) restored")
            ->success()
            ->send();
    }

    /** Permanently delete ALL trashed records for the current company */
    public function emptyTrash(): void
    {
        $cid   = $this->cid();
        $total = 0;

        foreach (static::modelRegistry() as $typeKey => $meta) {
            if ($this->activeType !== 'all' && $this->activeType !== $typeKey) {
                continue;
            }
            $count  = $meta['model']::onlyTrashed()->where('company_id', $cid)->count();
            $meta['model']::onlyTrashed()->where('company_id', $cid)->forceDelete();
            $total += $count;
        }

        Notification::make()
            ->title("{$total} record(s) permanently deleted")
            ->danger()
            ->send();
    }

    // ─── Header Actions ───────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            Action::make('restoreAll')
                ->label('Restore All')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Restore All Trashed Records?')
                ->modalDescription('This will restore all items currently visible in the trash. Items will return to their original location.')
                ->action(fn() => $this->restoreAll()),

            Action::make('emptyTrash')
                ->label('Empty Trash')
                ->icon('heroicon-o-fire')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Permanently Delete All?')
                ->modalDescription('⚠️ This action CANNOT be undone. All trashed records will be permanently removed from the database.')
                ->modalSubmitActionLabel('Yes, empty trash')
                ->action(fn() => $this->emptyTrash()),
        ];
    }

    // ─── Navigation Badge ─────────────────────────────────────────

    public static function getNavigationBadge(): ?string
    {
        if (!auth()->check()) return null;

        $cid   = auth()->user()->company_id;
        $total = 0;

        foreach (static::modelRegistry() as $meta) {
            $total += $meta['model']::onlyTrashed()->where('company_id', $cid)->count();
        }

        return $total > 0 ? (string) $total : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
