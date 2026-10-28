<?php

namespace App\Console\Commands;

use App\Models\CdeProject;
use App\Models\Company;
use App\Models\User;
use App\Services\CdeFolderTemplateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SetupMohestProjectsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'infrahub:setup-mohest-projects 
                            {--password=InfraHub@2026! : Default password for created users}
                            {--force : Force re-initialization if already exists}';

    /**
     * The console command description.
     */
    protected $description = 'Setup "MoHEST / Dr. John Garang Sudan" company, company admin, projects, and consultant team';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $defaultPassword = $this->option('password') ?: 'InfraHub@2026!';
        $companyName = 'MoHEST / Dr. John Garang Sudan';
        $adminEmail = 'appcellon@gmail.com';

        $this->info("=====================================================================");
        $this->info("  Setting up MoHEST / Dr. John Garang University Projects on InfraHub");
        $this->info("=====================================================================\n");

        DB::beginTransaction();

        try {
            // ── 1. Create or Find Company ──────────────────────────────────────
            $this->info("1. Creating / Resolving Company: \"{$companyName}\"...");
            $company = Company::firstOrCreate(
                ['name' => $companyName],
                [
                    'email' => $adminEmail,
                    'country' => 'South Sudan',
                    'currency' => 'USD',
                    'currency_symbol' => '$',
                    'currency_position' => 'before',
                    'is_active' => true,
                    'is_trial' => false,
                    'max_users' => 100,
                    'max_projects' => 50,
                    'max_storage_gb' => 250,
                    'subscription_starts_at' => now(),
                    'subscription_expires_at' => now()->addYears(5),
                    'billing_cycle' => 'unlimited',
                    'notes' => 'Government of South Sudan - Ministry of Higher Education, Science and Technology (MoHEST) & Dr. John Garang Memorial University of Science and Technology.',
                ]
            );
            $this->line("   ✓ Company ID: {$company->id} [{$company->name}]");

            // Enable standard Infrahub modules for the company
            $modules = [
                'cde', 'core', 'field_management', 'task_workflow', 'inventory',
                'cost_contracts', 'planning_progress', 'boq_management', 'equipment',
                'sheq', 'reporting', 'suggestion_box',
            ];
            foreach ($modules as $mod) {
                $company->enableModule($mod);
            }
            $this->line("   ✓ Enabled " . count($modules) . " enterprise modules for {$company->name}");

            // ── 2. Create Company Admin ───────────────────────────────────────
            $this->info("\n2. Creating Company Admin: {$adminEmail}...");
            $adminUser = User::where('email', $adminEmail)->first();
            if (!$adminUser) {
                $adminUser = User::create([
                    'name' => 'MoHEST Company Admin',
                    'email' => $adminEmail,
                    'password' => Hash::make($defaultPassword),
                    'company_id' => $company->id,
                    'user_type' => 'company_admin',
                    'job_title' => 'Company Administrator',
                    'department' => 'Administration & Project Oversight',
                    'email_verified_at' => now(),
                    'is_active' => true,
                    'must_change_password' => false,
                ]);
                $this->line("   ✓ Created new Company Admin: {$adminUser->name} ({$adminEmail})");
            } else {
                $adminUser->update([
                    'company_id' => $company->id,
                    'user_type' => 'company_admin',
                    'is_active' => true,
                ]);
                $adminUser->attachToCompany($company->id, [
                    'user_type' => 'company_admin',
                    'job_title' => 'Company Administrator',
                ]);
                $this->line("   ✓ Updated existing user to Company Admin: {$adminUser->name} ({$adminEmail})");
            }

            // ── 3. Define the Two Projects ────────────────────────────────────
            $this->info("\n3. Creating Projects...");
            $projectsConfig = [
                [
                    'name' => 'Digital Skills Training Centre at Dr. John Garang University of Science',
                    'code' => 'DJG-DSTC',
                    'description' => 'Construction and Establishment of Digital Skills Training Centre at Dr. John Garang Memorial University of Science and Technology, Bor, South Sudan.',
                    'city' => 'Bor',
                    'country' => 'South Sudan',
                    'project_type' => 'building',
                    'status' => 'active',
                ],
                [
                    'name' => 'Technology S-MoHEST-521333-CW-RFB-LOT 3',
                    'code' => 'SMOHEST-LOT3',
                    'description' => 'MoHEST Head Office Technology Facilities & Infrastructure Works - Contract Ref: S-MoHEST-521333-CW-RFB-LOT 3, Juba, South Sudan.',
                    'city' => 'Juba',
                    'country' => 'South Sudan',
                    'project_type' => 'building',
                    'status' => 'active',
                ],
            ];

            $createdProjects = [];
            foreach ($projectsConfig as $pData) {
                $project = CdeProject::withoutGlobalScopes()->where('company_id', $company->id)
                    ->where(function ($query) use ($pData) {
                        $query->where('name', $pData['name'])
                              ->orWhere('code', $pData['code']);
                    })
                    ->first();

                if (!$project) {
                    $project = CdeProject::create([
                        'company_id' => $company->id,
                        'name' => $pData['name'],
                        'code' => $pData['code'],
                        'description' => $pData['description'],
                        'city' => $pData['city'],
                        'country' => $pData['country'],
                        'project_type' => $pData['project_type'],
                        'status' => $pData['status'],
                        'currency' => 'USD',
                        'currency_symbol' => '$',
                        'currency_position' => 'before',
                        'billing_status' => 'active',
                        'manager_id' => $adminUser->id,
                        'start_date' => now(),
                    ]);
                    $this->line("   ✓ Created Project: {$project->name} [Code: {$project->code}] (ID: {$project->id})");
                } else {
                    $this->line("   ✓ Found existing Project: {$project->name} [Code: {$project->code}] (ID: {$project->id})");
                }

                // Enable all modules for the project
                foreach ($modules as $mod) {
                    $project->enableModule($mod);
                }

                // Initialize CDE directory structure template
                if ($project->folders()->count() === 0) {
                    CdeFolderTemplateService::initialize($project, 'infrastructure', true);
                    $this->line("     - Initialized InfraHub CDE Engineering & EPC directory structure template");
                }

                // Ensure Company Admin is a project member
                $project->members()->syncWithoutDetaching([
                    $adminUser->id => [
                        'role' => 'supervisor',
                        'invited_by' => $adminUser->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);

                $createdProjects[] = $project;
            }

            // ── 4. Consultants Team Registry ─────────────────────────────────
            $this->info("\n4. Registering Consultants Team & Adding to Projects...");

            $consultants = [
                [
                    'sno' => 1,
                    'name' => 'Aita Joel',
                    'email' => 'joadahconsult@gmail.com',
                    'phone' => '0772877529',
                    'position' => 'CEO',
                    'key_roles' => 'General Oversight',
                    'user_type' => 'manager',
                    'project_role' => 'supervisor',
                ],
                [
                    'sno' => 2,
                    'name' => 'Gatre Patrick',
                    'email' => 'patgatre@joadahconsult.com',
                    'phone' => '0784188814',
                    'position' => 'Project Manager',
                    'key_roles' => 'Contract Administration and Project Management',
                    'user_type' => 'manager',
                    'project_role' => 'engineer',
                ],
                [
                    'sno' => 3,
                    'name' => 'Dans Naturinda',
                    'email' => 'dansnaturinda@gmail.com',
                    'phone' => '0772508518',
                    'position' => 'Team Leader',
                    'key_roles' => 'Technical Oversight',
                    'user_type' => 'manager',
                    'project_role' => 'engineer',
                ],
                // S/No 4 is Architect with pending details
                [
                    'sno' => 5,
                    'name' => 'Dr. John Muhumuza Kakitahi',
                    'email' => 'jkmuhumuza@outlook.com',
                    'phone' => '0788500023',
                    'position' => 'Quantity Surveyor',
                    'key_roles' => 'Technical Oversight',
                    'user_type' => 'member',
                    'project_role' => 'member',
                ],
                [
                    'sno' => 6,
                    'name' => 'Brian Kasumba',
                    'email' => 'bryansk50@gmail.com',
                    'phone' => null,
                    'position' => 'Quantity Surveyor',
                    'key_roles' => 'Certification of Contractors Payments. Preparation of Cost Appraisal Report',
                    'user_type' => 'member',
                    'project_role' => 'member',
                ],
                [
                    'sno' => 7,
                    'name' => 'Alurile Jimmy',
                    'email' => 'alurile04@gmail.com',
                    'phone' => null,
                    'position' => 'Clerk of Work for MoHEST Headoffice project',
                    'key_roles' => 'Day to day site supervision. Approving Contractors Daily Reports',
                    'user_type' => 'member',
                    'project_role' => 'supervisor',
                ],
                [
                    'sno' => 8,
                    'name' => 'Andabati Morish',
                    'email' => 'morisherinayo2@gmail.com',
                    'phone' => null,
                    'position' => 'Clerk of Work for Dr John Garang University Site',
                    'key_roles' => 'Day to day site supervision. Approving Contractors Daily Reports',
                    'user_type' => 'member',
                    'project_role' => 'supervisor',
                ],
                [
                    'sno' => 9,
                    'name' => 'Aanyu Stella',
                    'email' => 'aanst2014@gmail.com',
                    'phone' => '0782978938',
                    'position' => 'Environmental Specialist',
                    'key_roles' => 'Review of Contractors Monthly ESMP reports on Environmental Matters.',
                    'user_type' => 'member',
                    'project_role' => 'member',
                ],
                [
                    'sno' => 10,
                    'name' => 'Lilian Nakanwagi',
                    'email' => 'liliannakanwagi@gmail.com',
                    'phone' => '0779492469',
                    'position' => 'Social Specialist',
                    'key_roles' => 'Review of Contractors Monthly ESMP reports on Social Matters.',
                    'user_type' => 'member',
                    'project_role' => 'member',
                ],
            ];

            $headers = ['S/No', 'Name', 'Email', 'Position / Key Roles', 'Phone', 'Project Access'];
            $tableRows = [];

            foreach ($consultants as $cData) {
                $user = User::where('email', $cData['email'])->first();

                if (!$user) {
                    $user = User::create([
                        'name' => $cData['name'],
                        'email' => $cData['email'],
                        'phone' => $cData['phone'],
                        'password' => Hash::make($defaultPassword),
                        'company_id' => $company->id,
                        'user_type' => $cData['user_type'],
                        'job_title' => $cData['position'],
                        'department' => 'Project Consultancy Team',
                        'email_verified_at' => now(),
                        'is_active' => true,
                        'must_change_password' => false,
                    ]);
                } else {
                    $user->update([
                        'company_id' => $user->company_id ?: $company->id,
                        'phone' => $user->phone ?: $cData['phone'],
                        'job_title' => $user->job_title ?: $cData['position'],
                    ]);
                    $user->attachToCompany($company->id, [
                        'user_type' => $cData['user_type'],
                        'job_title' => $cData['position'],
                        'phone' => $cData['phone'],
                    ]);
                }

                // Attach to both projects
                foreach ($createdProjects as $proj) {
                    $proj->members()->syncWithoutDetaching([
                        $user->id => [
                            'role' => $cData['project_role'],
                            'invited_by' => $adminUser->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                    ]);
                }

                $tableRows[] = [
                    $cData['sno'],
                    $cData['name'],
                    $cData['email'],
                    $cData['position'] . ' — ' . $cData['key_roles'],
                    $cData['phone'] ?: 'N/A',
                    'Both Projects (' . implode(', ', array_map(fn($p) => $p->code, $createdProjects)) . ')',
                ];
            }

            // Note on row 4 (Architect)
            $tableRows[] = [
                '4',
                '[Pending Appointment]',
                '—',
                'Architect (Design & Technical Oversight)',
                '—',
                'Slot reserved',
            ];

            $this->table($headers, $tableRows);

            DB::commit();

            $this->info("\n=====================================================================");
            $this->info("  ✓ Successfully configured MoHEST / Dr. John Garang Sudan Projects!");
            $this->info("=====================================================================");
            $this->line("Company Admin Login : {$adminEmail}");
            $this->line("Default Password    : {$defaultPassword}");
            $this->line("Panel URL           : " . url('/app/login'));
            $this->line("Projects created    : 2");
            $this->line("Consultants added   : " . count($consultants));
            $this->info("=====================================================================\n");

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Failed to setup MoHEST projects: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
