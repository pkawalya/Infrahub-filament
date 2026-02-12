<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Define all resource permissions ────────────────────────────
        $resources = [
            'asset',
            'cde::project',
            'client',
            'company',
            'company::email::template',
            'company::role',
            'company::user',
            'email::template',
            'invoice',
            'role',
            'safety::incident',
            'subscription',
            'task',
            'user',
            'work::order',
        ];

        $abilities = [
            'view_any',
            'view',
            'create',
            'update',
            'delete',
            'restore',
            'force_delete',
            'force_delete_any',
            'restore_any',
            'replicate',
            'reorder',
        ];

        foreach ($resources as $resource) {
            foreach ($abilities as $ability) {
                Permission::firstOrCreate([
                    'name' => "{$ability}_{$resource}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // ── Page & Widget permissions ──────────────────────────────────
        $pagePermissions = [
            'page_Dashboard',
            'page_SystemSettings',
        ];
        $widgetPermissions = [
            'widget_PlatformOverview',
            'widget_TenantDashboardOverview',
            'widget_WorkOrdersByStatusChart',
            'widget_ProjectProgressChart',
            'widget_TasksTrendChart',
            'widget_CompaniesByPlanChart',
            'widget_UserGrowthChart',
        ];

        foreach (array_merge($pagePermissions, $widgetPermissions) as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $allPerms = Permission::pluck('name');
        $this->command->info("Total permissions: {$allPerms->count()}");

        // ── super_admin role (Shield handles Gate::before bypass) ─────
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        // super_admin doesn't need explicit permissions — Shield's Gate::before grants all

        // ── company_admin role — full access ──────────────────────────
        $companyAdmin = Role::firstOrCreate(['name' => 'company_admin', 'guard_name' => 'web']);
        $companyAdmin->syncPermissions($allPerms);
        $this->command->info("company_admin: {$companyAdmin->permissions->count()} permissions");

        // ── manager role — view + create + update ─────────────────────
        $managerPerms = Permission::where('name', 'like', 'view_%')
            ->orWhere('name', 'like', 'create_%')
            ->orWhere('name', 'like', 'update_%')
            ->orWhere('name', 'like', 'page_%')
            ->orWhere('name', 'like', 'widget_%')
            ->pluck('name');
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->syncPermissions($managerPerms);
        $this->command->info("manager: {$manager->permissions->count()} permissions");

        // ── technician role — view + update ───────────────────────────
        $techPerms = Permission::where('name', 'like', 'view_%')
            ->orWhere('name', 'like', 'update_%')
            ->orWhere('name', 'like', 'page_%')
            ->orWhere('name', 'like', 'widget_%')
            ->pluck('name');
        $technician = Role::firstOrCreate(['name' => 'technician', 'guard_name' => 'web']);
        $technician->syncPermissions($techPerms);
        $this->command->info("technician: {$technician->permissions->count()} permissions");

        // ── member role — view only ───────────────────────────────────
        $memberPerms = Permission::where('name', 'like', 'view_%')
            ->orWhere('name', 'like', 'page_%')
            ->orWhere('name', 'like', 'widget_%')
            ->pluck('name');
        $member = Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
        $member->syncPermissions($memberPerms);
        $this->command->info("member: {$member->permissions->count()} permissions");

        // ── panel_user role ───────────────────────────────────────────
        $panelUser = Role::firstOrCreate(['name' => 'panel_user', 'guard_name' => 'web']);
        $panelUser->syncPermissions($memberPerms); // same as member by default

        // ── Assign roles to existing users based on user_type ─────────
        User::all()->each(function (User $user) {
            $roleMap = [
                'super_admin' => 'super_admin',
                'company_admin' => 'company_admin',
                'manager' => 'manager',
                'technician' => 'technician',
                'member' => 'member',
            ];

            $roleName = $roleMap[$user->user_type] ?? 'member';

            if (!$user->hasRole($roleName)) {
                $user->syncRoles([$roleName]);
            }
        });

        $this->command->info('Roles assigned to all users based on user_type.');
    }
}
