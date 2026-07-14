<?php

namespace Database\Seeders\TestCompany;

use App\Models\{Asset, BidStage, Boq, BoqItem, CdeProject, ChangeOrder, Company, Contract, ContractPayment, CrewAttendance, DailySiteDiary, DailySiteLog, Drawing, EquipmentAllocation, EquipmentFuelLog, Expense, Invoice, MaterialRequisition, Milestone, PaymentCertificate, Product, ProjectSuggestion, PurchaseOrder, Rfi, SafetyIncident, SafetyInspection, SnagItem, Subcontractor, SubcontractorPackage, Submittal, Supplier, Task, Tender, TenderBid, TenderStage, Transmittal, User, Vendor, Warehouse, WorkOrder, WorkerCertification};

class TestModuleDataSeeder
{
    public static function seed(Company $co, array $projects, User $admin, User $mgr, User $tech, Supplier $sup, Vendor $ven, Warehouse $wh): void
    {
        $cid = $co->id;

        // ── workflow: Default templates & steps ──
        $rfiTemplate = \App\Models\WorkflowTemplate::updateOrCreate(
            ['company_id' => $cid, 'module_type' => 'Rfi'],
            ['name' => 'Default RFI Review Process', 'is_active' => true]
        );
        $rfiTemplate->steps()->delete();
        $rfiTemplate->steps()->createMany([
            ['step_sequence' => 1, 'name' => 'Manager Review', 'approver_type' => 'role', 'approver_id' => 'manager'],
            ['step_sequence' => 2, 'name' => 'Director Approval', 'approver_type' => 'role', 'approver_id' => 'company_admin'],
        ]);

        $safetyTemplate = \App\Models\WorkflowTemplate::updateOrCreate(
            ['company_id' => $cid, 'module_type' => 'SafetyIncident'],
            ['name' => 'Standard SHEQ CAPA Signoff', 'is_active' => true]
        );
        $safetyTemplate->steps()->delete();
        $safetyTemplate->steps()->createMany([
            ['step_sequence' => 1, 'name' => 'Safety Inspector Verification', 'approver_type' => 'role', 'approver_id' => 'manager'],
            ['step_sequence' => 2, 'name' => 'CAPA Close-out', 'approver_type' => 'role', 'approver_id' => 'company_admin'],
        ]);

        foreach ($projects as $i => $p) {
            $pid = $p->id;

            // ── task_workflow: Tasks (20+ hierarchical tasks per project) ──
            $targetProgress = 40; // Default active progress
            if ($p->status === 'completed') {
                $targetProgress = 100;
            } elseif ($p->status === 'planning') {
                $targetProgress = 0;
            } else {
                // Staggered active progress based on project code
                if ($p->code === 'TST-ROAD-01') {
                    $targetProgress = 40;
                } elseif ($p->code === 'TST-SOLAR-02') {
                    $targetProgress = 18;
                } elseif ($p->code === 'TST-BUILD-03') {
                    $targetProgress = 65;
                }
            }

            $templates = self::getTaskTemplates($p->project_type, $p->status);
            
            // Adjust progress based on target
            $taskCount = count($templates);
            $doneCount = (int) round($taskCount * ($targetProgress / 100));

            foreach ($templates as $idx => &$t) {
                if ($t['is_summary'] ?? false) {
                    $t['status'] = 'to_do';
                    $t['progress'] = 0;
                    continue;
                }
                if ($targetProgress === 100) {
                    $t['status'] = 'done';
                    $t['progress'] = 100;
                } elseif ($targetProgress === 0) {
                    $t['status'] = 'to_do';
                    $t['progress'] = 0;
                } else {
                    if ($idx < $doneCount) {
                        $t['status'] = 'done';
                        $t['progress'] = 100;
                    } elseif ($idx === $doneCount) {
                        $t['status'] = 'in_progress';
                        $t['progress'] = 45;
                    } else {
                        $t['status'] = 'to_do';
                        $t['progress'] = 0;
                    }
                }
            }
            unset($t);

            // Pre-calculate dates in a loop
            for ($k = 0; $k < count($templates); $k++) {
                $t = &$templates[$k];
                if (isset($t['pred']) && $t['pred'] !== null) {
                    $predTask = $templates[$t['pred']];
                    $start = \Carbon\Carbon::parse($predTask['due_date'])->addDay();
                    $start = \App\Models\Task::skipWeekends($start);
                } else {
                    if (isset($t['parent']) && $t['parent'] !== null) {
                        $parentTask = $templates[$t['parent']];
                        $start = \Carbon\Carbon::parse($parentTask['start_date']);
                    } else {
                        $start = $p->start_date->copy();
                        $start = \App\Models\Task::skipWeekends($start);
                    }
                }

                $duration = $t['duration'];
                if ($t['is_milestone'] ?? false) {
                    $duration = 0;
                }

                $end = \App\Models\Task::calculateFinishDate($start, $duration);

                $t['start_date'] = $start->format('Y-m-d');
                $t['due_date'] = $end->format('Y-m-d');
            }
            unset($t);

            // Create tasks in DB & build mapping array for parents/predecessors
            $createdTasks = [];
            foreach ($templates as $idx => $t) {
                $parentId = null;
                if (isset($t['parent']) && $t['parent'] !== null && isset($createdTasks[$t['parent']])) {
                    $parentId = $createdTasks[$t['parent']]->id;
                }

                $createdTasks[$idx] = Task::updateOrCreate(
                    [
                        'title' => $t['title'] . " - " . $p->code,
                        'cde_project_id' => $pid,
                        'company_id' => $cid
                    ],
                    [
                        'parent_id' => $parentId,
                        'description' => $t['title'] . ' details',
                        'type' => ($t['is_milestone'] ?? false) ? 'milestone' : (($t['is_summary'] ?? false) ? 'summary' : 'task'),
                        'priority' => ($t['is_milestone'] ?? false) ? 'high' : 'medium',
                        'status' => $t['status'],
                        'start_date' => $t['start_date'],
                        'due_date' => $t['due_date'],
                        'duration_days' => $t['duration'],
                        'progress_percent' => $t['progress'],
                        'estimated_hours' => ($t['is_summary'] ?? false) ? 0 : rand(10, 40),
                        'created_by' => $admin->id,
                        'sort_order' => $idx + 1,
                    ]
                );
            }

            // Create dependencies
            foreach ($templates as $idx => $t) {
                if (isset($t['pred']) && $t['pred'] !== null && isset($createdTasks[$t['pred']])) {
                    \App\Models\TaskDependency::firstOrCreate([
                        'task_id' => $createdTasks[$idx]->id,
                        'depends_on_id' => $createdTasks[$t['pred']]->id,
                    ], [
                        'dependency_type' => 'finish_to_start',
                        'lag_days' => 0,
                    ]);
                }
            }

            // Regenerate WBS codes and roll up values from children
            Task::regenerateWbs($pid);

            // Roll up summary tasks (from bottom to top outline level, to handle nested summaries correctly)
            $summaryTasks = Task::where('cde_project_id', $pid)
                ->where('is_summary', true)
                ->orderBy('outline_level', 'desc')
                ->get();
            foreach ($summaryTasks as $st) {
                $st->rollUpFromChildren();
            }


            // ── planning_progress: Milestones ──
            $milestones = [];
            if ($p->code === 'TST-ROAD-01') {
                $milestones = [
                    ['name' => "Feasibility Study - {$p->code}", 'status' => 'completed', 'offset' => -120],
                    ['name' => "Design Approval - {$p->code}", 'status' => 'completed', 'offset' => -60],
                    ['name' => "Earthworks - {$p->code}", 'status' => 'pending', 'offset' => 30],
                    ['name' => "Paving & Surfacing - {$p->code}", 'status' => 'pending', 'offset' => 120],
                    ['name' => "Commissioning - {$p->code}", 'status' => 'pending', 'offset' => 240],
                ];
            } elseif ($p->code === 'TST-SOLAR-02') {
                $milestones = [
                    ['name' => "Site Preparation - {$p->code}", 'status' => 'completed', 'offset' => -45],
                    ['name' => "Structure Erection - {$p->code}", 'status' => 'pending', 'offset' => 45],
                    ['name' => "Panel Installation - {$p->code}", 'status' => 'pending', 'offset' => 120],
                    ['name' => "Inverter Testing - {$p->code}", 'status' => 'pending', 'offset' => 180],
                    ['name' => "Grid Connection - {$p->code}", 'status' => 'pending', 'offset' => 300],
                ];
            } elseif ($p->code === 'TST-BUILD-03') {
                $milestones = [
                    ['name' => "Excavation - {$p->code}", 'status' => 'completed', 'offset' => -150],
                    ['name' => "Foundation Concrete - {$p->code}", 'status' => 'completed', 'offset' => -100],
                    ['name' => "Superstructure Frame - {$p->code}", 'status' => 'completed', 'offset' => -30],
                    ['name' => "Brickwork - {$p->code}", 'status' => 'completed', 'offset' => -5],
                    ['name' => "Finishes & MEP - {$p->code}", 'status' => 'pending', 'offset' => 45],
                    ['name' => "Handover - {$p->code}", 'status' => 'pending', 'offset' => 90],
                ];
            } elseif ($p->code === 'TST-WATER-04') {
                $milestones = [
                    ['name' => "Civil Works - {$p->code}", 'status' => 'completed', 'offset' => -200],
                    ['name' => "Equipment Install - {$p->code}", 'status' => 'completed', 'offset' => -100],
                    ['name' => "Commissioning - {$p->code}", 'status' => 'completed', 'offset' => -10],
                ];
            } else {
                $milestones = [
                    ['name' => "Mobilization - {$p->code}", 'status' => 'completed', 'offset' => -30],
                    ['name' => "Mid-term review - {$p->code}", 'status' => 'pending', 'offset' => 60],
                ];
            }

            foreach ($milestones as $m) {
                Milestone::updateOrCreate(['name' => $m['name'], 'cde_project_id' => $pid, 'company_id' => $cid], ['description' => $m['name'], 'target_date' => now()->addDays($m['offset']), 'actual_date' => $m['status'] === 'completed' ? now()->subDays(5) : null, 'status' => $m['status'], 'priority' => 'high']);
            }

            // ── field_management: Daily Site Log ──
            if ($p->status === 'active') {
                DailySiteLog::updateOrCreate(['log_date' => now()->subDay()->format('Y-m-d'), 'cde_project_id' => $pid, 'company_id' => $cid], ['weather' => 'sunny', 'temperature_high' => 31, 'temperature_low' => 19, 'workers_on_site' => rand(20, 60), 'visitors_on_site' => rand(0, 5), 'work_performed' => 'Earth leveling and grid welding completed per schedule.', 'equipment_used' => 'Compactor, excavator, crane', 'status' => 'approved', 'created_by' => $tech->id, 'approved_by' => $mgr->id]);
                DailySiteDiary::updateOrCreate(['diary_date' => now()->subDays(2)->format('Y-m-d'), 'cde_project_id' => $pid, 'company_id' => $cid], ['weather' => 'cloudy', 'temperature' => 28, 'workers_on_site' => rand(15, 40), 'subcontractor_workers' => rand(5, 15), 'equipment_on_site' => rand(3, 8), 'work_performed' => 'Formwork erection and rebar tying for columns.', 'work_planned_tomorrow' => 'Concrete pour for ground beams.', 'prepared_by' => $tech->id, 'approved_by' => $mgr->id, 'approved_at' => now()]);
            }

            // ── core: Work Orders ──
            WorkOrder::updateOrCreate(['wo_number' => "WO-{$p->code}", 'company_id' => $cid], ['cde_project_id' => $pid, 'title' => "Deploy site offices - {$p->name}", 'description' => 'Set up temporary offices and tool stores.', 'status' => 'in_progress', 'priority' => 'high', 'assigned_to' => $tech->id, 'due_date' => now()->addDays(15), 'started_at' => now()->subDays(3), 'created_by' => $admin->id]);

            // ── boq_management: BOQ + Items ──
            $boq = Boq::updateOrCreate(['boq_number' => "BOQ-{$p->code}", 'company_id' => $cid], ['cde_project_id' => $pid, 'name' => "{$p->name} - Main BOQ", 'description' => "BOQ for {$p->name}", 'status' => 'approved', 'total_value' => 0, 'currency' => 'USD', 'created_by' => $admin->id]);
            $tv = 0;
            foreach ([
                ['item_code' => 'B001', 'description' => 'Site clearing', 'unit' => 'm2', 'quantity' => 5000, 'unit_rate' => 12, 'category' => 'material'],
                ['item_code' => 'B002', 'description' => 'Concrete C30', 'unit' => 'm3', 'quantity' => 800, 'unit_rate' => 280, 'category' => 'material'],
                ['item_code' => 'B003', 'description' => 'Steel reinforcement', 'unit' => 'ton', 'quantity' => 120, 'unit_rate' => 1150, 'category' => 'material'],
            ] as $j => $bi) {
                $amt = $bi['quantity'] * $bi['unit_rate']; $tv += $amt;
                BoqItem::updateOrCreate(['boq_id' => $boq->id, 'item_code' => $bi['item_code']], array_merge($bi, ['amount' => $amt, 'sort_order' => $j + 1]));
            }
            $boq->update(['total_value' => $tv]);

            // ── cde: RFIs, Submittals, Transmittals, Drawings ──
            Rfi::updateOrCreate(['rfi_number' => "RFI-{$p->code}-01", 'company_id' => $cid], ['cde_project_id' => $pid, 'subject' => "Foundation depth clarification - {$p->code}", 'question' => 'Please confirm required depth for pad foundations.', 'status' => 'open', 'priority' => 'high', 'due_date' => now()->addDays(7), 'raised_by' => $tech->id, 'assigned_to' => $mgr->id]);
            Submittal::updateOrCreate(['submittal_number' => "SUB-{$p->code}-01", 'company_id' => $cid], ['cde_project_id' => $pid, 'title' => "Concrete mix design - {$p->code}", 'type' => 'product_data', 'status' => 'approved', 'submitted_by' => $tech->id, 'reviewer_id' => $mgr->id, 'due_date' => now()->addDays(14), 'reviewed_at' => now()]);
            Transmittal::updateOrCreate(['transmittal_number' => "TRN-{$p->code}-01", 'company_id' => $cid], ['cde_project_id' => $pid, 'subject' => "IFC Drawings Package - {$p->code}", 'status' => 'sent', 'from_user_id' => $mgr->id, 'to_organization' => 'UNRA', 'to_contact' => 'Engineer', 'purpose' => 'for_review', 'sent_at' => now()->subDays(3)]);
            Drawing::updateOrCreate(['drawing_number' => "DRG-{$p->code}-01", 'company_id' => $cid], ['cde_project_id' => $pid, 'title' => "General Layout Plan - {$p->code}", 'discipline' => 'civil', 'drawing_type' => 'plan', 'current_revision' => 'A', 'status' => 'ifc', 'scale' => '1:500', 'drawn_by' => $tech->id, 'checked_by' => $mgr->id, 'drawn_date' => now()->subMonths(2)]);

            // ── sheq: Safety Incidents, Inspections, Snag Items ──
            SafetyIncident::updateOrCreate(['incident_number' => "INC-{$p->code}-01", 'company_id' => $cid], ['cde_project_id' => $pid, 'title' => "Near miss at excavation zone - {$p->code}", 'type' => 'near_miss', 'severity' => 'medium', 'status' => 'resolved', 'location' => 'Zone A', 'incident_date' => now()->subDays(10), 'root_cause' => 'Inadequate barricading', 'corrective_action' => 'Barriers installed and toolbox talk conducted', 'reported_by' => $tech->id, 'investigated_by' => $mgr->id]);
            SafetyInspection::updateOrCreate(['inspection_number' => "INS-{$p->code}-01", 'company_id' => $cid], ['cde_project_id' => $pid, 'title' => "Weekly site inspection - {$p->code}", 'type' => 'routine', 'status' => 'completed', 'scheduled_date' => now()->subDays(3), 'completed_date' => now()->subDays(3), 'location' => 'Full site', 'score' => 85, 'inspector_id' => $mgr->id]);
            SnagItem::updateOrCreate(['snag_number' => "SNG-{$p->code}-01", 'company_id' => $cid], ['cde_project_id' => $pid, 'title' => "Crack in column C4 - {$p->code}", 'category' => 'structural', 'severity' => 'major', 'status' => 'open', 'location' => 'Block A Level 2', 'due_date' => now()->addDays(7), 'reported_by' => $tech->id, 'assigned_to' => $mgr->id]);

            // ── inventory: Purchase Orders ──
            $st = rand(50000, 200000); $tx = $st * 0.18;
            PurchaseOrder::updateOrCreate(['po_number' => "PO-{$p->code}-01", 'company_id' => $cid], ['cde_project_id' => $pid, 'supplier_id' => $sup->id, 'warehouse_id' => $wh->id, 'status' => 'approved', 'order_date' => now()->subDays(10), 'expected_date' => now()->addDays(5), 'subtotal' => $st, 'tax_amount' => $tx, 'shipping_cost' => 1500, 'total_amount' => $st + $tx + 1500, 'notes' => 'Construction materials for phase 1', 'created_by' => $admin->id, 'approved_by' => $mgr->id]);
            MaterialRequisition::updateOrCreate(['requisition_number' => "MR-{$p->code}-01", 'company_id' => $cid], ['cde_project_id' => $pid, 'warehouse_id' => $wh->id, 'requester_id' => $tech->id, 'status' => 'approved', 'priority' => 'high', 'required_date' => now()->addDays(3), 'purpose' => 'Foundation concrete materials', 'approved_by' => $mgr->id, 'approved_at' => now()]);

            // ── cost_contracts: Contracts, Change Orders, Payment Certs ──
            $cv = $p->budget * 0.7;
            $contract = Contract::updateOrCreate(['contract_number' => "CON-{$p->code}-01", 'company_id' => $cid], ['vendor_id' => $ven->id, 'title' => "Main Works - {$p->name}", 'type' => 'main', 'status' => 'active', 'start_date' => $p->start_date, 'end_date' => $p->end_date, 'original_value' => $cv, 'revised_value' => $cv * 1.05, 'description' => "Primary contract for {$p->name}", 'created_by' => $admin->id]);
            ContractPayment::updateOrCreate(['reference' => "PAY-{$p->code}-01", 'company_id' => $cid], ['contract_id' => $contract->id, 'type' => 'payment', 'amount' => $cv * 0.2, 'payment_date' => now()->subDays(20), 'payment_method' => 'bank_transfer', 'created_by' => $admin->id]);
            ChangeOrder::updateOrCreate(['co_number' => "CO-{$p->code}-01", 'company_id' => $cid], ['contract_id' => $contract->id, 'title' => "Additional drainage - {$p->code}", 'status' => 'approved', 'amount' => $cv * 0.03, 'time_extension_days' => 14, 'requested_by' => $tech->id, 'approved_by' => $mgr->id]);
            PaymentCertificate::updateOrCreate(['certificate_number' => "IPC-{$p->code}-01", 'company_id' => $cid], ['cde_project_id' => $pid, 'contract_id' => $contract->id, 'type' => 'interim', 'status' => 'certified', 'period_from' => now()->subMonths(2), 'period_to' => now()->subMonth(), 'gross_value_to_date' => $cv * 0.3, 'previous_certified' => 0, 'this_certificate_gross' => $cv * 0.3, 'net_payable' => $cv * 0.27, 'vat_amount' => $cv * 0.03 * 0.18, 'total_payable' => $cv * 0.27 + ($cv * 0.03 * 0.18), 'retention_deduction' => $cv * 0.03, 'prepared_by' => $tech->id, 'checked_by' => $mgr->id, 'certified_by' => $admin->id, 'submitted_date' => now()->subDays(25), 'certified_date' => now()->subDays(20)]);

            // ── financials: Invoices, Expenses ──
            Invoice::updateOrCreate(['invoice_number' => "INV-{$p->code}-01", 'company_id' => $cid], ['cde_project_id' => $pid, 'client_id' => null, 'subtotal' => 5000000, 'tax_rate' => 18, 'tax_amount' => 900000, 'total_amount' => 5900000, 'amount_paid' => 2000000, 'status' => 'partially_paid', 'issue_date' => now()->subDays(30), 'due_date' => now(), 'created_by' => $admin->id]);
            Expense::updateOrCreate(['reference_number' => "EXP-{$p->code}-01", 'company_id' => $cid], ['cde_project_id' => $pid, 'title' => "Transport & fuel - {$p->code}", 'amount' => rand(500000, 2000000), 'expense_date' => now()->subDays(5), 'category' => 'transport', 'status' => 'approved', 'recorded_by' => $tech->id]);

            // ── suggestion_box ──
            ProjectSuggestion::updateOrCreate(['content' => "Improve tool storage - {$p->code}", 'company_id' => $cid], ['cde_project_id' => $pid, 'author_id' => $tech->id, 'is_anonymous' => true, 'category' => 'equipment', 'status' => 'new', 'upvotes' => rand(1, 10)]);
        }

        // ── hr_management: Crew attendance + Worker certs (company-wide) ──
        $activeProject = collect($projects)->firstWhere('status', 'active');
        if ($activeProject) {
            CrewAttendance::updateOrCreate(['attendance_date' => now()->subDay()->format('Y-m-d'), 'user_id' => $tech->id, 'company_id' => $cid], ['cde_project_id' => $activeProject->id, 'clock_in' => '07:00', 'clock_out' => '17:00', 'hours_worked' => 10, 'overtime_hours' => 2, 'status' => 'present', 'site_location' => 'Main site', 'approved_by' => $mgr->id]);
        }
        WorkerCertification::updateOrCreate(['certificate_number' => 'OSHA-TST-001', 'company_id' => $cid], ['user_id' => $tech->id, 'certification_name' => 'OSHA Safety Certificate', 'issuing_body' => 'Occupational Safety Authority', 'issued_date' => now()->subYear(), 'expiry_date' => now()->addYear()]);

        // ── subcontractors ──
        $sub = Subcontractor::updateOrCreate(['email' => 'subcon@test-company.com', 'company_id' => $cid], ['name' => 'Precision Electrical Ltd', 'contact_person' => 'James Okello', 'phone' => '+256 700 555 666', 'specialty' => 'electrical', 'status' => 'active', 'rating' => 4, 'safety_certified' => true, 'insurance_expiry' => now()->addMonths(6), 'license_expiry' => now()->addYear(), 'address' => 'Industrial Area, Kampala', 'created_by' => $admin->id]);
        if ($activeProject) {
            SubcontractorPackage::updateOrCreate(['title' => "Electrical works - {$activeProject->code}", 'company_id' => $cid], ['subcontractor_id' => $sub->id, 'cde_project_id' => $activeProject->id, 'scope_of_work' => 'Full electrical installation including panels and cabling.', 'status' => 'in_progress', 'contract_value' => 15000000, 'paid_to_date' => 5000000, 'start_date' => now()->subMonth(), 'end_date' => now()->addMonths(3), 'progress_percent' => 35, 'created_by' => $admin->id]);
        }

        // ── bidding: Tenders, Bids & Workflow Stages ──
        $co->seedDefaultWorkflowStages(); // ensure TenderStages + BidStages exist

        $draftStage = TenderStage::where('company_id', $cid)->where('slug', 'draft')->first();
        $pubStage   = TenderStage::where('company_id', $cid)->where('slug', 'published')->first();
        $evalStage  = TenderStage::where('company_id', $cid)->where('slug', 'evaluation')->first();
        $bidSubmitted = BidStage::where('company_id', $cid)->where('slug', 'submitted')->first();
        $bidReview    = BidStage::where('company_id', $cid)->where('slug', 'under_review')->first();
        $bidShort     = BidStage::where('company_id', $cid)->where('slug', 'shortlisted')->first();

        $tender1 = Tender::updateOrCreate(['reference' => 'TND-TST-001', 'company_id' => $cid], ['title' => 'Entebbe Airport Extension - MEP Package', 'client_name' => 'Civil Aviation Authority', 'source' => 'public', 'status' => 'preparing', 'tender_stage_id' => $pubStage?->id, 'stage_changed_at' => now()->subDays(10), 'estimated_value' => 250000000, 'submission_deadline' => now()->addDays(30), 'category' => 'construction', 'region' => 'Central', 'win_probability' => 60, 'strategy_notes' => 'Focus on competitive pricing and local content.', 'assigned_to' => $mgr->id, 'created_by' => $admin->id]);
        $tender2 = Tender::updateOrCreate(['reference' => 'TND-TST-002', 'company_id' => $cid], ['title' => 'Hoima-Kaiso Road Rehabilitation', 'client_name' => 'UNRA', 'source' => 'public', 'status' => 'submitted', 'tender_stage_id' => $evalStage?->id, 'stage_changed_at' => now()->subDays(3), 'estimated_value' => 180000000, 'bid_amount' => 165000000, 'submission_deadline' => now()->subDays(5), 'submitted_at' => now()->subDays(5), 'category' => 'civil', 'region' => 'Western', 'win_probability' => 40, 'assigned_to' => $mgr->id, 'created_by' => $admin->id]);
        $tender3 = Tender::updateOrCreate(['reference' => 'TND-TST-003', 'company_id' => $cid], ['title' => 'Mbarara Regional Hospital Expansion', 'client_name' => 'Ministry of Health', 'source' => 'public', 'status' => 'identified', 'tender_stage_id' => $draftStage?->id, 'estimated_value' => 95000000, 'submission_deadline' => now()->addDays(60), 'category' => 'construction', 'region' => 'Western', 'win_probability' => 75, 'strategy_notes' => 'Strong local presence. Partner with local MEP firm.', 'assigned_to' => $mgr->id, 'created_by' => $admin->id]);

        // Bids on Tender 2 (evaluation stage — multiple bidders)
        TenderBid::updateOrCreate(['reference' => 'BID-TST-001', 'company_id' => $cid], ['tender_id' => $tender2->id, 'bidder_name' => 'Roko Construction', 'bidder_email' => 'bids@roko.ug', 'bidder_phone' => '+256 414 233 456', 'bid_amount' => 172000000, 'technical_score' => 78.5, 'financial_score' => 82.0, 'total_score' => 80.3, 'bid_stage_id' => $bidShort?->id, 'stage_changed_at' => now()->subDays(2), 'submitted_at' => now()->subDays(7), 'evaluated_at' => now()->subDays(2), 'evaluation_notes' => 'Strong technical team but slightly above budget estimate.', 'evaluated_by' => $mgr->id, 'created_by' => $admin->id]);
        TenderBid::updateOrCreate(['reference' => 'BID-TST-002', 'company_id' => $cid], ['tender_id' => $tender2->id, 'bidder_name' => 'Dott Services Ltd', 'bidder_email' => 'tenders@dott.co.ug', 'bidder_phone' => '+256 414 567 890', 'bid_amount' => 158000000, 'technical_score' => 85.0, 'financial_score' => 90.5, 'total_score' => 87.8, 'bid_stage_id' => $bidShort?->id, 'stage_changed_at' => now()->subDays(2), 'submitted_at' => now()->subDays(8), 'evaluated_at' => now()->subDays(2), 'evaluation_notes' => 'Lowest compliant bid with excellent past performance.', 'evaluated_by' => $mgr->id, 'created_by' => $admin->id]);
        TenderBid::updateOrCreate(['reference' => 'BID-TST-003', 'company_id' => $cid], ['tender_id' => $tender2->id, 'bidder_name' => 'SBI International', 'bidder_email' => 'info@sbi-intl.com', 'bidder_phone' => '+256 312 100 200', 'bid_amount' => 195000000, 'technical_score' => 70.0, 'financial_score' => 55.0, 'total_score' => 62.5, 'bid_stage_id' => $bidReview?->id, 'stage_changed_at' => now()->subDay(), 'submitted_at' => now()->subDays(6), 'evaluation_notes' => 'High price and limited local experience.', 'created_by' => $admin->id]);

        // Bid on Tender 1 (still preparing — early submission)
        TenderBid::updateOrCreate(['reference' => 'BID-TST-004', 'company_id' => $cid], ['tender_id' => $tender1->id, 'bidder_name' => 'China Wu Yi', 'bidder_email' => 'uganda@chinawuyi.com', 'bidder_phone' => '+256 414 678 900', 'bid_amount' => 240000000, 'bid_stage_id' => $bidSubmitted?->id, 'submitted_at' => now()->subDays(2), 'created_by' => $admin->id]);

        // ── equipment: Assets, Allocations, Fuel Logs ──
        $asset = Asset::updateOrCreate(['asset_tag' => 'AST-EXC-001', 'company_id' => $cid], ['name' => 'CAT 320 Excavator', 'serial_number' => 'CAT320-2024-UG001', 'status' => 'assigned', 'condition' => 'good', 'meter_reading' => 4500, 'meter_unit' => 'hours', 'current_holder_id' => $tech->id, 'purchase_date' => now()->subYears(2), 'purchase_cost' => 350000, 'warranty_expiry' => now()->addYear(), 'last_service_date' => now()->subMonth(), 'next_service_date' => now()->addMonths(2), 'useful_life_years' => 10, 'salvage_value' => 50000, 'created_by' => $admin->id]);
        Asset::updateOrCreate(['asset_tag' => 'AST-CRN-002', 'company_id' => $cid], ['name' => 'Liebherr Tower Crane', 'serial_number' => 'LH-TC-2023-005', 'status' => 'available', 'condition' => 'good', 'meter_reading' => 2200, 'meter_unit' => 'hours', 'purchase_date' => now()->subYear(), 'purchase_cost' => 800000, 'warranty_expiry' => now()->addMonths(6), 'useful_life_years' => 15, 'salvage_value' => 100000, 'created_by' => $admin->id]);
        if ($activeProject) {
            EquipmentAllocation::updateOrCreate(['asset_id' => $asset->id, 'cde_project_id' => $activeProject->id, 'company_id' => $cid], ['operator_id' => $tech->id, 'start_date' => now()->subWeeks(2), 'end_date' => now()->addMonths(2), 'status' => 'active', 'daily_rate' => 500, 'created_by' => $admin->id]);
            EquipmentFuelLog::updateOrCreate(['asset_id' => $asset->id, 'log_date' => now()->subDay()->format('Y-m-d'), 'company_id' => $cid], ['cde_project_id' => $activeProject->id, 'liters' => 120, 'cost_per_liter' => 4.50, 'total_cost' => 540, 'meter_reading' => 4520, 'supplier' => 'Total Energies', 'created_by' => $tech->id]);
        }

        // ── inventory: Products & Operations (Full Spectrum) ──
        self::seedInventoryModuleData($cid, $admin, $mgr, $tech, $activeProject, $projects);

        // Ensure all seeded RFIs have workflow instances
        foreach (\App\Models\Rfi::where('company_id', $cid)->get() as $rfi) {
            if (!$rfi->workflowInstance()->exists()) {
                $rfi->startWorkflow();
            }
        }
        // Ensure all seeded Safety Incidents have workflow instances
        foreach (\App\Models\SafetyIncident::where('company_id', $cid)->get() as $si) {
            if (!$si->workflowInstance()->exists()) {
                $si->startWorkflow();
            }
        }
    }

    private static function seedInventoryModuleData($cid, $admin, $mgr, $tech, $activeProject, $projects): void
    {
        // 1. Seed Product Categories
        $civil = \App\Models\ProductCategory::updateOrCreate(
            ['slug' => 'civil-materials', 'company_id' => $cid],
            ['name' => 'Civil Construction Materials', 'description' => 'Cement, aggregates, steel rebar, sand, bricks and concrete blocks']
        );
        $electrical = \App\Models\ProductCategory::updateOrCreate(
            ['slug' => 'electrical-materials', 'company_id' => $cid],
            ['name' => 'Electrical Package', 'description' => 'Cables, panels, conduits, lighting fixtures and switches']
        );
        $safety = \App\Models\ProductCategory::updateOrCreate(
            ['slug' => 'safety-sheq', 'company_id' => $cid],
            ['name' => 'Safety & PPE Equipment', 'description' => 'Hard hats, boots, high-vis vests, goggles, safety harnesses']
        );
        $mechanical = \App\Models\ProductCategory::updateOrCreate(
            ['slug' => 'mechanical-parts', 'company_id' => $cid],
            ['name' => 'Mechanical & Machinery Consumables', 'description' => 'Hydraulic oil, grease, spare parts, engine filters']
        );
        $general = \App\Models\ProductCategory::updateOrCreate(
            ['slug' => 'general-supplies', 'company_id' => $cid],
            ['name' => 'General Site Supplies', 'description' => 'Tools, timber, nails, fencing mesh, warning tape']
        );

        // 2. Seed Suppliers
        $supCement = Supplier::updateOrCreate(
            ['email' => 'sales@himacement.com', 'company_id' => $cid],
            ['name' => 'Hima Cement Uganda Ltd', 'phone' => '+256 414 456 123', 'address' => 'Industrial Area, Kampala', 'contact_person' => 'Daniel Kibuuka', 'payment_terms' => 'Net 30', 'notes' => 'Primary cement vendor. Direct factory distributor.']
        );
        $supSteel = Supplier::updateOrCreate(
            ['email' => 'tenders@roofings.co.ug', 'company_id' => $cid],
            ['name' => 'Roofings Group Uganda', 'phone' => '+256 312 340 100', 'address' => 'Llubi, Kampala', 'contact_person' => 'Sarah Namubiru', 'payment_terms' => 'Net 45', 'notes' => 'Rebar and electrical wire supplier. Highly reliable.']
        );
        $supTotal = Supplier::updateOrCreate(
            ['email' => 'b2b.sales@totalenergies.co.ug', 'company_id' => $cid],
            ['name' => 'TotalEnergies Marketing Uganda', 'phone' => '+256 414 300 000', 'address' => 'Eighth Street, Industrial Area, Kampala', 'contact_person' => 'Mark Okot', 'payment_terms' => 'Net 15', 'notes' => 'Fuel, grease, and lubricants contracts.']
        );

        // 3. Seed Warehouses
        $whMain = Warehouse::updateOrCreate(
            ['code' => 'KLA-YARD-01', 'company_id' => $cid],
            ['name' => 'Kampala Central Logistics Yard', 'address' => 'Plot 45-47, Industrial Area', 'city' => 'Kampala', 'country' => 'Uganda', 'manager_id' => $mgr->id, 'is_active' => true, 'is_default' => true]
        );
        $whJinja = Warehouse::updateOrCreate(
            ['code' => 'JJA-HUB-02', 'company_id' => $cid],
            ['name' => 'Jinja Transit Storage Yard', 'address' => 'Plot 12, Kyabazinga Way', 'city' => 'Jinja', 'country' => 'Uganda', 'manager_id' => $tech->id, 'is_active' => true, 'is_default' => false]
        );

        // 4. Seed Products
        $cement = Product::updateOrCreate(
            ['sku' => 'CEM-50KG', 'company_id' => $cid],
            [
                'product_category_id' => $civil->id,
                'supplier_id' => $supCement->id,
                'name' => 'Portland Cement 50kg',
                'unit_of_measure' => 'bag',
                'cost_price' => 32000,
                'selling_price' => 35000,
                'reorder_level' => 100,
                'reorder_quantity' => 500,
                'max_order_level' => 2000,
                'is_active' => true,
                'track_inventory' => true,
                'condition' => 'new',
                'lead_time_days' => 3
            ]
        );

        $steel = Product::updateOrCreate(
            ['sku' => 'STL-Y16', 'company_id' => $cid],
            [
                'product_category_id' => $civil->id,
                'supplier_id' => $supSteel->id,
                'name' => 'Steel Reinforcement Y16',
                'unit_of_measure' => 'ton',
                'cost_price' => 3200000,
                'selling_price' => 3500000,
                'reorder_level' => 5,
                'reorder_quantity' => 20,
                'max_order_level' => 50,
                'is_active' => true,
                'track_inventory' => true,
                'condition' => 'new',
                'lead_time_days' => 7
            ]
        );

        $cables = Product::updateOrCreate(
            ['sku' => 'CAB-1.5', 'company_id' => $cid],
            [
                'product_category_id' => $electrical->id,
                'supplier_id' => $supSteel->id,
                'name' => 'Electrical Cables 1.5mm',
                'unit_of_measure' => 'roll',
                'cost_price' => 120000,
                'selling_price' => 150000,
                'reorder_level' => 20,
                'reorder_quantity' => 50,
                'max_order_level' => 150,
                'is_active' => true,
                'track_inventory' => true,
                'condition' => 'new',
                'lead_time_days' => 5
            ]
        );

        $vests = Product::updateOrCreate(
            ['sku' => 'VST-SAFE', 'company_id' => $cid],
            [
                'product_category_id' => $safety->id,
                'name' => 'High-Vis Safety Vest (Emerald Green)',
                'unit_of_measure' => 'each',
                'cost_price' => 15000,
                'selling_price' => 20000,
                'reorder_level' => 30,
                'reorder_quantity' => 100,
                'max_order_level' => 300,
                'is_active' => true,
                'track_inventory' => true,
                'condition' => 'new',
                'lead_time_days' => 2
            ]
        );

        $helmets = Product::updateOrCreate(
            ['sku' => 'HHT-SAFE', 'company_id' => $cid],
            [
                'product_category_id' => $safety->id,
                'name' => 'Heavy-Duty Builder Hard Hat (Yellow)',
                'unit_of_measure' => 'each',
                'cost_price' => 25000,
                'selling_price' => 30000,
                'reorder_level' => 25,
                'reorder_quantity' => 75,
                'max_order_level' => 200,
                'is_active' => true,
                'track_inventory' => true,
                'condition' => 'new',
                'lead_time_days' => 3
            ]
        );

        $oil = Product::updateOrCreate(
            ['sku' => 'OIL-HYD', 'company_id' => $cid],
            [
                'product_category_id' => $mechanical->id,
                'supplier_id' => $supTotal->id,
                'name' => 'Hydraulic Oil ISO 46 (20L)',
                'unit_of_measure' => 'piece',
                'cost_price' => 250000,
                'selling_price' => 300000,
                'reorder_level' => 10,
                'reorder_quantity' => 30,
                'max_order_level' => 100,
                'expiry_tracking_enabled' => true,
                'expiry_date' => now()->addDays(365),
                'is_active' => true,
                'track_inventory' => true,
                'condition' => 'new',
                'lead_time_days' => 4
            ]
        );

        $sand = Product::updateOrCreate(
            ['sku' => 'SND-FINE', 'company_id' => $cid],
            [
                'product_category_id' => $civil->id,
                'name' => 'Lake Sand (Fine Aggregate)',
                'unit_of_measure' => 'cum',
                'cost_price' => 80000,
                'selling_price' => 95000,
                'reorder_level' => 15,
                'reorder_quantity' => 50,
                'max_order_level' => 500,
                'is_active' => true,
                'track_inventory' => true,
                'condition' => 'new',
                'lead_time_days' => 1
            ]
        );

        // 5. Seed Stock Levels
        $products = [$cement, $steel, $cables, $vests, $helmets, $oil, $sand];
        $quantities = [
            'CEM-50KG' => ['wh1' => [800, 100], 'wh2' => [300, 0]],
            'STL-Y16'  => ['wh1' => [25, 5],    'wh2' => [8, 0]],
            'CAB-1.5'  => ['wh1' => [60, 10],   'wh2' => [20, 0]],
            'VST-SAFE' => ['wh1' => [150, 0],   'wh2' => [50, 0]],
            'HHT-SAFE' => ['wh1' => [85, 0],    'wh2' => [30, 0]],
            'OIL-HYD'  => ['wh1' => [40, 2],    'wh2' => [15, 0]],
            'SND-FINE' => ['wh1' => [120, 0],   'wh2' => [30, 0]],
        ];

        foreach ($products as $p) {
            $q = $quantities[$p->sku];
            
            // Warehouse 1 levels
            \App\Models\StockLevel::updateOrCreate(
                ['product_id' => $p->id, 'warehouse_id' => $whMain->id],
                [
                    'quantity_on_hand' => $q['wh1'][0],
                    'quantity_reserved' => $q['wh1'][1],
                    'quantity_available' => $q['wh1'][0] - $q['wh1'][1],
                    'bin_location' => 'ZONE-A' . rand(1, 4) . '-B' . rand(1, 9),
                    'average_cost' => $p->cost_price,
                    'last_movement_at' => now()->subDays(rand(1, 10))
                ]
            );

            // Warehouse 2 levels
            \App\Models\StockLevel::updateOrCreate(
                ['product_id' => $p->id, 'warehouse_id' => $whJinja->id],
                [
                    'quantity_on_hand' => $q['wh2'][0],
                    'quantity_reserved' => $q['wh2'][1],
                    'quantity_available' => $q['wh2'][0] - $q['wh2'][1],
                    'bin_location' => 'ZONE-J' . rand(1, 2) . '-B' . rand(1, 5),
                    'average_cost' => $p->cost_price * 1.02,
                    'last_movement_at' => now()->subDays(rand(1, 10))
                ]
            );
        }

        // 6. Purchase Orders (PO)
        // PO 1: Received
        $po1 = PurchaseOrder::updateOrCreate(
            ['po_number' => 'PO-2025-001', 'company_id' => $cid],
            [
                'cde_project_id' => $activeProject?->id,
                'supplier_id' => $supCement->id,
                'warehouse_id' => $whMain->id,
                'status' => 'received',
                'order_date' => now()->subDays(30),
                'expected_date' => now()->subDays(25),
                'received_date' => now()->subDays(24),
                'subtotal' => 32000 * 500,
                'tax_amount' => (32000 * 500) * 0.18,
                'shipping_cost' => 500000,
                'total_amount' => (32000 * 500) * 1.18 + 500000,
                'is_quarterly' => true,
                'quarter' => 'Q4-2025',
                'payment_terms' => 'Net 30',
                'delivery_address' => $whMain->address,
                'currency' => 'UGX',
                'notes' => 'Special cement batch order for foundation works.',
                'created_by' => $mgr->id,
                'approved_by' => $admin->id,
                'submitted_at' => now()->subDays(30),
                'approved_at' => now()->subDays(29),
            ]
        );

        $poi1 = \App\Models\PurchaseOrderItem::updateOrCreate(
            ['purchase_order_id' => $po1->id, 'product_id' => $cement->id],
            [
                'quantity_ordered' => 500,
                'quantity_received' => 500,
                'unit_price' => 32000,
                'total_price' => 32000 * 500,
                'notes' => 'Must be delivered dry and wrapped on plastic pallets.'
            ]
        );

        // Goods Received Note (GRN) for PO 1
        $grn1 = \App\Models\GoodsReceivedNote::updateOrCreate(
            ['grn_number' => 'GRN-2025-001', 'company_id' => $cid],
            [
                'cde_project_id' => $activeProject?->id,
                'purchase_order_id' => $po1->id,
                'supplier_id' => $supCement->id,
                'warehouse_id' => $whMain->id,
                'status' => 'accepted',
                'received_date' => now()->subDays(24),
                'delivery_note_ref' => 'DN-HIMA-99881',
                'delivery_date' => now()->subDays(24),
                'carrier_name' => 'Kibuuka Logistics Ltd',
                'vehicle_plate' => 'UBA 123A',
                'driver_name' => 'Fred Mukasa',
                'inspector_id' => $tech->id,
                'inspection_passed' => true,
                'invoice_reference' => 'INV-HIMA-8812',
                'notes' => 'Seals intact. Moisture levels checked and passed. Delivery accepted in full.',
                'received_by' => $tech->id,
                'inspected_by' => $tech->id,
            ]
        );

        \App\Models\GrnItem::updateOrCreate(
            ['goods_received_note_id' => $grn1->id, 'product_id' => $cement->id],
            [
                'purchase_order_item_id' => $poi1->id,
                'description' => 'Portland Cement 50kg bags',
                'quantity_expected' => 500,
                'quantity_received' => 500,
                'quantity_accepted' => 500,
                'quantity_rejected' => 0,
                'condition' => 'good'
            ]
        );

        // PO 2: Partially Received
        $po2 = PurchaseOrder::updateOrCreate(
            ['po_number' => 'PO-2026-002', 'company_id' => $cid],
            [
                'cde_project_id' => $activeProject?->id,
                'supplier_id' => $supSteel->id,
                'warehouse_id' => $whMain->id,
                'status' => 'partially_received',
                'order_date' => now()->subDays(15),
                'expected_date' => now()->subDays(10),
                'received_date' => now()->subDays(9),
                'subtotal' => (3200000 * 10) + (120000 * 20),
                'tax_amount' => 34400000 * 0.18,
                'shipping_cost' => 800000,
                'total_amount' => 34400000 * 1.18 + 800000,
                'is_quarterly' => true,
                'quarter' => 'Q1-2026',
                'payment_terms' => 'Net 45',
                'delivery_address' => $whMain->address,
                'currency' => 'UGX',
                'notes' => 'Steel rebar and wire cabling package.',
                'created_by' => $mgr->id,
                'approved_by' => $admin->id,
                'submitted_at' => now()->subDays(15),
                'approved_at' => now()->subDays(14),
            ]
        );

        $poi2_1 = \App\Models\PurchaseOrderItem::updateOrCreate(
            ['purchase_order_id' => $po2->id, 'product_id' => $steel->id],
            [
                'quantity_ordered' => 10,
                'quantity_received' => 6,
                'unit_price' => 3200000,
                'total_price' => 32000000,
                'notes' => 'ASTM standard grade 60 steel rebar.'
            ]
        );

        $poi2_2 = \App\Models\PurchaseOrderItem::updateOrCreate(
            ['purchase_order_id' => $po2->id, 'product_id' => $cables->id],
            [
                'quantity_ordered' => 20,
                'quantity_received' => 20,
                'unit_price' => 120000,
                'total_price' => 2400000,
                'notes' => '1.5mm standard insulated wiring.'
            ]
        );

        // Goods Received Note (GRN) for PO 2
        $grn2 = \App\Models\GoodsReceivedNote::updateOrCreate(
            ['grn_number' => 'GRN-2026-002', 'company_id' => $cid],
            [
                'cde_project_id' => $activeProject?->id,
                'purchase_order_id' => $po2->id,
                'supplier_id' => $supSteel->id,
                'warehouse_id' => $whMain->id,
                'status' => 'partial',
                'received_date' => now()->subDays(9),
                'delivery_note_ref' => 'DN-ROOF-77221',
                'delivery_date' => now()->subDays(9),
                'carrier_name' => 'Roofings Fleet Services',
                'vehicle_plate' => 'UBC 999F',
                'driver_name' => 'Moses Ssewankambo',
                'inspector_id' => $tech->id,
                'inspection_passed' => true,
                'invoice_reference' => 'INV-ROOF-5531',
                'notes' => 'Received 6 tons out of 10 tons of steel rebar. Cabling received in full. Awaiting balance of steel next week.',
                'received_by' => $tech->id,
                'inspected_by' => $tech->id,
            ]
        );

        \App\Models\GrnItem::updateOrCreate(
            ['goods_received_note_id' => $grn2->id, 'product_id' => $steel->id],
            [
                'purchase_order_item_id' => $poi2_1->id,
                'description' => 'Steel Reinforcement Y16',
                'quantity_expected' => 10,
                'quantity_received' => 6,
                'quantity_accepted' => 6,
                'quantity_rejected' => 0,
                'condition' => 'good'
            ]
        );

        \App\Models\GrnItem::updateOrCreate(
            ['goods_received_note_id' => $grn2->id, 'product_id' => $cables->id],
            [
                'purchase_order_item_id' => $poi2_2->id,
                'description' => 'Electrical Cables 1.5mm rolls',
                'quantity_expected' => 20,
                'quantity_received' => 20,
                'quantity_accepted' => 20,
                'quantity_rejected' => 0,
                'condition' => 'good'
            ]
        );

        // PO 3: Approved (Ordered) but not yet received
        $po3 = PurchaseOrder::updateOrCreate(
            ['po_number' => 'PO-2026-003', 'company_id' => $cid],
            [
                'cde_project_id' => $activeProject?->id,
                'supplier_id' => $supTotal->id,
                'warehouse_id' => $whMain->id,
                'status' => 'approved',
                'order_date' => now()->subDays(2),
                'expected_date' => now()->addDays(5),
                'subtotal' => 250000 * 30,
                'tax_amount' => 7500000 * 0.18,
                'shipping_cost' => 150000,
                'total_amount' => 7500000 * 1.18 + 150000,
                'is_quarterly' => false,
                'payment_terms' => 'Net 15',
                'delivery_address' => $whMain->address,
                'currency' => 'UGX',
                'notes' => 'Hydraulic oil refills for Caterpillar machinery fleet.',
                'created_by' => $mgr->id,
                'approved_by' => $admin->id,
                'submitted_at' => now()->subDays(2),
                'approved_at' => now()->subDay(),
            ]
        );

        \App\Models\PurchaseOrderItem::updateOrCreate(
            ['purchase_order_id' => $po3->id, 'product_id' => $oil->id],
            [
                'quantity_ordered' => 30,
                'quantity_received' => 0,
                'unit_price' => 250000,
                'total_price' => 7500000,
                'notes' => 'Lubricants and oil drums.'
            ]
        );

        // 7. Material Requisitions (MR)
        // MR 1: Approved, Pending Issuance
        $mr1 = MaterialRequisition::updateOrCreate(
            ['requisition_number' => 'REQ-KLA-101', 'company_id' => $cid],
            [
                'cde_project_id' => $activeProject?->id,
                'warehouse_id' => $whMain->id,
                'requester_id' => $tech->id,
                'status' => 'approved',
                'priority' => 'high',
                'required_date' => now()->addDays(2),
                'purpose' => 'Site drainage channel concrete reinforcement works.',
                'notes' => 'Urgent requisition. Work scheduled for Wednesday morning.',
                'approved_by' => $mgr->id,
                'approved_at' => now()->subHours(12),
            ]
        );

        \App\Models\MaterialRequisitionItem::updateOrCreate(
            ['material_requisition_id' => $mr1->id, 'product_id' => $cement->id],
            [
                'quantity_requested' => 150,
                'quantity_approved' => 150,
                'quantity_issued' => 0,
                'notes' => 'Hima MultiPurpose Cement.'
            ]
        );

        \App\Models\MaterialRequisitionItem::updateOrCreate(
            ['material_requisition_id' => $mr1->id, 'product_id' => $steel->id],
            [
                'quantity_requested' => 2,
                'quantity_approved' => 2,
                'quantity_issued' => 0,
                'notes' => 'Y16 reinforcing steel rebar.'
            ]
        );

        // MR 2: Fully Issued
        $mr2 = MaterialRequisition::updateOrCreate(
            ['requisition_number' => 'REQ-KLA-102', 'company_id' => $cid],
            [
                'cde_project_id' => $activeProject?->id,
                'warehouse_id' => $whMain->id,
                'requester_id' => $tech->id,
                'status' => 'issued',
                'priority' => 'normal',
                'required_date' => now()->subDays(3),
                'purpose' => 'Cabling and distribution board setup in temp site office.',
                'notes' => 'Requires safety gear allocation as well.',
                'approved_by' => $mgr->id,
                'approved_at' => now()->subDays(3),
            ]
        );

        \App\Models\MaterialRequisitionItem::updateOrCreate(
            ['material_requisition_id' => $mr2->id, 'product_id' => $cables->id],
            [
                'quantity_requested' => 10,
                'quantity_approved' => 10,
                'quantity_issued' => 10,
                'notes' => '1.5mm cabling rolls.'
            ]
        );

        \App\Models\MaterialRequisitionItem::updateOrCreate(
            ['material_requisition_id' => $mr2->id, 'product_id' => $vests->id],
            [
                'quantity_requested' => 15,
                'quantity_approved' => 15,
                'quantity_issued' => 15,
                'notes' => 'Emerald safety vests for subcontractors.'
            ]
        );

        // Material Issuance for MR 2
        $issue1 = \App\Models\MaterialIssuance::updateOrCreate(
            ['issuance_number' => 'ISS-2026-001', 'company_id' => $cid],
            [
                'cde_project_id' => $activeProject?->id,
                'warehouse_id' => $whMain->id,
                'issued_to' => $tech->id,
                'issued_to_name' => $tech->name,
                'purpose' => 'site_use',
                'status' => 'issued',
                'issue_date' => now()->subDays(3),
                'material_requisition_id' => $mr2->id,
                'created_by' => $mgr->id,
                'approved_by' => $mgr->id,
                'notes' => 'Fully issued and verified on site.'
            ]
        );

        \App\Models\MaterialIssuanceItem::updateOrCreate(
            ['material_issuance_id' => $issue1->id, 'product_id' => $cables->id],
            [
                'quantity_issued' => 10,
                'quantity_returned' => 0,
                'condition_on_issue' => 'new'
            ]
        );

        \App\Models\MaterialIssuanceItem::updateOrCreate(
            ['material_issuance_id' => $issue1->id, 'product_id' => $vests->id],
            [
                'quantity_issued' => 15,
                'quantity_returned' => 0,
                'condition_on_issue' => 'new'
            ]
        );

        // 8. Stock Transfer
        $transfer1 = \App\Models\StockTransfer::updateOrCreate(
            ['transfer_number' => 'ST-2026-001', 'company_id' => $cid],
            [
                'cde_project_id' => $activeProject?->id,
                'from_warehouse_id' => $whMain->id,
                'to_warehouse_id' => $whJinja->id,
                'status' => 'received',
                'priority' => 'normal',
                'transfer_date' => now()->subDays(5),
                'requested_date' => now()->subDays(7),
                'shipped_date' => now()->subDays(6),
                'received_date' => now()->subDays(5),
                'reason' => 'Transferring extra cement batch and cable stock to Jinja transit yard for Jinja fiber backbone project works.',
                'created_by' => $mgr->id,
                'requested_by' => $tech->id,
                'approved_by' => $mgr->id,
                'shipped_by' => $tech->id,
                'received_by' => $tech->id,
                'level1_approved_by' => $mgr->id,
                'level1_approved_at' => now()->subDays(7),
            ]
        );

        \App\Models\StockTransferItem::updateOrCreate(
            ['stock_transfer_id' => $transfer1->id, 'product_id' => $cement->id],
            [
                'quantity_requested' => 50,
                'quantity_shipped' => 50,
                'quantity_received' => 50,
                'notes' => 'Ensure bag dry transport covers are used.'
            ]
        );

        \App\Models\StockTransferItem::updateOrCreate(
            ['stock_transfer_id' => $transfer1->id, 'product_id' => $cables->id],
            [
                'quantity_requested' => 5,
                'quantity_shipped' => 5,
                'quantity_received' => 5,
                'notes' => '1.5mm rolls.'
            ]
        );

        // 9. Stock Adjustment
        $adjustment1 = \App\Models\StockAdjustment::updateOrCreate(
            ['adjustment_number' => 'ADJ-2026-001', 'company_id' => $cid],
            [
                'cde_project_id' => $activeProject?->id,
                'warehouse_id' => $whMain->id,
                'product_id' => $cement->id,
                'type' => 'damage',
                'quantity_before' => 805,
                'quantity_after' => 800,
                'quantity_change' => -5,
                'reason' => 'Water damage to 5 bags of Portland cement due to rainwater leakage near bay 4.',
                'notes' => 'Leak repaired. 5 bags written off as scrap.',
                'performed_by' => $tech->id,
                'approved_by' => $mgr->id,
            ]
        );

        // 10. Inventory Transactions (Movement Ledger)
        $txns = [
            [
                'product' => $cement,
                'warehouse' => $whMain,
                'type' => 'purchase',
                'quantity' => 500,
                'before' => 300,
                'after' => 800,
                'cost' => 32000,
                'ref_type' => 'GoodsReceivedNote',
                'ref_id' => $grn1->id,
                'notes' => 'Receipt of PO-2025-001.'
            ],
            [
                'product' => $steel,
                'warehouse' => $whMain,
                'type' => 'purchase',
                'quantity' => 6,
                'before' => 19,
                'after' => 25,
                'cost' => 3200000,
                'ref_type' => 'GoodsReceivedNote',
                'ref_id' => $grn2->id,
                'notes' => 'Partial receipt of PO-2026-002.'
            ],
            [
                'product' => $cables,
                'warehouse' => $whMain,
                'type' => 'purchase',
                'quantity' => 20,
                'before' => 40,
                'after' => 60,
                'cost' => 120000,
                'ref_type' => 'GoodsReceivedNote',
                'ref_id' => $grn2->id,
                'notes' => 'Full receipt of PO-2026-002 cabling.'
            ],
            [
                'product' => $cables,
                'warehouse' => $whMain,
                'type' => 'sale',
                'quantity' => -10,
                'before' => 70,
                'after' => 60,
                'cost' => 120000,
                'ref_type' => 'MaterialIssuance',
                'ref_id' => $issue1->id,
                'notes' => 'Site office cabling issue.'
            ],
            [
                'product' => $vests,
                'warehouse' => $whMain,
                'type' => 'sale',
                'quantity' => -15,
                'before' => 165,
                'after' => 150,
                'cost' => 15000,
                'ref_type' => 'MaterialIssuance',
                'ref_id' => $issue1->id,
                'notes' => 'High-vis vests for contractors.'
            ],
            [
                'product' => $cement,
                'warehouse' => $whMain,
                'type' => 'transfer_out',
                'quantity' => -50,
                'before' => 855,
                'after' => 805,
                'cost' => 32000,
                'ref_type' => 'StockTransfer',
                'ref_id' => $transfer1->id,
                'notes' => 'Stock transfer to Jinja.'
            ],
            [
                'product' => $cement,
                'warehouse' => $whJinja,
                'type' => 'transfer_in',
                'quantity' => 50,
                'before' => 250,
                'after' => 300,
                'cost' => 32640,
                'ref_type' => 'StockTransfer',
                'ref_id' => $transfer1->id,
                'notes' => 'Received stock transfer from Kampala.'
            ],
            [
                'product' => $cement,
                'warehouse' => $whMain,
                'type' => 'adjustment',
                'quantity' => -5,
                'before' => 805,
                'after' => 800,
                'cost' => 32000,
                'ref_type' => 'StockAdjustment',
                'ref_id' => $adjustment1->id,
                'notes' => 'Write off water damaged bags.'
            ],
        ];

        foreach ($txns as $idx => $t) {
            \App\Models\InventoryTransaction::updateOrCreate(
                [
                    'company_id' => $cid,
                    'product_id' => $t['product']->id,
                    'warehouse_id' => $t['warehouse']->id,
                    'reference_type' => $t['ref_type'],
                    'reference_id' => $t['ref_id'],
                ],
                [
                    'type' => $t['type'],
                    'quantity' => $t['quantity'],
                    'balance_before' => $t['before'],
                    'balance_after' => $t['after'],
                    'unit_cost' => $t['cost'],
                    'notes' => $t['notes'],
                    'created_by' => $mgr->id,
                    'created_at' => now()->subDays(10 - $idx),
                ]
            );
        }

        // 11. Inventory Audit Logs (URA compliance)
        $auditLogs = [
            [
                'event' => 'po_created',
                'desc' => 'Purchase Order PO-2025-001 created for Hima Cement Uganda Ltd by Daniel Kibuuka.',
                'prod' => $cement,
                'wh' => $whMain,
                'ref_type' => 'PurchaseOrder',
                'ref_id' => $po1->id,
                'ref_num' => 'PO-2025-001',
                'before' => null, 'after' => null, 'cost' => 32000
            ],
            [
                'event' => 'po_approved',
                'desc' => 'Purchase Order PO-2025-001 approved by admin@test-company.com.',
                'prod' => $cement,
                'wh' => $whMain,
                'ref_type' => 'PurchaseOrder',
                'ref_id' => $po1->id,
                'ref_num' => 'PO-2025-001',
                'before' => null, 'after' => null, 'cost' => 32000
            ],
            [
                'event' => 'po_received',
                'desc' => 'Goods Received Note GRN-2025-001 processed. Portland Cement 50kg (500 bags) added to stock at Kampala Central Logistics Yard.',
                'prod' => $cement,
                'wh' => $whMain,
                'ref_type' => 'GoodsReceivedNote',
                'ref_id' => $grn1->id,
                'ref_num' => 'GRN-2025-001',
                'before' => 300, 'after' => 800, 'cost' => 32000
            ],
            [
                'event' => 'material_issued',
                'desc' => 'Material Issuance ISS-2026-001 completed. 10 rolls of cables and 15 safety vests issued to John Technician.',
                'prod' => $cables,
                'wh' => $whMain,
                'ref_type' => 'MaterialIssuance',
                'ref_id' => $issue1->id,
                'ref_num' => 'ISS-2026-001',
                'before' => 70, 'after' => 60, 'cost' => 120000
            ],
            [
                'event' => 'stock_transferred',
                'desc' => 'Stock Transfer ST-2026-001 completed. 50 bags of Portland Cement transferred from Kampala Central Logistics Yard to Jinja Transit Storage Yard.',
                'prod' => $cement,
                'wh' => $whMain,
                'ref_type' => 'StockTransfer',
                'ref_id' => $transfer1->id,
                'ref_num' => 'ST-2026-001',
                'before' => 855, 'after' => 805, 'cost' => 32000
            ],
            [
                'event' => 'stock_adjusted',
                'desc' => 'Physical stock adjustment ADJ-2026-001. Portland Cement reduced by 5 bags at Kampala Central Logistics Yard due to water damage.',
                'prod' => $cement,
                'wh' => $whMain,
                'ref_type' => 'StockAdjustment',
                'ref_id' => $adjustment1->id,
                'ref_num' => 'ADJ-2026-001',
                'before' => 805, 'after' => 800, 'cost' => 32000
            ]
        ];

        foreach ($auditLogs as $idx => $al) {
            \App\Models\InventoryAuditLog::create([
                'company_id' => $cid,
                'cde_project_id' => $activeProject?->id,
                'event_type' => $al['event'],
                'description' => $al['desc'],
                'product_id' => $al['prod']?->id,
                'warehouse_id' => $al['wh']?->id,
                'quantity_before' => $al['before'],
                'quantity_after' => $al['after'],
                'quantity_change' => ($al['after'] !== null && $al['before'] !== null) ? ($al['after'] - $al['before']) : null,
                'unit_cost' => $al['cost'],
                'total_value' => ($al['after'] !== null) ? ($al['after'] * $al['cost']) : null,
                'reference_type' => $al['ref_type'],
                'reference_id' => $al['ref_id'],
                'reference_number' => $al['ref_num'],
                'performed_by' => $mgr->id,
                'ip_address' => '127.0.0.1',
                'created_at' => now()->subDays(10 - $idx),
            ]);
        }
    }

    private static function getTaskTemplates(string $type, string $status): array
    {
        $templates = [];

        if ($type === 'road') {
            $templates = [
                // 1.0 MOBILIZATION
                ['title' => 'Project Mobilization & Office Setup', 'is_summary' => true, 'parent' => null, 'duration' => 15],
                ['title' => 'Secure Environmental & Right-of-Way Clearances', 'is_summary' => false, 'parent' => 0, 'duration' => 10, 'pred' => null],
                ['title' => 'Establish Site Offices, Material Yards & Testing Lab', 'is_summary' => false, 'parent' => 0, 'duration' => 12, 'pred' => 1],
                ['title' => 'Topographical Survey & Alignment Stakeout', 'is_summary' => false, 'parent' => 0, 'duration' => 8, 'pred' => 2],
                ['title' => 'Mobilization Milestone', 'is_summary' => false, 'is_milestone' => true, 'parent' => 0, 'duration' => 0, 'pred' => 3],

                // 2.0 SITE CLEARING & EARTHWORKS
                ['title' => 'Site Clearing & Earthworks', 'is_summary' => true, 'parent' => null, 'duration' => 30],
                ['title' => 'Bush Clearing & Topsoil Stripping (Chainage 0-10km)', 'is_summary' => false, 'parent' => 5, 'duration' => 15, 'pred' => 4],
                ['title' => 'Subgrade Excavation & Cut-and-Fill Operations', 'is_summary' => false, 'parent' => 5, 'duration' => 20, 'pred' => 6],
                ['title' => 'Subgrade Compaction & Core Cutter Density Testing', 'is_summary' => false, 'parent' => 5, 'duration' => 10, 'pred' => 7],

                // 3.0 DRAINAGE WORKS
                ['title' => 'Drainage Infrastructure', 'is_summary' => true, 'parent' => null, 'duration' => 25],
                ['title' => 'Trench Excavation for Concrete Box Culverts', 'is_summary' => false, 'parent' => 9, 'duration' => 12, 'pred' => 8],
                ['title' => 'Installation of Pre-Cast Culvert Rings & Wings', 'is_summary' => false, 'parent' => 9, 'duration' => 15, 'pred' => 10],
                ['title' => 'Drainage Backfilling & Inlet/Outlet Masonry', 'is_summary' => false, 'parent' => 9, 'duration' => 8, 'pred' => 11],

                // 4.0 PAVEMENT LAYERS
                ['title' => 'Pavement Sub-Base & Base Courses', 'is_summary' => true, 'parent' => null, 'duration' => 35],
                ['title' => 'Laying of Natural Gravel Sub-Base (G30 Class)', 'is_summary' => false, 'parent' => 13, 'duration' => 18, 'pred' => 12],
                ['title' => 'Laying of Crushed Stone Base Course (CRR Class)', 'is_summary' => false, 'parent' => 13, 'duration' => 15, 'pred' => 14],
                ['title' => 'Application of MC-30 Bituminous Prime Coat', 'is_summary' => false, 'parent' => 13, 'duration' => 5, 'pred' => 15],

                // 5.0 SURFACING
                ['title' => 'Asphalt Surfacing & Markings', 'is_summary' => true, 'parent' => null, 'duration' => 20],
                ['title' => 'Asphalt Concrete Binder Course Layer Laying', 'is_summary' => false, 'parent' => 17, 'duration' => 10, 'pred' => 16],
                ['title' => 'Asphalt Wearing Course Final Layer Laying', 'is_summary' => false, 'parent' => 17, 'duration' => 8, 'pred' => 18],
                ['title' => 'Thermoplastic Road Markings & Signage Installation', 'is_summary' => false, 'parent' => 17, 'duration' => 7, 'pred' => 19],

                // 6.0 COMMISSIONING
                ['title' => 'Pre-Commissioning Inspection & Snagging', 'is_summary' => false, 'parent' => null, 'duration' => 10, 'pred' => 20],
                ['title' => 'Project Handover Ceremony & Official Opening', 'is_summary' => false, 'is_milestone' => true, 'parent' => null, 'duration' => 0, 'pred' => 21]
            ];
        } elseif ($type === 'energy') {
            $templates = [
                // 1.0 SITE PREPARATION
                ['title' => 'Engineering & Site Preparation', 'is_summary' => true, 'parent' => null, 'duration' => 20],
                ['title' => 'Detailed Geotechnical & Solar Irradiation Studies', 'is_summary' => false, 'parent' => 0, 'duration' => 10, 'pred' => null],
                ['title' => 'Site Grading, Clearing & Perimeter Security Fencing', 'is_summary' => false, 'parent' => 0, 'duration' => 15, 'pred' => 1],
                ['title' => 'Access Road Construction & Drainage Channels', 'is_summary' => false, 'parent' => 0, 'duration' => 10, 'pred' => 2],

                // 2.0 STRUCTURAL FOUNDATIONS
                ['title' => 'Civil Works & Mounting Structure Foundations', 'is_summary' => true, 'parent' => null, 'duration' => 25],
                ['title' => 'Piling Operations for Solar Panel Racking', 'is_summary' => false, 'parent' => 4, 'duration' => 15, 'pred' => 3],
                ['title' => 'Assembly and Installation of Steel Mounting Racks', 'is_summary' => false, 'parent' => 4, 'duration' => 18, 'pred' => 5],

                // 3.0 PV SYSTEM INSTALLATION
                ['title' => 'Photovoltaic (PV) Module Installation', 'is_summary' => true, 'parent' => null, 'duration' => 30],
                ['title' => 'Mounting & Clamping of 10MW PV Solar Panels', 'is_summary' => false, 'parent' => 7, 'duration' => 20, 'pred' => 6],
                ['title' => 'DC String Cabling, Combiner Box Wiring & Grounding', 'is_summary' => false, 'parent' => 7, 'duration' => 15, 'pred' => 8],

                // 4.0 POWER CONVERSION & ELECTRICAL
                ['title' => 'Power Inverters & Electrical Systems', 'is_summary' => true, 'parent' => null, 'duration' => 20],
                ['title' => 'Installation of Solar Inverters & Step-up Transformers', 'is_summary' => false, 'parent' => 10, 'duration' => 12, 'pred' => 9],
                ['title' => 'AC Cabling & Switchyard Equipment Installation', 'is_summary' => false, 'parent' => 10, 'duration' => 10, 'pred' => 11],

                // 5.0 GRID CONNECTION
                ['title' => 'Grid Transmission Line Construction', 'is_summary' => true, 'parent' => null, 'duration' => 25],
                ['title' => 'Erection of Transmission Poles to Substation', 'is_summary' => false, 'parent' => 13, 'duration' => 15, 'pred' => 12],
                ['title' => 'Cable Stringing, Sagging & Insulator Testing', 'is_summary' => false, 'parent' => 13, 'duration' => 12, 'pred' => 14],

                // 6.0 TESTING & COMMISSIONING
                ['title' => 'Testing & Grid Energization', 'is_summary' => true, 'parent' => null, 'duration' => 20],
                ['title' => 'Pre-Commissioning Insulation & Relay Coordination Tests', 'is_summary' => false, 'parent' => 16, 'duration' => 10, 'pred' => 15],
                ['title' => 'Inverter Commissioning & SCADA Integration', 'is_summary' => false, 'parent' => 16, 'duration' => 8, 'pred' => 17],
                ['title' => 'National Grid Interconnection & Commercial Operation', 'is_summary' => false, 'is_milestone' => true, 'parent' => 16, 'duration' => 0, 'pred' => 18]
            ];
        } elseif ($type === 'building') {
            $templates = [
                // 1.0 SUBSTRUCTURE
                ['title' => 'Substructure Works', 'is_summary' => true, 'parent' => null, 'duration' => 30],
                ['title' => 'Site Excavation & Pit Blasting to Level', 'is_summary' => false, 'parent' => 0, 'duration' => 15, 'pred' => null],
                ['title' => 'Anti-Termite Treatment & Blinding Concrete Layer', 'is_summary' => false, 'parent' => 0, 'duration' => 5, 'pred' => 1],
                ['title' => 'Reinforced Concrete Raft Foundation Pouring', 'is_summary' => false, 'parent' => 0, 'duration' => 12, 'pred' => 2],
                ['title' => 'Foundation Curing & Waterproofing Membranes', 'is_summary' => false, 'parent' => 0, 'duration' => 8, 'pred' => 3],

                // 2.0 SUPERSTRUCTURE
                ['title' => 'Superstructure Frame Construction', 'is_summary' => true, 'parent' => null, 'duration' => 45],
                ['title' => 'Formwork Erection & Column Rebar Fixing (G-F)', 'is_summary' => false, 'parent' => 5, 'duration' => 14, 'pred' => 4],
                ['title' => 'Concrete Pour for Ground Floor Columns & Beams', 'is_summary' => false, 'parent' => 5, 'duration' => 8, 'pred' => 6],
                ['title' => 'Suspended Floor Slab Reinforcement & Cast (1st F)', 'is_summary' => false, 'parent' => 5, 'duration' => 15, 'pred' => 7],
                ['title' => 'Roof Slab Casting & Parapet Wall Construction', 'is_summary' => false, 'parent' => 5, 'duration' => 12, 'pred' => 8],

                // 3.0 WALLING & ENCLOSURE
                ['title' => 'Masonry & Wall Framing', 'is_summary' => true, 'parent' => null, 'duration' => 25],
                ['title' => 'Solid Block Walling for External Enclosures', 'is_summary' => false, 'parent' => 10, 'duration' => 15, 'pred' => 9],
                ['title' => 'Internal Partitioning (Brickwork & Drywall)', 'is_summary' => false, 'parent' => 10, 'duration' => 12, 'pred' => 11],

                // 4.0 MEP INSTALLATION
                ['title' => 'Mechanical, Electrical & Plumbing (MEP) First Fix', 'is_summary' => true, 'parent' => null, 'duration' => 30],
                ['title' => 'Electrical Conduit Laying & Switchbox Installation', 'is_summary' => false, 'parent' => 13, 'duration' => 15, 'pred' => 12],
                ['title' => 'Water Supply Pipes & Drainage Stack Installation', 'is_summary' => false, 'parent' => 13, 'duration' => 12, 'pred' => 14],

                // 5.0 FINISHES
                ['title' => 'Finishing & Glazing Works', 'is_summary' => true, 'parent' => null, 'duration' => 35],
                ['title' => 'Wall Plastering, Undercoat Painting & Tiling', 'is_summary' => false, 'parent' => 16, 'duration' => 20, 'pred' => 15],
                ['title' => 'Aluminum Window Glazing & External Painting', 'is_summary' => false, 'parent' => 16, 'duration' => 15, 'pred' => 17],

                // 6.0 HANDOVER
                ['title' => 'Testing, Commissioning & Safety Clearance', 'is_summary' => false, 'parent' => null, 'duration' => 10, 'pred' => 18],
                ['title' => 'Final Client Handover & Occupancy Permit Issued', 'is_summary' => false, 'is_milestone' => true, 'parent' => null, 'duration' => 0, 'pred' => 19]
            ];
        } elseif ($type === 'water') {
            $templates = [
                // 1.0 CIVIL INTAKE
                ['title' => 'Water Intake Structure Construction', 'is_summary' => true, 'parent' => null, 'duration' => 25],
                ['title' => 'River Bed Excavation & Cofferdam Installation', 'is_summary' => false, 'parent' => 0, 'duration' => 12, 'pred' => null],
                ['title' => 'Reinforced Concrete Intake Screen Chamber Casting', 'is_summary' => false, 'parent' => 0, 'duration' => 15, 'pred' => 1],
                ['title' => 'Inlet Pipe Sluice Gate Installation & Testing', 'is_summary' => false, 'parent' => 0, 'duration' => 8, 'pred' => 2],

                // 2.0 WATER TANKS
                ['title' => 'Clarifier & Sedimentation Tanks', 'is_summary' => true, 'parent' => null, 'duration' => 35],
                ['title' => 'Excavation & Base Concrete for Clarifier 1', 'is_summary' => false, 'parent' => 4, 'duration' => 15, 'pred' => 3],
                ['title' => 'Circular Tank Wall Formwork & Concrete Pour', 'is_summary' => false, 'parent' => 4, 'duration' => 18, 'pred' => 5],
                ['title' => 'Clarifier Sludge Scraper Mechanism Installation', 'is_summary' => false, 'parent' => 4, 'duration' => 10, 'pred' => 6],

                // 3.0 FILTRATION UNIT
                ['title' => 'Rapid Sand Filtration Block', 'is_summary' => true, 'parent' => null, 'duration' => 30],
                ['title' => 'Filtration Galleries Cast-in-situ Concrete Works', 'is_summary' => false, 'parent' => 8, 'duration' => 15, 'pred' => 7],
                ['title' => 'Under-Drain System Installation & Media Placement', 'is_summary' => false, 'parent' => 8, 'duration' => 12, 'pred' => 9],

                // 4.0 CHEMICAL TREATMENT
                ['title' => 'Chemical Dosing & Chlorination House', 'is_summary' => true, 'parent' => null, 'duration' => 20],
                ['title' => 'Alum & Chlorine Dosing Pumps Assembly', 'is_summary' => false, 'parent' => 11, 'duration' => 10, 'pred' => 10],
                ['title' => 'Gas Chlorination Leak Sensors & Piping Setup', 'is_summary' => false, 'parent' => 11, 'duration' => 12, 'pred' => 12],

                // 5.0 PUMPING STATION
                ['title' => 'High-Lift Pumping Station & Control Panel', 'is_summary' => true, 'parent' => null, 'duration' => 25],
                ['title' => 'Installation of 300kW High-Lift Centrifugal Pumps', 'is_summary' => false, 'parent' => 14, 'duration' => 12, 'pred' => 13],
                ['title' => 'Electrical Control Panels & SCADA Wiring', 'is_summary' => false, 'parent' => 14, 'duration' => 15, 'pred' => 15],

                // 6.0 SYSTEM TESTING
                ['title' => 'System Hydro-testing & Disinfection Run', 'is_summary' => false, 'parent' => null, 'duration' => 12, 'pred' => 16],
                ['title' => 'Water Quality Approval & Commissioning Clearance', 'is_summary' => false, 'is_milestone' => true, 'parent' => null, 'duration' => 0, 'pred' => 17]
            ];
        } else { // telecom
            $templates = [
                // 1.0 PERMITS
                ['title' => 'Rights-of-Way & Route Survey', 'is_summary' => true, 'parent' => null, 'duration' => 15],
                ['title' => 'Detailed Route Survey & Utility Mapping (Jinja-Iganga)', 'is_summary' => false, 'parent' => 0, 'duration' => 8, 'pred' => null],
                ['title' => 'Acquire Municipal Rights-of-Way permits', 'is_summary' => false, 'parent' => 0, 'duration' => 10, 'pred' => 1],

                // 2.0 TRENCHING
                ['title' => 'Trenching & Micro-Duct Installation', 'is_summary' => true, 'parent' => null, 'duration' => 30],
                ['title' => 'Mechanical Trench Excavation (1.2m depth)', 'is_summary' => false, 'parent' => 3, 'duration' => 15, 'pred' => 2],
                ['title' => 'Laying of HDPE Micro-Ducts & Warning Tapes', 'is_summary' => false, 'parent' => 3, 'duration' => 12, 'pred' => 4],
                ['title' => 'Trench Backfilling & Asphalt reinstatement', 'is_summary' => false, 'parent' => 3, 'duration' => 10, 'pred' => 5],

                // 3.0 MANHOLES
                ['title' => 'Fiber Pulling Chambers & Manholes', 'is_summary' => true, 'parent' => null, 'duration' => 20],
                ['title' => 'Pre-cast Concrete Manholes Erection (every 500m)', 'is_summary' => false, 'parent' => 7, 'duration' => 12, 'pred' => 6],
                ['title' => 'Manhole Cover Sealing & Drainage Valve Setup', 'is_summary' => false, 'parent' => 7, 'duration' => 10, 'pred' => 8],

                // 4.0 FIBER BLOWING
                ['title' => 'Fiber Optic Cable Blowing & Splicing', 'is_summary' => true, 'parent' => null, 'duration' => 25],
                ['title' => 'Pneumatic Blowing of 96-Core Armored Fiber Cable', 'is_summary' => false, 'parent' => 10, 'duration' => 12, 'pred' => 9],
                ['title' => 'Fusion Splicing at Manhole Joints & Pigtail Termin', 'is_summary' => false, 'parent' => 10, 'duration' => 15, 'pred' => 11],

                // 5.0 TERMINATION
                ['title' => 'Substation Termination & Optical Testing', 'is_summary' => true, 'parent' => null, 'duration' => 20],
                ['title' => 'ODF Installation in Jinja Terminal Station', 'is_summary' => false, 'parent' => 13, 'duration' => 8, 'pred' => 12],
                ['title' => 'OTDR Fiber Integrity & Attenuation Testing', 'is_summary' => false, 'parent' => 13, 'duration' => 12, 'pred' => 14],

                // 6.0 HANDOVER
                ['title' => 'Fiber Network Commissioning & Client Link Signoff', 'is_summary' => false, 'is_milestone' => true, 'parent' => null, 'duration' => 0, 'pred' => 15]
            ];
        }

        return $templates;
    }
}
