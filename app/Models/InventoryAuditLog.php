<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

/**
 * Immutable audit trail for all inventory events – required for URA compliance.
 *
 * Every stock movement (PO, GRN, issuance, transfer, adjustment) should
 * create a corresponding InventoryAuditLog record.
 */
class InventoryAuditLog extends Model
{
    use BelongsToCompany;

    // Never allow mass-update of audit records
    protected $guarded = ['id'];

    protected $casts = [
        'metadata'        => 'array',
        'quantity_before' => 'decimal:2',
        'quantity_after'  => 'decimal:2',
        'quantity_change' => 'decimal:2',
        'unit_cost'       => 'decimal:2',
        'total_value'     => 'decimal:2',
        'is_quarterly'    => 'boolean',
    ];

    /** Human-readable labels for URA reports */
    public static array $eventTypes = [
        'po_created'       => 'PO Created',
        'po_approved'      => 'PO Approved',
        'po_received'      => 'Goods Received (GRN)',
        'stock_adjusted'   => 'Stock Adjusted',
        'material_issued'  => 'Material Issued',
        'stock_transferred'=> 'Stock Transferred',
        'asset_registered' => 'Asset Registered',
        'asset_disposed'   => 'Asset Disposed',
        'product_added'    => 'Product Added',
        'product_updated'  => 'Product Updated',
        'reorder_alert'    => 'Low Stock Alert',
    ];

    // ─── Relationships ──────────────────────────────────────
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    // ─── Static Factory ─────────────────────────────────────
    /**
     * Record an audit entry for any inventory event.
     */
    public static function record(
        int $companyId,
        string $eventType,
        string $description,
        ?int $projectId = null,
        ?int $productId = null,
        ?int $warehouseId = null,
        ?float $quantityBefore = null,
        ?float $quantityAfter = null,
        ?float $unitCost = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $referenceNumber = null,
        ?array $metadata = null,
    ): static {
        $change = (is_numeric($quantityAfter) && is_numeric($quantityBefore))
            ? round($quantityAfter - $quantityBefore, 4)
            : null;

        $totalValue = (is_numeric($quantityAfter) && is_numeric($unitCost))
            ? round($quantityAfter * $unitCost, 2)
            : null;

        return static::create([
            'company_id'       => $companyId,
            'cde_project_id'   => $projectId,
            'event_type'       => $eventType,
            'description'      => $description,
            'product_id'       => $productId,
            'warehouse_id'     => $warehouseId,
            'quantity_before'  => $quantityBefore,
            'quantity_after'   => $quantityAfter,
            'quantity_change'  => $change,
            'unit_cost'        => $unitCost,
            'total_value'      => $totalValue,
            'reference_type'   => $referenceType,
            'reference_id'     => $referenceId,
            'reference_number' => $referenceNumber,
            'metadata'         => $metadata,
            'performed_by'     => auth()->id(),
            'ip_address'       => request()->ip(),
        ]);
    }
}
