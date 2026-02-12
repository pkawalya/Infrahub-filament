<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Invoice;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_invoice');
    }

    public function view(AuthUser $authUser, Invoice $invoice): bool
    {
        return $authUser->can('view_invoice');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_invoice');
    }

    public function update(AuthUser $authUser, Invoice $invoice): bool
    {
        return $authUser->can('update_invoice');
    }

    public function delete(AuthUser $authUser, Invoice $invoice): bool
    {
        return $authUser->can('delete_invoice');
    }

    public function restore(AuthUser $authUser, Invoice $invoice): bool
    {
        return $authUser->can('restore_invoice');
    }

    public function forceDelete(AuthUser $authUser, Invoice $invoice): bool
    {
        return $authUser->can('force_delete_invoice');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_invoice');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_invoice');
    }

    public function replicate(AuthUser $authUser, Invoice $invoice): bool
    {
        return $authUser->can('replicate_invoice');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_invoice');
    }

}