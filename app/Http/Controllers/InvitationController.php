<?php

namespace App\Http\Controllers;

use App\Models\UserInvitation;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    /**
     * Show the invitation acceptance page.
     */
    public function accept(string $token)
    {
        $invitation = UserInvitation::findByToken($token);

        if (!$invitation) {
            return view('invitations.invalid');
        }

        return view('invitations.accept', [
            'invitation' => $invitation,
            'user' => $invitation->user,
            'company' => $invitation->company,
        ]);
    }

    /**
     * Process the invitation acceptance.
     */
    public function confirm(Request $request, string $token)
    {
        $invitation = UserInvitation::findByToken($token);

        if (!$invitation) {
            return redirect()->route('login')
                ->with('error', 'This invitation link is invalid or has expired.');
        }

        // Accept the invitation
        $invitation->accept();

        // Determine where to redirect based on user type
        $loginUrl = match ($invitation->user->user_type) {
            'super_admin' => '/admin/login',
            'client' => '/client/login',
            default => '/app/login',
        };

        return redirect($loginUrl)
            ->with('status', 'Your invitation has been accepted! Please log in with your credentials.');
    }
}
