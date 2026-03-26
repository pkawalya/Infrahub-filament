<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\GoodsReceivedNote;
use App\Models\MaterialIssuance;
use App\Models\MaterialRequisition;
use App\Models\PurchaseOrder;
use App\Models\StockAdjustment;
use App\Models\StockTransfer;
use App\Models\User;
use Filament\Actions\Action as FilamentAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Centralised notification dispatcher for all Inventory module events.
 *
 * Sends Filament in-app (database) notifications to relevant stakeholders
 * at every stage of the inventory workflow, and optionally delegates to
 * ModuleNotificationService for email delivery.
 */
class InventoryNotificationService
{
    public function __construct(
        protected ModuleNotificationService $module,
    ) {
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PURCHASE ORDER EVENTS
    // ─────────────────────────────────────────────────────────────────────────

    /** PO created (notifies company admins) */
    public function poCreated(PurchaseOrder $po, User $creator): void
    {
        $this->sendToAdmins(
            companyId: $po->company_id,
            title: "📦 New PO Created: {$po->po_number}",
            body: "Purchase Order {$po->po_number} has been created by {$creator->name}. Total: " . $this->fmt($po->total_amount),
            icon: 'heroicon-o-shopping-cart',
            color: 'info',
            url: $this->projectUrl($po->cde_project_id, 'purchase_orders'),
            exclude: $creator->id,
        );
    }

    /** PO submitted for approval */
    public function poSubmitted(PurchaseOrder $po, User $submitter): void
    {
        // Notify admins to approve
        $this->sendToAdmins(
            companyId: $po->company_id,
            title: "🔔 PO Awaiting Approval: {$po->po_number}",
            body: "{$submitter->name} submitted PO {$po->po_number} for approval. Total: " . $this->fmt($po->total_amount),
            icon: 'heroicon-o-clock',
            color: 'warning',
            url: $this->projectUrl($po->cde_project_id, 'purchase_orders'),
        );

        // Also notify the creator (if different) via module service
        if ($po->creator && $po->creator->id !== $submitter->id) {
            $this->sendToUser(
                user: $po->creator,
                title: "Your PO {$po->po_number} is under review",
                body: "Submitted by {$submitter->name}. Awaiting approval.",
                icon: 'heroicon-o-clock',
                color: 'warning',
                url: $this->projectUrl($po->cde_project_id, 'purchase_orders'),
            );
        }
    }

    /** PO approved */
    public function poApproved(PurchaseOrder $po, User $approver): void
    {
        // Notify creator
        if ($po->creator) {
            $this->sendToUser(
                user: $po->creator,
                title: "✅ PO {$po->po_number} Approved!",
                body: "Your purchase order has been approved by {$approver->name}. You may now proceed to order.",
                icon: 'heroicon-o-check-circle',
                color: 'success',
                url: $this->projectUrl($po->cde_project_id, 'purchase_orders'),
            );
        }
    }

    /** PO rejected */
    public function poRejected(PurchaseOrder $po, User $rejector, ?string $reason = null): void
    {
        if ($po->creator) {
            $body = "Your purchase order {$po->po_number} was rejected by {$rejector->name}.";
            if ($reason) {
                $body .= " Reason: {$reason}";
            }

            $this->sendToUser(
                user: $po->creator,
                title: "❌ PO {$po->po_number} Rejected",
                body: $body,
                icon: 'heroicon-o-x-circle',
                color: 'danger',
                url: $this->projectUrl($po->cde_project_id, 'purchase_orders'),
            );
        }
    }

    /** PO marked as ordered (sent to supplier) */
    public function poOrdered(PurchaseOrder $po): void
    {
        // Notify store managers / admins that goods should arrive soon
        $this->sendToAdmins(
            companyId: $po->company_id,
            title: "🚚 PO {$po->po_number} Ordered from Supplier",
            body: "Purchase order {$po->po_number} has been placed with " . ($po->supplier?->name ?? 'supplier') . ". Delivery expected: " . ($po->expected_date ? $po->expected_date->format('M d, Y') : 'TBD'),
            icon: 'heroicon-o-truck',
            color: 'info',
            url: $this->projectUrl($po->cde_project_id, 'grn'),
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GRN EVENTS
    // ─────────────────────────────────────────────────────────────────────────

    /** Goods received against a PO */
    public function grnCreated(GoodsReceivedNote $grn, User $receiver): void
    {
        $totalReceived = $grn->total_received;
        $totalRejected = $grn->total_rejected;

        $body = "GRN {$grn->grn_number} recorded by {$receiver->name}. " .
            "Received: {$totalReceived}" .
            ($totalRejected > 0 ? ", Rejected: {$totalRejected}" : '') .
            " at " . ($grn->warehouse?->name ?? 'warehouse') . ".";

        // If partial receipt or rejections, flag as warning
        $color = $totalRejected > 0 ? 'warning' : 'success';
        $icon = $totalRejected > 0 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-inbox-arrow-down';
        $title = $totalRejected > 0
            ? "⚠ GRN {$grn->grn_number} — {$totalRejected} items rejected"
            : "📥 GRN {$grn->grn_number} — Goods Received";

        $this->sendToAdmins(
            companyId: $grn->company_id,
            title: $title,
            body: $body,
            icon: $icon,
            color: $color,
            url: $this->projectUrl($grn->cde_project_id, 'grn'),
        );

        // Notify PO creator
        if ($grn->purchaseOrder?->creator) {
            $this->sendToUser(
                user: $grn->purchaseOrder->creator,
                title: "Goods Received — PO {$grn->purchaseOrder->po_number}",
                body: $body,
                icon: $icon,
                color: $color,
                url: $this->projectUrl($grn->cde_project_id, 'grn'),
            );
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MATERIAL REQUISITION EVENTS
    // ─────────────────────────────────────────────────────────────────────────

    /** New requisition created */
    public function requisitionCreated(MaterialRequisition $req, User $requester): void
    {
        $this->sendToAdmins(
            companyId: $req->company_id,
            title: "📋 New Requisition: {$req->requisition_number}",
            body: "{$requester->name} raised a {$req->priority} priority requisition for " . $req->items->count() . " product(s). Required by: " . ($req->required_date ? $req->required_date->format('M d, Y') : 'ASAP'),
            icon: 'heroicon-o-clipboard-document-list',
            color: $req->priority === 'urgent' || $req->priority === 'high' ? 'danger' : 'warning',
            url: $this->projectUrl($req->cde_project_id, 'requisitions'),
        );
    }

    /** Requisition approved */
    public function requisitionApproved(MaterialRequisition $req, User $approver): void
    {
        if ($req->requester) {
            $this->sendToUser(
                user: $req->requester,
                title: "✅ Requisition {$req->requisition_number} Approved",
                body: "Your material requisition has been approved by {$approver->name}. Materials will be issued shortly.",
                icon: 'heroicon-o-check-badge',
                color: 'success',
                url: $this->projectUrl($req->cde_project_id, 'requisitions'),
            );
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MATERIAL ISSUANCE EVENTS
    // ─────────────────────────────────────────────────────────────────────────

    /** Materials issued */
    public function materialsIssued(MaterialIssuance $issuance, User $issuer): void
    {
        // Notify recipient if different from issuer
        $recipient = $issuance->issuedTo;
        if ($recipient && $recipient->id !== $issuer->id) {
            $this->sendToUser(
                user: $recipient,
                title: "📤 Materials Issued to You — {$issuance->issuance_number}",
                body: "{$issuer->name} issued " . $issuance->items->count() . " material type(s) from {$issuance->warehouse?->name}.",
                icon: 'heroicon-o-clipboard-document-check',
                color: 'info',
                url: $this->projectUrl($issuance->cde_project_id, 'issuances'),
            );
        }

        // Notify admins
        $this->sendToAdmins(
            companyId: $issuance->company_id,
            title: "📤 Materials Issued: {$issuance->issuance_number}",
            body: "{$issuer->name} issued materials from {$issuance->warehouse?->name}. Against requisition: {$issuance->requisition?->requisition_number}",
            icon: 'heroicon-o-clipboard-document-check',
            color: 'success',
            url: $this->projectUrl($issuance->cde_project_id, 'issuances'),
            exclude: $issuer->id,
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STOCK TRANSFER EVENTS
    // ─────────────────────────────────────────────────────────────────────────

    /** Stock transferred between warehouses */
    public function stockTransferred(StockTransfer $transfer, User $initiator): void
    {
        $from = $transfer->fromWarehouse?->name ?? 'source';
        $to = $transfer->toWarehouse?->name ?? 'destination';
        $qty = $transfer->items->sum('quantity_received');

        $this->sendToAdmins(
            companyId: $transfer->company_id,
            title: "🔄 Stock Transfer: {$transfer->transfer_number}",
            body: "{$initiator->name} transferred {$qty} unit(s) from {$from} → {$to}.",
            icon: 'heroicon-o-arrows-right-left',
            color: 'info',
            url: $this->projectUrl($transfer->cde_project_id, 'transfers'),
            exclude: $initiator->id,
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STOCK ADJUSTMENT EVENTS
    // ─────────────────────────────────────────────────────────────────────────

    /** Stock adjusted */
    public function stockAdjusted(StockAdjustment $adj, User $performer): void
    {
        $product = $adj->product?->name ?? 'Product';
        $change = $adj->quantity_change >= 0 ? "+{$adj->quantity_change}" : (string) $adj->quantity_change;
        $color = $adj->quantity_change < 0 ? 'danger' : 'success';

        $this->sendToAdmins(
            companyId: $adj->company_id,
            title: "⚖ Stock Adjusted: {$product} ({$change})",
            body: "{$performer->name} adjusted stock for {$product} in {$adj->warehouse?->name}. Reason: {$adj->reason}",
            icon: 'heroicon-o-scale',
            color: $color,
            url: $this->projectUrl($adj->cde_project_id, 'adjustments'),
            exclude: $performer->id,
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LOW-STOCK ALERT
    // ─────────────────────────────────────────────────────────────────────────

    /** Low stock alert for a product falling below reorder level */
    public function lowStockAlert(int $companyId, int $projectId, string $productName, float $onHand, float $reorderLevel): void
    {
        $this->sendToAdmins(
            companyId: $companyId,
            title: "⚠ Low Stock Alert: {$productName}",
            body: "Stock for {$productName} has dropped to {$onHand} units (minimum: {$reorderLevel}). Please raise a purchase order.",
            icon: 'heroicon-o-exclamation-triangle',
            color: 'danger',
            url: $this->projectUrl($projectId, 'adjustments'),
        );
    }

    /** Over-stock alert when stock exceeds the max order level */
    public function overStockAlert(int $companyId, int $projectId, string $productName, float $onHand, float $maxLevel): void
    {
        $this->sendToAdmins(
            companyId: $companyId,
            title: "↑ Over-Stock Alert: {$productName}",
            body: "Stock for {$productName} is at {$onHand} units, exceeding the max level of {$maxLevel}. Consider re-distributing or halting orders.",
            icon: 'heroicon-o-arrow-up-circle',
            color: 'warning',
            url: $this->projectUrl($projectId, 'stock_monitor'),
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ASSET EVENTS
    // ─────────────────────────────────────────────────────────────────────────

    /** Asset checked out to a user */
    public function assetCheckedOut(Asset $asset, User $holder, User $issuedBy): void
    {
        // Notify holder
        if ($holder->id !== $issuedBy->id) {
            $this->sendToUser(
                user: $holder,
                title: "🏷 Asset Assigned to You: {$asset->display_name}",
                body: "{$asset->display_name} (Tag: {$asset->asset_tag}) has been assigned to you by {$issuedBy->name}.",
                icon: 'heroicon-o-tag',
                color: 'info',
                url: $this->projectUrl($asset->cde_project_id, 'assets'),
            );
        }
    }

    /** Asset checked in */
    public function assetCheckedIn(Asset $asset, User $checkedInBy): void
    {
        $this->sendToAdmins(
            companyId: $asset->company_id,
            title: "✅ Asset Returned: {$asset->display_name}",
            body: "{$asset->display_name} (Tag: {$asset->asset_tag}) has been returned/checked in by {$checkedInBy->name}.",
            icon: 'heroicon-o-arrow-down-tray',
            color: 'success',
            url: $this->projectUrl($asset->cde_project_id, 'assets'),
            exclude: $checkedInBy->id,
        );
    }

    /** Asset sent for maintenance */
    public function assetMaintenance(Asset $asset, User $loggedBy, string $type): void
    {
        $this->sendToAdmins(
            companyId: $asset->company_id,
            title: "🔧 Asset Maintenance: {$asset->display_name}",
            body: "{$loggedBy->name} logged a {$type} for {$asset->display_name} (Tag: {$asset->asset_tag}).",
            icon: 'heroicon-o-wrench-screwdriver',
            color: 'warning',
            url: $this->projectUrl($asset->cde_project_id, 'assets'),
            exclude: $loggedBy->id,
        );
    }

    /** Asset disposed / retired / lost */
    public function assetDisposed(Asset $asset, User $disposedBy, string $mode): void
    {
        $labels = ['retire' => 'Retired', 'dispose' => 'Disposed', 'lost' => 'Reported Lost'];
        $label = $labels[$mode] ?? ucfirst($mode);
        $icons = ['retire' => 'heroicon-o-pause-circle', 'dispose' => 'heroicon-o-trash', 'lost' => 'heroicon-o-exclamation-triangle'];

        $this->sendToAdmins(
            companyId: $asset->company_id,
            title: "🏷 Asset {$label}: {$asset->display_name}",
            body: "{$disposedBy->name} marked {$asset->display_name} (Tag: {$asset->asset_tag}) as {$label}.",
            icon: $icons[$mode] ?? 'heroicon-o-trash',
            color: $mode === 'lost' ? 'danger' : 'warning',
            url: $this->projectUrl($asset->cde_project_id, 'assets'),
            exclude: $disposedBy->id,
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function sendToAdmins(
        int $companyId,
        string $title,
        string $body,
        string $icon,
        string $color,
        ?string $url = null,
        ?int $exclude = null,
    ): void {
        $admins = \App\Models\User::where('company_id', $companyId)
            ->where('is_active', true)
            ->whereIn('user_type', ['company_admin', 'super_admin'])
            ->when($exclude, fn($q) => $q->where('id', '!=', $exclude))
            ->get();

        foreach ($admins as $admin) {
            /** @var User $admin */
            $this->dispatchInApp($admin, $title, $body, $icon, $color, $url);
        }
    }

    private function sendToUser(
        User $user,
        string $title,
        string $body,
        string $icon,
        string $color,
        ?string $url = null,
    ): void {
        $this->dispatchInApp($user, $title, $body, $icon, $color, $url);
    }

    private function dispatchInApp(
        User $user,
        string $title,
        string $body,
        string $icon,
        string $color,
        ?string $url = null,
    ): void {
        try {
            $notif = Notification::make()
                ->title($title)
                ->body($body)
                ->icon($icon)
                ->iconColor($color);

            if ($url) {
                $notif->actions([
                    FilamentAction::make('view')
                        ->label('View')
                        ->url($url),
                ]);
            }

            $notif->sendToDatabase($user);
        } catch (\Throwable $e) {
            Log::warning("InventoryNotification failed for {$user->email}: {$e->getMessage()}");
        }
    }

    private function projectUrl(int $projectId, string $tab): string
    {
        return "/app/cde-projects/{$projectId}/inventory?tab={$tab}";
    }

    private function fmt(float|string|null $amount): string
    {
        return 'UGX ' . number_format((float) ($amount ?? 0), 0);
    }
}
