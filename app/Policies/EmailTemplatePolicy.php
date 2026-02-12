<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\EmailTemplate;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmailTemplatePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_company::email::template');
    }

    public function view(AuthUser $authUser, EmailTemplate $emailTemplate): bool
    {
        return $authUser->can('view_company::email::template');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_company::email::template');
    }

    public function update(AuthUser $authUser, EmailTemplate $emailTemplate): bool
    {
        return $authUser->can('update_company::email::template');
    }

    public function delete(AuthUser $authUser, EmailTemplate $emailTemplate): bool
    {
        return $authUser->can('delete_company::email::template');
    }

    public function restore(AuthUser $authUser, EmailTemplate $emailTemplate): bool
    {
        return $authUser->can('restore_company::email::template');
    }

    public function forceDelete(AuthUser $authUser, EmailTemplate $emailTemplate): bool
    {
        return $authUser->can('force_delete_company::email::template');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_company::email::template');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_company::email::template');
    }

    public function replicate(AuthUser $authUser, EmailTemplate $emailTemplate): bool
    {
        return $authUser->can('replicate_company::email::template');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_company::email::template');
    }

}