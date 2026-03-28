<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PaymentCertificate;
use App\Policies\Concerns\EnforcesCompanyOwnership;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PaymentCertificatePolicy
{
    use HandlesAuthorization, EnforcesCompanyOwnership;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_payment::certificate');
    }

    public function view(AuthUser $authUser, PaymentCertificate $paymentCertificate): bool
    {
        return $this->ownedByCompany($authUser, $paymentCertificate) && $authUser->can('view_payment::certificate');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_payment::certificate');
    }

    public function update(AuthUser $authUser, PaymentCertificate $paymentCertificate): bool
    {
        return $this->ownedByCompany($authUser, $paymentCertificate) && $authUser->can('update_payment::certificate');
    }

    public function delete(AuthUser $authUser, PaymentCertificate $paymentCertificate): bool
    {
        return $this->ownedByCompany($authUser, $paymentCertificate) && $authUser->can('delete_payment::certificate');
    }

    public function restore(AuthUser $authUser, PaymentCertificate $paymentCertificate): bool
    {
        return $this->ownedByCompany($authUser, $paymentCertificate) && $authUser->can('restore_payment::certificate');
    }

    public function forceDelete(AuthUser $authUser, PaymentCertificate $paymentCertificate): bool
    {
        return $this->ownedByCompany($authUser, $paymentCertificate) && $authUser->can('force_delete_payment::certificate');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_payment::certificate');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_payment::certificate');
    }

    public function replicate(AuthUser $authUser, PaymentCertificate $paymentCertificate): bool
    {
        return $this->ownedByCompany($authUser, $paymentCertificate) && $authUser->can('replicate_payment::certificate');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_payment::certificate');
    }
}