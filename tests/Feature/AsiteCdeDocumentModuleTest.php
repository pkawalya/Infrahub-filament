<?php

namespace Tests\Feature;

use App\Models\CdeDocument;
use App\Models\CdeFolder;
use App\Models\CdeProject;
use App\Models\Company;
use App\Models\User;
use App\Services\AsiteStructureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsiteCdeDocumentModuleTest extends TestCase
{
    public function test_asite_structure_service_generates_exact_hierarchy(): void
    {
        $company = Company::firstOrCreate(['name' => 'Test Corp'], ['code' => 'TC', 'status' => 'active']);
        $project = CdeProject::withoutGlobalScopes()->firstOrCreate([
            'name' => 'AATC - Kampala',
            'company_id' => $company->id,
        ], [
            'code' => 'KAATC',
            'status' => 'active',
        ]);

        $folder = AsiteStructureService::initialize($project, true);

        $this->assertEquals('01.1.2 Project Protocols Documents', $folder->name);

        // Check top level root folders
        $rootFolders = CdeFolder::where('cde_project_id', $project->id)
            ->whereNull('parent_id')
            ->pluck('name')
            ->toArray();

        $this->assertContains('01 Development Management', $rootFolders);
        $this->assertContains('02 Design', $rootFolders);
        $this->assertContains('03 Planning', $rootFolders);
        $this->assertContains('04 Commercial', $rootFolders);
        $this->assertContains('05 Contract Management', $rootFolders);

        // Check subfolder 01.1 Pre-Contract Documents
        $f01 = CdeFolder::where('cde_project_id', $project->id)->where('name', '01 Development Management')->first();
        $this->assertNotNull($f01);

        $f01_1 = CdeFolder::where('parent_id', $f01->id)->where('name', '01.1 Pre-Contract Documents')->first();
        $this->assertNotNull($f01_1);

        // Check 01.1 children
        $children01_1 = CdeFolder::where('parent_id', $f01_1->id)->pluck('name')->toArray();
        $this->assertContains('01.1.1 Project Execution Plan', $children01_1);
        $this->assertContains('01.1.2 Project Protocols Documents', $children01_1);
        $this->assertContains('01.1.3 Site Geotechnical Investigation Report', $children01_1);
        $this->assertContains('01.1.4 Traffic Impact Assessment', $children01_1);
        $this->assertContains('01.1.5 ESIA', $children01_1);

        // Check 6 sample documents in 01.1.2
        $docs = $folder->documents()->get();
        $this->assertCount(6, $docs);

        $docNumbers = $docs->pluck('document_number')->toArray();
        $this->assertContains('KAATC-AFX-PM-ALL-ALL-RPT-6', $docNumbers);
        $this->assertContains('KAATC-JO-PM-ALL-ALL-EMP-5', $docNumbers);
        $this->assertContains('KAATC-AFX-PM-ALL-ALL-MAN-2', $docNumbers);
        $this->assertContains('KAATC-AFX-PM-ALL-ALL-MAN-1', $docNumbers);
        $this->assertContains('KAATC-JO-PM-ALL-ALL-MAN-', $docNumbers);
        $this->assertContains('KAATC-JCL-PM-ALL-ALL-PRC-', $docNumbers);
    }

    public function test_folder_creation_without_iso_standards_and_with_comments_purpose(): void
    {
        $company = Company::firstOrCreate(['name' => 'Test Corp'], ['code' => 'TC', 'status' => 'active']);
        $user = User::firstOrCreate(
            ['email' => 'author@test.com'],
            ['name' => 'Test Author', 'company_id' => $company->id, 'password' => bcrypt('password')]
        );
        $project = CdeProject::withoutGlobalScopes()->firstOrCreate([
            'name' => 'AATC - Kampala',
            'company_id' => $company->id,
        ], [
            'code' => 'KAATC',
            'status' => 'active',
        ]);

        // Create folder with comments & purpose (no ISO standards required)
        $folder = CdeFolder::create([
            'company_id' => $company->id,
            'cde_project_id' => $project->id,
            'name' => '06 Health & Safety Management',
            'description' => 'Mandatory PPE guidelines, risk assessments, and incident reporting forms.',
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($folder->id);
        $this->assertEquals('06 Health & Safety Management', $folder->name);
        $this->assertEquals('Mandatory PPE guidelines, risk assessments, and incident reporting forms.', $folder->description);

        // Add a comment to the folder
        $comment = \App\Models\CdeDocumentComment::create([
            'cde_folder_id' => $folder->id,
            'user_id' => $user->id,
            'comment' => 'Ensure all submittals include safety officer sign-off before field deployment.',
            'is_resolved' => false,
        ]);

        $this->assertNotNull($comment->id);
        $this->assertEquals($folder->id, $comment->folder->id);
        $this->assertFalse($comment->is_resolved);

        // Toggle resolution
        $comment->update(['is_resolved' => true]);
        $this->assertTrue($comment->fresh()->is_resolved);

        // Update folder purpose / guidelines
        $folder->update(['description' => 'Updated HSE protocol guidelines for Q4.']);
        $this->assertEquals('Updated HSE protocol guidelines for Q4.', $folder->fresh()->description);
    }

    public function test_threaded_comments_on_documents_and_folders(): void
    {
        $company = Company::firstOrCreate(['name' => 'Test Corp'], ['code' => 'TC', 'status' => 'active']);
        $user = User::firstOrCreate(
            ['email' => 'reviewer@test.com'],
            ['name' => 'Reviewer User', 'company_id' => $company->id, 'password' => bcrypt('password')]
        );
        $project = CdeProject::withoutGlobalScopes()->firstOrCreate([
            'name' => 'AATC - Kampala',
            'company_id' => $company->id,
        ], [
            'code' => 'KAATC',
            'status' => 'active',
        ]);

        $folder = CdeFolder::where('cde_project_id', $project->id)->first();

        // Top level parent comment
        $parent = \App\Models\CdeDocumentComment::create([
            'cde_folder_id' => $folder->id,
            'user_id' => $user->id,
            'comment' => 'Please provide the missing calibration certificates.',
            'is_resolved' => false,
        ]);

        // Threaded reply
        $reply = \App\Models\CdeDocumentComment::create([
            'cde_folder_id' => $folder->id,
            'parent_id' => $parent->id,
            'user_id' => $user->id,
            'comment' => 'Certificates have been attached in Revision P02.',
            'is_resolved' => false,
        ]);

        $this->assertEquals($parent->id, $reply->parent->id);
        $this->assertCount(1, $parent->fresh()->replies);
        $this->assertEquals('Certificates have been attached in Revision P02.', $parent->fresh()->replies->first()->comment);
    }
}
