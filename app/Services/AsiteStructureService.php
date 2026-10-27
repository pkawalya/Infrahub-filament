<?php

namespace App\Services;

use App\Models\CdeDocument;
use App\Models\CdeDocumentComment;
use App\Models\CdeFolder;
use App\Models\CdeProject;
use App\Models\User;
use Carbon\Carbon;

class AsiteStructureService
{
    /**
     * Initialize the exact ASITE folder and document hierarchy for a given CdeProject.
     */
    public static function initialize(CdeProject $project, bool $seedSampleDocuments = true): CdeFolder
    {
        $companyId = $project->company_id;
        $user = User::where('company_id', $companyId)->first() ?? User::first();
        if (!$user) {
            $user = User::firstOrCreate(
                ['email' => 'admin@' . strtolower($project->code ?: 'infrahub') . '.com'],
                ['name' => 'Project Admin', 'company_id' => $companyId, 'password' => bcrypt('password')]
            );
        }
        $userId = $user->id;

        // 1. Root Folders
        $f01 = CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '01 Development Management',
            'parent_id' => null,
        ], [
            'company_id' => $companyId,
            'sort_order' => 1,
            'description' => 'Pre-contract deliverables, client briefs, statutory approvals, and feasibility studies.',
            'created_by' => $userId,
        ]);

        $f02 = CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '02 Design',
            'parent_id' => null,
        ], [
            'company_id' => $companyId,
            'sort_order' => 2,
            'description' => 'Architectural, structural, civil, and MEP engineering drawings, models, and specifications.',
            'created_by' => $userId,
        ]);

        $f03 = CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '03 Planning',
            'parent_id' => null,
        ], [
            'company_id' => $companyId,
            'sort_order' => 3,
            'description' => 'Construction schedules, milestone programs, site logistics, and lookahead plans.',
            'created_by' => $userId,
        ]);

        $f04 = CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '04 Commercial',
            'parent_id' => null,
        ], [
            'company_id' => $companyId,
            'sort_order' => 4,
            'description' => 'Procurement records, bill of quantities (BOQ), tender submissions, and valuations.',
            'created_by' => $userId,
        ]);

        $f05 = CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '05 Contract Management',
            'parent_id' => null,
        ], [
            'company_id' => $companyId,
            'sort_order' => 5,
            'description' => 'Main contract agreements, subcontracts, change orders, and site instructions.',
            'created_by' => $userId,
        ]);

        // 2. Subfolders under 01 Development Management
        $f01_1 = CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '01.1 Pre-Contract Documents',
            'parent_id' => $f01->id,
        ], [
            'company_id' => $companyId,
            'sort_order' => 1,
            'description' => 'Baseline execution plans, protocol documents, geotechnical reports, and environmental assessments.',
            'created_by' => $userId,
        ]);

        $f01_2 = CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '01.2 Local Authority Planning Approvals',
            'parent_id' => $f01->id,
        ], [
            'company_id' => $companyId,
            'sort_order' => 2,
            'description' => 'Building control permits, environmental licenses, and municipal council clearances.',
            'created_by' => $userId,
        ]);

        // 3. Children under 01.1 Pre-Contract Documents
        $f01_1_1 = CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '01.1.1 Project Execution Plan',
            'parent_id' => $f01_1->id,
        ], [
            'company_id' => $companyId,
            'sort_order' => 1,
            'description' => 'Overall project execution strategy, milestones, roles, and communication framework.',
            'created_by' => $userId,
        ]);

        $f01_1_2 = CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '01.1.2 Project Protocols Documents',
            'parent_id' => $f01_1->id,
        ], [
            'company_id' => $companyId,
            'sort_order' => 2,
            'description' => 'Project governance protocols, quality assurance standards, and reporting guidelines.',
            'created_by' => $userId,
        ]);

        $f01_1_3 = CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '01.1.3 Site Geotechnical Investigation Report',
            'parent_id' => $f01_1->id,
        ], [
            'company_id' => $companyId,
            'sort_order' => 3,
            'description' => 'Borehole logs, soil bearing capacity reports, and foundation engineering recommendations.',
            'created_by' => $userId,
        ]);

        $f01_1_4 = CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '01.1.4 Traffic Impact Assessment',
            'parent_id' => $f01_1->id,
        ], [
            'company_id' => $companyId,
            'sort_order' => 4,
            'description' => 'Traffic flow analysis, site access routing, and mitigation plans.',
            'created_by' => $userId,
        ]);

        $f01_1_5 = CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '01.1.5 ESIA',
            'parent_id' => $f01_1->id,
        ], [
            'company_id' => $companyId,
            'sort_order' => 5,
            'description' => 'Environmental and Social Impact Assessment study, mitigation matrix, and audit reports.',
            'created_by' => $userId,
        ]);

        // 4. Subfolders under 02 Design
        CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '02.1 Architectural',
            'parent_id' => $f02->id,
        ], [
            'company_id' => $companyId,
            'sort_order' => 1,
            'description' => 'Architectural floor plans, exterior elevations, building sections, finishes, and schedules.',
            'created_by' => $userId,
        ]);

        CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '02.2 Structural',
            'parent_id' => $f02->id,
        ], [
            'company_id' => $companyId,
            'sort_order' => 2,
            'description' => 'Foundation details, reinforcement drawings, structural steel layouts, and load calculation sheets.',
            'created_by' => $userId,
        ]);

        CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '02.3 MEP Services',
            'parent_id' => $f02->id,
        ], [
            'company_id' => $companyId,
            'sort_order' => 3,
            'description' => 'Mechanical HVAC layouts, electrical distribution schematics, and plumbing/drainage plans.',
            'created_by' => $userId,
        ]);

        // If requested, seed the sample documents from the ASITE screenshot into 01.1.2 Project Protocols Documents
        if ($seedSampleDocuments && $f01_1_2->documents()->count() === 0) {
            $sampleDocs = [
                [
                    'document_number' => 'KAATC-AFX-PM-ALL-ALL-RPT-6',
                    'title' => 'Project Management Progress & Execution Monthly Protocol Report',
                    'discipline' => 'civil',
                    'revision' => 'P06',
                    'status' => 'S4',
                    'file_type' => 'docx',
                    'file_size' => 1845000,
                    'created_at' => Carbon::parse('2026-09-04 10:15:00'),
                    'is_flagged' => true,
                    'due_date' => Carbon::now()->addDays(2),
                ],
                [
                    'document_number' => 'KAATC-JO-PM-ALL-ALL-EMP-5',
                    'title' => 'Environmental Management Protocols and Execution Manual Package',
                    'discipline' => 'other',
                    'revision' => 'P05',
                    'status' => 'S3',
                    'file_type' => 'zip',
                    'file_size' => 12450000,
                    'created_at' => Carbon::parse('2026-09-03 14:22:00'),
                    'is_flagged' => false,
                    'due_date' => Carbon::now()->addDays(5),
                ],
                [
                    'document_number' => 'KAATC-AFX-PM-ALL-ALL-MAN-2',
                    'title' => 'Project Quality Assurance and Operational Protocol Manual (Vol 2)',
                    'discipline' => 'structural',
                    'revision' => 'P02',
                    'status' => 'S4',
                    'file_type' => 'pdf',
                    'file_size' => 4250000,
                    'created_at' => Carbon::parse('2026-09-03 11:45:00'),
                    'is_flagged' => true,
                    'due_date' => Carbon::now()->addDays(7),
                ],
                [
                    'document_number' => 'KAATC-AFX-PM-ALL-ALL-MAN-1',
                    'title' => 'Project Quality Assurance and Operational Protocol Manual (Vol 1)',
                    'discipline' => 'structural',
                    'revision' => 'P01',
                    'status' => 'S4',
                    'file_type' => 'pdf',
                    'file_size' => 5120000,
                    'created_at' => Carbon::parse('2026-09-03 09:30:00'),
                    'is_flagged' => false,
                    'due_date' => Carbon::now()->addDays(10),
                ],
                [
                    'document_number' => 'KAATC-JO-PM-ALL-ALL-MAN-',
                    'title' => 'Health, Safety and Environmental Protocols Standard Operating Procedure',
                    'discipline' => 'other',
                    'revision' => 'P01',
                    'status' => 'S2',
                    'file_type' => 'docx',
                    'file_size' => 2340000,
                    'created_at' => Carbon::parse('2026-09-03 08:15:00'),
                    'is_flagged' => false,
                    'due_date' => Carbon::now()->subDays(1), // overdue
                ],
                [
                    'document_number' => 'KAATC-JCL-PM-ALL-ALL-PRC-',
                    'title' => 'Commercial Procurement Protocols, Subcontractor Packages & Tracker',
                    'discipline' => 'other',
                    'revision' => 'P01',
                    'status' => 'S1',
                    'file_type' => 'xlsx',
                    'file_size' => 890000,
                    'created_at' => Carbon::parse('2026-09-03 07:45:00'),
                    'is_flagged' => false,
                    'due_date' => Carbon::now()->today(), // due today
                ],
            ];

            foreach ($sampleDocs as $data) {
                $doc = CdeDocument::create([
                    'company_id' => $companyId,
                    'cde_project_id' => $project->id,
                    'cde_folder_id' => $f01_1_2->id,
                    'document_number' => $data['document_number'],
                    'title' => $data['title'],
                    'discipline' => $data['discipline'],
                    'revision' => $data['revision'],
                    'status' => $data['status'],
                    'file_type' => $data['file_type'],
                    'file_size' => $data['file_size'],
                    'file_path' => 'documents/samples/' . $data['document_number'] . '.' . $data['file_type'],
                    'uploaded_by' => $userId,
                    'is_flagged' => $data['is_flagged'],
                    'due_date' => $data['due_date'],
                    'created_at' => $data['created_at'],
                    'updated_at' => $data['created_at'],
                ]);

                // Create a sample comment
                CdeDocumentComment::create([
                    'cde_document_id' => $doc->id,
                    'user_id' => $userId,
                    'comment' => "Reviewed per ISO 19650 protocols. Verification confirmed for {$data['revision']}.",
                    'is_resolved' => false,
                    'created_at' => $data['created_at']->copy()->addHours(2),
                ]);
            }
        }

        return $f01_1_2;
    }
}
