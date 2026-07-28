<?php

use App\Models\CdeProject;
use App\Models\Company;
use App\Models\ProjectSuggestion;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->company = Company::create([
        'name' => 'Suggestion Test Company',
        'slug' => 'suggestion-test-company',
        'is_active' => true,
    ]);

    $this->user = User::create([
        'company_id' => $this->company->id,
        'name' => 'John Field Worker',
        'email' => 'john@suggestiontest.com',
        'password' => bcrypt('password'),
        'user_type' => 'user',
        'is_active' => true,
    ]);

    $this->project = CdeProject::create([
        'company_id' => $this->company->id,
        'name' => 'Highway Construction Alpha',
        'code' => 'PRJ-HW-01',
    ]);
});

test('can create project-specific suggestion', function () {
    $suggestion = ProjectSuggestion::create([
        'company_id' => $this->company->id,
        'cde_project_id' => $this->project->id,
        'user_id' => $this->user->id,
        'title' => 'Add extra safety signages at Gate 2',
        'content' => 'High truck traffic requires additional warning signs.',
        'category' => 'safety',
        'status' => 'submitted',
    ]);

    $this->assertDatabaseHas('project_suggestions', [
        'id' => $suggestion->id,
        'company_id' => $this->company->id,
        'cde_project_id' => $this->project->id,
        'category' => 'safety',
    ]);

    expect($suggestion->cde_project_id)->toBe($this->project->id);
    expect($suggestion->project->name)->toBe('Highway Construction Alpha');
});

test('can create company-wide general suggestion without project context', function () {
    $suggestion = ProjectSuggestion::create([
        'company_id' => $this->company->id,
        'cde_project_id' => null,
        'user_id' => $this->user->id,
        'title' => 'Company-wide weekly safety newsletter',
        'content' => 'Distribute a weekly digest to all site engineers.',
        'category' => 'innovation',
        'status' => 'submitted',
    ]);

    $this->assertDatabaseHas('project_suggestions', [
        'id' => $suggestion->id,
        'company_id' => $this->company->id,
        'cde_project_id' => null,
        'category' => 'innovation',
    ]);

    expect($suggestion->cde_project_id)->toBeNull();
    expect($suggestion->project)->toBeNull();
});
