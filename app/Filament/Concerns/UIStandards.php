<?php

/**
 * ╔══════════════════════════════════════════════════════════════════╗
 * ║              INFRAHUB UI STANDARDIZATION GUIDE                  ║
 * ║              ──────────────────────────────────                  ║
 * ║  Canonical patterns for Filament resources and pages.           ║
 * ║  Import this class and use the static helpers to guarantee      ║
 * ║  visual consistency across all modules.                         ║
 * ╚══════════════════════════════════════════════════════════════════╝
 */

namespace App\Filament\Concerns;

/**
 * Centralized UI constants and helpers for Filament resources.
 *
 * Usage in any resource/page:
 *   use App\Filament\Concerns\UIStandards;
 *   UIStandards::statusColor('approved')   → 'success'
 *   UIStandards::priorityColor('critical') → 'danger'
 */
class UIStandards
{
    // ═══════════════════════════════════════════════════════════
    //  1. STATUS → COLOR MAPPING (use everywhere)
    // ═══════════════════════════════════════════════════════════
    //
    //  PROBLEM FOUND: Status colors are inconsistent across resources.
    //  WorkOrders use 'info' for 'in_progress', Tasks use 'warning'.
    //  Some resources use 'primary', others use 'info' for the same concept.
    //
    //  STANDARD: Use this single mapping everywhere.

    public static function statusColor(string $status): string
    {
        return match ($status) {
            // ── Lifecycle start ──
            'draft', 'new' => 'gray',

            // ── Awaiting action ──
            'pending', 'submitted', 'under_review',
            'awaiting_approval', 'on_hold' => 'warning',

            // ── In progress ──
            'active', 'in_progress', 'open',
            'assigned', 'in_review' => 'info',

            // ── Positive outcomes ──
            'approved', 'completed', 'resolved',
            'done', 'closed', 'paid' => 'success',

            // ── Negative outcomes ──
            'rejected', 'cancelled', 'failed',
            'overdue', 'expired', 'void' => 'danger',

            // ── Fallback ──
            default => 'gray',
        };
    }

    // ═══════════════════════════════════════════════════════════
    //  2. PRIORITY → COLOR MAPPING
    // ═══════════════════════════════════════════════════════════

    public static function priorityColor(string $priority): string
    {
        return match ($priority) {
            'low' => 'gray',
            'medium' => 'info',
            'high' => 'warning',
            'critical', 'urgent' => 'danger',
            default => 'gray',
        };
    }

    // ═══════════════════════════════════════════════════════════
    //  3. USER TYPE → COLOR MAPPING
    // ═══════════════════════════════════════════════════════════

    public static function userTypeColor(string $type): string
    {
        return match ($type) {
            'super_admin' => 'danger',
            'company_admin' => 'warning',
            'manager' => 'info',
            'member' => 'success',
            'technician' => 'primary',
            'client' => 'gray',
            default => 'gray',
        };
    }

    // ═══════════════════════════════════════════════════════════
    //  4. STANDARD NAVIGATION GROUPS & ICONS
    // ═══════════════════════════════════════════════════════════
    //
    //  PROBLEM FOUND:
    //  - 2 resources share 'heroicon-o-clipboard-document-check'
    //  - 2 resources share 'heroicon-o-user-group'
    //  - 7 resources have no explicit navigationLabel
    //
    //  STANDARD GROUPS (App Panel):
    //  ┌─────────────────────┬──────────────────────────────────┐
    //  │ Group               │ Resources                         │
    //  ├─────────────────────┼──────────────────────────────────┤
    //  │ Projects            │ Projects, Documents, Drawings,   │
    //  │                     │ Transmittals, BOQ                │
    //  │ Task & Workflow     │ Tasks, Snags, Work Orders        │
    //  │ SHEQ                │ Safety, Inspections, Site Diary  │
    //  │ Work Orders         │ Work Orders, Change Orders       │
    //  │ Company             │ Clients, Invoices, Tenders,      │
    //  │                     │ Subcontractors, Assets           │
    //  │ Settings            │ Users, Roles, Email Templates    │
    //  └─────────────────────┴──────────────────────────────────┘
    //
    //  RECOMMENDED UNIQUE ICONS:
    //  Projects       → heroicon-o-building-office-2
    //  Documents      → heroicon-o-document-text
    //  Tasks          → heroicon-o-clipboard-document-list
    //  Work Orders    → heroicon-o-wrench-screwdriver
    //  Safety         → heroicon-o-shield-exclamation
    //  Site Diaries   → heroicon-o-book-open
    //  Invoices       → heroicon-o-banknotes
    //  Clients        → heroicon-o-user-group
    //  Assets         → heroicon-o-truck
    //  Drawings       → heroicon-o-map
    //  Crew           → heroicon-o-users
    //  Equipment      → heroicon-o-cog-6-tooth
    //  Tenders        → heroicon-o-document-currency-dollar
    //  Users          → heroicon-o-user-circle
    //  Roles          → heroicon-o-shield-check
    //  Subcontractors → heroicon-o-building-storefront

    // ═══════════════════════════════════════════════════════════
    //  5. STANDARD DATE FORMATS
    // ═══════════════════════════════════════════════════════════
    //
    //  PROBLEM FOUND: Inconsistent date formats:
    //    - Some use ->date() (default format)
    //    - Some use ->date('M d, Y')
    //    - Some use ->date('D, M d Y')
    //    - Some use ->dateTime() (includes time)
    //
    //  STANDARD:
    //    Tables:     ->date('M d, Y')       for dates
    //                ->dateTime('M d, Y H:i') for timestamps
    //    Infolists:  ->dateTime()            (full datetime)

    public const DATE_FORMAT = 'M d, Y';
    public const DATETIME_FORMAT = 'M d, Y H:i';

    // ═══════════════════════════════════════════════════════════
    //  6. STANDARD TEXT TRUNCATION
    // ═══════════════════════════════════════════════════════════
    //
    //  PROBLEM FOUND: Inconsistent limits:
    //    - Titles: 35, 40 (pick one)
    //    - Project names: 20
    //    - User names: 15
    //    - Descriptions: 50
    //
    //  STANDARD:
    //    Title column:       ->limit(40)
    //    Project name:       ->limit(25)
    //    User name:          ->limit(20)
    //    Description/notes:  ->limit(50)

    public const LIMIT_TITLE = 40;
    public const LIMIT_PROJECT = 25;
    public const LIMIT_NAME = 20;
    public const LIMIT_DESCRIPTION = 50;

    // ═══════════════════════════════════════════════════════════
    //  7. STANDARD TABLE CONVENTIONS
    // ═══════════════════════════════════════════════════════════
    //
    //  Every table MUST have:
    //  ✅ ->defaultSort('created_at', 'desc')  (or a primary identifier)
    //  ✅ Primary column with ->searchable()->sortable()->weight('bold')
    //  ✅ Status column with ->badge()->color(fn($state) => UIStandards::statusColor($state))
    //  ✅ Created date column with ->date(UIStandards::DATE_FORMAT)->sortable()
    //  ✅ At least one filter (status or date range)
    //  ✅ Actions: View, Edit (conditionally Delete)
    //
    //  MISSING VIEW PAGES (12 resources):
    //  AssetResource, ClientResource, CrewAttendanceResource,
    //  DailySiteDiaryResource, EquipmentAllocations, EquipmentFuelLogs,
    //  InvoiceResource, SafetyIncidentResource, SubcontractorResource,
    //  TaskResource, TenderResource, WorkOrderResource
    //
    //  → Each of these should have a ViewXxx page for better UX.

    // ═══════════════════════════════════════════════════════════
    //  8. STANDARD FORM SECTION NAMES
    // ═══════════════════════════════════════════════════════════
    //
    //  PROBLEM FOUND: Inconsistent section naming:
    //    'Details' vs 'Asset Details' vs 'Description' vs 'Description & Notes'
    //    'Additional' vs 'Additional Information'
    //
    //  STANDARD SECTION NAMING:
    //    1st section: '{ModelLabel} Details'    (e.g., 'Task Details')
    //    2nd section: 'Assignment & Dates'     (assignee, due date, priority)
    //    3rd section: 'Attachments & Notes'    (files, comments)
    //    4th section: 'Advanced Settings'      (visibility, permissions)

    // ═══════════════════════════════════════════════════════════
    //  9. STANDARD EMPTY STATE PLACEHOLDERS
    // ═══════════════════════════════════════════════════════════
    //
    //  CURRENT: '—' (em dash) everywhere → consistent ✅
    //  Exceptions: 'No notes.' for long text fields → also acceptable ✅
    //
    //  STANDARD:
    //    Short fields: ->placeholder('—')
    //    Long text:    ->placeholder('No notes added.')
    //    Select:       ->placeholder('Select...')
    //    DateTime:     ->placeholder('Not set')

    public const PLACEHOLDER_SHORT = '—';
    public const PLACEHOLDER_TEXT = 'No notes added.';
    public const PLACEHOLDER_SELECT = 'Select...';
    public const PLACEHOLDER_DATE = 'Not set';

    // ═══════════════════════════════════════════════════════════
    //  10. STANDARD INFOLIST PATTERN
    // ═══════════════════════════════════════════════════════════
    //
    //  PROBLEM FOUND: 8 resources have NO infolist at all.
    //  This means clicking "View" shows nothing or a raw form.
    //
    //  MISSING INFOLISTS:
    //  CdeProjectResource, ChangeOrderResource, CrewAttendanceResource,
    //  DailySiteDiaryResource, DrawingResource, PaymentCertificateResource,
    //  SubcontractorResource, TenderResource
    //
    //  STANDARD INFOLIST STRUCTURE:
    //  Section 1: Key information (2 cols)
    //  Section 2: Status & dates (2 cols)
    //  Section 3: Description (full width)
    //  Section 4: Metadata (created_at, updated_at, created_by)

    // ═══════════════════════════════════════════════════════════
    //  11. MISC PATTERNS
    // ═══════════════════════════════════════════════════════════

    /**
     * Standard money formatting helper.
     * Usage: ->formatStateUsing(UIStandards::money())
     */
    public static function money(string $currency = 'UGX'): \Closure
    {
        return fn($state) => $state !== null
            ? $currency . ' ' . number_format((float) $state, 0)
            : '—';
    }

    /**
     * Standard percentage formatting.
     * Usage: ->formatStateUsing(UIStandards::percentage())
     */
    public static function percentage(): \Closure
    {
        return fn($state) => $state !== null
            ? number_format((float) $state, 1) . '%'
            : '—';
    }
}
