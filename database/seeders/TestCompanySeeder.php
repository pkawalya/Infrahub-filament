<?php

namespace Database\Seeders;

use App\Models\{CdeProject, Client, Company, Subscription, Supplier, User, Vendor, Warehouse};
use Database\Seeders\TestCompany\TestModuleDataSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestCompanySeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. Company ──────────────────────────────────────────
        $allModules = array_keys(\App\Models\Module::$availableModules);

        $sub = Subscription::firstOrCreate(['slug' => 'enterprise'], [
            'name' => 'Enterprise', 'description' => 'Full-feature enterprise plan',
            'monthly_price' => 0, 'yearly_price' => 0, 'billing_cycle' => 'yearly',
            'max_users' => 100, 'max_projects' => 100, 'max_storage_gb' => 100,
            'included_modules' => $allModules, 'is_active' => true, 'sort_order' => 99,
        ]);

        $company = Company::updateOrCreate(['slug' => 'test-company-ltd'], [
            'name' => 'Test Company Ltd', 'email' => 'contact@test-company.com',
            'phone' => '+256 700 111 222', 'address' => 'Plot 45, Kampala Road',
            'city' => 'Kampala', 'country' => 'Uganda', 'timezone' => 'Africa/Kampala',
            'currency' => 'UGX', 'currency_symbol' => 'UGX', 'currency_position' => 'after',
            'subscription_id' => $sub->id, 'billing_cycle' => 'yearly',
            'subscription_starts_at' => now(), 'subscription_expires_at' => now()->addYear(),
            'max_users' => 100, 'max_projects' => 100, 'max_storage_gb' => 100,
            'is_active' => true, 'activated_at' => now(),
        ]);

        // Enable all modules at company level first
        foreach ($allModules as $code) {
            $company->enableModule($code);
        }
        $this->command->info('🏢 Test Company Ltd created (' . count($allModules) . ' modules enabled)');

        // ─── 2. Users (2FA bypassed) ─────────────────────────────
        $base = ['password' => Hash::make('password'), 'email_verified_at' => now(), 'company_id' => $company->id, 'is_active' => true, 'bypass_2fa' => true];
        $admin = User::updateOrCreate(['email' => 'admin@test-company.com'], array_merge($base, ['name' => 'Test Admin', 'user_type' => 'company_admin']));
        $mgr   = User::updateOrCreate(['email' => 'manager@test-company.com'], array_merge($base, ['name' => 'Test Manager', 'user_type' => 'manager']));
        $tech  = User::updateOrCreate(['email' => 'tech@test-company.com'], array_merge($base, ['name' => 'Test Technician', 'user_type' => 'technician']));
        $this->command->info('👥 3 users seeded (2FA bypassed)');

        // ─── 3. Shared entities ──────────────────────────────────
        $client = Client::updateOrCreate(['email' => 'client@test-company.com', 'company_id' => $company->id], ['name' => 'UNRA', 'company_name' => 'Uganda National Roads Authority', 'phone' => '+256 417 312 100', 'city' => 'Kampala', 'country' => 'Uganda', 'address' => 'Plot 5, Lourdel Rd', 'is_active' => true]);
        $supplier = Supplier::updateOrCreate(['email' => 'sales@himacement.com', 'company_id' => $company->id], ['name' => 'Hima Cement', 'phone' => '+256 414 251 000', 'contact_person' => 'Jane Auma', 'payment_terms' => 'Net 30', 'address' => 'Kasese, Uganda', 'is_active' => true]);
        $vendor = Vendor::updateOrCreate(['email' => 'uganda@kolin.com.tr', 'company_id' => $company->id], ['name' => 'Kolin Construction', 'phone' => '+256 414 789 012', 'contact_person' => 'Mehmet Yilmaz', 'address' => 'Kololo, Kampala', 'is_active' => true]);
        $warehouse = Warehouse::updateOrCreate(['code' => 'WH-MAIN', 'company_id' => $company->id], ['name' => 'Main Site Store', 'address' => 'Namanve Industrial Park', 'city' => 'Kampala', 'country' => 'Uganda', 'manager_id' => $mgr->id, 'is_active' => true, 'is_default' => true]);

        // ─── 4. Projects (staggered timelines) ────────────────────
        $projectsData = [
            ['name' => 'Kampala Northern Bypass Upgrade', 'code' => 'TST-ROAD-01', 'description' => 'Rehab of Northern Bypass from 2 to 4 lanes.', 'status' => 'active', 'project_type' => 'road', 'budget' => 85000000, 'city' => 'Kampala', 'start_date' => now()->subMonths(8), 'end_date' => now()->addMonths(10), 'progress_percent' => 40],
            ['name' => 'Soroti Solar Power Station', 'code' => 'TST-SOLAR-02', 'description' => '10MW solar PV with battery storage.', 'status' => 'active', 'project_type' => 'energy', 'budget' => 120000000, 'city' => 'Soroti', 'start_date' => now()->subMonths(3), 'end_date' => now()->addMonths(14), 'progress_percent' => 18],
            ['name' => 'Naguru Housing Complex', 'code' => 'TST-BUILD-03', 'description' => '12-storey mixed-use complex.', 'status' => 'active', 'project_type' => 'building', 'budget' => 90000000, 'city' => 'Kampala', 'start_date' => now()->subMonths(6), 'end_date' => now()->addMonths(4), 'progress_percent' => 65],
            ['name' => 'Gulu Water Treatment Plant', 'code' => 'TST-WATER-04', 'description' => '20,000m³/day water treatment facility.', 'status' => 'completed', 'project_type' => 'water', 'budget' => 35000000, 'city' => 'Gulu', 'start_date' => now()->subMonths(14), 'end_date' => now()->subMonths(2), 'progress_percent' => 100],
            ['name' => 'Jinja Fiber Backbone', 'code' => 'TST-FIBER-05', 'description' => 'Fiber optic backbone for Eastern Uganda.', 'status' => 'planning', 'project_type' => 'telecom', 'budget' => 45000000, 'city' => 'Jinja', 'start_date' => now()->addMonths(1), 'end_date' => now()->addMonths(9), 'progress_percent' => 0],
        ];
        $projects = [];
        foreach ($projectsData as $pd) {
            $data = collect($pd)->except(['progress_percent'])->toArray();
            $project = CdeProject::updateOrCreate(
                ['code' => $pd['code'], 'company_id' => $company->id],
                array_merge($data, [
                    'company_id' => $company->id,
                    'client_id' => $client->id,
                    'manager_id' => $mgr->id,
                    'billing_status' => 'active',
                    'country' => 'Uganda'
                ])
            );
            $project->syncModules($allModules, $admin->id);
            $projects[] = $project;
        }
        $this->command->info('📋 5 projects seeded (' . count($allModules) . ' modules enabled per project)');

        // ─── 5. Module data (all 18 modules) ─────────────────────
        TestModuleDataSeeder::seed($company, $projects, $admin, $mgr, $tech, $supplier, $vendor, $warehouse);
        $this->command->info('✅ All module data seeded across 5 projects');
    }
}
