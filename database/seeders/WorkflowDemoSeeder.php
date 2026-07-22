<?php

namespace Database\Seeders;

use App\Models\CdeFolder;
use App\Models\CdeProject;
use App\Models\ChangeOrder;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Drawing;
use App\Models\Invoice;
use App\Models\Ncr;
use App\Models\Tender;
use App\Models\User;
use App\Models\Vendor;
use App\Models\WorkOrder;
use Illuminate\Database\Seeder;

class WorkflowDemoSeeder extends Seeder
{
    public function run(): void
    {
        $company = \App\Models\Company::where('slug', 'test-company-ltd')->first();

        if (!$company) {
            $this->command->error('Run TestCompanySeeder first.');
            return;
        }

        $cid = $company->id;

        $users = User::where('company_id', $cid)->get();
        $admin = $users->where('user_type', 'company_admin')->first();
        $manager = $users->where('user_type', 'manager')->first() ?? $admin;
        $tech = $users->where('user_type', 'technician')->first() ?? $admin;

        $projects = CdeProject::where('company_id', $cid)->get();
        if ($projects->isEmpty()) {
            $this->command->error('No projects found for test company.');
            return;
        }

        $client = Client::where('company_id', $cid)->first();
        if (!$client) {
            $client = Client::create([
                'company_id' => $cid, 'name' => 'Ministry of Works',
                'email' => 'permsec@works.go.ug', 'is_active' => true,
            ]);
        }

        // ═══════════════════════════════════════════════════════════
        // 1. NCRs — ISO 9001 CAPA Workflow
        // ═══════════════════════════════════════════════════════════
        $this->command->info('Seeding NCRs ...');

        $ncrs = [
            ['ncr_number' => 'NCR-2026-001', 'title' => 'Concrete strength below spec on pier P12',
             'type' => 'product', 'severity' => 'major', 'status' => 'open',
             'description' => '28-day compressive test results for pier P12 showed 22 MPa vs spec 30 MPa.',
             'assigned_to' => $tech->id, 'due_date' => now()->addDays(14)],
            ['ncr_number' => 'NCR-2026-002', 'title' => 'Welding defects on main beam splices',
             'type' => 'product', 'severity' => 'critical', 'status' => 'investigating',
             'description' => 'UT scan revealed lack of fusion at multiple splice joints on girder G3.',
             'root_cause' => 'Welder qualification not verified for this procedure.',
             'assigned_to' => $tech->id, 'due_date' => now()->addDays(7)],
            ['ncr_number' => 'NCR-2026-003', 'title' => 'Reinforcement spacing out of tolerance',
             'type' => 'product', 'severity' => 'minor', 'status' => 'corrective_action',
             'description' => 'Top reinforcement in slab S2 has 180mm spacing vs 150mm specified.',
             'root_cause' => 'Incorrect spacer placement during steel fixing.',
             'corrective_action' => 'Remove and re-fix bars at correct spacing per the bending schedule.',
             'assigned_to' => $tech->id, 'due_date' => now()->addDays(3)],
            ['ncr_number' => 'NCR-2026-004', 'title' => 'Waterproof membrane damaged during backfill',
             'type' => 'process', 'severity' => 'major', 'status' => 'verified',
             'description' => 'Backfill operation tore the waterproof membrane on retaining wall RW3.',
             'root_cause' => 'Protection board not installed before backfill.',
             'corrective_action' => 'Membrane repaired, protection board installed, backfill redone.',
             'verification_notes' => 'Visual inspection confirms repair. Water test passed.',
             'assigned_to' => $tech->id, 'verified_by' => $manager->id,
             'verified_at' => now()->subDay(), 'due_date' => now()->subDay()],
            ['ncr_number' => 'NCR-2026-005', 'title' => 'Door frame installation out of plumb',
             'type' => 'product', 'severity' => 'minor', 'status' => 'closed',
             'description' => 'Five door frames on Level 2 are 8-12mm out of plumb.',
             'root_cause' => 'Frames not shimmed correctly during installation.',
             'corrective_action' => 'Frames removed, shimmed, and reinstalled to within tolerance.',
             'verification_notes' => 'All five frames rechecked — within 3mm tolerance.',
             'closure_notes' => 'Accepted by QA. Photo evidence attached.',
             'assigned_to' => $tech->id, 'verified_by' => $manager->id,
             'verified_at' => now()->subDays(3), 'closed_at' => now()->subDay(),
             'due_date' => now()->subDays(5)],
        ];

        $project1 = $projects->first();
        foreach ($ncrs as $data) {
            Ncr::firstOrCreate(
                ['ncr_number' => $data['ncr_number'], 'company_id' => $cid],
                array_merge($data, [
                    'company_id' => $cid,
                    'cde_project_id' => $projects[rand(0, $projects->count() - 1)]->id,
                    'reported_by' => $admin->id,
                ])
            );
        }

        // ═══════════════════════════════════════════════════════════
        // 2. Drawings — ISO 19650 Status Transitions
        // ═══════════════════════════════════════════════════════════
        $this->command->info('Seeding drawings ...');

        $drawings = [
            ['drawing_number' => 'KJE-002-C-001', 'title' => 'Pier P12 Reinforcement Details',
             'discipline' => 'structural', 'current_revision' => 'B', 'status' => 'approved'],
            ['drawing_number' => 'KJE-002-C-002', 'title' => 'Girder G3 Splice Details',
             'discipline' => 'structural', 'current_revision' => 'A', 'status' => 'for_review'],
            ['drawing_number' => 'KJE-002-A-001', 'title' => 'General Arrangement - Plan View',
             'discipline' => 'architectural', 'current_revision' => 'C', 'status' => 'ifc'],
            ['drawing_number' => 'KJE-002-M-001', 'title' => 'MEP Rough-in Schematic',
             'discipline' => 'mechanical', 'current_revision' => 'A', 'status' => 'wip'],
            ['drawing_number' => 'KJE-002-E-001', 'title' => 'Lighting Layout - Level 1',
             'discipline' => 'electrical', 'current_revision' => 'B', 'status' => 'as_built'],
            ['drawing_number' => 'KJE-002-S-001', 'title' => 'Site Grading & Drainage Plan',
             'discipline' => 'civil', 'current_revision' => 'A', 'status' => 'superseded'],
        ];

        foreach ($drawings as $data) {
            Drawing::firstOrCreate(
                ['drawing_number' => $data['drawing_number'], 'company_id' => $cid],
                array_merge($data, [
                    'company_id' => $cid,
                    'cde_project_id' => $project1->id,
                    'drawn_by' => $tech->id,
                    'drawn_date' => now()->subDays(rand(10, 60)),
                ])
            );
        }

        // ═══════════════════════════════════════════════════════════
        // 3. Invoices — ISO 9001 Status Transitions
        // ═══════════════════════════════════════════════════════════
        $this->command->info('Seeding invoices ...');

        $invoices = [
            ['invoice_number' => 'INV-2026-001', 'status' => 'paid', 'total_amount' => 4500000],
            ['invoice_number' => 'INV-2026-002', 'status' => 'sent', 'total_amount' => 2200000],
            ['invoice_number' => 'INV-2026-003', 'status' => 'draft', 'total_amount' => 3800000],
            ['invoice_number' => 'INV-2026-004', 'status' => 'partially_paid', 'total_amount' => 6100000, 'amount_paid' => 3000000],
            ['invoice_number' => 'INV-2026-005', 'status' => 'overdue', 'total_amount' => 1800000],
        ];

        foreach ($invoices as $data) {
            Invoice::firstOrCreate(
                ['invoice_number' => $data['invoice_number'], 'company_id' => $cid],
                array_merge($data, [
                    'company_id' => $cid,
                    'client_id' => $client->id,
                    'issue_date' => now()->subDays(rand(5, 45)),
                ])
            );
        }

        // ═══════════════════════════════════════════════════════════
        // 4. Work Orders — ISO 9001 Status Transitions
        // ═══════════════════════════════════════════════════════════
        $this->command->info('Seeding work orders ...');

        $workOrders = [
            ['wo_number' => 'WO-TST-001', 'title' => 'Concrete pour - Pier P12', 'status' => 'completed', 'priority' => 'high'],
            ['wo_number' => 'WO-TST-002', 'title' => 'Steel fixing - Deck slab Section A', 'status' => 'in_progress', 'priority' => 'high'],
            ['wo_number' => 'WO-TST-003', 'title' => 'MEP installation - Pump house', 'status' => 'on_hold', 'priority' => 'medium'],
            ['wo_number' => 'WO-TST-004', 'title' => 'Drainage works - Access road', 'status' => 'approved', 'priority' => 'medium'],
            ['wo_number' => 'WO-TST-005', 'title' => 'Safety signage installation', 'status' => 'pending', 'priority' => 'low'],
            ['wo_number' => 'WO-TST-006', 'title' => 'Crane maintenance & certification', 'status' => 'cancelled', 'priority' => 'critical'],
        ];

        foreach ($workOrders as $data) {
            WorkOrder::firstOrCreate(
                ['wo_number' => $data['wo_number'], 'company_id' => $cid],
                array_merge($data, [
                    'company_id' => $cid,
                    'cde_project_id' => $project1->id,
                    'description' => $data['title'],
                    'assigned_to' => $tech->id,
                    'due_date' => now()->addDays(rand(5, 30)),
                    'created_by' => $admin->id,
                ])
            );
        }

        // ═══════════════════════════════════════════════════════════
        // 5. Tenders — ISO 9001 Status Transitions
        // ═══════════════════════════════════════════════════════════
        $this->command->info('Seeding tenders ...');

        $tenders = [
            ['reference' => 'TND-TST-001', 'title' => 'Kampala Northern Bypass Phase 3',
             'status' => 'awarded', 'client_name' => 'UNRA', 'win_probability' => 100,
             'bid_amount' => 125000000],
            ['reference' => 'TND-TST-002', 'title' => 'Entebbe Airport Cargo Terminal',
             'status' => 'shortlisted', 'client_name' => 'CAA Uganda', 'win_probability' => 65,
             'bid_amount' => 45000000],
            ['reference' => 'TND-TST-003', 'title' => 'Jinja Water Supply Upgrade',
             'status' => 'submitted', 'client_name' => 'NWSC', 'win_probability' => 45,
             'bid_amount' => 28000000],
            ['reference' => 'TND-TST-004', 'title' => 'Gulu District Roads Rehabilitation',
             'status' => 'preparing', 'client_name' => 'UNRA', 'win_probability' => null],
            ['reference' => 'TND-TST-005', 'title' => 'Fort Portal Solar Mini-Grid',
             'status' => 'identified', 'client_name' => 'REA Uganda', 'win_probability' => null],
            ['reference' => 'TND-TST-006', 'title' => 'Tororo Industrial Park Access Road',
             'status' => 'lost', 'client_name' => 'Uganda Development Corp', 'win_probability' => 30,
             'bid_amount' => 18000000],
        ];

        foreach ($tenders as $data) {
            Tender::firstOrCreate(
                ['reference' => $data['reference'], 'company_id' => $cid],
                array_merge($data, [
                    'company_id' => $cid,
                    'created_by' => $admin->id,
                    'assigned_to' => $manager->id,
                ])
            );
        }

        // ═══════════════════════════════════════════════════════════
        // 6. Contracts + ChangeOrders — ISO 19650 Transitions
        // ═══════════════════════════════════════════════════════════
        $this->command->info('Seeding contracts & change orders ...');

        $vendor = Vendor::where('company_id', $cid)->first();
        if (!$vendor) {
            $vendor = Vendor::create([
                'company_id' => $cid, 'name' => 'Kolin Construction Uganda',
                'email' => 'uganda@kolin.com.tr', 'type' => 'subcontractor',
                'payment_terms' => 'Net 45',
            ]);
        }

        $contract = Contract::firstOrCreate(
            ['contract_number' => 'CTR-TST-001', 'company_id' => $cid],
            [
                'company_id' => $cid,
                'vendor_id' => $vendor->id,
                'title' => 'Main Civil Works - Kampala Bypass',
                'type' => 'lump_sum',
                'status' => 'active',
                'start_date' => now()->subMonths(6),
                'end_date' => now()->addMonths(18),
                'original_value' => 85000000,
                'revised_value' => 89200000,
                'created_by' => $admin->id,
            ]
        );

        $changeOrders = [
            ['co_number' => 'CO-TST-001', 'title' => 'Additional earthworks due to soil conditions',
             'status' => 'implemented', 'amount' => 2200000, 'time_extension_days' => 14],
            ['co_number' => 'CO-TST-002', 'title' => 'Redesign of drainage culvert at CH 5+200',
             'status' => 'approved', 'amount' => 980000, 'time_extension_days' => 7],
            ['co_number' => 'CO-TST-003', 'title' => 'Extra layer of asphalt on approach ramps',
             'status' => 'under_review', 'amount' => 1450000],
            ['co_number' => 'CO-TST-004', 'title' => 'Pedestrian bridge railing upgrade',
             'status' => 'draft', 'amount' => 450000],
            ['co_number' => 'CO-TST-005', 'title' => 'Relocation of water main at Section 2',
             'status' => 'rejected', 'amount' => 3100000],
        ];

        foreach ($changeOrders as $data) {
            ChangeOrder::firstOrCreate(
                ['co_number' => $data['co_number'], 'company_id' => $cid],
                array_merge($data, [
                    'company_id' => $cid,
                    'contract_id' => $contract->id,
                    'requested_by' => $admin->id,
                    'approved_by' => in_array($data['status'], ['approved', 'implemented']) ? $manager->id : null,
                    'approved_at' => in_array($data['status'], ['approved', 'implemented']) ? now()->subDays(rand(1, 10)) : null,
                ])
            );
        }

        // ═══════════════════════════════════════════════════════════
        // Summary
        // ═══════════════════════════════════════════════════════════
        $this->command->info('');
        $this->command->info('✅ Workflow demo data seeded:');
        $this->command->info('   • ' . count($ncrs) . ' NCRs (ISO 9001 CAPA)');
        $this->command->info('   • ' . count($drawings) . ' drawings (ISO 19650)');
        $this->command->info('   • ' . count($invoices) . ' invoices');
        $this->command->info('   • ' . count($workOrders) . ' work orders');
        $this->command->info('   • ' . count($tenders) . ' tenders');
        $this->command->info('   • ' . count($changeOrders) . ' change orders (ISO 19650)');
    }
}
