<?php

namespace App\Support;

use App\Models\CdeProject;

/**
 * Centralized storage path generator for multi-tenant file organization.
 *
 * All files are organized as:
 *   companies/{company_id}/projects/{project_id}/{category}/{filename}
 *
 * Company-level files (not tied to a project):
 *   companies/{company_id}/assets/{filename}
 */
class StoragePath
{
    /**
     * Get the base path for a company.
     */
    public static function company(?int $companyId = null): string
    {
        $companyId = $companyId ?? auth()->user()?->company_id;

        return "companies/{$companyId}";
    }

    /**
     * Get the base path for a project within its company.
     */
    public static function project(CdeProject|int $project, ?int $companyId = null): string
    {
        if ($project instanceof CdeProject) {
            $companyId = $companyId ?? $project->company_id;
            $projectId = $project->id;
        } else {
            $projectId = $project;
        }

        return static::company($companyId) . "/projects/{$projectId}";
    }

    /**
     * Get path for project documents (CDE uploads).
     */
    public static function documents(CdeProject|int $project, ?int $companyId = null): string
    {
        return static::project($project, $companyId) . '/documents';
    }

    /**
     * Get path for project images.
     */
    public static function images(CdeProject|int $project, ?int $companyId = null): string
    {
        return static::project($project, $companyId) . '/images';
    }

    /**
     * Get path for project invoices.
     */
    public static function invoices(CdeProject|int $project, ?int $companyId = null): string
    {
        return static::project($project, $companyId) . '/invoices';
    }

    /**
     * Get path for project receipts.
     */
    public static function receipts(CdeProject|int $project, ?int $companyId = null): string
    {
        return static::project($project, $companyId) . '/receipts';
    }

    /**
     * Get path for RFI attachments.
     */
    public static function rfi(CdeProject|int $project, ?int $companyId = null): string
    {
        return static::project($project, $companyId) . '/rfi';
    }

    /**
     * Get path for BOQ files.
     */
    public static function boq(CdeProject|int $project, ?int $companyId = null): string
    {
        return static::project($project, $companyId) . '/boq';
    }

    /**
     * Get path for project reports.
     */
    public static function reports(CdeProject|int $project, ?int $companyId = null): string
    {
        return static::project($project, $companyId) . '/reports';
    }

    /**
     * Get path for company-level assets (logos, branding, etc.).
     */
    public static function companyAssets(?int $companyId = null): string
    {
        return static::company($companyId) . '/assets';
    }

    /**
     * Get a generic project category path.
     */
    public static function projectCategory(CdeProject|int $project, string $category, ?int $companyId = null): string
    {
        return static::project($project, $companyId) . '/' . $category;
    }
}
