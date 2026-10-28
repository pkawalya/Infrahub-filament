<?php

namespace App\Services;

use App\Models\CdeDocument;
use App\Models\CdeDocumentComment;
use App\Models\CdeFolder;
use App\Models\CdeProject;
use App\Models\User;
use Carbon\Carbon;

class CdeFolderTemplateService
{
    /**
     * Available folder structure templates for InfraHub CDE.
     */
    public static function getAvailableTemplates(): array
    {
        return [
            'infrastructure' => [
                'name' => 'Infrastructure & EPC Lifecycle Standard',
                'description' => 'Comprehensive multi-stage engineering structure covering governance, technical design, commercial, controls, site operations, QHSE, and commissioning handover.',
            ],
            'iso19650' => [
                'name' => 'ISO 19650 Information Containers',
                'description' => 'International BIM information management standard with 00 Work in Progress, 01 Shared, 02 Published, and 03 Archived containers.',
            ],
            'basic' => [
                'name' => 'Core Project Repository',
                'description' => 'Lightweight standard folder directory for general documentation, drawings, commercial contracts, and reports.',
            ],
        ];
    }

    /**
     * Initialize folder hierarchy and optional sample documents for a given CdeProject.
     */
    public static function initialize(CdeProject $project, string $template = 'infrastructure', bool $seedSampleDocuments = true): CdeFolder
    {
        $companyId = $project->company_id;
        $user = User::where('company_id', $companyId)->first() ?? User::first();
        if (!$user) {
            $user = User::firstOrCreate(
                ['email' => 'admin@' . strtolower($project->code ?: 'infrahub') . '.com'],
                ['name' => 'Project Administrator', 'company_id' => $companyId, 'password' => bcrypt('password')]
            );
        }
        $userId = $user->id;

        return match ($template) {
            'iso19650' => self::buildIso19650Template($project, $userId, $companyId, $seedSampleDocuments),
            'basic' => self::buildBasicTemplate($project, $userId, $companyId, $seedSampleDocuments),
            default => self::buildInfrastructureTemplate($project, $userId, $companyId, $seedSampleDocuments),
        };
    }

    /**
     * Build Infrastructure & EPC Project Lifecycle Structure.
     */
    protected static function buildInfrastructureTemplate(CdeProject $project, int $userId, ?int $companyId, bool $seedSampleDocuments): CdeFolder
    {
        // 01 Project Governance & Administration
        $f01 = CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '01 Project Governance & Legal',
            'parent_id' => null,
        ], [
            'company_id' => $companyId,
            'sort_order' => 1,
            'description' => 'Project charters, client briefs, statutory authorizations, and stakeholder agreements.',
            'created_by' => $userId,
        ]);

        $f01_1 = CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '01.1 Execution Plans & Governance Protocols',
            'parent_id' => $f01->id,
        ], [
            'company_id' => $companyId,
            'sort_order' => 1,
            'description' => 'Project execution strategy, communication protocols, governance frameworks, and monthly progress reviews.',
            'created_by' => $userId,
        ]);

        CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '01.2 Statutory & Environmental Permits',
            'parent_id' => $f01->id,
        ], [
            'company_id' => $companyId,
            'sort_order' => 2,
            'description' => 'Municipal planning approvals, environmental licenses, and regulatory compliance clearances.',
            'created_by' => $userId,
        ]);

        CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '01.3 Stakeholder Agreements & MOUs',
            'parent_id' => $f01->id,
        ], [
            'company_id' => $companyId,
            'sort_order' => 3,
            'description' => 'Utility clearances, right-of-way permissions, and inter-agency coordination pacts.',
            'created_by' => $userId,
        ]);

        // 02 Engineering & Technical Design
        $f02 = CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '02 Engineering & Technical Design',
            'parent_id' => null,
        ], [
            'company_id' => $companyId,
            'sort_order' => 2,
            'description' => 'Topographical, architectural, structural, civil, and MEP engineering drawings and models.',
            'created_by' => $userId,
        ]);

        $f02_1 = CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '02.1 Geotechnical & Site Survey Data',
            'parent_id' => $f02->id,
        ], [
            'company_id' => $companyId,
            'sort_order' => 1,
            'description' => 'Borehole logs, soil bearing capacity reports, GIS survey maps, and seismic evaluations.',
            'created_by' => $userId,
        ]);

        CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '02.2 Architectural Drawings & BIM Models',
            'parent_id' => $f02->id,
        ], [
            'company_id' => $companyId,
            'sort_order' => 2,
            'description' => 'General layouts, building elevations, cross sections, finishes schedules, and 3D IFC models.',
            'created_by' => $userId,
        ]);

        CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '02.3 Civil & Structural Engineering',
            'parent_id' => $f02->id,
        ], [
            'company_id' => $companyId,
            'sort_order' => 3,
            'description' => 'Foundation layouts, structural steel detailing, reinforcement bar schedules, and load calculation packages.',
            'created_by' => $userId,
        ]);

        CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '02.4 MEP & Utilities Infrastructure',
            'parent_id' => $f02->id,
        ], [
            'company_id' => $companyId,
            'sort_order' => 4,
            'description' => 'Electrical distribution single-line diagrams, HVAC air handling schematics, plumbing, and drainage layouts.',
            'created_by' => $userId,
        ]);

        // 03 Commercial, Procurement & Contracts
        $f03 = CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '03 Commercial & Procurement',
            'parent_id' => null,
        ], [
            'company_id' => $companyId,
            'sort_order' => 3,
            'description' => 'Tender packages, bills of quantities (BOQ), commercial agreements, valuations, and variation orders.',
            'created_by' => $userId,
        ]);

        CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '03.1 Bills of Quantities & Cost Estimates',
            'parent_id' => $f03->id,
        ], [
            'company_id' => $companyId,
            'sort_order' => 1,
            'description' => 'Priced BOQs, elemental cost plans, rate breakdown analysis, and material budget allowances.',
            'created_by' => $userId,
        ]);

        CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '03.2 Contract Agreements & Variation Orders',
            'parent_id' => $f03->id,
        ], [
            'company_id' => $companyId,
            'sort_order' => 2,
            'description' => 'Prime contract documents, subcontracts, approved change orders, and site instructions.',
            'created_by' => $userId,
        ]);

        // 04 Project Controls & Scheduling
        $f04 = CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '04 Project Controls & Scheduling',
            'parent_id' => null,
        ], [
            'company_id' => $companyId,
            'sort_order' => 4,
            'description' => 'Baseline master programmes, 3-week lookahead schedules, resource curves, and progress reports.',
            'created_by' => $userId,
        ]);

        // 05 Site Operations & Quality Assurance (QHSE)
        $f05 = CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '05 Site Operations & QHSE',
            'parent_id' => null,
        ], [
            'company_id' => $companyId,
            'sort_order' => 5,
            'description' => 'Health & safety protocols, quality inspection checklists, environmental management, and non-conformances.',
            'created_by' => $userId,
        ]);

        CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '05.1 Health, Safety & Environment Protocols',
            'parent_id' => $f05->id,
        ], [
            'company_id' => $companyId,
            'sort_order' => 1,
            'description' => 'Site safety rules, emergency response plans, hazard registers, and environmental monitoring reports.',
            'created_by' => $userId,
        ]);

        CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '05.2 Quality Assurance & Material Testing',
            'parent_id' => $f05->id,
        ], [
            'company_id' => $companyId,
            'sort_order' => 2,
            'description' => 'Concrete cube crush tests, compaction certificates, mill certificates, and inspection request records.',
            'created_by' => $userId,
        ]);

        // 06 Testing, Commissioning & Handover
        CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '06 Testing, Handover & As-Built',
            'parent_id' => null,
        ], [
            'company_id' => $companyId,
            'sort_order' => 6,
            'description' => 'As-built record drawings, operations and maintenance (O&M) manuals, commissioning reports, and completion certificates.',
            'created_by' => $userId,
        ]);

        // Seed representative project documents if requested
        if ($seedSampleDocuments && $f01_1->documents()->count() === 0) {
            self::seedSampleDocuments($project, $f01_1, $userId);
        }

        return $f01_1;
    }

    /**
     * Build ISO 19650 Information Containers Structure.
     */
    protected static function buildIso19650Template(CdeProject $project, int $userId, ?int $companyId, bool $seedSampleDocuments): CdeFolder
    {
        $wip = CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '00 Work in Progress (WIP)',
            'parent_id' => null,
        ], [
            'company_id' => $companyId,
            'sort_order' => 1,
            'description' => 'Draft files, models, and sketches currently being developed by internal task teams.',
            'created_by' => $userId,
        ]);

        $shared = CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '01 Shared (Collaborative)',
            'parent_id' => null,
        ], [
            'company_id' => $companyId,
            'sort_order' => 2,
            'description' => 'Information released for interdisciplinary coordination, design reviews, and engineering approvals.',
            'created_by' => $userId,
        ]);

        CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '02 Published (Authorized)',
            'parent_id' => null,
        ], [
            'company_id' => $companyId,
            'sort_order' => 3,
            'description' => 'Approved deliverables sign-off for statutory clearance, procurement tendering, and construction assembly.',
            'created_by' => $userId,
        ]);

        CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => '03 Archived (Historical Record)',
            'parent_id' => null,
        ], [
            'company_id' => $companyId,
            'sort_order' => 4,
            'description' => 'Superseded revisions, historical audit logs, and verified project handover packages.',
            'created_by' => $userId,
        ]);

        if ($seedSampleDocuments && $shared->documents()->count() === 0) {
            self::seedSampleDocuments($project, $shared, $userId);
        }

        return $shared;
    }

    /**
     * Build Basic Core Project Structure.
     */
    protected static function buildBasicTemplate(CdeProject $project, int $userId, ?int $companyId, bool $seedSampleDocuments): CdeFolder
    {
        $gen = CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => 'General Documentation',
            'parent_id' => null,
        ], [
            'company_id' => $companyId,
            'sort_order' => 1,
            'description' => 'Project briefs, administration reports, meeting minutes, and transmittals.',
            'created_by' => $userId,
        ]);

        CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => 'Technical Drawings & Specifications',
            'parent_id' => null,
        ], [
            'company_id' => $companyId,
            'sort_order' => 2,
            'description' => 'Engineering plans, specifications, schedules, and models.',
            'created_by' => $userId,
        ]);

        CdeFolder::firstOrCreate([
            'cde_project_id' => $project->id,
            'name' => 'Commercial & Contracts',
            'parent_id' => null,
        ], [
            'company_id' => $companyId,
            'sort_order' => 3,
            'description' => 'Contract agreements, procurement orders, and payment applications.',
            'created_by' => $userId,
        ]);

        if ($seedSampleDocuments && $gen->documents()->count() === 0) {
            self::seedSampleDocuments($project, $gen, $userId);
        }

        return $gen;
    }

    /**
     * Seed realistic infrastructure project documents using the project's own code.
     */
    protected static function seedSampleDocuments(CdeProject $project, CdeFolder $targetFolder, int $userId): void
    {
        $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $project->code ?: 'INFRA'));
        if (strlen($code) < 3) {
            $code = 'INFRA';
        }

        $sampleDocs = [
            [
                'document_number' => "{$code}-MGT-PRP-001",
                'title' => 'Project Execution Plan & Governance Protocols',
                'discipline' => 'general',
                'revision' => 'R01',
                'status' => 'S4',
                'file_type' => 'docx',
                'file_size' => 1845000,
                'created_at' => Carbon::now()->subDays(5),
                'is_flagged' => true,
                'due_date' => Carbon::now()->addDays(3),
            ],
            [
                'document_number' => "{$code}-ENV-EMP-PLN-001",
                'title' => 'Construction Environmental & Social Management Plan (CESMP)',
                'discipline' => 'other',
                'revision' => 'R02',
                'status' => 'S3',
                'file_type' => 'zip',
                'file_size' => 12450000,
                'created_at' => Carbon::now()->subDays(6),
                'is_flagged' => false,
                'due_date' => Carbon::now()->addDays(6),
            ],
            [
                'document_number' => "{$code}-QSE-QAP-MAN-001",
                'title' => 'Quality Assurance & Site Operational Control Manual',
                'discipline' => 'civil',
                'revision' => 'R01',
                'status' => 'S4',
                'file_type' => 'pdf',
                'file_size' => 4250000,
                'created_at' => Carbon::now()->subDays(7),
                'is_flagged' => true,
                'due_date' => Carbon::now()->addDays(8),
            ],
            [
                'document_number' => "{$code}-ENG-DWG-GEN-001",
                'title' => 'Master Infrastructure Layout & Utility Integration Plan',
                'discipline' => 'structural',
                'revision' => 'R01',
                'status' => 'S4',
                'file_type' => 'pdf',
                'file_size' => 5120000,
                'created_at' => Carbon::now()->subDays(8),
                'is_flagged' => false,
                'due_date' => Carbon::now()->addDays(12),
            ],
            [
                'document_number' => "{$code}-SAF-HSE-SOP-001",
                'title' => 'Site Occupational Health, Safety & Emergency Procedures',
                'discipline' => 'safety',
                'revision' => 'R01',
                'status' => 'S2',
                'file_type' => 'docx',
                'file_size' => 2340000,
                'created_at' => Carbon::now()->subDays(9),
                'is_flagged' => false,
                'due_date' => Carbon::now()->subDays(1), // overdue for demo
            ],
            [
                'document_number' => "{$code}-COM-BOQ-SUM-001",
                'title' => 'Consolidated Master Bill of Quantities & Commercial Schedule',
                'discipline' => 'commercial',
                'revision' => 'R01',
                'status' => 'S3',
                'file_type' => 'xlsx',
                'file_size' => 3890000,
                'created_at' => Carbon::now()->subDays(10),
                'is_flagged' => false,
                'due_date' => Carbon::now()->addDays(15),
            ],
        ];

        foreach ($sampleDocs as $docData) {
            $isFlagged = $docData['is_flagged'];
            $dueDate = $docData['due_date'];
            unset($docData['is_flagged'], $docData['due_date']);

            $doc = CdeDocument::create(array_merge($docData, [
                'company_id' => $project->company_id,
                'cde_project_id' => $project->id,
                'cde_folder_id' => $targetFolder->id,
                'created_by' => $userId,
                'is_flagged' => $isFlagged,
                'due_date' => $dueDate,
            ]));

            // Add an initial comment thread on the first document
            if ($docData['document_number'] === "{$code}-MGT-PRP-001") {
                $c1 = CdeDocumentComment::create([
                    'cde_document_id' => $doc->id,
                    'user_id' => $userId,
                    'comment' => 'Initial execution protocol uploaded for multidisciplinary review. Please verify engineering governance matrix by end of week.',
                    'status' => 'open',
                    'created_at' => Carbon::now()->subDays(4),
                ]);

                CdeDocumentComment::create([
                    'cde_document_id' => $doc->id,
                    'parent_id' => $c1->id,
                    'user_id' => $userId,
                    'comment' => 'Reviewed against project specifications. Commercial milestones and compliance checkpoints are confirmed.',
                    'status' => 'open',
                    'created_at' => Carbon::now()->subDays(2),
                ]);
            }
        }
    }
}
