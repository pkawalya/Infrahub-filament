<?php

namespace Tests\Feature;

use App\Filament\App\Resources\CdeProjectResource\Pages\Modules\CdePage;
use App\Models\CdeActivityLog;
use App\Models\CdeDocument;
use App\Models\CdeDocumentComment;
use App\Models\CdeDocumentVersion;
use App\Models\CdeFolder;
use App\Models\CdeProject;
use App\Models\Company;
use App\Models\Transmittal;
use App\Models\TransmittalItem;
use App\Models\User;
use App\Services\CdeFolderTemplateService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CdeDocumentModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupCdeEnvironment(): array
    {
        $suffix = uniqid();
        $company = Company::create([
            'name' => "Test Infra {$suffix}",
            'code' => "TI{$suffix}",
            'status' => 'active',
        ]);

        $user = User::create([
            'name' => 'Project Engineer',
            'email' => "engineer_{$suffix}@test.com",
            'password' => bcrypt('secret'),
            'company_id' => $company->id,
            'user_type' => 'company_admin',
            'is_active' => true,
        ]);

        $project = CdeProject::create([
            'name' => "Bridge Expansion {$suffix}",
            'code' => "BEX{$suffix}",
            'company_id' => $company->id,
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $this->actingAs($user);

        $page = new CdePage();
        $page->record = $project;

        return [$company, $user, $project, $page];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 1. Template & Seeding Tests
    // ──────────────────────────────────────────────────────────────────────────

    public function test_cde_folder_template_service_generates_infrastructure_lifecycle_hierarchy(): void
    {
        $suffix = uniqid();
        $company = Company::create(['name' => "Apex Infra {$suffix}", 'code' => "AI{$suffix}", 'status' => 'active']);
        $project = CdeProject::withoutGlobalScopes()->create([
            'name' => "Metro Rail Line {$suffix}",
            'company_id' => $company->id,
            'code' => "MRL03",
            'status' => 'active',
        ]);

        $folder = CdeFolderTemplateService::initialize($project, 'infrastructure', true);

        $this->assertEquals('01.1 Execution Plans & Governance Protocols', $folder->name);

        // Check top level root folders
        $rootFolders = CdeFolder::where('cde_project_id', $project->id)
            ->whereNull('parent_id')
            ->pluck('name')
            ->toArray();

        $this->assertContains('01 Project Governance & Legal', $rootFolders);
        $this->assertContains('02 Engineering & Technical Design', $rootFolders);
        $this->assertContains('03 Commercial & Procurement', $rootFolders);
        $this->assertContains('04 Project Controls & Scheduling', $rootFolders);
        $this->assertContains('05 Site Operations & QHSE', $rootFolders);
        $this->assertContains('06 Testing, Handover & As-Built', $rootFolders);

        // Check subfolder under 01 Project Governance & Legal
        $f01 = CdeFolder::where('cde_project_id', $project->id)->where('name', '01 Project Governance & Legal')->first();
        $this->assertNotNull($f01);

        $f01_1 = CdeFolder::where('parent_id', $f01->id)->where('name', '01.1 Execution Plans & Governance Protocols')->first();
        $this->assertNotNull($f01_1);

        // Check sample documents seeded with project's own code
        $docs = $folder->documents()->get();
        $this->assertCount(6, $docs);

        $docNumbers = $docs->pluck('document_number')->toArray();
        $this->assertContains('MRL03-MGT-PRP-001', $docNumbers);
        $this->assertContains('MRL03-ENV-EMP-PLN-001', $docNumbers);
        $this->assertContains('MRL03-QSE-QAP-MAN-001', $docNumbers);
        $this->assertContains('MRL03-ENG-DWG-GEN-001', $docNumbers);
        $this->assertContains('MRL03-SAF-HSE-SOP-001', $docNumbers);
        $this->assertContains('MRL03-COM-BOQ-SUM-001', $docNumbers);
    }

    public function test_cde_folder_template_service_iso19650_containers(): void
    {
        $suffix = uniqid();
        $company = Company::create(['name' => "Apex ISO {$suffix}", 'code' => "ISO{$suffix}", 'status' => 'active']);
        $project = CdeProject::withoutGlobalScopes()->create([
            'name' => "Highway Interchange {$suffix}",
            'company_id' => $company->id,
            'code' => "HWY02",
            'status' => 'active',
        ]);

        $folder = CdeFolderTemplateService::initialize($project, 'iso19650', true);

        $rootFolders = CdeFolder::where('cde_project_id', $project->id)
            ->whereNull('parent_id')
            ->pluck('name')
            ->toArray();

        $this->assertContains('00 Work in Progress (WIP)', $rootFolders);
        $this->assertContains('01 Shared (Collaborative)', $rootFolders);
        $this->assertContains('02 Published (Authorized)', $rootFolders);
        $this->assertContains('03 Archived (Historical Record)', $rootFolders);
    }

    public function test_folder_creation_with_purpose_and_comments(): void
    {
        [$company, $user, $project] = $this->setupCdeEnvironment();

        $folder = CdeFolder::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'name' => '07 Site Inspection Records',
            'description' => 'Daily compaction and concrete testing sign-off records.',
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($folder->id);
        $this->assertEquals('07 Site Inspection Records', $folder->name);
        $this->assertEquals('Daily compaction and concrete testing sign-off records.', $folder->description);

        $comment = CdeDocumentComment::create([
            'cde_folder_id' => $folder->id,
            'user_id' => $user->id,
            'comment' => 'Please upload batch inspection tickets every Friday before 4 PM.',
            'is_resolved' => false,
        ]);

        $this->assertNotNull($comment->id);
        $this->assertEquals($folder->id, $comment->folder->id);
        $this->assertFalse($comment->is_resolved);

        $comment->update(['is_resolved' => true]);
        $this->assertTrue($comment->fresh()->is_resolved);
    }

    public function test_threaded_comments_on_documents_and_folders(): void
    {
        [$company, $user, $project] = $this->setupCdeEnvironment();

        $folder = CdeFolder::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'name' => '08 Coordination Notes',
            'created_by' => $user->id,
        ]);

        // Top level parent comment
        $parent = CdeDocumentComment::create([
            'cde_folder_id' => $folder->id,
            'user_id' => $user->id,
            'comment' => 'Please confirm latest submittal references.',
            'is_resolved' => false,
        ]);

        // Threaded reply
        $reply = CdeDocumentComment::create([
            'cde_folder_id' => $folder->id,
            'parent_id' => $parent->id,
            'user_id' => $user->id,
            'comment' => 'Referencing Rev R02 in folder 01.1.',
            'is_resolved' => false,
        ]);

        $this->assertEquals($parent->id, $reply->parent->id);
        $this->assertCount(1, $parent->fresh()->replies);
        $this->assertEquals('Referencing Rev R02 in folder 01.1.', $parent->fresh()->replies->first()->comment);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Section 1: Navigation, Breadcrumbs & Tree State
    // ──────────────────────────────────────────────────────────────────────────

    public function test_section1_navigation_breadcrumbs_and_tree_state(): void
    {
        [$company, $user, $project, $page] = $this->setupCdeEnvironment();

        // Create 3-level folder hierarchy
        $rootFolder = CdeFolder::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'name' => '01 Project Governance',
            'parent_id' => null,
            'created_by' => $user->id,
        ]);

        $subFolder = CdeFolder::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'name' => '01.1 Legal & Contracts',
            'parent_id' => $rootFolder->id,
            'created_by' => $user->id,
        ]);

        $deepFolder = CdeFolder::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'name' => '01.1.1 Addenda',
            'parent_id' => $subFolder->id,
            'created_by' => $user->id,
        ]);

        // 1. Root folder selection
        $page->selectFolder(null);
        $this->assertNull($page->currentFolderId);
        $this->assertEquals('Root', $page->folderPath);

        // 2. Select deep child folder
        $page->selectFolder($deepFolder->id);
        $this->assertEquals($deepFolder->id, $page->currentFolderId);

        // Ancestors should be auto-expanded
        $this->assertContains($rootFolder->id, $page->expandedFolderIds);
        $this->assertContains($subFolder->id, $page->expandedFolderIds);

        // Breadcrumb path verification
        $this->assertEquals('01 Project Governance / 01.1 Legal & Contracts / 01.1.1 Addenda', $page->folderPath);

        // 3. Tree collapse toggle
        $this->assertFalse($page->isTreeCollapsed);
        $page->toggleTreeCollapse();
        $this->assertTrue($page->isTreeCollapsed);
        $page->toggleTreeCollapse();
        $this->assertFalse($page->isTreeCollapsed);

        // 4. Collapse all and Expand all
        $page->collapseAllFolders();
        $this->assertEmpty($page->expandedFolderIds);
        $page->expandAllFolders();
        $this->assertContains($rootFolder->id, $page->expandedFolderIds);
        $this->assertContains($subFolder->id, $page->expandedFolderIds);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Section 2: Left Quick Filter Strip & Scoping
    // ──────────────────────────────────────────────────────────────────────────

    public function test_section2_quick_filter_counts_and_scoping(): void
    {
        [$company, $user, $project, $page] = $this->setupCdeEnvironment();

        $folder = CdeFolder::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'name' => 'General Files',
            'created_by' => $user->id,
        ]);

        // Doc 1: Incomplete WIP
        $docWip = CdeDocument::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'cde_folder_id' => $folder->id,
            'document_number' => 'DOC-WIP-01',
            'title' => 'Structural Calculation WIP',
            'status' => 'wip',
            'revision' => 'P01.01',
            'discipline' => 'structural',
            'uploaded_by' => $user->id,
        ]);

        // Doc 2: Overdue draft
        $docOverdue = CdeDocument::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'cde_folder_id' => $folder->id,
            'document_number' => 'DOC-OVD-02',
            'title' => 'Architectural Floor Plan Overdue',
            'status' => 'draft',
            'revision' => 'P01.01',
            'discipline' => 'architecture',
            'due_date' => now()->subDays(3)->toDateString(),
            'uploaded_by' => $user->id,
        ]);

        // Doc 3: Due today under review
        $docDueToday = CdeDocument::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'cde_folder_id' => $folder->id,
            'document_number' => 'DOC-TODAY-03',
            'title' => 'Electrical Single Line Diagram Due Today',
            'status' => 'under_review',
            'revision' => 'P01.02',
            'discipline' => 'electrical',
            'due_date' => now()->toDateString(),
            'uploaded_by' => $user->id,
        ]);

        // Doc 4: Approved & published (complete)
        $docApproved = CdeDocument::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'cde_folder_id' => $folder->id,
            'document_number' => 'DOC-APP-04',
            'title' => 'Drainage Layout Approved',
            'status' => 'approved',
            'revision' => 'C01.01',
            'discipline' => 'civil',
            'uploaded_by' => $user->id,
        ]);

        // Add an open comment to Doc 1
        CdeDocumentComment::create([
            'cde_document_id' => $docWip->id,
            'user_id' => $user->id,
            'comment' => 'Please verify column loads.',
            'is_resolved' => false,
        ]);

        // Test getQuickFilterCounts()
        $counts = $page->getQuickFilterCounts();
        $this->assertEquals(4, $counts['all']);
        $this->assertEquals(3, $counts['incomplete']);
        $this->assertEquals(1, $counts['overdue']);
        $this->assertEquals(1, $counts['due_today']);
        $this->assertEquals(1, $counts['unread_comments']);

        // Test filter scoping in getDocuments()
        $page->selectFolder($folder->id);

        $page->setQuickFilter('incomplete');
        $incompleteDocs = $page->getDocuments()->pluck('document_number')->toArray();
        $this->assertContains('DOC-WIP-01', $incompleteDocs);
        $this->assertContains('DOC-OVD-02', $incompleteDocs);
        $this->assertContains('DOC-TODAY-03', $incompleteDocs);
        $this->assertNotContains('DOC-APP-04', $incompleteDocs);

        $page->setQuickFilter('overdue');
        $overdueDocs = $page->getDocuments()->pluck('document_number')->toArray();
        $this->assertEquals(['DOC-OVD-02'], $overdueDocs);

        $page->setQuickFilter('due_today');
        $todayDocs = $page->getDocuments()->pluck('document_number')->toArray();
        $this->assertEquals(['DOC-TODAY-03'], $todayDocs);

        $page->setQuickFilter('unread_comments');
        $commentDocs = $page->getDocuments()->pluck('document_number')->toArray();
        $this->assertEquals(['DOC-WIP-01'], $commentDocs);

        $page->setQuickFilter('all');
        $allDocs = $page->getDocuments()->pluck('document_number')->toArray();
        $this->assertCount(4, $allDocs);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Section 3: Folder Explorer & Tree CRUD Validation
    // ──────────────────────────────────────────────────────────────────────────

    public function test_section3_folder_explorer_crud_and_deletion_validation(): void
    {
        [$company, $user, $project, $page] = $this->setupCdeEnvironment();

        // 1. Create root folder via modal submission
        $page->openNewFolderModal(null);
        $this->assertTrue($page->showNewFolderModal);
        $this->assertNull($page->newFolderParentId);

        $page->newFolderName = '05 Geotechnical Reports';
        $page->newFolderDescription = 'Soil boring logs, CBR tests and borehole data.';
        $page->submitNewFolder();

        $this->assertFalse($page->showNewFolderModal);
        $geoFolder = CdeFolder::where('cde_project_id', $project->id)->where('name', '05 Geotechnical Reports')->first();
        $this->assertNotNull($geoFolder);
        $this->assertEquals('Soil boring logs, CBR tests and borehole data.', $geoFolder->description);

        // 2. Create subfolder under 05 Geotechnical Reports
        $page->openNewFolderModal($geoFolder->id);
        $this->assertEquals($geoFolder->id, $page->newFolderParentId);
        $page->newFolderName = '05.1 Borehole Logs';
        $page->newFolderDescription = 'Rotary drilling sample logs.';
        $page->submitNewFolder();

        $subFolder = CdeFolder::where('parent_id', $geoFolder->id)->where('name', '05.1 Borehole Logs')->first();
        $this->assertNotNull($subFolder);

        // 3. Rename subfolder
        $page->openRenameFolderModal($subFolder->id);
        $this->assertTrue($page->showRenameFolderModal);
        $page->renameFolderName = '05.1 Core Drilling & Boreholes';
        $page->renameFolderDescription = 'Updated description for drilling.';
        $page->submitRenameFolder();

        $this->assertFalse($page->showRenameFolderModal);
        $this->assertEquals('05.1 Core Drilling & Boreholes', $subFolder->fresh()->name);
        $this->assertEquals('Updated description for drilling.', $subFolder->fresh()->description);

        // 4. Test delete validation: Cannot delete folder containing subfolders
        $page->deleteFolder($geoFolder->id);
        $this->assertNotNull(CdeFolder::find($geoFolder->id));

        // 5. Test delete validation: Add document to subfolder, cannot delete subfolder
        $doc = CdeDocument::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'cde_folder_id' => $subFolder->id,
            'document_number' => 'BH-LOG-001',
            'title' => 'Borehole Log BH-01',
            'status' => 'wip',
            'uploaded_by' => $user->id,
        ]);
        $page->deleteFolder($subFolder->id);
        $this->assertNotNull(CdeFolder::find($subFolder->id));

        // Remove document, now subfolder can be deleted
        $doc->forceDelete();
        $page->deleteFolder($subFolder->id);
        $this->assertNull(CdeFolder::find($subFolder->id));

        // Now root folder has no children, it can be deleted
        $page->deleteFolder($geoFolder->id);
        $this->assertNull(CdeFolder::find($geoFolder->id));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Section 4: Document Registry Table, Search, Filtering & Multi-Column Sorting
    // ──────────────────────────────────────────────────────────────────────────

    public function test_section4_document_registry_search_filtering_and_sorting(): void
    {
        [$company, $user, $project, $page] = $this->setupCdeEnvironment();

        $folder = CdeFolder::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'name' => 'Design Registry',
            'created_by' => $user->id,
        ]);

        $docCivil = CdeDocument::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'cde_folder_id' => $folder->id,
            'document_number' => 'BEX-CIV-DWG-001',
            'title' => 'Culvert General Arrangement',
            'discipline' => 'civil',
            'status' => 'S0',
            'file_type' => 'dwg',
            'is_flagged' => false,
            'created_at' => now()->subDays(5),
            'uploaded_by' => $user->id,
        ]);

        $docStructural = CdeDocument::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'cde_folder_id' => $folder->id,
            'document_number' => 'BEX-STR-CAL-002',
            'title' => 'Pier Abutment Stress Calculations',
            'discipline' => 'structural',
            'status' => 'S4',
            'file_type' => 'pdf',
            'is_flagged' => true,
            'created_at' => now()->subDays(2),
            'uploaded_by' => $user->id,
        ]);

        $docArch = CdeDocument::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'cde_folder_id' => $folder->id,
            'document_number' => 'BEX-ARC-SPE-003',
            'title' => 'Bridge Parapet Aesthetic Specification',
            'discipline' => 'architecture',
            'status' => 'S2',
            'file_type' => 'docx',
            'is_flagged' => false,
            'created_at' => now()->subDay(),
            'uploaded_by' => $user->id,
        ]);

        $page->selectFolder($folder->id);

        // 1. Search by title
        $page->searchDoc = 'Abutment';
        $results = $page->getDocuments();
        $this->assertEquals(1, $results->total());
        $this->assertEquals('BEX-STR-CAL-002', $results->first()->document_number);

        // 2. Search by document reference
        $page->searchDoc = 'CIV-DWG';
        $results = $page->getDocuments();
        $this->assertEquals(1, $results->total());
        $this->assertEquals('BEX-CIV-DWG-001', $results->first()->document_number);

        $page->searchDoc = '';

        // 3. Dropdown filters
        $page->filterDiscipline = 'structural';
        $this->assertEquals(1, $page->getDocuments()->total());
        $this->assertEquals('BEX-STR-CAL-002', $page->getDocuments()->first()->document_number);

        $page->filterDiscipline = '';
        $page->filterStatus = 'S2';
        $this->assertEquals(1, $page->getDocuments()->total());
        $this->assertEquals('BEX-ARC-SPE-003', $page->getDocuments()->first()->document_number);

        $page->filterStatus = '';
        $page->filterFileType = 'dwg';
        $this->assertEquals(1, $page->getDocuments()->total());
        $this->assertEquals('BEX-CIV-DWG-001', $page->getDocuments()->first()->document_number);

        // 4. Active filter count and clear all
        $page->filterFileType = '';
        $page->filterDiscipline = 'civil';
        $page->filterStatus = 'S0';
        $page->searchDoc = 'Culvert';
        $this->assertEquals(3, $page->getActiveFilterCount());

        $page->clearAllFilters();
        $this->assertEquals(0, $page->getActiveFilterCount());
        $this->assertEmpty($page->filterDiscipline);
        $this->assertEmpty($page->filterStatus);
        $this->assertEmpty($page->searchDoc);

        // 5. Sorting by flag (P column)
        $page->setSort('flag');
        $sortedFlag = $page->getDocuments()->items();
        $this->assertTrue((bool)$sortedFlag[0]->is_flagged);
        $this->assertEquals('BEX-STR-CAL-002', $sortedFlag[0]->document_number);

        // Sorting by document reference asc / desc
        $page->setSort('doc_ref');
        $this->assertEquals('BEX-STR-CAL-002', $page->getDocuments()->items()[0]->document_number);
        $page->setSort('doc_ref');
        $this->assertEquals('BEX-ARC-SPE-003', $page->getDocuments()->items()[0]->document_number);

        // Sorting by title asc
        $page->setSort('title');
        $page->sortDirection = 'asc';
        $this->assertEquals('Bridge Parapet Aesthetic Specification', $page->getDocuments()->items()[0]->title);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Section 5: Multi-Select, Batch Operations & Flagging
    // ──────────────────────────────────────────────────────────────────────────

    public function test_section5_multi_select_batch_operations_and_flagging(): void
    {
        [$company, $user, $project, $page] = $this->setupCdeEnvironment();

        $folder = CdeFolder::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'name' => 'Batch Processing',
            'created_by' => $user->id,
        ]);

        $d1 = CdeDocument::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'cde_folder_id' => $folder->id,
            'document_number' => 'BATCH-001',
            'title' => 'Batch Doc 1',
            'status' => 'wip',
            'is_flagged' => false,
            'uploaded_by' => $user->id,
        ]);

        $d2 = CdeDocument::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'cde_folder_id' => $folder->id,
            'document_number' => 'BATCH-002',
            'title' => 'Batch Doc 2',
            'status' => 'wip',
            'is_flagged' => false,
            'uploaded_by' => $user->id,
        ]);

        $page->selectFolder($folder->id);

        // 1. Toggle individual flag
        $page->toggleFlag($d1->id);
        $this->assertTrue((bool)$d1->fresh()->is_flagged);
        $page->toggleFlag($d1->id);
        $this->assertFalse((bool)$d1->fresh()->is_flagged);

        // 2. Toggle single doc selection
        $page->toggleSelectDoc($d1->id);
        $this->assertContains($d1->id, $page->selectedDocIds);
        $page->toggleSelectDoc($d1->id);
        $this->assertNotContains($d1->id, $page->selectedDocIds);

        // 3. Toggle select all
        $page->toggleSelectAll();
        $this->assertTrue($page->selectAll);
        $this->assertContains($d1->id, $page->selectedDocIds);
        $this->assertContains($d2->id, $page->selectedDocIds);

        // 4. Batch flag
        $page->batchFlag();
        $this->assertTrue((bool)$d1->fresh()->is_flagged);
        $this->assertTrue((bool)$d2->fresh()->is_flagged);

        // 5. Batch download ZIP generation
        $downloadResponse = $page->batchDownload();
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\BinaryFileResponse::class, $downloadResponse);
        $this->assertStringEndsWith('.zip', $downloadResponse->getFile()->getFilename());

        // 6. Batch delete
        $page->batchDelete();
        $this->assertSoftDeleted('cde_documents', ['id' => $d1->id]);
        $this->assertSoftDeleted('cde_documents', ['id' => $d2->id]);
        $this->assertEmpty($page->selectedDocIds);
        $this->assertFalse($page->selectAll);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Section 6: Document Detail Drawer, Revisions, Status & Audit Trail
    // ──────────────────────────────────────────────────────────────────────────

    public function test_section6_document_drawer_revisions_status_and_audit(): void
    {
        [$company, $user, $project, $page] = $this->setupCdeEnvironment();

        $folder = CdeFolder::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'name' => 'Drawer Specs',
            'created_by' => $user->id,
        ]);

        $doc = CdeDocument::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'cde_folder_id' => $folder->id,
            'document_number' => 'SPEC-DW-001',
            'title' => 'Concrete Mix Specifications',
            'status' => 'wip',
            'revision' => 'P01.01',
            'discipline' => 'materials',
            'file_path' => 'documents/spec.pdf',
            'file_type' => 'pdf',
            'file_size' => 102400,
            'uploaded_by' => $user->id,
        ]);

        // 1. Open drawer
        $page->openDrawer($doc->id, 'attributes');
        $this->assertTrue($page->drawerOpen);
        $this->assertEquals($doc->id, $page->selectedDocId);
        $this->assertEquals('attributes', $page->drawerTab);
        $this->assertEquals($doc->id, $page->getSelectedDocument()->id);

        // 2. Status update with activity logging
        $page->updateDocumentStatus($doc->id, 'approved');
        $this->assertEquals('approved', $doc->fresh()->status);

        $activity = CdeActivityLog::where('loggable_type', CdeDocument::class)
            ->where('loggable_id', $doc->id)
            ->where('action', 'status_changed')
            ->first();
        $this->assertNotNull($activity);
        $this->assertStringContainsString('approved', $activity->description);

        // Check relationship on document
        $this->assertTrue($doc->activityLogs()->exists());

        // 3. New revision creation
        $page->addDocumentRevision($doc->id, 'P01.02', 'Adjusted water-cement ratio per QA review');
        $this->assertEquals('P01.02', $doc->fresh()->revision);
        $this->assertEquals('Adjusted water-cement ratio per QA review', $doc->fresh()->version_notes);

        // Previous revision archived in CdeDocumentVersion
        $version = CdeDocumentVersion::where('cde_document_id', $doc->id)->first();
        $this->assertNotNull($version);
        $this->assertEquals('P01.01', $version->version_number);

        // 4. Close drawer
        $page->closeDrawer();
        $this->assertFalse($page->drawerOpen);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Section 7: Distribution & Transmittals Section
    // ──────────────────────────────────────────────────────────────────────────

    public function test_section7_distribution_and_transmittals_section(): void
    {
        [$company, $user, $project, $page] = $this->setupCdeEnvironment();

        $folder = CdeFolder::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'name' => 'Transmittal Files',
            'created_by' => $user->id,
        ]);

        $doc = CdeDocument::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'cde_folder_id' => $folder->id,
            'document_number' => 'TR-DOC-001',
            'title' => 'Issued for Construction Drawings',
            'status' => 'approved',
            'revision' => 'C01.01',
            'uploaded_by' => $user->id,
        ]);

        // Create Transmittal
        $transmittal = Transmittal::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'transmittal_number' => 'TR-2026-001',
            'subject' => 'IFC Package Lot 3 Substructure',
            'purpose' => 'for_approval',
            'from_user_id' => $user->id,
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        // Attach item
        TransmittalItem::create([
            'transmittal_id' => $transmittal->id,
            'cde_document_id' => $doc->id,
            'description' => 'Substructure General Notes and Details',
            'copies' => 1,
        ]);

        $transmittals = $page->getTransmittals();
        $this->assertCount(1, $transmittals);
        $this->assertEquals('TR-2026-001', $transmittals->first()->transmittal_number);
        $this->assertEquals('IFC Package Lot 3 Substructure', $transmittals->first()->subject);
        $this->assertCount(1, $transmittals->first()->items);
        $this->assertEquals($doc->id, $transmittals->first()->items->first()->document->id);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Section 8: Discussions & Comments Hub
    // ──────────────────────────────────────────────────────────────────────────

    public function test_section8_discussions_and_comments_hub(): void
    {
        [$company, $user, $project, $page] = $this->setupCdeEnvironment();

        $folder = CdeFolder::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'name' => 'Coordination Hub',
            'created_by' => $user->id,
        ]);

        $doc = CdeDocument::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'cde_folder_id' => $folder->id,
            'document_number' => 'DISC-001',
            'title' => 'Structural Slab Rebar Schedule',
            'status' => 'under_review',
            'uploaded_by' => $user->id,
        ]);

        // 1. Post document review comment
        $page->selectedDocId = $doc->id;
        $page->newCommentText = 'Please cross-verify bar spacing near column C3.';
        $page->postComment();

        $docComment = CdeDocumentComment::where('cde_document_id', $doc->id)->first();
        $this->assertNotNull($docComment);
        $this->assertEquals('Please cross-verify bar spacing near column C3.', $docComment->comment);
        $this->assertFalse($docComment->is_resolved);

        // 2. Post folder comment
        $page->currentFolderId = $folder->id;
        $page->newFolderCommentText = 'Weekly coordination notes uploaded every Monday.';
        $page->postFolderComment();

        $folderComment = CdeDocumentComment::where('cde_folder_id', $folder->id)->first();
        $this->assertNotNull($folderComment);
        $this->assertEquals('Weekly coordination notes uploaded every Monday.', $folderComment->comment);

        // 3. Threaded reply
        $page->replyCommentText = 'Column C3 spacing verified at 150mm c/c.';
        $page->replyToComment($docComment->id);

        $reply = CdeDocumentComment::where('parent_id', $docComment->id)->first();
        $this->assertNotNull($reply);
        $this->assertEquals('Column C3 spacing verified at 150mm c/c.', $reply->comment);

        // 4. Toggle resolve/reopen
        $page->toggleCommentResolved($docComment->id);
        $this->assertTrue((bool)$docComment->fresh()->is_resolved);
        $page->toggleCommentResolved($docComment->id);
        $this->assertFalse((bool)$docComment->fresh()->is_resolved);

        // 5. Query project comments with filters
        $page->commentFilterStatus = 'open';
        $openComments = $page->getAllProjectComments();
        $this->assertTrue($openComments->contains('id', $docComment->id));

        $page->commentSearch = 'spacing near column';
        $searched = $page->getAllProjectComments();
        $this->assertTrue($searched->contains('id', $docComment->id));

        // 6. Delete comment
        $page->deleteComment($reply->id);
        $this->assertNull(CdeDocumentComment::find($reply->id));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Section 9: ISO 19650 Pipeline & Maturity Matrix
    // ──────────────────────────────────────────────────────────────────────────

    public function test_section9_iso19650_pipeline_and_maturity_matrix(): void
    {
        [$company, $user, $project, $page] = $this->setupCdeEnvironment();

        $folder = CdeFolder::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'name' => 'Pipeline Containers',
            'created_by' => $user->id,
        ]);

        // Create documents across pipeline stages
        CdeDocument::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'cde_folder_id' => $folder->id,
            'document_number' => 'PIPE-WIP',
            'title' => 'WIP Draft',
            'status' => 'S0',
            'discipline' => 'civil',
            'uploaded_by' => $user->id,
        ]);

        CdeDocument::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'cde_folder_id' => $folder->id,
            'document_number' => 'PIPE-REV',
            'title' => 'Under Review Coordination',
            'status' => 'S3',
            'discipline' => 'civil',
            'uploaded_by' => $user->id,
        ]);

        CdeDocument::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'cde_folder_id' => $folder->id,
            'document_number' => 'PIPE-APP',
            'title' => 'Stage Approved Model',
            'status' => 'S4',
            'discipline' => 'civil',
            'uploaded_by' => $user->id,
        ]);

        CdeDocument::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'cde_folder_id' => $folder->id,
            'document_number' => 'PIPE-PUB',
            'title' => 'AIM Handover Asset',
            'status' => 'S7',
            'discipline' => 'mechanical',
            'uploaded_by' => $user->id,
        ]);

        // 1. Pipeline status breakdown
        $pipeline = $page->getStatusPipeline();
        $this->assertCount(5, $pipeline);

        $labels = array_column($pipeline, 'label');
        $this->assertContains('WIP', $labels);
        $this->assertContains('Under Review', $labels);
        $this->assertContains('Approved', $labels);
        $this->assertContains('Published', $labels);

        $wipItem = collect($pipeline)->firstWhere('label', 'WIP');
        $this->assertEquals(1, $wipItem['count']);

        $underReviewItem = collect($pipeline)->firstWhere('label', 'Under Review');
        $this->assertEquals(1, $underReviewItem['count']);

        $approvedItem = collect($pipeline)->firstWhere('label', 'Approved');
        $this->assertEquals(1, $approvedItem['count']);

        $pubItem = collect($pipeline)->firstWhere('label', 'Published');
        $this->assertEquals(1, $pubItem['count']);

        // 2. Document health
        $health = $page->getDocumentHealth();
        $this->assertArrayHasKey('stale', $health);
        $this->assertArrayHasKey('review_backlog', $health);
        $this->assertArrayHasKey('needs_revision', $health);
        $this->assertEquals(1, $health['review_backlog']);

        // 3. Submission matrix
        $matrixData = $page->getSubmissionMatrix();
        $this->assertArrayHasKey('levels', $matrixData);
        $this->assertArrayHasKey('matrix', $matrixData);
        $this->assertArrayHasKey('totals', $matrixData);
        $this->assertEquals(4, $matrixData['total_docs']);

        // Civil row (3 docs: S0, S3, S4 -> 1 completed (S4) -> 33% completion)
        $civilRow = collect($matrixData['matrix'])->firstWhere('discipline', 'Civil');
        $this->assertNotNull($civilRow);
        $this->assertEquals(3, $civilRow['total']);
        $this->assertEquals(1, $civilRow['S0']);
        $this->assertEquals(1, $civilRow['S3']);
        $this->assertEquals(1, $civilRow['S4']);
        $this->assertEquals(33, $civilRow['completion_pct']);
    }
}
