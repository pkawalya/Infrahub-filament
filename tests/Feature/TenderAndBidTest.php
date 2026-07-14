<?php

use App\Models\User;
use App\Models\Company;
use App\Models\Tender;
use App\Models\TenderStage;
use App\Models\BidStage;
use App\Models\TenderBid;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->company = Company::firstOrCreate(
        ['slug' => 'acme-field-services'],
        [
            'name' => 'Acme Field Services',
            'email' => 'hello@acme-fs.com',
            'phone' => '+1 (555) 123-4567',
            'is_active' => true,
        ]
    );

    $this->user = User::firstOrCreate(
        ['email' => 'admin@acme-fs.com'],
        [
            'name' => 'Company Admin',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
            'user_type' => 'company_admin',
            'is_active' => true,
        ]
    );

    // Make sure tender stages are seeded
    if (TenderStage::where('company_id', $this->company->id)->count() === 0) {
        Artisan::call('db:seed', ['--class' => 'TenderWorkflowSeeder']);
    }

    $this->tenderStage = TenderStage::where('company_id', $this->company->id)->where('is_default', true)->first();
    $this->bidStage = BidStage::where('company_id', $this->company->id)->where('is_default', true)->first();
});

it('can view tenders with data', function () {
    $tender = Tender::create([
        'company_id' => $this->company->id,
        'title' => 'Test Tender',
        'reference' => 'TND-2026-001',
        'client_name' => 'Client Inc.',
        'status' => 'identified',
        'tender_stage_id' => $this->tenderStage->id,
        'created_by' => $this->user->id,
    ]);

    actingAs($this->user)
        ->get('/app/tenders')
        ->assertStatus(200);

    actingAs($this->user)
        ->get('/app/tenders/' . $tender->getRouteKey())
        ->assertStatus(200);

    actingAs($this->user)
        ->get('/app/tenders/' . $tender->getRouteKey() . '/edit')
        ->assertStatus(200);
});

it('can view tenders with bids', function () {
    $tender = Tender::create([
        'company_id' => $this->company->id,
        'title' => 'Test Tender with Bid',
        'reference' => 'TND-2026-002',
        'client_name' => 'Client Inc.',
        'status' => 'identified',
        'tender_stage_id' => $this->tenderStage->id,
        'created_by' => $this->user->id,
    ]);

    $bid = TenderBid::create([
        'company_id' => $this->company->id,
        'tender_id' => $tender->id,
        'reference' => 'BID-2026-001',
        'bidder_name' => 'Bidder Company',
        'bidder_email' => 'bidder@example.com',
        'bid_amount' => 50000.00,
        'bid_stage_id' => $this->bidStage->id,
        'created_by' => $this->user->id,
    ]);

    actingAs($this->user)
        ->get('/app/tenders/' . $tender->getRouteKey())
        ->assertStatus(200);
});

it('automatically seeds default stages on company creation', function () {
    $suffix = uniqid();
    $newCompany = Company::create([
        'name' => 'New Test Company ' . $suffix,
        'slug' => 'new-test-company-' . $suffix,
        'is_active' => true,
    ]);

    // Check stages count
    expect(TenderStage::where('company_id', $newCompany->id)->count())->toBe(6);
    expect(BidStage::where('company_id', $newCompany->id)->count())->toBe(6);

    // Check defaults
    $defaultTenderStage = TenderStage::where('company_id', $newCompany->id)->where('is_default', true)->first();
    expect($defaultTenderStage)->not->toBeNull();
    expect($defaultTenderStage->slug)->toBe('draft');

    $defaultBidStage = BidStage::where('company_id', $newCompany->id)->where('is_default', true)->first();
    expect($defaultBidStage)->not->toBeNull();
    expect($defaultBidStage->slug)->toBe('submitted');
});

