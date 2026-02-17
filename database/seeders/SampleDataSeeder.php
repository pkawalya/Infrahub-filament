<?php

namespace Database\Seeders;

use App\Models\Boq;
use App\Models\BoqItem;
use App\Models\CdeProject;
use App\Models\Client;
use App\Models\Company;
use App\Models\Contract;
use App\Models\DailySiteLog;
use App\Models\Milestone;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\SafetyIncident;
use App\Models\Supplier;
use App\Models\Task;
use App\Models\User;
use App\Models\Vendor;
use App\Models\WorkOrder;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('slug', 'acme-field-services')->first();

        if (!$company) {
            $this->command->error('❌ Demo company not found. Run SaasFoundationSeeder first.');
            return;
        }

        $users = User::where('company_id', $company->id)->get();
        $admin = $users->where('user_type', 'company_admin')->first();
        $manager = $users->where('user_type', 'manager')->first() ?? $admin;
        $technician = $users->where('user_type', 'technician')->first() ?? $admin;

        // ─── 1. Clients ─────────────────────────────────────────
        $this->command->info('📋 Seeding clients...');

        $clientsData = [
            ['name' => 'Uganda National Roads Authority', 'email' => 'info@unra.go.ug', 'phone' => '+256 417 312 100', 'company_name' => 'UNRA', 'city' => 'Kampala', 'country' => 'Uganda', 'address' => 'Plot 5, Lourdel Rd'],
            ['name' => 'Multiplex Construction', 'email' => 'projects@multiplex.ug', 'phone' => '+256 414 234 567', 'company_name' => 'Multiplex Ltd', 'city' => 'Kampala', 'country' => 'Uganda', 'address' => '14 Jinja Road'],
            ['name' => 'Roko Construction Ltd', 'email' => 'info@roko.co.ug', 'phone' => '+256 414 567 890', 'company_name' => 'Roko Construction', 'city' => 'Kampala', 'country' => 'Uganda', 'address' => '28 Industrial Area'],
            ['name' => 'Stirling Civil Engineering', 'email' => 'office@stirling.ug', 'phone' => '+256 414 890 123', 'company_name' => 'Stirling', 'city' => 'Entebbe', 'country' => 'Uganda', 'address' => '5 Airport Road'],
            ['name' => 'China Wu Yi', 'email' => 'uganda@chinawuyi.com', 'phone' => '+256 414 345 678', 'company_name' => 'China Wu Yi Uganda', 'city' => 'Kampala', 'country' => 'Uganda', 'address' => '22 Bombo Road'],
        ];

        $clients = [];
        foreach ($clientsData as $data) {
            $clients[] = Client::firstOrCreate(
                ['email' => $data['email'], 'company_id' => $company->id],
                array_merge($data, ['company_id' => $company->id, 'is_active' => true])
            );
        }

        // ─── 2. Suppliers ────────────────────────────────────────
        $this->command->info('🏭 Seeding suppliers...');

        $suppliersData = [
            ['name' => 'Hima Cement', 'email' => 'sales@himacement.com', 'phone' => '+256 414 251 000', 'contact_person' => 'Jane Auma', 'payment_terms' => 'Net 30', 'address' => 'Kasese, Uganda'],
            ['name' => 'Roofings Group', 'email' => 'orders@roofings.co.ug', 'phone' => '+256 414 320 600', 'contact_person' => 'Peter Ochieng', 'payment_terms' => 'Net 45', 'address' => 'Lubowa, Kampala'],
            ['name' => 'Steel & Tube Industries', 'email' => 'info@steeltube.ug', 'phone' => '+256 414 567 321', 'contact_person' => 'David Ssali', 'payment_terms' => 'Net 30', 'address' => 'Namanve Industrial Park'],
            ['name' => 'National Water & Sewerage', 'email' => 'commercial@nwsc.co.ug', 'phone' => '+256 414 315 100', 'contact_person' => 'Grace Nakato', 'payment_terms' => 'Prepaid', 'address' => 'Jinja Road, Kampala'],
        ];

        $suppliers = [];
        foreach ($suppliersData as $data) {
            $suppliers[] = Supplier::firstOrCreate(
                ['email' => $data['email'], 'company_id' => $company->id],
                array_merge($data, ['company_id' => $company->id, 'is_active' => true])
            );
        }

        // ─── 3. Vendors ──────────────────────────────────────────
        $this->command->info('🤝 Seeding vendors...');

        $vendorsData = [
            ['name' => 'Kolin Construction', 'email' => 'uganda@kolin.com.tr', 'phone' => '+256 414 789 012', 'contact_person' => 'Mehmet Yilmaz', 'type' => 'subcontractor', 'address' => 'Kololo, Kampala'],
            ['name' => 'DOTT Services', 'email' => 'info@dottservices.biz', 'phone' => '+256 414 123 789', 'contact_person' => 'Ahmed Hassan', 'type' => 'subcontractor', 'address' => 'Industrial Area, Kampala'],
            ['name' => 'SBI International', 'email' => 'projects@sbi-intl.com', 'phone' => '+256 414 456 789', 'contact_person' => 'Anna Petrov', 'type' => 'consultant', 'address' => 'Nakasero, Kampala'],
        ];

        $vendors = [];
        foreach ($vendorsData as $data) {
            $vendors[] = Vendor::firstOrCreate(
                ['email' => $data['email'], 'company_id' => $company->id],
                array_merge($data, ['company_id' => $company->id, 'is_active' => true])
            );
        }

        // ─── 4. CDE Projects ────────────────────────────────────
        $this->command->info('🏗️ Seeding projects...');

        $projectsData = [
            [
                'name' => 'Kampala-Jinja Expressway - Phase 2',
                'code' => 'KJE-002',
                'description' => 'Construction of the 77km dual carriageway expressway connecting Kampala to Jinja, including bridges, interchanges, and toll plazas.',
                'status' => 'active',
                'start_date' => now()->subMonths(6),
                'end_date' => now()->addMonths(18),
                'budget' => 450000000.00,
                'address' => 'Kampala-Jinja Highway',
                'city' => 'Kampala',
                'country' => 'Uganda',
            ],
            [
                'name' => 'Entebbe Airport Terminal Expansion',
                'code' => 'EIA-001',
                'description' => 'Expansion and modernization of the Entebbe International Airport terminal building, including new arrivals hall, departures lounge, and VIP facilities.',
                'status' => 'active',
                'start_date' => now()->subMonths(3),
                'end_date' => now()->addMonths(24),
                'budget' => 200000000.00,
                'address' => 'Entebbe International Airport',
                'city' => 'Entebbe',
                'country' => 'Uganda',
            ],
            [
                'name' => 'Karuma Hydropower Dam - Civil Works',
                'code' => 'KHD-003',
                'description' => 'Civil engineering works for the 600MW Karuma Hydropower Project on the River Nile, including dam construction, spillway, and access roads.',
                'status' => 'active',
                'start_date' => now()->subMonths(12),
                'end_date' => now()->addMonths(12),
                'budget' => 1700000000.00,
                'address' => 'Karuma Falls, River Nile',
                'city' => 'Kiryandongo',
                'country' => 'Uganda',
            ],
            [
                'name' => 'Lira Regional Hospital Construction',
                'code' => 'LRH-004',
                'description' => 'Construction of a 300-bed regional referral hospital with outpatient department, surgical theaters, ICU, and staff quarters.',
                'status' => 'planning',
                'start_date' => now()->addMonth(),
                'end_date' => now()->addMonths(30),
                'budget' => 85000000.00,
                'address' => 'Lira City',
                'city' => 'Lira',
                'country' => 'Uganda',
            ],
            [
                'name' => 'Mbarara Industrial Park',
                'code' => 'MIP-005',
                'description' => 'Development of a 200-acre industrial park in Mbarara, including road networks, utilities, warehousing, and administrative offices.',
                'status' => 'planning',
                'start_date' => now()->addMonths(2),
                'end_date' => now()->addMonths(36),
                'budget' => 120000000.00,
                'address' => 'Mbarara-Masaka Road',
                'city' => 'Mbarara',
                'country' => 'Uganda',
            ],
        ];

        $projects = [];
        foreach ($projectsData as $i => $data) {
            $projects[] = CdeProject::firstOrCreate(
                ['code' => $data['code'], 'company_id' => $company->id],
                array_merge($data, [
                    'company_id' => $company->id,
                    'client_id' => $clients[$i % count($clients)]->id,
                    'manager_id' => $manager->id,
                ])
            );
        }

        // ─── 5. Contracts ────────────────────────────────────────
        $this->command->info('📝 Seeding contracts...');

        $contractsData = [
            ['title' => 'Main Civil Works Contract', 'contract_number' => 'KJE-MC-001', 'type' => 'main', 'status' => 'active', 'original_value' => 320000000, 'revised_value' => 335000000],
            ['title' => 'Electrical & Mechanical Works', 'contract_number' => 'EIA-EM-001', 'type' => 'sub', 'status' => 'active', 'original_value' => 45000000, 'revised_value' => 45000000],
            ['title' => 'Dam Construction - Phase A', 'contract_number' => 'KHD-DC-001', 'type' => 'main', 'status' => 'active', 'original_value' => 890000000, 'revised_value' => 920000000],
            ['title' => 'Architectural & Design Services', 'contract_number' => 'LRH-AD-001', 'type' => 'consultancy', 'status' => 'draft', 'original_value' => 2500000, 'revised_value' => 2500000],
            ['title' => 'Road & Utility Infrastructure', 'contract_number' => 'MIP-RU-001', 'type' => 'main', 'status' => 'draft', 'original_value' => 55000000, 'revised_value' => 55000000],
        ];

        $contracts = [];
        foreach ($contractsData as $i => $data) {
            $contracts[] = Contract::firstOrCreate(
                ['contract_number' => $data['contract_number'], 'company_id' => $company->id],
                array_merge($data, [
                    'company_id' => $company->id,
                    'cde_project_id' => $projects[$i]->id,
                    'vendor_id' => $vendors[$i % count($vendors)]->id,
                    'start_date' => $projects[$i]->start_date,
                    'end_date' => $projects[$i]->end_date,
                    'description' => 'Contract for ' . $data['title'],
                    'created_by' => $admin->id,
                ])
            );
        }

        // ─── 6. BOQs & Items ────────────────────────────────────
        $this->command->info('📊 Seeding BOQs...');

        foreach ($projects as $i => $project) {
            if ($i >= 3)
                continue; // Only active projects

            $boq = Boq::firstOrCreate(
                ['boq_number' => 'BOQ-' . $project->code, 'company_id' => $company->id],
                [
                    'company_id' => $company->id,
                    'cde_project_id' => $project->id,
                    'contract_id' => $contracts[$i]->id,
                    'name' => $project->name . ' - Main BOQ',
                    'description' => 'Bill of Quantities for ' . $project->name,
                    'status' => 'approved',
                    'total_value' => 0,
                    'currency' => 'USD',
                    'created_by' => $admin->id,
                ]
            );

            $boqItemsData = [
                ['item_code' => 'A001', 'description' => 'Site Clearance & Preparation', 'unit' => 'm²', 'quantity' => 50000, 'unit_rate' => 12.50, 'category' => 'Preliminaries'],
                ['item_code' => 'A002', 'description' => 'Excavation to Foundation Level', 'unit' => 'm³', 'quantity' => 25000, 'unit_rate' => 35.00, 'category' => 'Earthworks'],
                ['item_code' => 'A003', 'description' => 'Reinforced Concrete Class 30', 'unit' => 'm³', 'quantity' => 8500, 'unit_rate' => 280.00, 'category' => 'Concrete Works'],
                ['item_code' => 'A004', 'description' => 'Steel Reinforcement Y16', 'unit' => 'ton', 'quantity' => 1200, 'unit_rate' => 1150.00, 'category' => 'Steel Works'],
                ['item_code' => 'A005', 'description' => 'Formwork to Columns & Beams', 'unit' => 'm²', 'quantity' => 15000, 'unit_rate' => 42.00, 'category' => 'Formwork'],
                ['item_code' => 'A006', 'description' => 'Waterproofing Membrane', 'unit' => 'm²', 'quantity' => 10000, 'unit_rate' => 28.00, 'category' => 'Finishes'],
                ['item_code' => 'A007', 'description' => 'Aggregate Base Course', 'unit' => 'm³', 'quantity' => 30000, 'unit_rate' => 65.00, 'category' => 'Road Works'],
                ['item_code' => 'A008', 'description' => 'Asphalt Wearing Course', 'unit' => 'm²', 'quantity' => 120000, 'unit_rate' => 18.50, 'category' => 'Road Works'],
            ];

            $totalValue = 0;
            foreach ($boqItemsData as $j => $item) {
                $amount = $item['quantity'] * $item['unit_rate'];
                $totalValue += $amount;
                BoqItem::firstOrCreate(
                    ['boq_id' => $boq->id, 'item_code' => $item['item_code']],
                    array_merge($item, [
                        'boq_id' => $boq->id,
                        'amount' => $amount,
                        'sort_order' => $j + 1,
                    ])
                );
            }

            $boq->update(['total_value' => $totalValue]);
        }

        // ─── 7. Tasks ───────────────────────────────────────────
        $this->command->info('✅ Seeding tasks...');

        $tasksData = [
            ['title' => 'Complete geotechnical survey report', 'priority' => 'high', 'status' => 'done', 'estimated_hours' => 40, 'actual_hours' => 38, 'progress_percent' => 100],
            ['title' => 'Review structural drawings - Bridge Section', 'priority' => 'high', 'status' => 'in_progress', 'estimated_hours' => 24, 'actual_hours' => 16, 'progress_percent' => 65],
            ['title' => 'Submit environmental impact assessment', 'priority' => 'critical', 'status' => 'review', 'estimated_hours' => 80, 'actual_hours' => 72, 'progress_percent' => 90],
            ['title' => 'Procure reinforcement steel - Phase 1', 'priority' => 'high', 'status' => 'in_progress', 'estimated_hours' => 16, 'actual_hours' => 8, 'progress_percent' => 50],
            ['title' => 'Install temporary site offices', 'priority' => 'medium', 'status' => 'done', 'estimated_hours' => 32, 'actual_hours' => 30, 'progress_percent' => 100],
            ['title' => 'Survey & mark foundation layout', 'priority' => 'high', 'status' => 'in_progress', 'estimated_hours' => 20, 'actual_hours' => 12, 'progress_percent' => 60],
            ['title' => 'Concrete testing - Week 14 samples', 'priority' => 'medium', 'status' => 'to_do', 'estimated_hours' => 8, 'actual_hours' => 0, 'progress_percent' => 0],
            ['title' => 'Update site safety plan', 'priority' => 'critical', 'status' => 'to_do', 'estimated_hours' => 12, 'actual_hours' => 0, 'progress_percent' => 0],
            ['title' => 'Prepare monthly progress report', 'priority' => 'medium', 'status' => 'in_progress', 'estimated_hours' => 16, 'actual_hours' => 6, 'progress_percent' => 35],
            ['title' => 'Coordinate with utility relocation team', 'priority' => 'high', 'status' => 'blocked', 'estimated_hours' => 24, 'actual_hours' => 4, 'progress_percent' => 15],
        ];

        foreach ($tasksData as $i => $data) {
            Task::firstOrCreate(
                ['title' => $data['title'], 'company_id' => $company->id],
                array_merge($data, [
                    'company_id' => $company->id,
                    'cde_project_id' => $projects[$i % 3]->id,
                    'due_date' => now()->addDays(rand(-10, 30)),
                    'created_by' => $admin->id,
                ])
            );
        }

        // ─── 8. Milestones ──────────────────────────────────────
        $this->command->info('🎯 Seeding milestones...');

        $milestonesData = [
            ['name' => 'Site Mobilization Complete', 'status' => 'completed', 'priority' => 'high', 'days_offset' => -60],
            ['name' => 'Foundation Works - 50%', 'status' => 'in_progress', 'priority' => 'high', 'days_offset' => -10],
            ['name' => 'Superstructure Commencement', 'status' => 'pending', 'priority' => 'critical', 'days_offset' => 30],
            ['name' => 'Roofing & Waterproofing', 'status' => 'pending', 'priority' => 'medium', 'days_offset' => 90],
            ['name' => 'MEP Installation Complete', 'status' => 'pending', 'priority' => 'high', 'days_offset' => 150],
            ['name' => 'Practical Completion', 'status' => 'pending', 'priority' => 'critical', 'days_offset' => 270],
            ['name' => 'Final Handover & Defects Liability', 'status' => 'pending', 'priority' => 'high', 'days_offset' => 365],
        ];

        foreach ($milestonesData as $i => $data) {
            foreach (array_slice($projects, 0, 3) as $project) {
                Milestone::firstOrCreate(
                    ['name' => $data['name'], 'cde_project_id' => $project->id, 'company_id' => $company->id],
                    [
                        'company_id' => $company->id,
                        'cde_project_id' => $project->id,
                        'description' => 'Milestone: ' . $data['name'],
                        'target_date' => now()->addDays($data['days_offset']),
                        'actual_date' => $data['status'] === 'completed' ? now()->addDays($data['days_offset'] + rand(-5, 5)) : null,
                        'status' => $data['status'],
                        'priority' => $data['priority'],
                    ]
                );
            }
        }

        // ─── 9. Daily Site Logs ──────────────────────────────────
        $this->command->info('📝 Seeding daily site logs...');

        $weatherOptions = ['sunny', 'partly_cloudy', 'cloudy', 'rainy'];
        $workDescriptions = [
            'Continued excavation at Block A. Poured concrete for column footings C1-C8. Steel fixing team completed rebar for ground beams.',
            'Formwork installation for first floor slab. Concrete testing samples taken. MEP rough-in commenced in basement.',
            'Drainage works along access road. Backfilling completed at eastern wing. Surveying for Block B layout.',
            'Heavy rain delayed concrete pour. Indoor activities: steel cutting, rebar bending, and safety training conducted.',
            'Completed concrete pour for ground floor slab Block A. Crane mobilized to site. Steel erection commenced.',
        ];

        foreach (array_slice($projects, 0, 3) as $project) {
            for ($d = 14; $d >= 0; $d--) {
                $logDate = now()->subDays($d);
                if ($logDate->isWeekend())
                    continue;

                DailySiteLog::firstOrCreate(
                    ['log_date' => $logDate->format('Y-m-d'), 'cde_project_id' => $project->id, 'company_id' => $company->id],
                    [
                        'company_id' => $company->id,
                        'cde_project_id' => $project->id,
                        'weather' => $weatherOptions[array_rand($weatherOptions)],
                        'temperature_high' => rand(26, 34),
                        'temperature_low' => rand(18, 23),
                        'workers_on_site' => rand(45, 120),
                        'visitors_on_site' => rand(0, 8),
                        'work_performed' => $workDescriptions[array_rand($workDescriptions)],
                        'materials_received' => rand(0, 1) ? 'Cement: 200 bags, Steel: 5 tons, Aggregates: 3 trucks' : null,
                        'equipment_used' => 'Excavator, Concrete mixer, Crane, Compactor',
                        'delays' => rand(0, 1) ? 'Rain delay: 2 hours in the afternoon' : null,
                        'safety_incidents' => null,
                        'notes' => null,
                        'status' => $d > 3 ? 'approved' : ($d > 1 ? 'submitted' : 'draft'),
                        'created_by' => $technician->id,
                        'approved_by' => $d > 3 ? $manager->id : null,
                    ]
                );
            }
        }

        // ─── 10. Safety Incidents ────────────────────────────────
        $this->command->info('🛡️ Seeding safety incidents...');

        $incidentsData = [
            ['title' => 'Worker slip on wet surface', 'type' => 'near_miss', 'severity' => 'low', 'status' => 'closed', 'location' => 'Block A - Ground Floor', 'root_cause' => 'Wet surface not barricaded', 'corrective_action' => 'Non-slip mats installed, warning signs placed'],
            ['title' => 'Scaffolding loose bolts identified', 'type' => 'hazard', 'severity' => 'high', 'status' => 'resolved', 'location' => 'Block B - Level 3', 'root_cause' => 'Inspection missed during erection', 'corrective_action' => 'All scaffolding re-inspected, checklist updated'],
            ['title' => 'Minor hand injury during steel fixing', 'type' => 'first_aid', 'severity' => 'medium', 'status' => 'investigating', 'location' => 'Rebar Yard', 'root_cause' => 'Gloves not worn', 'corrective_action' => 'Mandatory PPE enforcement, toolbox talk conducted'],
            ['title' => 'Excavation near utility line', 'type' => 'near_miss', 'severity' => 'critical', 'status' => 'reported', 'location' => 'Access Road - Section 3', 'root_cause' => 'Underground utilities not mapped', 'corrective_action' => 'Ground-penetrating radar survey ordered'],
        ];

        foreach ($incidentsData as $i => $data) {
            SafetyIncident::firstOrCreate(
                ['title' => $data['title'], 'company_id' => $company->id],
                array_merge($data, [
                    'company_id' => $company->id,
                    'cde_project_id' => $projects[$i % 3]->id,
                    'incident_number' => 'INC-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'description' => $data['title'] . '. ' . ($data['root_cause'] ?? ''),
                    'incident_date' => now()->subDays(rand(1, 30)),
                    'reported_by' => $technician->id,
                    'investigated_by' => $data['status'] !== 'reported' ? $manager->id : null,
                ])
            );
        }

        // ─── 11. Purchase Orders ─────────────────────────────────
        $this->command->info('📦 Seeding purchase orders...');

        $poData = [
            ['po_number' => 'PO-2026-001', 'status' => 'received', 'notes' => 'Urgent: concrete pour scheduled next week'],
            ['po_number' => 'PO-2026-002', 'status' => 'ordered', 'notes' => 'Standard delivery - reinforcement steel'],
            ['po_number' => 'PO-2026-003', 'status' => 'approved', 'notes' => 'Formwork materials for Level 2'],
            ['po_number' => 'PO-2026-004', 'status' => 'draft', 'notes' => 'Safety equipment replenishment'],
        ];

        foreach ($poData as $i => $data) {
            $subtotal = rand(15000, 250000);
            $tax = $subtotal * 0.18;
            $shipping = rand(500, 3000);

            PurchaseOrder::firstOrCreate(
                ['po_number' => $data['po_number'], 'company_id' => $company->id],
                array_merge($data, [
                    'company_id' => $company->id,
                    'cde_project_id' => $projects[$i % 3]->id,
                    'supplier_id' => $suppliers[$i % count($suppliers)]->id,
                    'order_date' => now()->subDays(rand(5, 30)),
                    'expected_date' => now()->addDays(rand(5, 30)),
                    'received_date' => $data['status'] === 'received' ? now()->subDays(rand(1, 5)) : null,
                    'subtotal' => $subtotal,
                    'tax_amount' => $tax,
                    'shipping_cost' => $shipping,
                    'total_amount' => $subtotal + $tax + $shipping,
                    'created_by' => $admin->id,
                    'approved_by' => in_array($data['status'], ['approved', 'ordered', 'received']) ? $manager->id : null,
                ])
            );
        }

        // ─── 12. Work Orders ─────────────────────────────────────
        $this->command->info('🔧 Seeding work orders...');

        $workOrdersData = [
            ['wo_number' => 'WO-2026-001', 'title' => 'Concrete pour - Foundation Block A', 'status' => 'completed', 'priority' => 'high'],
            ['wo_number' => 'WO-2026-002', 'title' => 'Steel reinforcement - Ground beams', 'status' => 'in_progress', 'priority' => 'high'],
            ['wo_number' => 'WO-2026-003', 'title' => 'Electrical rough-in - Basement', 'status' => 'in_progress', 'priority' => 'medium'],
            ['wo_number' => 'WO-2026-004', 'title' => 'Drainage installation - Access road', 'status' => 'approved', 'priority' => 'medium'],
            ['wo_number' => 'WO-2026-005', 'title' => 'Scaffolding erection - Block B', 'status' => 'pending', 'priority' => 'high'],
            ['wo_number' => 'WO-2026-006', 'title' => 'Fire safety system - Phase 1', 'status' => 'pending', 'priority' => 'critical'],
        ];

        foreach ($workOrdersData as $i => $data) {
            WorkOrder::firstOrCreate(
                ['wo_number' => $data['wo_number'], 'company_id' => $company->id],
                array_merge($data, [
                    'company_id' => $company->id,
                    'cde_project_id' => $projects[$i % 3]->id,
                    'description' => 'Work order for ' . $data['title'],
                    'assigned_to' => $technician->id,
                    'due_date' => now()->addDays(rand(3, 45)),
                    'started_at' => in_array($data['status'], ['in_progress', 'completed']) ? now()->subDays(rand(5, 20)) : null,
                    'completed_at' => $data['status'] === 'completed' ? now()->subDays(rand(1, 5)) : null,
                    'created_by' => $admin->id,
                ])
            );
        }

        // ─── Summary ────────────────────────────────────────────
        $this->command->info('');
        $this->command->info('✅ Sample data seeded successfully:');
        $this->command->info('   • ' . count($clients) . ' clients');
        $this->command->info('   • ' . count($suppliers) . ' suppliers');
        $this->command->info('   • ' . count($vendors) . ' vendors');
        $this->command->info('   • ' . count($projects) . ' projects');
        $this->command->info('   • ' . count($contracts) . ' contracts');
        $this->command->info('   • 3 BOQs with 8 items each');
        $this->command->info('   • ' . count($tasksData) . ' tasks');
        $this->command->info('   • ' . count($milestonesData) * 3 . ' milestones');
        $this->command->info('   • ~30 daily site logs');
        $this->command->info('   • ' . count($incidentsData) . ' safety incidents');
        $this->command->info('   • ' . count($poData) . ' purchase orders');
        $this->command->info('   • ' . count($workOrdersData) . ' work orders');
    }
}
