<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Module;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SaasFoundationSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. Seed Subscription Plans ──────────────────────────
        $starter = Subscription::create([
            'name' => 'Starter',
            'slug' => 'starter',
            'description' => 'For small teams getting started with field service management.',
            'monthly_price' => 29.00,
            'yearly_price' => 290.00,
            'max_users' => 5,
            'max_projects' => 5,
            'max_storage_gb' => 5,
            'included_modules' => ['core'],
            'features' => ['Work Orders', 'Client Management', 'Basic Reporting'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $professional = Subscription::create([
            'name' => 'Professional',
            'slug' => 'professional',
            'description' => 'For growing teams that need advanced field management.',
            'monthly_price' => 79.00,
            'yearly_price' => 790.00,
            'max_users' => 25,
            'max_projects' => 25,
            'max_storage_gb' => 25,
            'included_modules' => ['core', 'cde', 'field_management', 'task_workflow', 'inventory'],
            'features' => ['All Starter features', 'Document Management', 'GPS Tracking', 'Inventory'],
            'is_active' => true,
            'is_popular' => true,
            'sort_order' => 2,
        ]);

        $enterprise = Subscription::create([
            'name' => 'Enterprise',
            'slug' => 'enterprise',
            'description' => 'For large organizations with complex requirements.',
            'monthly_price' => 199.00,
            'yearly_price' => 1990.00,
            'max_users' => 100,
            'max_projects' => 100,
            'max_storage_gb' => 100,
            'included_modules' => [
                'core',
                'cde',
                'field_management',
                'task_workflow',
                'inventory',
                'cost_contracts',
                'planning_progress',
                'boq_management',
                'sheq',
                'reporting',
            ],
            'features' => ['All Professional features', 'Contracts & Budget', 'SHEQ', 'BOQ', 'Custom Reports'],
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // ─── 2. Seed Modules Registry ────────────────────────────
        foreach (Module::$availableModules as $code => $data) {
            Module::create([
                'code' => $code,
                'name' => $data['name'],
                'description' => $data['description'],
                'icon' => $data['icon'] ?? null,
                'is_active' => true,
                'is_core' => $data['is_core'] ?? false,
            ]);
        }

        // ─── 3. Create Super Admin ──────────────────────────────
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@infrahub.io',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'user_type' => 'super_admin',
            'is_active' => true,
        ]);

        // ─── 4. Create Demo Company & Users ─────────────────────
        $demoCompany = Company::create([
            'name' => 'Acme Field Services',
            'slug' => 'acme-field-services',
            'email' => 'hello@acme-fs.com',
            'phone' => '+1 (555) 123-4567',
            'address' => '123 Service Lane',
            'city' => 'Dallas',
            'state' => 'TX',
            'country' => 'US',
            'timezone' => 'America/Chicago',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'subscription_id' => $enterprise->id,
            'billing_cycle' => 'yearly',
            'subscription_starts_at' => now(),
            'subscription_expires_at' => now()->addYear(),
            'max_users' => 100,
            'max_projects' => 100,
            'max_storage_gb' => 100,
            'is_active' => true,
            'activated_at' => now(),
        ]);

        // Enable all modules for demo company
        $demoCompany->syncModulesFromSubscription();

        // Create company admin
        $companyAdmin = User::create([
            'name' => 'Company Admin',
            'email' => 'admin@acme-fs.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'company_id' => $demoCompany->id,
            'user_type' => 'company_admin',
            'is_active' => true,
        ]);

        // Create team members
        $roles = [
            ['name' => 'John Technician', 'email' => 'john@acme-fs.com', 'user_type' => 'technician'],
            ['name' => 'Sarah Manager', 'email' => 'sarah@acme-fs.com', 'user_type' => 'manager'],
            ['name' => 'Mike Member', 'email' => 'mike@acme-fs.com', 'user_type' => 'member'],
        ];

        foreach ($roles as $r) {
            User::create([
                'name' => $r['name'],
                'email' => $r['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'company_id' => $demoCompany->id,
                'user_type' => $r['user_type'],
                'is_active' => true,
            ]);
        }

        $this->command->info('✅ SaaS foundation seeded:');
        $this->command->info('   • 3 subscription plans (Starter, Professional, Enterprise)');
        $this->command->info('   • ' . count(Module::$availableModules) . ' modules registered');
        $this->command->info('   • Super Admin: admin@infrahub.io / password');
        $this->command->info('   • Demo Company: Acme Field Services');
        $this->command->info('   • Company Admin: admin@acme-fs.com / password');
    }
}
