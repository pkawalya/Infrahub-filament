<?php

namespace App\Http\Controllers;

use App\Models\UserInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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
     * Process the invitation acceptance — user sets their own password.
     */
    public function confirm(Request $request, string $token)
    {
        $invitation = UserInvitation::findByToken($token);

        if (!$invitation) {
            return redirect('/app/login')
                ->with('error', 'This invitation link is invalid or has expired.');
        }

        // Validate the password
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        // Accept the invitation (marks email as verified)
        $invitation->accept();

        // Set the user's password and clear must_change_password
        $invitation->user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
            'password_changed_at' => now(),
        ]);

        // Determine where to redirect based on user type
        $loginUrl = match ($invitation->user->user_type) {
            'super_admin' => '/admin/login',
            'client' => '/client/login',
            default => '/app/login',
        };

        return redirect($loginUrl)
            ->with('status', 'Your password has been set! Please log in to get started.');
    }
}
