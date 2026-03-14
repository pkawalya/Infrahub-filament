<?php

namespace App\Console\Commands;

use App\Models\{
    Company,
    CdeProject,
    User,
    Warehouse,
    Supplier,
    Product,
    PurchaseOrder,
    PurchaseOrderItem,
    GoodsReceivedNote,
    GrnItem,
    MaterialRequisition,
    MaterialRequisitionItem,
    MaterialIssuance,
    MaterialIssuanceItem,
    StockLevel,
    StockAdjustment,
    StockTransfer,
    StockTransferItem,
    DeliveryNote,
    DeliveryNoteItem,
    ProductTracking
};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestInventoryWorkflow extends Command
{
    protected $signature = 'inventory:test-workflow {--cleanup : Clean up test data after run}';
    protected $description = 'Run a full end-to-end inventory workflow test (PR → PO → GRN → Issuance → Delivery)';

    private int $cid;
    private int $pid;
    private int $uid;
    private array $createdIds = [];

    public function handle(): int
    {
        $this->info('');
        $this->line('╔══════════════════════════════════════════════════════════╗');
        $this->line('║        INFRAHUB INVENTORY MODULE — FULL WORKFLOW TEST      ║');
        $this->line('╚══════════════════════════════════════════════════════════╝');
        $this->info('');

        // ── Bootstrap ─────────────────────────────────────────────────────────
        $company = Company::first();
        $project = CdeProject::first();
        $user = User::first();

        if (!$company || !$project || !$user) {
            $this->error('No company, project or user found. Please seed the database first.');
            return 1;
        }

        $this->cid = $company->id;
        $this->pid = $project->id;
        $this->uid = $user->id;

        $this->comment("Company  : {$company->name} (ID:{$this->cid})");
        $this->comment("Project  : {$project->name} (ID:{$this->pid})");
        $this->comment("User     : {$user->name} (ID:{$this->uid})");
        $this->info('');

        DB::beginTransaction();
        try {
            $warehouse = $this->step1_warehouse();
            $warehouse2 = $this->step1b_second_warehouse();
            $supplier = $this->step2_supplier();
            $product = $this->step3_product();
            $product2 = $this->step3b_second_product();

            $po = $this->step4_create_po($supplier, $product, $product2);
            $this->step5_submit_po($po);
            $this->step6_approve_po($po);
            $this->step7_mark_ordered($po);

            $grn = $this->step8_create_grn($po, $product, $product2, $warehouse);

            $req = $this->step9_material_requisition($product, $product2, $warehouse);
            $this->step10_approve_requisition($req);

            $issuance = $this->step11_issue_materials($req, $product, $warehouse);

            $dn = $this->step12_delivery_note($product, $product2, $warehouse);
            $this->step13_mark_delivered($dn);

            $this->step14_stock_transfer($product, $warehouse, $warehouse2);
            $this->step15_stock_adjustment($product, $warehouse);

            $this->step16_verify_tracking($product);
            $this->step17_verify_stock_levels($product, $warehouse);

            DB::commit();

            $this->info('');
            $this->line('╔══════════════════════════════════════════════════════════╗');
            $this->line('║       ✅  ALL WORKFLOW STAGES PASSED SUCCESSFULLY!        ║');
            $this->line('╚══════════════════════════════════════════════════════════╝');
            $this->info('');

            if ($this->option('cleanup')) {
                $this->cleanup();
            }

            return 0;

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('');
            $this->error('❌ WORKFLOW FAILED: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile() . ':' . $e->getLine());
            $this->error('');
            if ($this->option('verbose')) {
                $this->error($e->getTraceAsString());
            }
            return 1;
        }
    }

    // ── STEP 1 ─────────────────────────────────────────────────────────────────
    private function step1_warehouse(): Warehouse
    {
        $this->section('STEP 1', 'Create / Verify Primary Warehouse');

        $warehouse = Warehouse::firstOrCreate(
            ['company_id' => $this->cid, 'name' => '[TEST] Central Store'],
            ['code' => 'TST-CENTRAL', 'city' => 'Kampala', 'is_active' => true]
        );

        $this->createdIds['warehouse'] = $warehouse->id;
        $this->ok("Warehouse: {$warehouse->name} (ID:{$warehouse->id})");
        return $warehouse;
    }

    private function step1b_second_warehouse(): Warehouse
    {
        $w2 = Warehouse::firstOrCreate(
            ['company_id' => $this->cid, 'name' => '[TEST] Site Store'],
            ['code' => 'TST-SITE', 'city' => 'Jinja', 'is_active' => true]
        );
        $this->createdIds['warehouse2'] = $w2->id;
        $this->ok("Second Warehouse: {$w2->name} (ID:{$w2->id})");
        return $w2;
    }

    // ── STEP 2 ─────────────────────────────────────────────────────────────────
    private function step2_supplier(): Supplier
    {
        $this->section('STEP 2', 'Create / Verify Supplier');

        $supplier = Supplier::firstOrCreate(
            ['company_id' => $this->cid, 'name' => '[TEST] Quality Supplies Ltd'],
            ['email' => 'test@qsl.com', 'phone' => '+256700000001', 'is_active' => true]
        );

        $this->createdIds['supplier'] = $supplier->id;
        $this->ok("Supplier: {$supplier->name} (ID:{$supplier->id})");
        return $supplier;
    }

    // ── STEP 3 ─────────────────────────────────────────────────────────────────
    private function step3_product(): Product
    {
        $this->section('STEP 3', 'Create / Verify Products');

        $product = Product::firstOrCreate(
            ['company_id' => $this->cid, 'sku' => 'TST-PIPE-100'],
            [
                'name' => '[TEST] Steel Pipe 100mm',
                'unit_of_measure' => 'm',
                'cost_price' => 15000,
                'selling_price' => 18000,
                'reorder_level' => 10,
                'is_active' => true,
                'track_inventory' => true,
                'condition' => 'new',
            ]
        );

        $this->createdIds['product'] = $product->id;
        $this->ok("Product 1: {$product->name} | Cost: " . number_format((float) $product->cost_price) . " | Reorder: {$product->reorder_level}");
        return $product;
    }

    private function step3b_second_product(): Product
    {
        $p2 = Product::firstOrCreate(
            ['company_id' => $this->cid, 'sku' => 'TST-VALVE-50'],
            [
                'name' => '[TEST] Butterfly Valve 50mm',
                'unit_of_measure' => 'pc',
                'cost_price' => 45000,
                'selling_price' => 55000,
                'reorder_level' => 5,
                'is_active' => true,
                'track_inventory' => true,
                'condition' => 'new',
            ]
        );
        $this->createdIds['product2'] = $p2->id;
        $this->ok("Product 2: {$p2->name} | Cost: " . number_format((float) $p2->cost_price));
        return $p2;
    }

    // ── STEP 4 ─────────────────────────────────────────────────────────────────
    private function step4_create_po(Supplier $supplier, Product $product, Product $product2): PurchaseOrder
    {
        $this->section('STEP 4', 'Create Purchase Order (Draft)');

        $subtotal = (50 * 15000) + (20 * 45000); // 750000 + 900000
        $suffix = substr((string) (now()->valueOf()), -6); // microsecond-based unique suffix

        $po = PurchaseOrder::create([
            'company_id' => $this->cid,
            'cde_project_id' => $this->pid,
            'po_number' => 'TST-PO-' . $suffix,
            'supplier_id' => $supplier->id,
            'status' => 'draft',
            'order_date' => now()->format('Y-m-d'),
            'expected_date' => now()->addDays(7)->format('Y-m-d'),
            'subtotal' => $subtotal,
            'tax_amount' => 0,
            'shipping_cost' => 25000,
            'total_amount' => $subtotal + 25000,
            'notes' => 'TEST: Full workflow integration test',
            'created_by' => $this->uid,
        ]);

        $this->createdIds['po'] = $po->id;

        // Line items
        $item1 = $po->items()->create([
            'product_id' => $product->id,
            'notes' => 'Steel Pipe for site works',
            'quantity_ordered' => 50,
            'quantity_received' => 0,
            'unit_price' => 15000,
            'total_price' => 750000,
        ]);

        $item2 = $po->items()->create([
            'product_id' => $product2->id,
            'notes' => 'Valves for pump room',
            'quantity_ordered' => 20,
            'quantity_received' => 0,
            'unit_price' => 45000,
            'total_price' => 900000,
        ]);

        // Track → ordered
        foreach ([$product->id, $product2->id] as $pId) {
            ProductTracking::create([
                'company_id' => $this->cid,
                'cde_project_id' => $this->pid,
                'product_id' => $pId,
                'stage' => 'ordered',
                'purchase_order_id' => $po->id,
                'quantity' => $pId === $product->id ? 50 : 20,
                'notes' => 'Ordered via ' . $po->po_number,
                'recorded_by' => $this->uid,
            ]);
        }

        $this->ok("PO: {$po->po_number} | Total: UGX " . number_format((float) $po->total_amount) . " | Items: 2");
        $this->ok("  ↳ Item 1: 50× {$product->name} @ UGX 15,000");
        $this->ok("  ↳ Item 2: 20× {$product2->name} @ UGX 45,000");
        $this->ok("  ↳ ProductTracking [ordered] for both products");

        return $po;
    }

    // ── STEP 5 ─────────────────────────────────────────────────────────────────
    private function step5_submit_po(PurchaseOrder $po): void
    {
        $this->section('STEP 5', 'Submit PO for Approval');

        if (!$po->canBeSubmitted()) {
            throw new \RuntimeException("PO {$po->po_number} cannot be submitted (status: {$po->status})");
        }

        $po->update(['status' => 'submitted', 'submitted_at' => now(), 'rejection_reason' => null]);
        $fresh = $po->fresh();

        $this->assertField('PO status', $fresh->status, 'submitted');
        $this->ok("PO submitted at: {$fresh->submitted_at}");
    }

    // ── STEP 6 ─────────────────────────────────────────────────────────────────
    private function step6_approve_po(PurchaseOrder $po): void
    {
        $this->section('STEP 6', 'Approve Purchase Order');

        if (!$po->canBeApproved()) {
            throw new \RuntimeException("PO cannot be approved (status: {$po->status})");
        }

        $po->update(['status' => 'approved', 'approved_by' => $this->uid, 'approved_at' => now()]);
        $fresh = $po->fresh();

        $this->assertField('PO status', $fresh->status, 'approved');
        $this->assertField('approved_by', (string) $fresh->approved_by, (string) $this->uid);
        $this->ok("PO approved at: {$fresh->approved_at}");
    }

    // ── STEP 7 ─────────────────────────────────────────────────────────────────
    private function step7_mark_ordered(PurchaseOrder $po): void
    {
        $this->section('STEP 7', 'Mark PO as "Ordered" (dispatched to supplier)');

        $po->update(['status' => 'ordered']);
        $this->assertField('PO status', $po->fresh()->status, 'ordered');
        $this->ok("PO is now in transit with supplier");
    }

    // ── STEP 8 ─────────────────────────────────────────────────────────────────
    private function step8_create_grn(PurchaseOrder $po, Product $product, Product $product2, Warehouse $warehouse): GoodsReceivedNote
    {
        $this->section('STEP 8', 'Create Goods Received Note (GRN) — Partial Receipt');

        $suffix = substr((string) (now()->valueOf()), -6);

        $grn = GoodsReceivedNote::create([
            'company_id' => $this->cid,
            'cde_project_id' => $this->pid,
            'grn_number' => 'TST-GRN-' . $suffix,
            'purchase_order_id' => $po->id,
            'supplier_id' => $po->supplier_id,
            'warehouse_id' => $warehouse->id,
            'status' => 'accepted',
            'received_date' => now()->format('Y-m-d'),
            'delivery_note_ref' => 'SUPP-DN-2024-TEST',
            'notes' => 'Partial delivery — pipes OK, 2 valves damaged',
            'received_by' => $this->uid,
        ]);

        $this->createdIds['grn'] = $grn->id;

        $po->load('items');
        $items = $po->items;

        // Product 1: 50 ordered, 50 received, 0 rejected → fully received
        $item1 = $items->firstWhere('product_id', $product->id);
        $grn->items()->create([
            'purchase_order_item_id' => $item1->id,
            'product_id' => $product->id,
            'description' => $product->name,
            'quantity_expected' => 50,
            'quantity_received' => 50,
            'quantity_accepted' => 50,
            'quantity_rejected' => 0,
            'condition' => 'good',
            'rejection_reason' => null,
        ]);
        $item1->increment('quantity_received', 50);

        // Product 2: 20 ordered, 20 received, 2 rejected → 18 accepted
        $item2 = $items->firstWhere('product_id', $product2->id);
        $grn->items()->create([
            'purchase_order_item_id' => $item2->id,
            'product_id' => $product2->id,
            'description' => $product2->name,
            'quantity_expected' => 20,
            'quantity_received' => 20,
            'quantity_accepted' => 18,
            'quantity_rejected' => 2,
            'condition' => 'mixed',
            'rejection_reason' => '2 units damaged upon delivery',
        ]);
        $item2->increment('quantity_received', 18);

        // Update stock levels
        foreach ([[$product->id, 50], [$product2->id, 18]] as [$pId, $qtyCR]) {
            $stock = StockLevel::firstOrCreate(
                ['product_id' => $pId, 'warehouse_id' => $warehouse->id],
                ['quantity_on_hand' => 0, 'quantity_reserved' => 0, 'quantity_available' => 0]
            );
            $stock->increment('quantity_on_hand', $qtyCR);
            $stock->increment('quantity_available', $qtyCR);

            ProductTracking::create([
                'company_id' => $this->cid,
                'cde_project_id' => $this->pid,
                'product_id' => $pId,
                'stage' => 'received',
                'purchase_order_id' => $po->id,
                'quantity' => $qtyCR,
                'location' => $warehouse->name,
                'notes' => 'Received via GRN ' . $grn->grn_number,
                'recorded_by' => $this->uid,
            ]);
        }

        // Update PO status
        $po->load('items');
        $allReceived = $po->items->every(fn($i) => $i->fresh()->quantity_received >= $i->quantity_ordered);
        $anyReceived = $po->items->contains(fn($i) => $i->fresh()->quantity_received > 0);
        $newStatus = $anyReceived && !$allReceived ? 'partially_received' : ($allReceived ? 'received' : $po->status);
        $po->update(['status' => $newStatus]);

        $stock1 = StockLevel::where('product_id', $product->id)->where('warehouse_id', $warehouse->id)->first();
        $stock2 = StockLevel::where('product_id', $product2->id)->where('warehouse_id', $warehouse->id)->first();

        $this->ok("GRN: {$grn->grn_number}");
        $this->ok("  ↳ {$product->name}: 50/50 received → Stock: {$stock1?->quantity_on_hand}");
        $this->ok("  ↳ {$product2->name}: 18/20 accepted (2 rejected) → Stock: {$stock2?->quantity_on_hand}");
        $this->ok("  ↳ PO Status → " . $po->fresh()->status);

        return $grn;
    }

    // ── STEP 9 ─────────────────────────────────────────────────────────────────
    private function step9_material_requisition(Product $product, Product $product2, Warehouse $warehouse): MaterialRequisition
    {
        $this->section('STEP 9', 'Create Material Requisition (REQ)');

        $suffix = substr((string) (now()->valueOf()), -6);

        $req = MaterialRequisition::create([
            'company_id' => $this->cid,
            'cde_project_id' => $this->pid,
            'requisition_number' => 'TST-REQ-' . $suffix,
            'requester_id' => $this->uid,
            'warehouse_id' => $warehouse->id,
            'status' => 'pending',
            'priority' => 'high',
            'purpose' => 'Site construction — pump room installation',
            'required_date' => now()->addDays(2)->format('Y-m-d'),
            'notes' => 'Urgent — pump room works starting Monday',
        ]);

        $this->createdIds['req'] = $req->id;

        $req->items()->create([
            'product_id' => $product->id,
            'quantity_requested' => 30,
            'notes' => 'For water supply line installation',
        ]);

        $req->items()->create([
            'product_id' => $product2->id,
            'quantity_requested' => 10,
            'notes' => 'Pump room inlet/outlet valves',
        ]);

        $this->ok("Requisition: {$req->requisition_number} | Priority: {$req->priority}");
        $this->ok("  ↳ 30× {$product->name}");
        $this->ok("  ↳ 10× {$product2->name}");

        return $req;
    }

    // ── STEP 10 ────────────────────────────────────────────────────────────────
    private function step10_approve_requisition(MaterialRequisition $req): void
    {
        $this->section('STEP 10', 'Approve Material Requisition');

        if ($req->status !== 'pending') {
            throw new \RuntimeException("REQ not pending (status: {$req->status})");
        }

        $req->update([
            'status' => 'approved',
            'approved_by' => $this->uid,
            'approved_at' => now(),
        ]);

        foreach ($req->items as $item) {
            $item->update(['quantity_approved' => $item->quantity_requested]);
        }

        $fresh = $req->fresh()->load('items');
        $this->assertField('REQ status', $fresh->status, 'approved');
        $this->ok("All items approved for full quantities");
    }

    // ── STEP 11 ────────────────────────────────────────────────────────────────
    private function step11_issue_materials(MaterialRequisition $req, Product $product, Warehouse $warehouse): MaterialIssuance
    {
        $this->section('STEP 11', 'Issue Materials (Against Approved Requisition)');

        // Validate requisition is approved
        $req->load('items');
        if (!in_array($req->status, ['approved', 'partially_issued'])) {
            throw new \RuntimeException("Cannot issue against REQ with status: {$req->status}");
        }

        $suffix = substr((string) (now()->valueOf()), -6);

        $issuance = MaterialIssuance::create([
            'company_id' => $this->cid,
            'cde_project_id' => $this->pid,
            'issuance_number' => 'TST-ISS-' . $suffix,
            'warehouse_id' => $warehouse->id,
            'material_requisition_id' => $req->id,
            'issued_to' => $this->uid,
            'issued_to_name' => null,
            'purpose' => 'site_use',
            'status' => 'issued',
            'issue_date' => now()->format('Y-m-d'),
            'notes' => "Issued against {$req->requisition_number}",
            'created_by' => $this->uid,
        ]);

        $this->createdIds['issuance'] = $issuance->id;

        // Issue 25 pipes (partial of 30 requested)
        $reqItem1 = $req->items->firstWhere('product_id', $product->id);
        $issueQty = 25;

        // Validate stock
        $stock = StockLevel::where('product_id', $product->id)->where('warehouse_id', $warehouse->id)->first();
        if (!$stock || $stock->quantity_available < $issueQty) {
            throw new \RuntimeException("Insufficient stock: available=" . ($stock?->quantity_available ?? 0) . ", requested={$issueQty}");
        }

        $issuance->items()->create([
            'product_id' => $product->id,
            'quantity_issued' => $issueQty,
            'quantity_returned' => 0,
            'condition_on_issue' => 'good',
        ]);

        // Decrement stock
        $stock->decrement('quantity_available', $issueQty);
        $stock->increment('quantity_reserved', $issueQty);

        // ProductTracking
        ProductTracking::create([
            'company_id' => $this->cid,
            'cde_project_id' => $this->pid,
            'product_id' => $product->id,
            'stage' => 'issued',
            'material_issuance_id' => $issuance->id,
            'quantity' => $issueQty,
            'location' => $warehouse->name,
            'notes' => "Issued via {$issuance->issuance_number} against {$req->requisition_number}",
            'recorded_by' => $this->uid,
        ]);

        // Update REQ item + status
        $reqItem1->increment('quantity_issued', $issueQty);
        $reqItem1->refresh();

        $req->load('items');
        $allIssued = $req->items->every(fn($i) => $i->quantity_issued >= ($i->quantity_approved ?? $i->quantity_requested));
        $req->update(['status' => $allIssued ? 'issued' : 'partially_issued']);

        $freshStock = $stock->fresh();
        $this->ok("Issuance: {$issuance->issuance_number}");
        $this->ok("  ↳ 25× {$product->name} issued to site");
        $this->ok("  ↳ REQ {$req->fresh()->requisition_number} status → " . $req->fresh()->status);
        $this->ok("  ↳ Stock: available={$freshStock->quantity_available}, reserved={$freshStock->quantity_reserved}");

        return $issuance;
    }

    // ── STEP 12 ────────────────────────────────────────────────────────────────
    private function step12_delivery_note(Product $product, Product $product2, Warehouse $warehouse): DeliveryNote
    {
        $this->section('STEP 12', 'Create Delivery Note (DN) — Dispatch to Site');

        $suffix = substr((string) (now()->valueOf()), -6);

        $dn = DeliveryNote::create([
            'company_id' => $this->cid,
            'cde_project_id' => $this->pid,
            'dn_number' => 'TST-DN-' . $suffix,
            'destination' => 'Site Camp — Pump Room',
            'destination_contact' => 'Site Engineer',
            'destination_phone' => '+256700000002',
            'vehicle_number' => 'UBB 123A',
            'driver_name' => 'John Okello',
            'driver_phone' => '+256700000003',
            'warehouse_id' => $warehouse->id,
            'dispatch_date' => now()->format('Y-m-d'),
            'notes' => 'Handle valves with care',
            'status' => 'dispatched',
            'dispatched_by' => $this->uid,
        ]);

        $this->createdIds['dn'] = $dn->id;

        foreach ([[$product, 20], [$product2, 8]] as [$prod, $qty]) {
            $dn->items()->create([
                'product_id' => $prod->id,
                'description' => $prod->name,
                'unit' => $prod->unit_of_measure,
                'quantity_dispatched' => $qty,
                'quantity_received' => 0,
            ]);

            ProductTracking::create([
                'company_id' => $this->cid,
                'cde_project_id' => $this->pid,
                'product_id' => $prod->id,
                'stage' => 'in_transit',
                'delivery_note_id' => $dn->id,
                'quantity' => $qty,
                'location' => 'Site Camp — Pump Room',
                'notes' => "Dispatched via {$dn->dn_number}",
                'recorded_by' => $this->uid,
            ]);
        }

        $this->ok("Delivery Note: {$dn->dn_number}");
        $this->ok("  ↳ 20× {$product->name} | 8× {$product2->name}");
        $this->ok("  ↳ Driver: {$dn->driver_name} | Vehicle: {$dn->vehicle_number}");
        $this->ok("  ↳ ProductTracking [in_transit] for both products");

        return $dn;
    }

    // ── STEP 13 ────────────────────────────────────────────────────────────────
    private function step13_mark_delivered(DeliveryNote $dn): void
    {
        $this->section('STEP 13', 'Mark Delivery Note as Delivered');

        $dn->load('items');
        foreach ($dn->items as $item) {
            $item->update(['quantity_received' => $item->quantity_dispatched, 'condition' => 'good']);

            if ($item->product_id) {
                ProductTracking::create([
                    'company_id' => $this->cid,
                    'cde_project_id' => $this->pid,
                    'product_id' => $item->product_id,
                    'stage' => 'delivered',
                    'delivery_note_id' => $dn->id,
                    'quantity' => $item->quantity_dispatched,
                    'location' => $dn->destination,
                    'notes' => "Delivered via {$dn->dn_number}",
                    'recorded_by' => $this->uid,
                ]);
            }
        }

        $dn->update(['status' => 'delivered', 'delivery_date' => now(), 'received_by_user' => $this->uid]);
        $this->assertField('DN status', $dn->fresh()->status, 'delivered');
        $this->ok("All items marked as delivered at: {$dn->destination}");
    }

    // ── STEP 14 ────────────────────────────────────────────────────────────────
    private function step14_stock_transfer(Product $product, Warehouse $from, Warehouse $to): void
    {
        $this->section('STEP 14', 'Stock Transfer (Warehouse-to-Warehouse)');

        $suffix = substr((string) (now()->valueOf()), -6);

        $transfer = StockTransfer::create([
            'company_id' => $this->cid,
            'cde_project_id' => $this->pid,
            'transfer_number' => 'TST-TRF-' . $suffix,
            'from_warehouse_id' => $from->id,
            'to_warehouse_id' => $to->id,
            'status' => 'received',
            'priority' => 'normal',
            'transfer_date' => now(),
            'requested_date' => now(),
            'received_date' => now(),
            'reason' => 'Replenish site store',
            'created_by' => $this->uid,
            'requested_by' => $this->uid,
            'received_by' => $this->uid,
        ]);

        $this->createdIds['transfer'] = $transfer->id;

        $qty = 5;
        $transfer->items()->create([
            'product_id' => $product->id,
            'quantity_requested' => $qty,
            'quantity_shipped' => $qty,
            'quantity_received' => $qty,
        ]);

        // Deduct from source
        $fromStock = StockLevel::where('product_id', $product->id)->where('warehouse_id', $from->id)->first();
        if ($fromStock) {
            $fromStock->decrement('quantity_on_hand', min($qty, $fromStock->quantity_on_hand));
            $fromStock->decrement('quantity_available', min($qty, $fromStock->quantity_available));
        }

        // Add to destination
        $toStock = StockLevel::firstOrCreate(
            ['product_id' => $product->id, 'warehouse_id' => $to->id],
            ['quantity_on_hand' => 0, 'quantity_reserved' => 0, 'quantity_available' => 0]
        );
        $toStock->increment('quantity_on_hand', $qty);
        $toStock->increment('quantity_available', $qty);

        $from->refresh();
        $to->refresh();
        $this->ok("Transfer: {$transfer->transfer_number}");
        $this->ok("  ↳ {$qty}× {$product->name}: {$from->name} → {$to->name}");
        $this->ok("  ↳ {$from->name} stock: on_hand=" . $fromStock?->fresh()->quantity_on_hand);
        $this->ok("  ↳ {$to->name} stock: on_hand=" . $toStock->fresh()->quantity_on_hand);
    }

    // ── STEP 15 ────────────────────────────────────────────────────────────────
    private function step15_stock_adjustment(Product $product, Warehouse $warehouse): void
    {
        $this->section('STEP 15', 'Stock Adjustment (Cycle Count / Correction)');

        $stock = StockLevel::where('product_id', $product->id)->where('warehouse_id', $warehouse->id)->first();
        $before = $stock?->quantity_on_hand ?? 0;
        $after = $before + 3; // Count found 3 extra
        $change = $after - $before;

        $suffix = substr((string) (now()->valueOf()), -6);

        StockAdjustment::create([
            'company_id' => $this->cid,
            'cde_project_id' => $this->pid,
            'adjustment_number' => 'TST-ADJ-' . $suffix,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'type' => 'count',
            'quantity_before' => $before,
            'quantity_after' => $after,
            'quantity_change' => $change,
            'reason' => 'Cycle count found 3 additional units',
            'notes' => 'Monthly stock count — discrepancy resolved',
            'performed_by' => $this->uid,
        ]);

        if ($stock) {
            $stock->update([
                'quantity_on_hand' => $after,
                'quantity_available' => max(0, $after - $stock->quantity_reserved),
            ]);
        }

        $this->ok("Adjustment: before={$before} → after={$after} (change: +{$change})");
        $this->ok("  ↳ {$product->name} in {$warehouse->name}");
    }

    // ── STEP 16 ────────────────────────────────────────────────────────────────
    private function step16_verify_tracking(Product $product): void
    {
        $this->section('STEP 16', 'Verify Product Lifecycle Tracking Timeline');

        $timeline = ProductTracking::where('product_id', $product->id)
            ->where('cde_project_id', $this->pid)
            ->orderBy('created_at')
            ->get();

        $this->ok("ProductTracking entries for {$product->name}: {$timeline->count()}");

        $stages = $timeline->pluck('stage')->unique()->values()->toArray();
        $this->ok("  ↳ Stages: " . implode(' → ', $stages));

        $expectedStages = ['ordered', 'received', 'issued', 'in_transit', 'delivered'];
        foreach ($expectedStages as $stage) {
            if (!in_array($stage, $stages)) {
                $this->warn("  ⚠ Missing stage: {$stage}");
            } else {
                $this->ok("  ✓ Stage [{$stage}]: ✅");
            }
        }
    }

    // ── STEP 17 ────────────────────────────────────────────────────────────────
    private function step17_verify_stock_levels(Product $product, Warehouse $warehouse): void
    {
        $this->section('STEP 17', 'Final Stock Level Verification');

        $stock = StockLevel::where('product_id', $product->id)->where('warehouse_id', $warehouse->id)->first();

        $this->ok("{$product->name} @ {$warehouse->name}:");
        $this->ok("  ↳ on_hand:   " . ($stock?->quantity_on_hand ?? 'N/A'));
        $this->ok("  ↳ reserved:  " . ($stock?->quantity_reserved ?? 'N/A'));
        $this->ok("  ↳ available: " . ($stock?->quantity_available ?? 'N/A'));

        if ($stock && $stock->quantity_on_hand < 0) {
            throw new \RuntimeException("INVALID: quantity_on_hand is negative ({$stock->quantity_on_hand})");
        }
        if ($stock && $stock->quantity_available < 0) {
            throw new \RuntimeException("INVALID: quantity_available is negative ({$stock->quantity_available})");
        }

        $this->ok("  ✅ All stock levels are non-negative — integrity OK");
    }

    // ── CLEANUP ────────────────────────────────────────────────────────────────
    private function cleanup(): void
    {
        $this->info('');
        $this->comment('── Cleaning up test data ──');

        // Clean in reverse dependency order
        if (isset($this->createdIds['dn'])) {
            DeliveryNoteItem::where('delivery_note_id', $this->createdIds['dn'])->delete();
            DeliveryNote::destroy($this->createdIds['dn']);
        }
        if (isset($this->createdIds['issuance'])) {
            MaterialIssuanceItem::where('material_issuance_id', $this->createdIds['issuance'])->delete();
            MaterialIssuance::destroy($this->createdIds['issuance']);
        }
        if (isset($this->createdIds['req'])) {
            MaterialRequisitionItem::where('material_requisition_id', $this->createdIds['req'])->delete();
            MaterialRequisition::destroy($this->createdIds['req']);
        }
        if (isset($this->createdIds['grn'])) {
            GrnItem::where('goods_received_note_id', $this->createdIds['grn'])->delete();
            GoodsReceivedNote::destroy($this->createdIds['grn']);
        }
        if (isset($this->createdIds['transfer'])) {
            StockTransferItem::where('stock_transfer_id', $this->createdIds['transfer'])->delete();
            StockTransfer::destroy($this->createdIds['transfer']);
        }
        if (isset($this->createdIds['po'])) {
            PurchaseOrderItem::where('purchase_order_id', $this->createdIds['po'])->delete();
            PurchaseOrder::destroy($this->createdIds['po']);
        }

        foreach (['product', 'product2'] as $key) {
            if (isset($this->createdIds[$key])) {
                ProductTracking::where('product_id', $this->createdIds[$key])
                    ->where('cde_project_id', $this->pid)->delete();
                StockLevel::where('product_id', $this->createdIds[$key])->delete();
                StockAdjustment::where('product_id', $this->createdIds[$key])->delete();
                Product::destroy($this->createdIds[$key]);
            }
        }

        foreach (['supplier', 'warehouse', 'warehouse2'] as $key) {
            if (isset($this->createdIds[$key])) {
                if ($key === 'supplier')
                    Supplier::destroy($this->createdIds[$key]);
                else
                    Warehouse::destroy($this->createdIds[$key]);
            }
        }

        $this->ok('Test data cleaned up successfully.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    private function section(string $step, string $title): void
    {
        $this->info('');
        $this->line("── {$step}: {$title} " . str_repeat('─', max(0, 58 - strlen($step) - strlen($title))));
    }

    private function ok(string $msg): void
    {
        $this->line("  <fg=green>✓</> {$msg}");
    }

    private function assertField(string $label, string $actual, string $expected): void
    {
        if ($actual !== $expected) {
            throw new \RuntimeException("{$label} expected '{$expected}', got '{$actual}'");
        }
        $this->ok("{$label}: {$actual}");
    }
}
