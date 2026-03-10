<?php

namespace Database\Seeders;

use App\Support\ModulePermissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ModulePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'web';

        $this->command->info('Seeding module permissions...');

        $allPerms = ModulePermissions::allPermissions();
        $created = 0;

        foreach ($allPerms as $perm) {
            Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => $guard,
            ]);
            $created++;
        }

        $this->command->info("✅ {$created} module permissions seeded.");

        // Seed role templates for new companies
        $this->command->info('Seeding role templates...');
        foreach (ModulePermissions::roleTemplates() as $slug => $template) {
            $role = \App\Models\Role::firstOrCreate([
                'name' => $slug,
                'guard_name' => $guard,
                'company_id' => null, // Global/template roles
            ]);

            $role->syncPermissions($template['permissions']);
            $this->command->info("  → {$template['label']} ({$slug}): " . count($template['permissions']) . " permissions");
        }

        $this->command->info('✅ Role templates seeded.');
    }
}
