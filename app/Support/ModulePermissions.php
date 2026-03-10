<?php

namespace App\Support;

/**
 * Central registry of module permissions.
 * Each module maps to a set of granular permissions (view, create, update, delete).
 * Used by: Filament resources (canAccess), API middleware, and role seeding.
 */
class ModulePermissions
{
    /**
     * Module definitions: code => [label, description, permissions[]]
     */
    public static function modules(): array
    {
        return [
            'projects' => [
                'label' => 'Projects',
                'description' => 'Project management, creation, and settings',
                'permissions' => ['projects.view', 'projects.create', 'projects.update', 'projects.delete'],
            ],
            'documents' => [
                'label' => 'Documents (CDE)',
                'description' => 'Document control, upload, review, approval',
                'permissions' => ['documents.view', 'documents.create', 'documents.update', 'documents.delete', 'documents.approve'],
            ],
            'tasks' => [
                'label' => 'Tasks & Scheduling',
                'description' => 'Task management, planning, Gantt scheduling',
                'permissions' => ['tasks.view', 'tasks.create', 'tasks.update', 'tasks.delete', 'tasks.assign'],
            ],
            'work_orders' => [
                'label' => 'Work Orders',
                'description' => 'Work order lifecycle management',
                'permissions' => ['work_orders.view', 'work_orders.create', 'work_orders.update', 'work_orders.delete'],
            ],
            'financials' => [
                'label' => 'Financials',
                'description' => 'Invoices, payments, revenue tracking',
                'permissions' => ['financials.view', 'financials.create', 'financials.update', 'financials.delete', 'financials.approve'],
            ],
            'boq' => [
                'label' => 'BOQ & Cost Management',
                'description' => 'Bill of quantities, cost tracking, variance',
                'permissions' => ['boq.view', 'boq.create', 'boq.update', 'boq.delete'],
            ],
            'inventory' => [
                'label' => 'Inventory & Materials',
                'description' => 'Stock management, requisitions, purchase orders',
                'permissions' => ['inventory.view', 'inventory.create', 'inventory.update', 'inventory.delete', 'inventory.approve'],
            ],
            'field_logs' => [
                'label' => 'Field Management',
                'description' => 'Daily site logs, site diary, field reporting',
                'permissions' => ['field_logs.view', 'field_logs.create', 'field_logs.update', 'field_logs.delete', 'field_logs.approve'],
            ],
            'equipment' => [
                'label' => 'Plant & Equipment',
                'description' => 'Equipment allocation, fuel logs, maintenance',
                'permissions' => ['equipment.view', 'equipment.create', 'equipment.update', 'equipment.delete'],
            ],
            'subcontractors' => [
                'label' => 'Subcontractors',
                'description' => 'Subcontractor management, work packages',
                'permissions' => ['subcontractors.view', 'subcontractors.create', 'subcontractors.update', 'subcontractors.delete'],
            ],
            'tenders' => [
                'label' => 'Tenders & Bidding',
                'description' => 'Tender pipeline, bid tracking',
                'permissions' => ['tenders.view', 'tenders.create', 'tenders.update', 'tenders.delete'],
            ],
            'crew' => [
                'label' => 'Crew & Attendance',
                'description' => 'Worker attendance, certifications',
                'permissions' => ['crew.view', 'crew.create', 'crew.update', 'crew.delete'],
            ],
            'safety' => [
                'label' => 'Safety (SHEQ)',
                'description' => 'Safety incidents, inspections, compliance',
                'permissions' => ['safety.view', 'safety.create', 'safety.update', 'safety.delete'],
            ],
            'assets' => [
                'label' => 'Assets',
                'description' => 'Company asset tracking and maintenance',
                'permissions' => ['assets.view', 'assets.create', 'assets.update', 'assets.delete'],
            ],
            'reports' => [
                'label' => 'Reports & Analytics',
                'description' => 'Report generation and export',
                'permissions' => ['reports.view', 'reports.export'],
            ],
            'settings' => [
                'label' => 'Company Settings',
                'description' => 'Company branding, roles, users, options',
                'permissions' => ['settings.view', 'settings.manage_roles', 'settings.manage_users', 'settings.manage_branding'],
            ],
        ];
    }

    /**
     * Get flat list of all permission strings.
     */
    public static function allPermissions(): array
    {
        $perms = [];
        foreach (static::modules() as $module) {
            $perms = array_merge($perms, $module['permissions']);
        }
        return $perms;
    }

    /**
     * Get permissions for a specific module.
     */
    public static function forModule(string $code): array
    {
        return static::modules()[$code]['permissions'] ?? [];
    }

    /**
     * Predefined role templates used by the seeder and role creation UI.
     */
    public static function roleTemplates(): array
    {
        return [
            'project_manager' => [
                'label' => 'Project Manager',
                'permissions' => array_merge(
                    self::forModule('projects'),
                    self::forModule('documents'),
                    self::forModule('tasks'),
                    self::forModule('work_orders'),
                    self::forModule('financials'),
                    self::forModule('boq'),
                    self::forModule('field_logs'),
                    self::forModule('equipment'),
                    self::forModule('subcontractors'),
                    self::forModule('crew'),
                    self::forModule('safety'),
                    self::forModule('reports'),
                ),
            ],
            'quantity_surveyor' => [
                'label' => 'Quantity Surveyor',
                'permissions' => array_merge(
                    ['projects.view'],
                    self::forModule('boq'),
                    self::forModule('financials'),
                    ['tenders.view', 'tenders.create', 'tenders.update'],
                    ['subcontractors.view'],
                    ['reports.view', 'reports.export'],
                ),
            ],
            'site_engineer' => [
                'label' => 'Site Engineer',
                'permissions' => array_merge(
                    ['projects.view'],
                    self::forModule('documents'),
                    self::forModule('tasks'),
                    self::forModule('field_logs'),
                    self::forModule('equipment'),
                    ['crew.view', 'crew.create', 'crew.update'],
                    self::forModule('safety'),
                    ['reports.view'],
                ),
            ],
            'foreman' => [
                'label' => 'Foreman',
                'permissions' => [
                    'projects.view',
                    'tasks.view',
                    'tasks.update',
                    'field_logs.view',
                    'field_logs.create',
                    'field_logs.update',
                    'equipment.view',
                    'equipment.create',
                    'crew.view',
                    'crew.create',
                    'crew.update',
                    'safety.view',
                    'safety.create',
                ],
            ],
            'accountant' => [
                'label' => 'Accountant',
                'permissions' => array_merge(
                    ['projects.view'],
                    self::forModule('financials'),
                    ['boq.view'],
                    ['subcontractors.view'],
                    ['reports.view', 'reports.export'],
                ),
            ],
            'viewer' => [
                'label' => 'Viewer (Read-Only)',
                'permissions' => [
                    'projects.view',
                    'documents.view',
                    'tasks.view',
                    'work_orders.view',
                    'financials.view',
                    'boq.view',
                    'field_logs.view',
                    'equipment.view',
                    'subcontractors.view',
                    'tenders.view',
                    'crew.view',
                    'safety.view',
                    'assets.view',
                    'reports.view',
                ],
            ],
        ];
    }
}
