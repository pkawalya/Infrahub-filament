<?php

namespace Database\Seeders;

use App\Models\BidStage;
use App\Models\BidStageTransition;
use App\Models\Company;
use App\Models\TenderStage;
use App\Models\TenderStageTransition;
use Illuminate\Database\Seeder;

class TenderWorkflowSeeder extends Seeder
{
    /**
     * Seed default tender and bid stages for all active companies.
     */
    public function run(): void
    {
        $companies = Company::where('is_active', true)->get();

        foreach ($companies as $company) {
            $this->seedTenderStages($company->id);
            $this->seedBidStages($company->id);
        }

        $this->command->info('✅ Tender & Bid workflow stages seeded for ' . $companies->count() . ' companies.');
    }

    protected function seedTenderStages(int $companyId): void
    {
        $stages = [
            ['name' => 'Draft',      'slug' => 'draft',      'color' => 'gray',    'icon' => 'heroicon-o-pencil-square', 'sort_order' => 1, 'is_default' => true],
            ['name' => 'Published',  'slug' => 'published',  'color' => 'info',    'icon' => 'heroicon-o-megaphone',     'sort_order' => 2],
            ['name' => 'Evaluation', 'slug' => 'evaluation', 'color' => 'warning', 'icon' => 'heroicon-o-clipboard-document-check', 'sort_order' => 3],
            ['name' => 'Awarded',    'slug' => 'awarded',    'color' => 'success', 'icon' => 'heroicon-o-trophy',        'sort_order' => 4, 'is_terminal' => true],
            ['name' => 'Closed',     'slug' => 'closed',     'color' => 'primary', 'icon' => 'heroicon-o-lock-closed',   'sort_order' => 5, 'is_terminal' => true],
            ['name' => 'Cancelled',  'slug' => 'cancelled',  'color' => 'danger',  'icon' => 'heroicon-o-x-circle',      'sort_order' => 6, 'is_terminal' => true],
        ];

        $created = [];
        foreach ($stages as $stage) {
            $created[$stage['slug']] = TenderStage::firstOrCreate(
                ['company_id' => $companyId, 'slug' => $stage['slug']],
                array_merge($stage, ['company_id' => $companyId, 'is_active' => true])
            );
        }

        // Define valid transitions: from → [to1, to2, ...]
        $transitions = [
            'draft'      => ['published', 'cancelled'],
            'published'  => ['evaluation', 'cancelled'],
            'evaluation' => ['awarded', 'closed', 'cancelled'],
            'awarded'    => ['closed'],
            'closed'     => [],  // Terminal
            'cancelled'  => [],  // Terminal
        ];

        foreach ($transitions as $from => $toList) {
            foreach ($toList as $to) {
                if (isset($created[$from], $created[$to])) {
                    TenderStageTransition::firstOrCreate([
                        'company_id'    => $companyId,
                        'from_stage_id' => $created[$from]->id,
                        'to_stage_id'   => $created[$to]->id,
                    ], [
                        'company_id'    => $companyId,
                        'is_active'     => true,
                    ]);
                }
            }
        }
    }

    protected function seedBidStages(int $companyId): void
    {
        $stages = [
            ['name' => 'Submitted',     'slug' => 'submitted',     'color' => 'info',    'icon' => 'heroicon-o-document-arrow-up', 'sort_order' => 1, 'is_default' => true],
            ['name' => 'Under Review',  'slug' => 'under_review',  'color' => 'warning', 'icon' => 'heroicon-o-eye',               'sort_order' => 2],
            ['name' => 'Shortlisted',   'slug' => 'shortlisted',   'color' => 'primary', 'icon' => 'heroicon-o-star',              'sort_order' => 3],
            ['name' => 'Accepted',      'slug' => 'accepted',      'color' => 'success', 'icon' => 'heroicon-o-check-circle',      'sort_order' => 4, 'is_terminal' => true],
            ['name' => 'Rejected',      'slug' => 'rejected',      'color' => 'danger',  'icon' => 'heroicon-o-x-circle',          'sort_order' => 5, 'is_terminal' => true],
            ['name' => 'Disqualified',  'slug' => 'disqualified',  'color' => 'danger',  'icon' => 'heroicon-o-no-symbol',         'sort_order' => 6, 'is_terminal' => true],
        ];

        $created = [];
        foreach ($stages as $stage) {
            $created[$stage['slug']] = BidStage::firstOrCreate(
                ['company_id' => $companyId, 'slug' => $stage['slug']],
                array_merge($stage, ['company_id' => $companyId, 'is_active' => true])
            );
        }

        $transitions = [
            'submitted'    => ['under_review', 'disqualified'],
            'under_review' => ['shortlisted', 'rejected', 'disqualified'],
            'shortlisted'  => ['accepted', 'rejected'],
            'accepted'     => [],
            'rejected'     => [],
            'disqualified' => [],
        ];

        foreach ($transitions as $from => $toList) {
            foreach ($toList as $to) {
                if (isset($created[$from], $created[$to])) {
                    BidStageTransition::firstOrCreate([
                        'company_id'    => $companyId,
                        'from_stage_id' => $created[$from]->id,
                        'to_stage_id'   => $created[$to]->id,
                    ], [
                        'company_id'    => $companyId,
                        'is_active'     => true,
                    ]);
                }
            }
        }
    }
}
