<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['code', 'name', 'description', 'icon', 'is_active', 'is_core', 'sort_order'];
    protected $casts = ['is_active' => 'boolean', 'is_core' => 'boolean'];

    public static array $availableModules = [
        'core' => [
            'name' => 'Core FSM',
            'description' => 'Work orders, assets, clients, invoices, estimations',
            'icon' => 'heroicon-o-wrench-screwdriver',
            'is_core' => true,
        ],
        'cde' => [
            'name' => 'Common Data Environment',
            'description' => 'Document management, RFIs, submittals, transmittals',
            'icon' => 'heroicon-o-folder-open',
        ],
        'field_management' => [
            'name' => 'Field Management',
            'description' => 'GPS tracking, routes, site check-ins, daily logs',
            'icon' => 'heroicon-o-map-pin',
        ],
        'task_workflow' => [
            'name' => 'Task & Workflow',
            'description' => 'Task management, assignments, time tracking',
            'icon' => 'heroicon-o-clipboard-document-check',
        ],
        'inventory' => [
            'name' => 'Inventory & Procurement',
            'description' => 'Stock management, warehouses, purchase orders',
            'icon' => 'heroicon-o-cube',
        ],
        'cost_contracts' => [
            'name' => 'Cost & Contracts',
            'description' => 'Budget tracking, contracts, change orders, claims',
            'icon' => 'heroicon-o-currency-dollar',
        ],
        'planning_progress' => [
            'name' => 'Planning & Progress',
            'description' => 'Schedules, milestones, timesheets, progress tracking',
            'icon' => 'heroicon-o-calendar-days',
        ],
        'boq_management' => [
            'name' => 'BOQ Management',
            'description' => 'Bill of quantities, material usage, cost variance',
            'icon' => 'heroicon-o-calculator',
        ],
        'sheq' => [
            'name' => 'SHEQ Management',
            'description' => 'Safety incidents, inspections, permits, toolbox talks',
            'icon' => 'heroicon-o-shield-check',
        ],
        'reporting' => [
            'name' => 'Reporting & Dashboards',
            'description' => 'Custom reports and analytics dashboards',
            'icon' => 'heroicon-o-chart-bar',
        ],
    ];

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
