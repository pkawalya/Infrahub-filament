<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetMaintenanceLog;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\GoodsReceivedNote;
use App\Models\GrnItem;
use App\Models\MaterialIssuance;
use App\Models\MaterialIssuanceItem;
use App\Models\MaterialRequisition;
use App\Models\MaterialRequisitionItem;
use App\Models\Milestone;
use App\Models\Product;
use App\Models\ProductTracking;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockAdjustment;
use App\Models\StockLevel;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Support\CurrencyHelper;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

use App\Filament\App\Concerns\ExportsTableCsv;
use App\Services\InventoryNotificationService;
use Livewire\Attributes\Url;

class InventoryPage extends BaseModulePage implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms, ExportsTableCsv;

    protected static string $moduleCode = 'inventory';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Inventory';
    protected static ?string $title = 'Inventory & Store Management';
    protected string $view = 'filament.app.pages.modules.inventory';

    // ─── Tab state ──────────────────────────────────────────
    #[Url(as: 'tab')]
    public string $activeInventoryTab = 'products';

    // ─── Modal state ────────────────────────────────────────
    public bool $showProductModal = false;
    public bool $showStoreModal = false;
    public bool $showIssuanceModal = false;
    public bool $showAssetModal = false;
    public bool $showCheckoutModal = false;
    public bool $showMaintenanceModal = false;
    public ?int $editingProductId = null;
    public ?int $editingStoreId = null;
    public ?int $activeAssetId = null;

    // ─── Asset tab state ────────────────────────────────────
    public string $assetSearch = '';
    public string $assetStatusFilter = 'all';
    public string $assetViewMode = 'table';
    public int $assetPage = 1;

    public array $productForm = [
        'name' => '',
        'sku' => '',
        'brand' => '',
        'model_number' => '',
        'serial_number' => '',
        'unit_of_measure' => 'each',
        'cost_price' => 0,
        'selling_price' => 0,
        'condition' => 'new',
        'reorder_level' => 0,
        'location' => '',
        'description' => '',
    ];

    public array $storeForm = [
        'name' => '',
        'code' => '',
        'city' => '',
        'address' => '',
    ];

    public array $issuanceForm = [
        'issued_to' => '',
        'issued_to_name' => '',
        'purpose' => 'site_use',
        'warehouse_id' => '',
        'issue_date' => '',
        'expected_return_date' => '',
        'product_id' => '',
        'quantity' => 1,
        'condition' => 'good',
        'notes' => '',
        'requisition_id' => null,
    ];

    public array $assetForm = [
        'name' => '',
        'product_id' => '',
        'serial_number' => '',
        'condition' => 'new',
        'warehouse_id' => '',
        'purchase_date' => '',
        'purchase_cost' => 0,
        'warranty_expiry' => '',
        'useful_life_years' => 5,
        'current_location' => '',
        'notes' => '',
    ];

    public array $checkoutForm = [
        'assigned_to' => '',
        'assigned_to_name' => '',
        'location' => '',
        'expected_return_date' => '',
        'notes' => '',
    ];

    public array $maintenanceForm = [
        'type' => 'inspection',
        'priority' => 'normal',
        'title' => '',
        'description' => '',
        'cost' => 0,
        'vendor' => '',
        'condition_after' => '',
        'scheduled_date' => '',
        'downtime_hours' => '',
        'meter_reading' => '',
        'next_service_date' => '',
        'parts_used' => '',
    ];

    // ─── PO Builder (invoice-style) ─────────────────────────
    public bool $showPOModal = false;
    public ?int $editingPOId = null;
    public array $poHeader = [
        'po_number' => '',
        'supplier_id' => '',
        'status' => 'draft',
        'order_date' => '',
        'expected_date' => '',
        'tax_amount' => 0,
        'shipping_cost' => 0,
        'notes' => '',
    ];
    public array $poItems = [];

    // ─── Delivery Note Builder ───────────────────────────────
    public bool $showDNModal = false;
    public array $dnHeader = [
        'destination' => '',
        'destination_contact' => '',
        'destination_phone' => '',
        'vehicle_number' => '',
        'driver_name' => '',
        'driver_phone' => '',
        'warehouse_id' => '',
        'material_issuance_id' => '',
        'milestone_id' => '',
        'dispatch_date' => '',
        'notes' => '',
    ];
    public array $dnItems = [];

    // ─── Stock Monitor ──────────────────────────────────────
    public bool $showStockMonitorModal = false;
    public ?int $stockMonitorStoreId = null;

    // ─── Material Requisitions ──────────────────────────────
    public bool $showRequisitionModal = false;
    public ?int $editingRequisitionId = null;
    public array $reqHeader = [
        'warehouse_id' => '',
        'priority' => 'normal',
        'purpose' => '',
        'required_date' => '',
        'notes' => '',
    ];
    public array $reqItems = [];

    public function initNewPO(): void
    {
        $this->editingPOId = null;
        $next = PurchaseOrder::where('cde_project_id', $this->pid())->count() + 1;
        $this->poHeader = [
            'po_number' => 'PO-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT),
            'supplier_id' => '',
            'status' => 'draft',
            'order_date' => now()->format('Y-m-d'),
            'expected_date' => '',
            'tax_amount' => 0,
            'shipping_cost' => 0,
            'notes' => '',
        ];
        $this->poItems = [['product_id' => '', 'description' => '', 'quantity' => 1, 'unit_price' => 0]];
        $this->showPOModal = true;
    }

    public function editPO(int $id): void
    {
        $po = PurchaseOrder::with('items')->find($id);
        if (!$po)
            return;
        $this->editingPOId = $id;
        $this->poHeader = [
            'po_number' => $po->po_number,
            'supplier_id' => $po->supplier_id,
            'status' => $po->status,
            'order_date' => $po->order_date?->format('Y-m-d'),
            'expected_date' => $po->expected_date?->format('Y-m-d'),
            'tax_amount' => $po->tax_amount ?? 0,
            'shipping_cost' => $po->shipping_cost ?? 0,
            'notes' => $po->notes ?? '',
        ];
        $this->poItems = $po->items->map(fn($i) => [
            'product_id' => $i->product_id ?? '',
            'description' => $i->notes ?? '',
            'quantity' => $i->quantity_ordered,
            'unit_price' => $i->unit_price,
        ])->toArray();
        if (empty($this->poItems)) {
            $this->poItems = [['product_id' => '', 'description' => '', 'quantity' => 1, 'unit_price' => 0]];
        }
        $this->showPOModal = true;
    }

    public function addPOItem(): void
    {
        $this->poItems[] = ['product_id' => '', 'description' => '', 'quantity' => 1, 'unit_price' => 0];
    }

    public function removePOItem(int $index): void
    {
        if (count($this->poItems) > 1) {
            array_splice($this->poItems, $index, 1);
            $this->poItems = array_values($this->poItems);
        }
    }

    public function getPOSubtotal(): float
    {
        return collect($this->poItems)->sum(fn($i) => ((float) ($i['quantity'] ?? 0)) * ((float) ($i['unit_price'] ?? 0)));
    }

    public function getPOTotal(): float
    {
        return $this->getPOSubtotal() + ((float) ($this->poHeader['tax_amount'] ?? 0)) + ((float) ($this->poHeader['shipping_cost'] ?? 0));
    }

    public function getSupplierOptions(): array
    {
        return Supplier::where('company_id', $this->cid())->where('is_active', true)->pluck('name', 'id')->toArray();
    }

    public bool $showQuickSupplierModal = false;
    public array $quickSupplierForm = ['name' => '', 'email' => '', 'phone' => '', 'contact_person' => ''];

    public function openQuickSupplier(): void
    {
        $this->quickSupplierForm = ['name' => '', 'email' => '', 'phone' => '', 'contact_person' => ''];
        $this->showQuickSupplierModal = true;
    }

    public function submitQuickSupplier(): void
    {
        $this->validate(['quickSupplierForm.name' => 'required|string|max:255']);
        $s = Supplier::create(array_merge($this->quickSupplierForm, [
            'company_id' => $this->cid(),
            'is_active' => true,
        ]));
        $this->showQuickSupplierModal = false;
        Notification::make()->title("Supplier \"{$s->name}\" created")->success()->send();
    }

    // ── Quick-Add Product ──────────────────────────────
    public bool $showQuickProductModal = false;
    public array $quickProductForm = ['name' => '', 'sku' => '', 'unit_of_measure' => 'each', 'cost_price' => 0];

    public function openQuickProduct(): void
    {
        $this->quickProductForm = ['name' => '', 'sku' => '', 'unit_of_measure' => 'each', 'cost_price' => 0];
        $this->showQuickProductModal = true;
    }

    public function submitQuickProduct(): void
    {
        $this->validate(['quickProductForm.name' => 'required|string|max:255']);
        $p = Product::create(array_merge($this->quickProductForm, [
            'company_id' => $this->cid(),
            'condition' => 'new',
            'selling_price' => 0,
            'reorder_level' => 0,
        ]));
        $this->showQuickProductModal = false;
        Notification::make()->title("Product \"{$p->name}\" created")->success()->send();
    }

    public function submitPO(): void
    {
        $this->validate(['poHeader.po_number' => 'required|string|max:50', 'poItems' => 'required|array|min:1']);
        $subtotal = $this->getPOSubtotal();
        $total = $this->getPOTotal();
        $poData = [
            'company_id' => $this->cid(),
            'cde_project_id' => $this->pid(),
            'po_number' => $this->poHeader['po_number'],
            'supplier_id' => $this->poHeader['supplier_id'] ?: null,
            'status' => $this->poHeader['status'],
            'order_date' => $this->poHeader['order_date'] ?: null,
            'expected_date' => $this->poHeader['expected_date'] ?: null,
            'subtotal' => $subtotal,
            'tax_amount' => $this->poHeader['tax_amount'] ?? 0,
            'shipping_cost' => $this->poHeader['shipping_cost'] ?? 0,
            'total_amount' => $total,
            'notes' => $this->poHeader['notes'] ?: null,
            'created_by' => auth()->id(),
        ];
        if ($this->editingPOId) {
            $po = PurchaseOrder::find($this->editingPOId);
            $po->update($poData);
            $po->items()->delete();
        } else {
            $po = PurchaseOrder::create($poData);
        }
        foreach ($this->poItems as $item) {
            if (empty($item['description']) && empty($item['product_id']))
                continue;
            $lineTotal = round(((float) ($item['quantity'] ?? 0)) * ((float) ($item['unit_price'] ?? 0)), 2);
            $po->items()->create([
                'product_id' => $item['product_id'] ?: null,
                'notes' => $item['description'] ?: null,
                'quantity_ordered' => $item['quantity'],
                'quantity_received' => 0,
                'unit_price' => $item['unit_price'],
                'total_price' => $lineTotal,
            ]);
            // Track product lifecycle
            if ($item['product_id'] && !$this->editingPOId) {
                ProductTracking::create([
                    'company_id' => $this->cid(),
                    'cde_project_id' => $this->pid(),
                    'product_id' => $item['product_id'],
                    'stage' => 'ordered',
                    'purchase_order_id' => $po->id,
                    'quantity' => $item['quantity'],
                    'notes' => 'Ordered via PO ' . $this->poHeader['po_number'],
                    'recorded_by' => auth()->id(),
                ]);
            }
        }
        $this->showPOModal = false;
        $label = $this->editingPOId ? 'updated' : 'created';
        Notification::make()->title("PO {$this->poHeader['po_number']} {$label}")->success()->send();

        // ── Stakeholder notifications ──────────────────────────────────────────
        if (!$this->editingPOId) {
            try {
                $po->load(['supplier', 'creator']);
                app(InventoryNotificationService::class)->poCreated($po, auth()->user());
            } catch (\Throwable) {
            }
        }
        // ──────────────────────────────────────────────────────────────────────
    }

    // ─── GRN Builder ─────────────────────────────────────────
    public bool $showGRNModal = false;
    public array $grnHeader = [
        'purchase_order_id' => '',
        'warehouse_id' => '',
        'delivery_note_ref' => '',
        'received_date' => '',
        'notes' => '',
    ];
    public array $grnItems = [];

    public function initNewGRN(?int $poId = null): void
    {
        $next = GoodsReceivedNote::where('company_id', $this->cid())->count() + 1;
        $this->grnHeader = [
            'purchase_order_id' => $poId ?? '',
            'warehouse_id' => '',
            'delivery_note_ref' => '',
            'received_date' => now()->format('Y-m-d'),
            'notes' => '',
        ];
        // Auto-fill from PO items
        if ($poId) {
            $po = PurchaseOrder::with('items.product')->find($poId);
            $this->grnHeader['warehouse_id'] = $po->warehouse_id ?? '';
            $this->grnItems = $po->items->map(fn($i) => [
                'po_item_id' => $i->id,
                'product_id' => $i->product_id ?? '',
                'description' => $i->notes ?? ($i->product?->name ?? ''),
                'qty_expected' => $i->quantity_ordered - $i->quantity_received,
                'qty_received' => $i->quantity_ordered - $i->quantity_received,
                'qty_rejected' => 0,
                'condition' => 'good',
                'rejection_reason' => '',
            ])->filter(fn($i) => $i['qty_expected'] > 0)->values()->toArray();
        }
        if (empty($this->grnItems)) {
            $this->grnItems = [['po_item_id' => '', 'product_id' => '', 'description' => '', 'qty_expected' => 0, 'qty_received' => 0, 'qty_rejected' => 0, 'condition' => 'good', 'rejection_reason' => '']];
        }
        $this->showGRNModal = true;
    }

    public function addGRNItem(): void
    {
        $this->grnItems[] = ['po_item_id' => '', 'product_id' => '', 'description' => '', 'qty_expected' => 0, 'qty_received' => 0, 'qty_rejected' => 0, 'condition' => 'good', 'rejection_reason' => ''];
    }

    public function removeGRNItem(int $index): void
    {
        if (count($this->grnItems) > 1) {
            array_splice($this->grnItems, $index, 1);
            $this->grnItems = array_values($this->grnItems);
        }
    }

    public function submitGRN(): void
    {
        $next = GoodsReceivedNote::where('company_id', $this->cid())->count() + 1;
        $grn = GoodsReceivedNote::create([
            'company_id' => $this->cid(),
            'cde_project_id' => $this->pid(),
            'grn_number' => 'GRN-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT),
            'purchase_order_id' => $this->grnHeader['purchase_order_id'] ?: null,
            'supplier_id' => $this->grnHeader['purchase_order_id'] ? PurchaseOrder::find($this->grnHeader['purchase_order_id'])?->supplier_id : null,
            'warehouse_id' => $this->grnHeader['warehouse_id'] ?: null,
            'status' => 'accepted',
            'received_date' => $this->grnHeader['received_date'] ?: now(),
            'delivery_note_ref' => $this->grnHeader['delivery_note_ref'] ?: null,
            'notes' => $this->grnHeader['notes'] ?: null,
            'received_by' => auth()->id(),
        ]);
        foreach ($this->grnItems as $item) {
            $qtyAccepted = max(0, ((float) ($item['qty_received'] ?? 0)) - ((float) ($item['qty_rejected'] ?? 0)));
            $grn->items()->create([
                'purchase_order_item_id' => $item['po_item_id'] ?: null,
                'product_id' => $item['product_id'] ?: null,
                'description' => $item['description'] ?: null,
                'quantity_expected' => $item['qty_expected'] ?? 0,
                'quantity_received' => $item['qty_received'] ?? 0,
                'quantity_accepted' => $qtyAccepted,
                'quantity_rejected' => $item['qty_rejected'] ?? 0,
                'condition' => $item['condition'] ?? 'good',
                'rejection_reason' => $item['rejection_reason'] ?: null,
            ]);
            // Update PO item received qty
            if ($item['po_item_id']) {
                $poItem = PurchaseOrderItem::find($item['po_item_id']);
                if ($poItem) {
                    $poItem->increment('quantity_received', $qtyAccepted);
                }
            }
            // Update stock level
            if ($item['product_id'] && $this->grnHeader['warehouse_id'] && $qtyAccepted > 0) {
                $stock = StockLevel::firstOrCreate(
                    ['product_id' => $item['product_id'], 'warehouse_id' => $this->grnHeader['warehouse_id']],
                    ['quantity_on_hand' => 0, 'quantity_reserved' => 0, 'quantity_available' => 0]
                );
                $stock->increment('quantity_on_hand', $qtyAccepted);
                $stock->increment('quantity_available', $qtyAccepted);
            }
            // Track product lifecycle
            if ($item['product_id'] && $qtyAccepted > 0) {
                ProductTracking::create([
                    'company_id' => $this->cid(),
                    'cde_project_id' => $this->pid(),
                    'product_id' => $item['product_id'],
                    'stage' => 'received',
                    'purchase_order_id' => $this->grnHeader['purchase_order_id'] ?: null,
                    'quantity' => $qtyAccepted,
                    'location' => Warehouse::find($this->grnHeader['warehouse_id'])?->name,
                    'notes' => 'Received via GRN ' . $grn->grn_number,
                    'recorded_by' => auth()->id(),
                ]);
            }
        }
        // Update PO status
        if ($this->grnHeader['purchase_order_id']) {
            $po = PurchaseOrder::with('items')->find($this->grnHeader['purchase_order_id']);
            if ($po) {
                $allReceived = $po->items->every(fn($i) => $i->quantity_received >= $i->quantity_ordered);
                $anyReceived = $po->items->contains(fn($i) => $i->quantity_received > 0);
                $po->update(['status' => $allReceived ? 'received' : ($anyReceived ? 'partially_received' : $po->status)]);
            }
        }
        $this->showGRNModal = false;
        Notification::make()->title("GRN {$grn->grn_number} created")->success()->send();

        // ── Stakeholder notifications ──────────────────────────────────────────
        try {
            $grn->load(['warehouse', 'purchaseOrder.creator', 'items']);
            app(InventoryNotificationService::class)->grnCreated($grn, auth()->user());
        } catch (\Throwable) {
        }
        // ──────────────────────────────────────────────────────────────────────
    }

    // ─── Stock Transfer Builder ──────────────────────────────
    public bool $showTransferModal = false;
    public array $transferHeader = [
        'from_warehouse_id' => '',
        'to_warehouse_id' => '',
        'priority' => 'normal',
        'reason' => '',
        'notes' => '',
    ];
    public array $transferItems = [];

    public function initNewTransfer(): void
    {
        $this->transferHeader = ['from_warehouse_id' => '', 'to_warehouse_id' => '', 'priority' => 'normal', 'reason' => '', 'notes' => ''];
        $this->transferItems = [['product_id' => '', 'quantity' => 1]];
        $this->showTransferModal = true;
    }

    public function addTransferItem(): void
    {
        $this->transferItems[] = ['product_id' => '', 'quantity' => 1];
    }

    public function removeTransferItem(int $index): void
    {
        if (count($this->transferItems) > 1) {
            array_splice($this->transferItems, $index, 1);
            $this->transferItems = array_values($this->transferItems);
        }
    }

    public function submitTransfer(): void
    {
        $this->validate([
            'transferHeader.from_warehouse_id' => 'required',
            'transferHeader.to_warehouse_id' => 'required',
            'transferItems' => 'required|array|min:1',
        ]);
        $next = StockTransfer::where('company_id', $this->cid())->count() + 1;
        $transfer = StockTransfer::create([
            'company_id' => $this->cid(),
            'cde_project_id' => $this->pid(),
            'transfer_number' => 'TRF-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT),
            'from_warehouse_id' => $this->transferHeader['from_warehouse_id'],
            'to_warehouse_id' => $this->transferHeader['to_warehouse_id'],
            'status' => 'received',
            'priority' => $this->transferHeader['priority'],
            'transfer_date' => now(),
            'requested_date' => now(),
            'received_date' => now(),
            'reason' => $this->transferHeader['reason'] ?: null,
            'notes' => $this->transferHeader['notes'] ?: null,
            'created_by' => auth()->id(),
            'requested_by' => auth()->id(),
            'received_by' => auth()->id(),
        ]);
        foreach ($this->transferItems as $item) {
            if (empty($item['product_id']))
                continue;
            $qty = (float) ($item['quantity'] ?? 0);
            $transfer->items()->create([
                'product_id' => $item['product_id'],
                'quantity_requested' => $qty,
                'quantity_shipped' => $qty,
                'quantity_received' => $qty,
            ]);
            // Deduct from source
            $fromStock = StockLevel::where('product_id', $item['product_id'])
                ->where('warehouse_id', $this->transferHeader['from_warehouse_id'])->first();
            if ($fromStock) {
                $fromStock->decrement('quantity_on_hand', min($qty, $fromStock->quantity_on_hand));
                $fromStock->decrement('quantity_available', min($qty, $fromStock->quantity_available));
            }
            // Add to destination
            $toStock = StockLevel::firstOrCreate(
                ['product_id' => $item['product_id'], 'warehouse_id' => $this->transferHeader['to_warehouse_id']],
                ['quantity_on_hand' => 0, 'quantity_reserved' => 0, 'quantity_available' => 0]
            );
            $toStock->increment('quantity_on_hand', $qty);
            $toStock->increment('quantity_available', $qty);
        }
        $this->showTransferModal = false;
        Notification::make()->title("Transfer {$transfer->transfer_number} completed")->success()->send();

        // ── Stakeholder notifications ──────────────────────────────────────────
        try {
            $transfer->load(['fromWarehouse', 'toWarehouse', 'items']);
            app(InventoryNotificationService::class)->stockTransferred($transfer, auth()->user());
        } catch (\Throwable) {
        }
        // ──────────────────────────────────────────────────────────────────────
    }

    // ─── Stock Adjustment ────────────────────────────────────
    public bool $showAdjustmentModal = false;
    public array $adjustmentForm = [
        'warehouse_id' => '',
        'product_id' => '',
        'type' => 'count',
        'new_quantity' => 0,
        'reason' => '',
        'notes' => '',
    ];

    public function initNewAdjustment(): void
    {
        $this->adjustmentForm = ['warehouse_id' => '', 'product_id' => '', 'type' => 'count', 'new_quantity' => 0, 'reason' => '', 'notes' => ''];
        $this->showAdjustmentModal = true;
    }

    public function submitAdjustment(): void
    {
        $this->validate([
            'adjustmentForm.warehouse_id' => 'required',
            'adjustmentForm.product_id' => 'required',
            'adjustmentForm.type' => 'required',
            'adjustmentForm.reason' => 'required|string|max:255',
        ]);
        $stock = StockLevel::firstOrCreate(
            ['product_id' => $this->adjustmentForm['product_id'], 'warehouse_id' => $this->adjustmentForm['warehouse_id']],
            ['quantity_on_hand' => 0, 'quantity_reserved' => 0, 'quantity_available' => 0]
        );
        $qtyBefore = $stock->quantity_on_hand;
        $qtyAfter = (float) $this->adjustmentForm['new_quantity'];
        $change = $qtyAfter - $qtyBefore;
        $next = StockAdjustment::where('company_id', $this->cid())->count() + 1;
        StockAdjustment::create([
            'company_id' => $this->cid(),
            'cde_project_id' => $this->pid(),
            'adjustment_number' => 'ADJ-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT),
            'warehouse_id' => $this->adjustmentForm['warehouse_id'],
            'product_id' => $this->adjustmentForm['product_id'],
            'type' => $this->adjustmentForm['type'],
            'quantity_before' => $qtyBefore,
            'quantity_after' => $qtyAfter,
            'quantity_change' => $change,
            'reason' => $this->adjustmentForm['reason'],
            'notes' => $this->adjustmentForm['notes'] ?: null,
            'performed_by' => auth()->id(),
        ]);
        $stock->update(['quantity_on_hand' => $qtyAfter, 'quantity_available' => max(0, $qtyAfter - $stock->quantity_reserved)]);
        $this->showAdjustmentModal = false;
        $dir = $change >= 0 ? '+' : '';
        Notification::make()->title("Stock adjusted ({$dir}{$change})")->success()->send();

        // ── Stakeholder notification──────────────────────────────────────────
        try {
            $adj = StockAdjustment::where('company_id', $this->cid())->latest()->first();
            if ($adj) {
                $adj->load(['product', 'warehouse']);
                app(InventoryNotificationService::class)->stockAdjusted($adj, auth()->user());
            }
        } catch (\Throwable) {
        }
        // ──────────────────────────────────────────────────────────────────────
    }

    // ─── Data Methods for New Tabs ───────────────────────────
    public function getGRNs()
    {
        return GoodsReceivedNote::where('company_id', $this->cid())
            ->where('cde_project_id', $this->pid())
            ->with(['purchaseOrder', 'warehouse', 'receivedBy', 'items'])
            ->latest()->get();
    }

    public function getTransfers()
    {
        return StockTransfer::where('company_id', $this->cid())
            ->with(['fromWarehouse', 'toWarehouse', 'creator', 'items'])
            ->latest()->get();
    }

    public function getAdjustments()
    {
        return StockAdjustment::where('company_id', $this->cid())
            ->with(['warehouse', 'product', 'performedBy'])
            ->latest()->limit(50)->get();
    }

    public function getReorderAlerts()
    {
        return Product::where('company_id', $this->cid())
            ->where('is_active', true)
            ->whereHas('stockLevels', fn($q) => $q->whereColumn('quantity_on_hand', '<=', 'products.reorder_level'))
            ->with('stockLevels')
            ->get();
    }

    public function getPOPipeline(): array
    {
        $base = PurchaseOrder::where('cde_project_id', $this->pid());
        return [
            ['label' => 'Draft', 'count' => (clone $base)->where('status', 'draft')->count(), 'bg' => '#f1f5f9', 'color' => '#475569'],
            ['label' => 'Sent', 'count' => (clone $base)->where('status', 'sent')->count(), 'bg' => '#dbeafe', 'color' => '#2563eb'],
            ['label' => 'Partial', 'count' => (clone $base)->where('status', 'partially_received')->count(), 'bg' => '#fef3c7', 'color' => '#d97706'],
            ['label' => 'Received', 'count' => (clone $base)->where('status', 'received')->count(), 'bg' => '#dcfce7', 'color' => '#16a34a'],
            ['label' => 'Cancelled', 'count' => (clone $base)->where('status', 'cancelled')->count(), 'bg' => '#fef2f2', 'color' => '#ef4444'],
        ];
    }

    public function getAvailablePOs()
    {
        return PurchaseOrder::where('cde_project_id', $this->pid())
            ->whereIn('status', ['approved', 'ordered', 'partially_received'])
            ->pluck('po_number', 'id')->toArray();
    }

    // ─── Delivery Notes ─────────────────────────────────────
    public function initNewDN(): void
    {
        $next = DeliveryNote::where('cde_project_id', $this->pid())->count() + 1;
        $this->dnHeader = [
            'destination' => '',
            'destination_contact' => '',
            'destination_phone' => '',
            'vehicle_number' => '',
            'driver_name' => '',
            'driver_phone' => '',
            'warehouse_id' => '',
            'material_issuance_id' => '',
            'milestone_id' => '',
            'dispatch_date' => now()->format('Y-m-d'),
            'notes' => '',
        ];
        $this->dnItems = [['product_id' => '', 'description' => '', 'quantity_dispatched' => 1, 'unit' => 'each']];
        $this->showDNModal = true;
    }

    public function addDNItem(): void
    {
        $this->dnItems[] = ['product_id' => '', 'description' => '', 'quantity_dispatched' => 1, 'unit' => 'each'];
    }

    public function removeDNItem(int $index): void
    {
        if (count($this->dnItems) > 1) {
            array_splice($this->dnItems, $index, 1);
            $this->dnItems = array_values($this->dnItems);
        }
    }

    public function submitDN(): void
    {
        $next = DeliveryNote::where('cde_project_id', $this->pid())->count() + 1;
        $dn = DeliveryNote::create([
            'company_id' => $this->cid(),
            'cde_project_id' => $this->pid(),
            'dn_number' => 'DN-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT),
            'destination' => $this->dnHeader['destination'] ?: null,
            'destination_contact' => $this->dnHeader['destination_contact'] ?: null,
            'destination_phone' => $this->dnHeader['destination_phone'] ?: null,
            'vehicle_number' => $this->dnHeader['vehicle_number'] ?: null,
            'driver_name' => $this->dnHeader['driver_name'] ?: null,
            'driver_phone' => $this->dnHeader['driver_phone'] ?: null,
            'warehouse_id' => $this->dnHeader['warehouse_id'] ?: null,
            'material_issuance_id' => $this->dnHeader['material_issuance_id'] ?: null,
            'milestone_id' => $this->dnHeader['milestone_id'] ?: null,
            'dispatch_date' => $this->dnHeader['dispatch_date'] ?: null,
            'notes' => $this->dnHeader['notes'] ?: null,
            'status' => 'dispatched',
            'dispatched_by' => auth()->id(),
        ]);

        foreach ($this->dnItems as $item) {
            if (empty($item['description']) && empty($item['product_id']))
                continue;
            $dn->items()->create([
                'product_id' => $item['product_id'] ?: null,
                'description' => $item['description'] ?: (Product::find($item['product_id'])?->name ?? ''),
                'unit' => $item['unit'] ?? 'each',
                'quantity_dispatched' => $item['quantity_dispatched'] ?? 0,
                'quantity_received' => 0,
            ]);

            // Track the product lifecycle
            if ($item['product_id']) {
                ProductTracking::create([
                    'company_id' => $this->cid(),
                    'cde_project_id' => $this->pid(),
                    'product_id' => $item['product_id'],
                    'stage' => 'in_transit',
                    'delivery_note_id' => $dn->id,
                    'milestone_id' => $this->dnHeader['milestone_id'] ?: null,
                    'quantity' => $item['quantity_dispatched'] ?? 0,
                    'location' => $this->dnHeader['destination'],
                    'recorded_by' => auth()->id(),
                ]);
            }
        }

        $this->showDNModal = false;
        Notification::make()->title("Delivery Note {$dn->dn_number} created")->success()->send();
    }

    public function markDNDelivered(int $id): void
    {
        $dn = DeliveryNote::with('items')->find($id);
        if (!$dn)
            return;

        foreach ($dn->items as $item) {
            $item->update(['quantity_received' => $item->quantity_dispatched, 'condition' => 'good']);

            if ($item->product_id) {
                ProductTracking::create([
                    'company_id' => $this->cid(),
                    'cde_project_id' => $this->pid(),
                    'product_id' => $item->product_id,
                    'stage' => 'delivered',
                    'delivery_note_id' => $dn->id,
                    'milestone_id' => $dn->milestone_id,
                    'quantity' => $item->quantity_dispatched,
                    'location' => $dn->destination,
                    'recorded_by' => auth()->id(),
                ]);
            }
        }

        $dn->update([
            'status' => 'delivered',
            'delivery_date' => now(),
            'received_by_user' => auth()->id(),
        ]);

        Notification::make()->title("DN {$dn->dn_number} marked as delivered")->success()->send();
    }

    public function getDeliveryNotes()
    {
        return DeliveryNote::where('cde_project_id', $this->pid())
            ->with(['items.product', 'warehouse', 'milestone', 'dispatcher'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function getMilestoneOptions(): array
    {
        return Milestone::where('cde_project_id', $this->pid())
            ->pluck('name', 'id')->toArray();
    }

    public function getIssuanceOptions(): array
    {
        return MaterialIssuance::where('cde_project_id', $this->pid())
            ->whereIn('status', ['draft', 'issued'])
            ->pluck('issuance_number', 'id')->toArray();
    }

    public function getApprovedRequisitionOptions(): array
    {
        return MaterialRequisition::where('cde_project_id', $this->pid())
            ->where('company_id', $this->cid())
            ->whereIn('status', ['approved', 'partially_issued'])
            ->get()
            ->mapWithKeys(fn($r) => [$r->id => $r->requisition_number . ' — ' . ($r->requester?->name ?? 'Unknown') . ' (' . ucfirst(str_replace('_', ' ', $r->status)) . ')'])
            ->toArray();
    }

    // ─── Stock Monitor (comprehensive per-store) ────────────
    public function openStockMonitor(int $warehouseId): void
    {
        $this->stockMonitorStoreId = $warehouseId;
        $this->showStockMonitorModal = true;
    }

    public function getStockMonitorData(): array
    {
        if (!$this->stockMonitorStoreId)
            return [];

        $warehouse = Warehouse::find($this->stockMonitorStoreId);
        if (!$warehouse)
            return [];

        $levels = StockLevel::where('warehouse_id', $this->stockMonitorStoreId)
            ->with('product')
            ->get();

        $totalItems = $levels->count();
        $totalOnHand = $levels->sum('quantity_on_hand');
        $totalReserved = $levels->sum('quantity_reserved');
        $totalAvailable = $levels->sum('quantity_available');
        $totalValue = $levels->sum(fn($sl) => $sl->quantity_on_hand * ($sl->product->cost_price ?? 0));

        $lowStock = $levels->filter(
            fn($sl) =>
            $sl->product && $sl->product->reorder_level > 0 && $sl->quantity_on_hand <= $sl->product->reorder_level
        );

        $outOfStock = $levels->filter(fn($sl) => $sl->quantity_on_hand <= 0);

        return [
            'warehouse' => $warehouse,
            'summary' => [
                'total_items' => $totalItems,
                'total_on_hand' => $totalOnHand,
                'total_reserved' => $totalReserved,
                'total_available' => $totalAvailable,
                'total_value' => $totalValue,
                'low_stock_count' => $lowStock->count(),
                'out_of_stock_count' => $outOfStock->count(),
            ],
            'items' => $levels->map(fn($sl) => [
                'id' => $sl->id,
                'product_id' => $sl->product_id,
                'product_name' => $sl->product->name ?? '—',
                'sku' => $sl->product->sku ?? '—',
                'unit' => $sl->product->unit_of_measure ?? 'each',
                'on_hand' => $sl->quantity_on_hand,
                'reserved' => $sl->quantity_reserved,
                'available' => $sl->quantity_available,
                'reorder_level' => $sl->product->reorder_level ?? 0,
                'cost_price' => $sl->product->cost_price ?? 0,
                'stock_value' => $sl->quantity_on_hand * ($sl->product->cost_price ?? 0),
                'bin_location' => $sl->bin_location ?? '—',
                'is_low' => $sl->product && $sl->product->reorder_level > 0 && $sl->quantity_on_hand <= $sl->product->reorder_level,
                'is_out' => $sl->quantity_on_hand <= 0,
            ])->sortBy('product_name')->values()->toArray(),
            'low_stock' => $lowStock->map(fn($sl) => [
                'product_name' => $sl->product->name ?? '—',
                'on_hand' => $sl->quantity_on_hand,
                'reorder_level' => $sl->product->reorder_level ?? 0,
                'deficit' => ($sl->product->reorder_level ?? 0) - $sl->quantity_on_hand,
            ])->values()->toArray(),
        ];
    }

    public function getAllStoresStockSummary(): array
    {
        $warehouses = Warehouse::where('company_id', $this->cid())
            ->where('is_active', true)
            ->with(['stockLevels.product'])
            ->get();

        return $warehouses->map(fn($wh) => [
            'id' => $wh->id,
            'name' => $wh->name,
            'code' => $wh->code,
            'total_products' => $wh->stockLevels->count(),
            'total_quantity' => $wh->stockLevels->sum('quantity_on_hand'),
            'total_value' => $wh->stockLevels->sum(fn($sl) => $sl->quantity_on_hand * ($sl->product->cost_price ?? 0)),
            'low_stock' => $wh->stockLevels->filter(
                fn($sl) =>
                $sl->product && $sl->product->reorder_level > 0 && $sl->quantity_on_hand <= $sl->product->reorder_level
            )->count(),
            'out_of_stock' => $wh->stockLevels->filter(fn($sl) => $sl->quantity_on_hand <= 0)->count(),
        ])->toArray();
    }

    // ─── Product Tracking (lifecycle timeline) ──────────────
    public function getProductTrackingTimeline(int $productId): array
    {
        return ProductTracking::where('product_id', $productId)
            ->where('cde_project_id', $this->pid())
            ->with(['milestone', 'task', 'purchaseOrder', 'deliveryNote', 'issuance', 'recorder'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'stage' => $t->stage,
                'stage_label' => ProductTracking::$stages[$t->stage] ?? $t->stage,
                'stage_color' => ProductTracking::$stageColors[$t->stage] ?? '#6b7280',
                'quantity' => $t->quantity,
                'location' => $t->location,
                'milestone' => $t->milestone?->name,
                'task' => $t->task?->name,
                'po' => $t->purchaseOrder?->po_number,
                'dn' => $t->deliveryNote?->dn_number,
                'issuance' => $t->issuance?->issuance_number,
                'notes' => $t->notes,
                'recorder' => $t->recorder?->name,
                'date' => $t->created_at->format('M d, Y H:i'),
            ])->toArray();
    }

    public function getProductTrackingSummary(): array
    {
        $products = Product::where('company_id', $this->cid())
            ->whereHas('tracking', fn($q) => $q->where('cde_project_id', $this->pid()))
            ->with(['tracking' => fn($q) => $q->where('cde_project_id', $this->pid())->latest()])
            ->get();

        return $products->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'sku' => $p->sku,
            'latest_stage' => $p->tracking->first()?->stage ?? 'unknown',
            'latest_stage_label' => ProductTracking::$stages[$p->tracking->first()?->stage ?? ''] ?? 'Unknown',
            'latest_stage_color' => ProductTracking::$stageColors[$p->tracking->first()?->stage ?? ''] ?? '#6b7280',
            'total_events' => $p->tracking->count(),
            'latest_location' => $p->tracking->first()?->location ?? '—',
            'latest_date' => $p->tracking->first()?->created_at?->format('M d, Y'),
        ])->toArray();
    }

    // ─── Helpers ────────────────────────────────────────────
    private function pid(): int
    {
        return $this->record->id;
    }
    private function cid(): int
    {
        return $this->record->company_id;
    }

    public function getStats(): array
    {
        $cid = $this->cid();
        $totalProducts = Product::where('company_id', $cid)->count();
        $lowStock = Product::where('company_id', $cid)
            ->whereHas('stockLevels', fn($q) => $q->whereColumn('quantity_on_hand', '<=', 'products.reorder_level'))
            ->count();
        $totalStores = Warehouse::where('company_id', $cid)->where('is_active', true)->count();
        $totalAssets = Asset::where('company_id', $cid)->count();
        $assignedAssets = Asset::where('company_id', $cid)->where('status', 'assigned')->count();

        return [
            [
                'label' => 'Products',
                'value' => $totalProducts,
                'sub' => $lowStock . ' low stock',
                'sub_type' => $lowStock > 0 ? 'warning' : 'success',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>',
            ],
            [
                'label' => 'Assets',
                'value' => $totalAssets,
                'sub' => $assignedAssets . ' assigned',
                'sub_type' => 'info',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#7c3aed" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" /></svg>',
                'icon_bg' => '#f5f3ff',
            ],
            [
                'label' => 'Stores',
                'value' => $totalStores,
                'sub' => 'Active locations',
                'sub_type' => 'neutral',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#3b82f6" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.15c0 .415.336.75.75.75z" /></svg>',
                'icon_bg' => '#eff6ff',
            ],
            [
                'label' => 'Active POs',
                'value' => PurchaseOrder::where('cde_project_id', $this->pid())->whereNotIn('status', ['received', 'cancelled'])->count(),
                'sub' => 'In procurement',
                'sub_type' => 'neutral',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#6366f1" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>',
                'icon_bg' => '#eef2ff',
            ],
        ];
    }

    // ─── Data Getters ───────────────────────────────────────
    public function getStores()
    {
        return Warehouse::where('company_id', $this->cid())->with(['manager', 'stockLevels'])->orderBy('name')->get();
    }

    public function getIssuances()
    {
        return MaterialIssuance::where('company_id', $this->cid())
            ->with(['issuedTo', 'warehouse', 'items'])
            ->orderByDesc('created_at')->get();
    }

    public function getAssets()
    {
        $query = Asset::where('company_id', $this->cid())
            ->with(['product', 'currentHolder', 'warehouse']);

        // Search
        if ($this->assetSearch) {
            $s = '%' . $this->assetSearch . '%';
            $query->where(function ($q) use ($s) {
                $q->where('display_name', 'like', $s)
                    ->orWhere('asset_tag', 'like', $s)
                    ->orWhere('serial_number', 'like', $s)
                    ->orWhereHas('product', fn($pq) => $pq->where('name', 'like', $s));
            });
        }

        // Status filter
        if ($this->assetStatusFilter !== 'all') {
            $query->where('status', $this->assetStatusFilter);
        }

        return $query->orderByDesc('created_at')->paginate(25, ['*'], 'assetPage', $this->assetPage);
    }

    public function getAssetStats(): array
    {
        $counts = Asset::where('company_id', $this->cid())
            ->selectRaw("status, COUNT(*) as cnt")
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();
        $counts['all'] = array_sum($counts);
        return $counts;
    }

    public function updatedAssetSearch(): void
    {
        $this->assetPage = 1;
    }
    public function updatedAssetStatusFilter(): void
    {
        $this->assetPage = 1;
    }
    public function gotoAssetPage(int $page): void
    {
        $this->assetPage = $page;
    }

    public function getTeamOptions(): array
    {
        return User::where('company_id', $this->cid())->pluck('name', 'id')->toArray();
    }

    public function getStoreOptions(): array
    {
        return Warehouse::where('company_id', $this->cid())->where('is_active', true)->pluck('name', 'id')->toArray();
    }

    public function getProductOptions(): array
    {
        return Product::where('company_id', $this->cid())->where('is_active', true)->pluck('name', 'id')->toArray();
    }

    public function getWarehouseOptions(): array
    {
        return Warehouse::where('company_id', $this->cid())->where('is_active', true)->pluck('name', 'id')->toArray();
    }

    public bool $showQuickWarehouseModal = false;
    public array $quickWarehouseForm = ['name' => '', 'code' => '', 'city' => '', 'address' => ''];

    public function openQuickWarehouse(): void
    {
        $this->quickWarehouseForm = ['name' => '', 'code' => '', 'city' => '', 'address' => ''];
        $this->showQuickWarehouseModal = true;
    }

    public function submitQuickWarehouse(): void
    {
        $this->validate(['quickWarehouseForm.name' => 'required|string|max:255']);
        $w = Warehouse::create(array_merge($this->quickWarehouseForm, [
            'company_id' => $this->cid(),
            'is_active' => true,
        ]));
        $this->showQuickWarehouseModal = false;
        Notification::make()->title("Warehouse \"{$w->name}\" created")->success()->send();
    }

    // ─── Product CRUD ───────────────────────────────────────
    public function openEditProductModal(int $id): void
    {
        $p = Product::find($id);
        if (!$p)
            return;
        $this->editingProductId = $id;
        $this->productForm = [
            'name' => $p->name,
            'sku' => $p->sku,
            'brand' => $p->brand,
            'model_number' => $p->model_number,
            'serial_number' => $p->serial_number,
            'unit_of_measure' => $p->unit_of_measure,
            'cost_price' => $p->cost_price,
            'selling_price' => $p->selling_price,
            'condition' => $p->condition ?? 'new',
            'reorder_level' => $p->reorder_level,
            'location' => $p->location ?? '',
            'description' => $p->description ?? '',
        ];
        $this->showProductModal = true;
    }

    public function submitProduct(): void
    {
        $this->validate(['productForm.name' => 'required|string|max:255']);
        $data = $this->productForm;
        $data['company_id'] = $this->cid();
        $data['is_active'] = true;
        $data['track_inventory'] = true;

        if ($this->editingProductId) {
            Product::find($this->editingProductId)?->update($data);
            Notification::make()->title('Product updated')->success()->send();
        } else {
            if (empty($data['sku'])) {
                $data['sku'] = 'PRD-' . str_pad((string) (Product::where('company_id', $this->cid())->count() + 1), 5, '0', STR_PAD_LEFT);
            }
            Product::create($data);
            Notification::make()->title('Product added')->success()->send();
        }

        $this->showProductModal = false;
        $this->editingProductId = null;
        $this->productForm = ['name' => '', 'sku' => '', 'brand' => '', 'model_number' => '', 'serial_number' => '', 'unit_of_measure' => 'each', 'cost_price' => 0, 'selling_price' => 0, 'condition' => 'new', 'reorder_level' => 0, 'location' => '', 'description' => ''];
    }

    // ─── Store CRUD ─────────────────────────────────────────
    public function openEditStoreModal(int $id): void
    {
        $s = Warehouse::find($id);
        if (!$s)
            return;
        $this->editingStoreId = $id;
        $this->storeForm = ['name' => $s->name, 'code' => $s->code, 'city' => $s->city, 'address' => $s->address];
        $this->showStoreModal = true;
    }



    public function submitStore(): void
    {
        $this->validate(['storeForm.name' => 'required|string|max:255']);
        $data = $this->storeForm;
        $data['company_id'] = $this->cid();
        $data['is_active'] = true;

        if ($this->editingStoreId) {
            Warehouse::find($this->editingStoreId)?->update($data);
            Notification::make()->title('Store updated')->success()->send();
        } else {
            Warehouse::create($data);
            Notification::make()->title('Store added')->success()->send();
        }

        $this->showStoreModal = false;
        $this->editingStoreId = null;
        $this->storeForm = ['name' => '', 'code' => '', 'city' => '', 'address' => ''];
    }

    // ─── Material Issuance (requisition-backed only) ──────────
    public function submitIssuance(): void
    {
        $this->validate([
            'issuanceForm.warehouse_id' => 'required',
            'issuanceForm.product_id' => 'required',
            'issuanceForm.quantity' => 'required|integer|min:1',
            'issuanceForm.requisition_id' => 'required',
        ], [
            'issuanceForm.requisition_id.required' => 'Materials can only be issued against an approved requisition.',
        ]);

        $data = $this->issuanceForm;

        // ── Validate requisition is approved ──
        $req = MaterialRequisition::with('items')->find($data['requisition_id']);
        if (!$req || !in_array($req->status, ['approved', 'partially_issued'])) {
            Notification::make()
                ->title('Invalid Requisition')
                ->body('Materials can only be issued against requisitions with status "Approved" or "Partially Issued".')
                ->danger()->send();
            return;
        }

        // ── Validate quantity does not exceed remaining ──
        $reqItem = $req->items()->where('product_id', $data['product_id'])->first();
        if (!$reqItem) {
            Notification::make()
                ->title('Product Not on Requisition')
                ->body('The selected product is not part of the chosen requisition.')
                ->danger()->send();
            return;
        }

        $target = $reqItem->quantity_approved ?? $reqItem->quantity_requested;
        $remaining = $target - $reqItem->quantity_issued;
        if ($data['quantity'] > $remaining) {
            Notification::make()
                ->title('Quantity Exceeds Remaining')
                ->body("Only {$remaining} remaining on this requisition item (approved: {$target}, already issued: {$reqItem->quantity_issued}).")
                ->danger()->send();
            return;
        }

        $number = 'ISS-' . str_pad((string) (MaterialIssuance::where('company_id', $this->cid())->count() + 1), 5, '0', STR_PAD_LEFT);

        $issuance = MaterialIssuance::create([
            'company_id' => $this->cid(),
            'cde_project_id' => $this->pid(),
            'issuance_number' => $number,
            'warehouse_id' => $data['warehouse_id'],
            'issued_to' => $data['issued_to'] ?: null,
            'issued_to_name' => $data['issued_to_name'] ?: null,
            'purpose' => $data['purpose'],
            'status' => 'issued',
            'issue_date' => $data['issue_date'] ?: now()->format('Y-m-d'),
            'expected_return_date' => $data['expected_return_date'] ?: null,
            'notes' => $data['notes'] ?: null,
            'created_by' => auth()->id(),
            'material_requisition_id' => $req->id,
        ]);

        MaterialIssuanceItem::create([
            'material_issuance_id' => $issuance->id,
            'product_id' => $data['product_id'],
            'quantity_issued' => $data['quantity'],
            'condition_on_issue' => $data['condition'] ?? 'good',
        ]);

        // ── Deduct from stock level ──────────────────────────────────────────
        $stock = StockLevel::firstOrCreate(
            ['product_id' => $data['product_id'], 'warehouse_id' => $data['warehouse_id']],
            ['quantity_on_hand' => 0, 'quantity_reserved' => 0, 'quantity_available' => 0]
        );

        // Validate sufficient available stock before issuing
        if ($stock->quantity_available < $data['quantity']) {
            // Roll back issuance creation
            $issuance->items()->delete();
            $issuance->delete();
            Notification::make()
                ->title('Insufficient Stock')
                ->body("Only {$stock->quantity_available} units available in this warehouse (requested: {$data['quantity']}).")
                ->danger()->send();
            return;
        }

        $stock->decrement('quantity_available', $data['quantity']);
        $stock->increment('quantity_reserved', $data['quantity']);
        // ────────────────────────────────────────────────────────────────────

        // Track product lifecycle
        ProductTracking::create([
            'company_id' => $this->cid(),
            'cde_project_id' => $this->pid(),
            'product_id' => $data['product_id'],
            'stage' => 'issued',
            'material_issuance_id' => $issuance->id,
            'quantity' => $data['quantity'],
            'location' => Warehouse::find($data['warehouse_id'])?->name,
            'notes' => 'Issued via ' . $number . ' against ' . $req->requisition_number,
            'recorded_by' => auth()->id(),
        ]);

        // Update Requisition item + status
        $reqItem->increment('quantity_issued', $data['quantity']);

        $allIssued = true;
        foreach ($req->fresh()->items as $ri) {
            $riTarget = $ri->quantity_approved ?? $ri->quantity_requested;
            if ($ri->quantity_issued < $riTarget) {
                $allIssued = false;
                break;
            }
        }
        $req->update(['status' => $allIssued ? 'issued' : 'partially_issued']);

        $this->showIssuanceModal = false;
        $this->issuanceForm = ['issued_to' => '', 'issued_to_name' => '', 'purpose' => 'site_use', 'warehouse_id' => '', 'issue_date' => '', 'expected_return_date' => '', 'product_id' => '', 'quantity' => 1, 'condition' => 'good', 'notes' => '', 'requisition_id' => null];
        Notification::make()->title("Materials issued: {$number}")->body("Against requisition {$req->requisition_number}")->success()->send();

        // ── Stakeholder notifications ──────────────────────────────────────────
        try {
            $issuance->load(['warehouse', 'issuedTo', 'requisition', 'items']);
            app(InventoryNotificationService::class)->materialsIssued($issuance, auth()->user());
        } catch (\Throwable) {
        }

        // ── Low-stock check after issuance ─────────────────────────────────────
        try {
            $stockFresh = $stock->fresh();
            $productItem = Product::find($data['product_id']);
            if (
                $productItem && $productItem->reorder_level > 0
                && $stockFresh->quantity_on_hand <= $productItem->reorder_level
            ) {
                app(InventoryNotificationService::class)->lowStockAlert(
                    companyId: $this->cid(),
                    projectId: $this->pid(),
                    productName: $productItem->name,
                    onHand: (float) $stockFresh->quantity_on_hand,
                    reorderLevel: (float) $productItem->reorder_level,
                );
            }
        } catch (\Throwable) {
        }
        // ──────────────────────────────────────────────────────────────────────
    }

    // ─── Asset CRUD ─────────────────────────────────────────
    public function submitAsset(): void
    {
        $this->validate(['assetForm.name' => 'required|string|max:255']);
        $data = $this->assetForm;
        $tag = 'AST-' . str_pad((string) (Asset::where('company_id', $this->cid())->count() + 1), 5, '0', STR_PAD_LEFT);

        Asset::create([
            'company_id' => $this->cid(),
            'cde_project_id' => $this->pid(),
            'asset_tag' => $tag,
            'name' => $data['name'],
            'product_id' => $data['product_id'] ?: null,
            'serial_number' => $data['serial_number'] ?: null,
            'condition' => $data['condition'],
            'status' => 'available',
            'warehouse_id' => $data['warehouse_id'] ?: null,
            'current_location' => $data['current_location'] ?: null,
            'purchase_date' => $data['purchase_date'] ?: null,
            'purchase_cost' => $data['purchase_cost'] ?: 0,
            'warranty_expiry' => $data['warranty_expiry'] ?: null,
            'useful_life_years' => $data['useful_life_years'] ?: 5,
            'notes' => $data['notes'] ?: null,
            'created_by' => auth()->id(),
        ]);

        $this->showAssetModal = false;
        $this->assetForm = ['name' => '', 'product_id' => '', 'serial_number' => '', 'condition' => 'new', 'warehouse_id' => '', 'purchase_date' => '', 'purchase_cost' => 0, 'warranty_expiry' => '', 'useful_life_years' => 5, 'current_location' => '', 'notes' => ''];
        Notification::make()->title("Asset registered: {$tag}")->success()->send();
    }

    public function openCheckoutModal(int $id): void
    {
        $this->activeAssetId = $id;
        $this->checkoutForm = ['assigned_to' => '', 'assigned_to_name' => '', 'location' => '', 'expected_return_date' => '', 'notes' => ''];
        $this->showCheckoutModal = true;
    }

    public function submitCheckout(): void
    {
        $asset = Asset::find($this->activeAssetId);
        if (!$asset)
            return;

        $data = $this->checkoutForm;

        AssetAssignment::create([
            'asset_id' => $asset->id,
            'action' => 'checkout',
            'assigned_to' => $data['assigned_to'] ?: null,
            'assigned_to_name' => $data['assigned_to_name'] ?: null,
            'location' => $data['location'] ?: null,
            'project_id' => $this->pid(),
            'condition_before' => $asset->condition,
            'checkout_date' => now()->format('Y-m-d'),
            'expected_return_date' => $data['expected_return_date'] ?: null,
            'notes' => $data['notes'] ?: null,
            'performed_by' => auth()->id(),
        ]);

        $asset->update([
            'status' => 'assigned',
            'current_holder_id' => $data['assigned_to'] ?: null,
            'current_location' => $data['location'] ?: $asset->current_location,
        ]);

        $this->showCheckoutModal = false;
        Notification::make()->title('Asset checked out')->success()->send();

        // ── Stakeholder notification
        try {
            $asset->load(['product', 'currentHolder', 'warehouse']);
            $holder = $data['assigned_to'] ? User::find($data['assigned_to']) : null;
            if ($holder) {
                app(InventoryNotificationService::class)->assetCheckedOut($asset, $holder, auth()->user());
            }
        } catch (\Throwable) {
        }
    }

    public function checkinAsset(int $id): void
    {
        $asset = Asset::find($id);
        if (!$asset)
            return;

        AssetAssignment::create([
            'asset_id' => $asset->id,
            'action' => 'checkin',
            'assigned_from' => $asset->current_holder_id,
            'condition_before' => $asset->condition,
            'checkout_date' => now()->format('Y-m-d'),
            'return_date' => now()->format('Y-m-d'),
            'performed_by' => auth()->id(),
        ]);

        $asset->update(['status' => 'available', 'current_holder_id' => null]);
        Notification::make()->title('Asset checked in')->success()->send();

        // ── Stakeholder notification
        try {
            $asset->load(['product', 'warehouse']);
            app(InventoryNotificationService::class)->assetCheckedIn($asset, auth()->user());
        } catch (\Throwable) {
        }
    }

    public function openMaintenanceModal(int $id): void
    {
        $asset = Asset::find($id);
        if (!$asset)
            return;
        $this->activeAssetId = $id;
        $this->maintenanceForm = [
            'type' => 'inspection',
            'priority' => 'normal',
            'title' => '',
            'description' => '',
            'cost' => 0,
            'vendor' => '',
            'condition_after' => $asset->condition,
            'scheduled_date' => '',
            'downtime_hours' => '',
            'meter_reading' => $asset->meter_reading ?? '',
            'next_service_date' => '',
            'parts_used' => '',
        ];
        $this->showMaintenanceModal = true;
    }

    public function submitMaintenance(): void
    {
        $this->validate(['maintenanceForm.title' => 'required|string|max:255']);
        $asset = Asset::find($this->activeAssetId);
        if (!$asset)
            return;

        $f = $this->maintenanceForm;

        AssetMaintenanceLog::create([
            'asset_id' => $asset->id,
            'type' => $f['type'],
            'priority' => $f['priority'] ?: 'normal',
            'title' => $f['title'],
            'description' => $f['description'] ?: null,
            'cost' => $f['cost'] ?: 0,
            'vendor' => $f['vendor'] ?: null,
            'downtime_hours' => $f['downtime_hours'] ?: null,
            'meter_reading' => $f['meter_reading'] ?: null,
            'parts_used' => $f['parts_used'] ?: null,
            'status' => 'completed',
            'scheduled_date' => $f['scheduled_date'] ?: null,
            'completed_date' => now()->format('Y-m-d'),
            'next_service_date' => $f['next_service_date'] ?: null,
            'condition_before' => $asset->condition,
            'condition_after' => $f['condition_after'] ?: $asset->condition,
            'performed_by' => auth()->id(),
        ]);

        // Update asset with new data
        $updates = ['last_service_date' => now()->format('Y-m-d')];
        if ($f['condition_after'] && $f['condition_after'] !== $asset->condition) {
            $updates['condition'] = $f['condition_after'];
        }
        if ($f['next_service_date']) {
            $updates['next_service_date'] = $f['next_service_date'];
        }
        if ($f['meter_reading']) {
            $updates['meter_reading'] = $f['meter_reading'];
        }
        $asset->update($updates);

        $this->showMaintenanceModal = false;
        Notification::make()->title('Maintenance logged')->success()->send();

        // ── Stakeholder notification
        try {
            $asset->load(['product', 'warehouse']);
            app(InventoryNotificationService::class)->assetMaintenance($asset, auth()->user(), $f['type']);
        } catch (\Throwable) {
        }
    }

    // ── Asset Detail / Timeline ────────────────────────────
    public bool $showAssetDetailModal = false;
    public ?array $assetDetail = null;
    public array $assetTimeline = [];

    public function openAssetDetail(int $id): void
    {
        $asset = Asset::with([
            'product',
            'currentHolder',
            'warehouse',
            'creator',
            'assignments.assignedTo',
            'assignments.performedBy',
            'maintenanceLogs.performer'
        ])->find($id);
        if (!$asset)
            return;

        $this->assetDetail = $asset->toArray();
        $this->assetDetail['_display_name'] = $asset->display_name;
        $this->assetDetail['_status_label'] = Asset::$statuses[$asset->status] ?? $asset->status;
        $this->assetDetail['_condition_label'] = Asset::$conditions[$asset->condition] ?? $asset->condition;
        $this->assetDetail['_holder_name'] = $asset->currentHolder?->name;
        $this->assetDetail['_warehouse_name'] = $asset->warehouse?->name;
        $this->assetDetail['_product_name'] = $asset->product?->name;
        $this->assetDetail['_creator_name'] = $asset->creator?->name;
        $this->assetDetail['_book_value'] = $asset->current_book_value;
        $this->assetDetail['_warranty_active'] = $asset->isWarrantyActive();
        $this->assetDetail['_qr_url'] = $asset->qr_code_url;

        // Build unified timeline
        $timeline = [];
        foreach ($asset->assignments as $a) {
            $timeline[] = [
                'type' => 'assignment',
                'action' => AssetAssignment::$actions[$a->action] ?? $a->action,
                'icon' => $a->action,
                'to' => $a->assignedTo?->name ?? $a->assigned_to_name,
                'location' => $a->location,
                'notes' => $a->notes,
                'date' => $a->checkout_date?->format('M d, Y'),
                'by' => $a->performedBy?->name ?? '—',
            ];
        }
        foreach ($asset->maintenanceLogs as $m) {
            $timeline[] = [
                'type' => 'maintenance',
                'action' => AssetMaintenanceLog::$types[$m->type] ?? $m->type,
                'icon' => 'maintenance',
                'to' => $m->title,
                'location' => $m->vendor,
                'notes' => $m->description,
                'date' => ($m->completed_date ?? $m->scheduled_date)?->format('M d, Y'),
                'by' => $m->performer?->name ?? '—',
                'cost' => $m->cost,
                'status' => AssetMaintenanceLog::$statuses[$m->status] ?? $m->status,
            ];
        }
        // Sort by date descending
        usort($timeline, fn($a, $b) => strtotime($b['date'] ?? '1970-01-01') - strtotime($a['date'] ?? '1970-01-01'));
        $this->assetTimeline = $timeline;
        $this->showAssetDetailModal = true;
    }

    // ── Transfer Asset ──────────────────────────────────────
    public bool $showTransferAssetModal = false;
    public array $transferAssetForm = ['warehouse_id' => '', 'location' => '', 'notes' => ''];

    public function openTransferAssetModal(int $id): void
    {
        $this->activeAssetId = $id;
        $this->transferAssetForm = ['warehouse_id' => '', 'location' => '', 'notes' => ''];
        $this->showTransferAssetModal = true;
    }

    public function submitTransferAsset(): void
    {
        $asset = Asset::find($this->activeAssetId);
        if (!$asset)
            return;

        $data = $this->transferAssetForm;
        $wh = $data['warehouse_id'] ? Warehouse::find($data['warehouse_id']) : null;

        AssetAssignment::create([
            'asset_id' => $asset->id,
            'action' => 'transfer',
            'assigned_from' => $asset->current_holder_id,
            'location' => $data['location'] ?: ($wh?->name),
            'project_id' => $this->pid(),
            'condition_before' => $asset->condition,
            'checkout_date' => now()->format('Y-m-d'),
            'notes' => $data['notes'] ?: null,
            'performed_by' => auth()->id(),
        ]);

        $asset->update([
            'warehouse_id' => $data['warehouse_id'] ?: $asset->warehouse_id,
            'current_location' => $data['location'] ?: ($wh?->name ?? $asset->current_location),
        ]);

        $this->showTransferAssetModal = false;
        Notification::make()->title('Asset transferred to ' . ($wh?->name ?? $data['location']))->success()->send();
    }

    // ── Dispose / Retire Asset ──────────────────────────────
    public bool $showDisposeAssetModal = false;
    public array $disposeAssetForm = ['action' => 'retire', 'reason' => '', 'disposal_method' => '', 'notes' => ''];

    public static array $disposalMethods = [
        'sold' => 'Sold',
        'donated' => 'Donated',
        'recycled' => 'Recycled',
        'scrapped' => 'Scrapped',
        'returned' => 'Returned to Vendor',
    ];

    public function openDisposeAssetModal(int $id, string $action = 'retire'): void
    {
        $this->activeAssetId = $id;
        $this->disposeAssetForm = ['action' => $action, 'reason' => '', 'disposal_method' => '', 'notes' => ''];
        $this->showDisposeAssetModal = true;
    }

    public function submitDisposeAsset(): void
    {
        $this->validate(['disposeAssetForm.reason' => 'required|string|max:500']);
        $asset = Asset::find($this->activeAssetId);
        if (!$asset)
            return;

        $data = $this->disposeAssetForm;
        $action = $data['action']; // 'retire', 'dispose', 'lost'

        AssetAssignment::create([
            'asset_id' => $asset->id,
            'action' => $action === 'dispose' ? 'retire' : $action,
            'condition_before' => $asset->condition,
            'checkout_date' => now()->format('Y-m-d'),
            'notes' => trim(($data['reason'] ?? '') . ($data['disposal_method'] ? ' | Method: ' . (self::$disposalMethods[$data['disposal_method']] ?? $data['disposal_method']) : '') . ($data['notes'] ? ' | ' . $data['notes'] : '')),
            'performed_by' => auth()->id(),
        ]);

        $newStatus = match ($action) {
            'retire' => 'retired',
            'dispose' => 'disposed',
            'lost' => 'lost',
            default => 'retired',
        };

        $asset->update([
            'status' => $newStatus,
            'current_holder_id' => null,
        ]);

        $this->showDisposeAssetModal = false;
        $labels = ['retire' => 'retired', 'dispose' => 'disposed', 'lost' => 'marked as lost'];
        Notification::make()->title('Asset ' . ($labels[$action] ?? 'updated'))->success()->send();

        // ── Stakeholder notification
        try {
            $asset->load(['product', 'warehouse']);
            app(InventoryNotificationService::class)->assetDisposed($asset, auth()->user(), $action);
        } catch (\Throwable) {
        }
    }

    // ── Update Condition ────────────────────────────────────
    public bool $showConditionModal = false;
    public array $conditionForm = ['condition' => '', 'meter_reading' => '', 'notes' => ''];

    public function openConditionModal(int $id): void
    {
        $asset = Asset::find($id);
        if (!$asset)
            return;
        $this->activeAssetId = $id;
        $this->conditionForm = [
            'condition' => $asset->condition,
            'meter_reading' => $asset->meter_reading ?? '',
            'notes' => '',
        ];
        $this->showConditionModal = true;
    }

    public function submitConditionUpdate(): void
    {
        $asset = Asset::find($this->activeAssetId);
        if (!$asset)
            return;

        $oldCondition = $asset->condition;
        $newCondition = $this->conditionForm['condition'];
        $meterReading = $this->conditionForm['meter_reading'] ?: null;

        if ($oldCondition !== $newCondition || $meterReading) {
            AssetAssignment::create([
                'asset_id' => $asset->id,
                'action' => 'transfer',
                'condition_before' => $oldCondition,
                'condition_after' => $newCondition,
                'meter_reading' => $meterReading,
                'checkout_date' => now()->format('Y-m-d'),
                'notes' => 'Condition updated: ' . (Asset::$conditions[$oldCondition] ?? $oldCondition) . ' → ' . (Asset::$conditions[$newCondition] ?? $newCondition)
                    . ($meterReading ? ' | Meter: ' . $meterReading : '')
                    . ($this->conditionForm['notes'] ? '. ' . $this->conditionForm['notes'] : ''),
                'performed_by' => auth()->id(),
            ]);

            $updates = ['condition' => $newCondition];
            if ($meterReading)
                $updates['meter_reading'] = $meterReading;
            $asset->update($updates);
        }

        $this->showConditionModal = false;
        Notification::make()->title('Condition updated to ' . (Asset::$conditions[$newCondition] ?? $newCondition))->success()->send();
    }

    // ── Edit Asset ──────────────────────────────────────────
    public bool $showEditAssetModal = false;
    public array $editAssetForm = [];

    public function openEditAssetModal(int $id): void
    {
        $asset = Asset::find($id);
        if (!$asset)
            return;
        $this->activeAssetId = $id;
        $this->editAssetForm = [
            'name' => $asset->name ?? '',
            'serial_number' => $asset->serial_number ?? '',
            'purchase_cost' => $asset->purchase_cost ?? 0,
            'purchase_date' => $asset->purchase_date?->format('Y-m-d') ?? '',
            'warranty_expiry' => $asset->warranty_expiry?->format('Y-m-d') ?? '',
            'useful_life_years' => $asset->useful_life_years ?? 5,
            'salvage_value' => $asset->salvage_value ?? 0,
            'meter_reading' => $asset->meter_reading ?? '',
            'meter_unit' => $asset->meter_unit ?? '',
            'notes' => $asset->notes ?? '',
        ];
        $this->showEditAssetModal = true;
    }

    public function submitEditAsset(): void
    {
        $asset = Asset::find($this->activeAssetId);
        if (!$asset)
            return;

        $asset->update(array_filter($this->editAssetForm, fn($v) => $v !== '' && $v !== null));
        $this->showEditAssetModal = false;
        Notification::make()->title('Asset updated')->success()->send();
    }

    // ── Replace Asset ────────────────────────────────────────
    public bool $showReplaceAssetModal = false;
    public array $replaceAssetForm = [
        'reason' => '',
        'new_asset_name' => '',
        'new_serial_number' => '',
        'new_purchase_cost' => 0,
        'new_purchase_date' => '',
        'new_warranty_expiry' => '',
        'new_condition' => 'new',
        'notes' => '',
    ];

    public function openReplaceAssetModal(int $id): void
    {
        $asset = Asset::find($id);
        if (!$asset)
            return;
        $this->activeAssetId = $id;
        $this->replaceAssetForm = [
            'reason' => '',
            'new_asset_name' => $asset->display_name . ' (Replacement)',
            'new_serial_number' => '',
            'new_purchase_cost' => 0,
            'new_purchase_date' => now()->format('Y-m-d'),
            'new_warranty_expiry' => '',
            'new_condition' => 'new',
            'notes' => '',
        ];
        $this->showReplaceAssetModal = true;
    }

    public function submitReplaceAsset(): void
    {
        $this->validate([
            'replaceAssetForm.reason' => 'required|string|max:500',
            'replaceAssetForm.new_asset_name' => 'required|string|max:255',
        ]);

        $oldAsset = Asset::find($this->activeAssetId);
        if (!$oldAsset)
            return;

        $f = $this->replaceAssetForm;
        $projectId = $this->pid();

        // Generate tag for new asset
        $nextNum = Asset::where('cde_project_id', $projectId)->count() + 1;
        $newTag = 'AST-' . str_pad((string) $nextNum, 5, '0', STR_PAD_LEFT);

        // Create the replacement asset
        $newAsset = Asset::create([
            'company_id' => $oldAsset->company_id,
            'product_id' => $oldAsset->product_id,
            'cde_project_id' => $projectId,
            'asset_tag' => $newTag,
            'name' => $f['new_asset_name'],
            'serial_number' => $f['new_serial_number'] ?: null,
            'status' => 'available',
            'condition' => $f['new_condition'],
            'warehouse_id' => $oldAsset->warehouse_id,
            'current_location' => $oldAsset->current_location,
            'purchase_date' => $f['new_purchase_date'] ?: null,
            'purchase_cost' => $f['new_purchase_cost'] ?: 0,
            'warranty_expiry' => $f['new_warranty_expiry'] ?: null,
            'useful_life_years' => $oldAsset->useful_life_years,
            'salvage_value' => $oldAsset->salvage_value,
            'depreciation_method' => $oldAsset->depreciation_method,
            'meter_unit' => $oldAsset->meter_unit,
            'replaces_id' => $oldAsset->id,
            'created_by' => auth()->id(),
        ]);

        // Link old → new
        $oldAsset->update([
            'status' => 'retired',
            'current_holder_id' => null,
            'replaced_by_id' => $newAsset->id,
        ]);

        // Audit trail on old asset
        AssetAssignment::create([
            'asset_id' => $oldAsset->id,
            'action' => 'retire',
            'condition_before' => $oldAsset->condition,
            'checkout_date' => now()->format('Y-m-d'),
            'notes' => 'Replaced by ' . $newTag . '. Reason: ' . $f['reason'] . ($f['notes'] ? ' | ' . $f['notes'] : ''),
            'performed_by' => auth()->id(),
        ]);

        $this->showReplaceAssetModal = false;
        Notification::make()
            ->title('Asset replaced — new asset ' . $newTag . ' created')
            ->success()
            ->send();
    }

    // ─── Material Requisitions Methods ─────────────────────────
    public function getRequisitions()
    {
        return MaterialRequisition::where('company_id', $this->cid())
            ->where('cde_project_id', $this->pid())
            ->with(['requester', 'warehouse', 'approver', 'items.product'])
            ->latest()->get();
    }

    public function initNewRequisition(?int $productId = null): void
    {
        $this->editingRequisitionId = null;
        $this->reqHeader = [
            'warehouse_id' => '',
            'priority' => 'normal',
            'purpose' => '',
            'required_date' => now()->addDays(2)->format('Y-m-d'),
            'notes' => '',
        ];
        if ($productId) {
            $this->reqItems = [['product_id' => $productId, 'quantity_requested' => 1, 'notes' => '']];
        } else {
            $this->reqItems = [['product_id' => '', 'quantity_requested' => 1, 'notes' => '']];
        }
        $this->showRequisitionModal = true;
    }

    public function addReqItem(): void
    {
        $this->reqItems[] = ['product_id' => '', 'quantity_requested' => 1, 'notes' => ''];
    }

    public function removeReqItem(int $index): void
    {
        if (count($this->reqItems) > 1) {
            array_splice($this->reqItems, $index, 1);
        }
    }

    public function submitRequisition(): void
    {
        $this->validate([
            'reqHeader.priority' => 'required',
            'reqHeader.purpose' => 'required|string|max:255',
            'reqItems.*.product_id' => 'required',
            'reqItems.*.quantity_requested' => 'required|integer|min:1',
        ]);

        $next = MaterialRequisition::where('cde_project_id', $this->pid())->count() + 1;

        $req = MaterialRequisition::create([
            'company_id' => $this->cid(),
            'cde_project_id' => $this->pid(),
            'requisition_number' => 'REQ-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT),
            'requester_id' => auth()->id(),
            'warehouse_id' => $this->reqHeader['warehouse_id'] ?: null,
            'status' => 'pending',
            'priority' => $this->reqHeader['priority'],
            'purpose' => $this->reqHeader['purpose'],
            'required_date' => $this->reqHeader['required_date'] ?: null,
            'notes' => $this->reqHeader['notes'] ?: null,
        ]);

        foreach ($this->reqItems as $item) {
            $req->items()->create([
                'product_id' => $item['product_id'],
                'quantity_requested' => $item['quantity_requested'],
                'notes' => $item['notes'] ?? null,
            ]);
        }

        $this->showRequisitionModal = false;
        Notification::make()->title("Requisition {$req->requisition_number} submitted")->success()->send();

        // ── Stakeholder notification
        try {
            $req->load(['requester', 'items', 'warehouse']);
            app(InventoryNotificationService::class)->requisitionCreated($req, auth()->user());
        } catch (\Throwable) {
        }
    }

    public function approveRequisition(int $id): void
    {
        $req = MaterialRequisition::find($id);
        if ($req && $req->status === 'pending') {
            $req->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            foreach ($req->items as $item) {
                $item->update(['quantity_approved' => $item->quantity_requested]);
            }
            Notification::make()->title("Requisition {$req->requisition_number} approved.")->success()->send();

            // ── Stakeholder notification
            try {
                $req->load(['requester', 'items']);
                app(InventoryNotificationService::class)->requisitionApproved($req, auth()->user());
            } catch (\Throwable) {
            }
        }
    }

    public function promptIssueRequisition(int $id): void
    {
        $req = MaterialRequisition::with('items')->find($id);
        if (!$req || !in_array($req->status, ['approved', 'partially_issued'])) {
            return;
        }

        // Auto-fill an issuance form for this requisition
        $this->issuanceForm = [
            'issued_to' => $req->requester_id,
            'issued_to_name' => '',
            'purpose' => 'site_use',
            'warehouse_id' => $req->warehouse_id ?? '',
            'issue_date' => now()->format('Y-m-d'),
            'expected_return_date' => '',
            'notes' => "Issuing against Requisition {$req->requisition_number}",
            'requisition_id' => $req->id, // Add tracking logic later inside submitIssuance
            'product_id' => $req->items->first()->product_id ?? '',
            'quantity' => ($req->items->first()->quantity_approved ?? $req->items->first()->quantity_requested) - $req->items->first()->quantity_issued,
            'condition' => 'good',
        ];

        $this->showIssuanceModal = true;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    // ─── Table: switches between Products and POs based on active tab ───
    public function table(Table $table): Table
    {
        if ($this->activeInventoryTab === 'products') {
            return $this->productsTable($table);
        }
        return $this->poTable($table);
    }

    private function productsTable(Table $table): Table
    {
        return $table
            ->query(Product::query()->where('company_id', $this->cid())->with(['category', 'stockLevels']))
            ->columns([
                Tables\Columns\TextColumn::make('sku')->label('SKU')->searchable()->sortable()->weight('bold')
                    ->copyable()->toggleable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->wrap()->toggleable(),
                Tables\Columns\TextColumn::make('brand')->searchable()->sortable()->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('category.name')->label('Category')->sortable()->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('unit_of_measure')->label('Unit')->toggleable(),
                Tables\Columns\TextColumn::make('total_stock')->label('In Stock')->sortable()
                    ->badge()
                    ->color(fn($record) => $record->total_stock <= 0 ? 'danger' : ($record->total_stock <= $record->reorder_level ? 'warning' : 'success'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('cost_price')->label('Cost')->formatStateUsing(CurrencyHelper::formatter(0))->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('selling_price')->label('Sell')->formatStateUsing(CurrencyHelper::formatter(0))->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('condition')
                    ->badge()->color(fn(string $state) => match ($state) { 'new' => 'success', 'good' => 'primary', 'fair' => 'warning', 'poor' => 'danger', 'damaged' => 'danger', default => 'gray'})
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('location')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\SelectFilter::make('condition')->options(Product::$conditions),
                Tables\Filters\Filter::make('low_stock')->label('Low Stock')
                    ->query(fn(\Illuminate\Database\Eloquent\Builder $query) => $query->whereHas('stockLevels', fn($q) => $q->whereColumn('quantity_on_hand', '<=', 'products.reorder_level')))->toggle(),
            ])
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\Action::make('edit')
                        ->icon('heroicon-o-pencil')->color('primary')
                        ->action(fn(Product $record) => $this->openEditProductModal($record->id)),
                    \Filament\Actions\Action::make('qrCode')
                        ->label('QR Code')->icon('heroicon-o-qr-code')->color('info')
                        ->url(fn(Product $record) => $record->qr_code_url, shouldOpenInNewTab: true),
                    \Filament\Actions\Action::make('delete')
                        ->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                        ->action(fn(Product $record) => $record->delete()),
                ]),
            ])
            ->emptyStateHeading('No Products')
            ->emptyStateDescription('Add products to manage your inventory catalog.')
            ->emptyStateIcon('heroicon-o-cube')
            ->striped()->paginated([10, 25, 50, 100]);
    }

    private function poTable(Table $table): Table
    {
        return $table
            ->query(PurchaseOrder::query()->where('cde_project_id', $this->pid())->with(['supplier', 'creator', 'approver', 'items']))
            ->columns([
                Tables\Columns\TextColumn::make('po_number')->label('PO #')->searchable()->sortable()->weight('bold')->toggleable()
                    ->icon('heroicon-o-shopping-cart')->copyable(),
                Tables\Columns\TextColumn::make('supplier.name')->label('Supplier')->searchable()->sortable()->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('status')->badge()->toggleable()
                    ->color(fn(string $state) => match ($state) { 'received' => 'success', 'ordered' => 'info', 'approved' => 'primary', 'partially_received' => 'warning', 'submitted' => 'info', 'rejected' => 'danger', 'cancelled' => 'danger', default => 'gray'})->sortable(),
                Tables\Columns\TextColumn::make('order_date')->date()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('expected_date')->label('Expected')->date()->sortable()->toggleable()
                    ->color(fn($record) => $record->expected_date?->isPast() && !in_array($record->status, ['received', 'cancelled']) ? 'danger' : null),
                Tables\Columns\TextColumn::make('items_count')->label('Items')->counts('items')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('total_amount')->label('Total')->formatStateUsing(CurrencyHelper::formatter(0))->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('approver.name')->label('Approved By')->placeholder('—')->toggleable()
                    ->description(fn($record) => $record->approved_at?->format('M d, Y')),
                Tables\Columns\TextColumn::make('creator.name')->label('Created By')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(PurchaseOrder::$statuses)->multiple(),
                Tables\Filters\SelectFilter::make('supplier_id')->label('Supplier')
                    ->options(fn() => Supplier::where('company_id', $this->cid())->pluck('name', 'id')),
            ])
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                    // ── Edit (only draft/rejected) ──
                    \Filament\Actions\Action::make('editPO')
                        ->label('Edit PO')->icon('heroicon-o-pencil-square')->color('primary')
                        ->visible(fn(PurchaseOrder $record) => $record->canBeSubmitted())
                        ->action(fn(PurchaseOrder $record) => $this->editPO($record->id)),

                    // ── Submit for Approval ──
                    \Filament\Actions\Action::make('submitForApproval')
                        ->label('Submit for Approval')->icon('heroicon-o-paper-airplane')->color('info')
                        ->visible(fn(PurchaseOrder $record) => $record->canBeSubmitted())
                        ->requiresConfirmation()
                        ->modalHeading('Submit PO for Approval')
                        ->modalDescription(fn(PurchaseOrder $record) => "Submit {$record->po_number} for approval? It will be locked for editing until approved or rejected.")
                        ->action(function (PurchaseOrder $record): void {
                            $record->update(['status' => 'submitted', 'submitted_at' => now(), 'rejection_reason' => null]);
                            Notification::make()->title("PO {$record->po_number} submitted for approval")->success()->send();
                            // ── Stakeholder notification
                            try {
                                $record->load(['supplier', 'creator']);
                                app(InventoryNotificationService::class)->poSubmitted($record, auth()->user());
                            } catch (\Throwable) {
                            }
                        }),

                    // ── Approve ──
                    \Filament\Actions\Action::make('approvePO')
                        ->label('Approve')->icon('heroicon-o-check-circle')->color('success')
                        ->visible(fn(PurchaseOrder $record) => $record->canBeApproved())
                        ->requiresConfirmation()
                        ->modalHeading('Approve Purchase Order')
                        ->modalDescription(fn(PurchaseOrder $record) => "Approve {$record->po_number} ({$record->supplier?->name}) for " . number_format((float) $record->total_amount, 2) . "?")
                        ->action(function (PurchaseOrder $record): void {
                            $record->update([
                                'status' => 'approved',
                                'approved_by' => auth()->id(),
                                'approved_at' => now(),
                            ]);
                            Notification::make()->title("PO {$record->po_number} approved")->success()->send();
                            // ── Stakeholder notification
                            try {
                                $record->load(['supplier', 'creator']);
                                app(InventoryNotificationService::class)->poApproved($record, auth()->user());
                            } catch (\Throwable) {
                            }
                        }),

                    // ── Reject ──
                    \Filament\Actions\Action::make('rejectPO')
                        ->label('Reject')->icon('heroicon-o-x-circle')->color('danger')
                        ->visible(fn(PurchaseOrder $record) => $record->canBeRejected())
                        ->schema([
                            Forms\Components\Textarea::make('rejection_reason')
                                ->label('Reason for Rejection')
                                ->required()
                                ->placeholder('Explain why this PO is being rejected...')
                                ->rows(3),
                        ])
                        ->action(function (array $data, PurchaseOrder $record): void {
                            $record->update([
                                'status' => 'rejected',
                                'rejection_reason' => $data['rejection_reason'],
                                'approved_by' => null,
                                'approved_at' => null,
                            ]);
                            Notification::make()->title("PO {$record->po_number} rejected")->warning()->send();
                            // ── Stakeholder notification
                            try {
                                $record->load(['supplier', 'creator']);
                                app(InventoryNotificationService::class)->poRejected($record, auth()->user(), $data['rejection_reason']);
                            } catch (\Throwable) {
                            }
                        }),

                    // ── Receive GRN ──
                    \Filament\Actions\Action::make('receiveGRN')
                        ->label('Receive (GRN)')->icon('heroicon-o-inbox-arrow-down')->color('success')
                        ->visible(fn(PurchaseOrder $record) => in_array($record->status, ['approved', 'ordered', 'partially_received']))
                        ->action(fn(PurchaseOrder $record) => $this->initNewGRN($record->id)),

                    // ── Mark as Ordered ──
                    \Filament\Actions\Action::make('markOrdered')
                        ->label('Mark Ordered')->icon('heroicon-o-truck')->color('info')
                        ->visible(fn(PurchaseOrder $record) => $record->status === 'approved')
                        ->requiresConfirmation()
                        ->action(function (PurchaseOrder $record): void {
                            $record->update(['status' => 'ordered', 'order_date' => $record->order_date ?? now()]);
                            Notification::make()->title("PO {$record->po_number} marked as ordered")->success()->send();
                            // ── Stakeholder notification
                            try {
                                $record->load(['supplier', 'creator']);
                                app(InventoryNotificationService::class)->poOrdered($record);
                            } catch (\Throwable) {
                            }
                        }),

                    // ── Cancel ──
                    \Filament\Actions\Action::make('cancelPO')
                        ->label('Cancel')->icon('heroicon-o-x-mark')->color('danger')
                        ->visible(fn(PurchaseOrder $record) => !in_array($record->status, ['received', 'cancelled']))
                        ->requiresConfirmation()
                        ->modalHeading('Cancel Purchase Order')
                        ->action(function (PurchaseOrder $record): void {
                            $record->update(['status' => 'cancelled']);
                            Notification::make()->title("PO {$record->po_number} cancelled")->warning()->send();
                        }),

                    // ── Delete (draft only) ──
                    \Filament\Actions\Action::make('delete')
                        ->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                        ->visible(fn(PurchaseOrder $record) => $record->status === 'draft')
                        ->action(fn(PurchaseOrder $record) => $record->delete()),
                ]),
            ])
            ->toolbarActions([
                $this->exportCsvAction('purchase_orders', fn() => PurchaseOrder::query()->where('cde_project_id', $this->pid())->with(['supplier', 'creator']), [
                    'po_number' => 'PO #',
                    'supplier.name' => 'Supplier',
                    'status' => 'Status',
                    'order_date' => 'Order Date',
                    'total_amount' => 'Total',
                    'creator.name' => 'Created By',
                ]),
            ])
            ->emptyStateHeading('No Purchase Orders')
            ->emptyStateDescription('Create purchase orders to track inventory procurement.')
            ->emptyStateIcon('heroicon-o-cube')
            ->striped()->paginated([10, 25, 50]);
    }
}
