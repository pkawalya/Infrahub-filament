<?php

namespace Tests\Feature;

use App\Models\CdeDocument;
use App\Models\CdeFolder;
use App\Models\CdeProject;
use App\Models\Company;
use App\Models\User;
use App\Services\CdeFolderTemplateService;
use Tests\TestCase;

class CdeDocumentModuleTest extends TestCase
{
    public function test_cde_folder_template_service_generates_infrastructure_lifecycle_hierarchy(): void
    {
        $company = Company::firstOrCreate(['name' => 'Apex Infra'], ['code' => 'AI', 'status' => 'active']);
        $project = CdeProject::withoutGlobalScopes()->firstOrCreate([
            'name' => 'Metro Rail Line 3',
            'company_id' => $company->id,
        ], [
            'code' => 'MRL3',
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
        $this->assertContains('MRL3-MGT-PRP-001', $docNumbers);
        $this->assertContains('MRL3-ENV-EMP-PLN-001', $docNumbers);
        $this->assertContains('MRL3-QSE-QAP-MAN-001', $docNumbers);
        $this->assertContains('MRL3-ENG-DWG-GEN-001', $docNumbers);
        $this->assertContains('MRL3-SAF-HSE-SOP-001', $docNumbers);
        $this->assertContains('MRL3-COM-BOQ-SUM-001', $docNumbers);
    }

    public function test_cde_folder_template_service_iso19650_containers(): void
    {
        $company = Company::firstOrCreate(['name' => 'Apex Infra'], ['code' => 'AI', 'status' => 'active']);
        $project = CdeProject::withoutGlobalScopes()->firstOrCreate([
            'name' => 'Highway Interchange',
            'company_id' => $company->id,
        ], [
            'code' => 'HWY02',
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
        $company = Company::firstOrCreate(['name' => 'Apex Infra'], ['code' => 'AI', 'status' => 'active']);
        $user = User::firstOrCreate(
            ['email' => 'engineer@test.com'],
            ['name' => 'Senior Engineer', 'company_id' => $company->id, 'password' => bcrypt('password')]
        );
        $project = CdeProject::withoutGlobalScopes()->firstOrCreate([
            'name' => 'Metro Rail Line 3',
            'company_id' => $company->id,
        ], [
            'code' => 'MRL3',
            'status' => 'active',
        ]);

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

        // Add a comment to the folder
        $comment = \App\Models\CdeDocumentComment::create([
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
        $company = Company::firstOrCreate(['name' => 'Apex Infra'], ['code' => 'AI', 'status' => 'active']);
        $user = User::firstOrCreate(
            ['email' => 'reviewer2@test.com'],
            ['name' => 'QA Auditor', 'company_id' => $company->id, 'password' => bcrypt('password')]
        );
        $project = CdeProject::withoutGlobalScopes()->firstOrCreate([
            'name' => 'Metro Rail Line 3',
            'company_id' => $company->id,
        ], [
            'code' => 'MRL3',
            'status' => 'active',
        ]);

        $folder = CdeFolder::where('cde_project_id', $project->id)->first();

        // Top level parent comment
        $parent = \App\Models\CdeDocumentComment::create([
            'cde_folder_id' => $folder->id,
            'user_id' => $user->id,
            'comment' => 'Please confirm latest submittal references.',
            'is_resolved' => false,
        ]);

        // Threaded reply
        $reply = \App\Models\CdeDocumentComment::create([
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
}
