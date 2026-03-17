<?php

namespace App\Http\Controllers;

use App\Models\ProjectInvitation;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectInvitationController extends Controller
{
    /**
     * Show the project invitation acceptance page.
     */
    public function accept(string $token)
    {
        $invitation = ProjectInvitation::findByToken($token);

        if (!$invitation) {
            return view('invitations.invalid');
        }

        return view('invitations.project-accept', [
            'invitation' => $invitation,
            'project' => $invitation->project,
            'company' => $invitation->company,
        ]);
    }

    /**
     * Process the project invitation acceptance.
     */
    public function confirm(Request $request, string $token)
    {
        $invitation = ProjectInvitation::findByToken($token);

        if (!$invitation) {
            return redirect('/app/login')
                ->with('error', 'This invitation link is invalid or has expired.');
        }

        // Check if the user is authenticated
        $user = auth()->user();

        if (!$user) {
            // Check if a user exists with this email
            $existingUser = User::where('email', $invitation->email)->first();

            if ($existingUser) {
                // Store the token in session and redirect to login
                session(['project_invite_token' => $token]);

                return redirect('/app/login')
                    ->with('status', 'Please log in to accept the project invitation.');
            }

            // No user exists — redirect to registration or user invitation flow
            return redirect('/app/login')
                ->with('error', 'No account found for this email. Please contact your administrator.');
        }

        // User is authenticated — accept the invitation
        if (strtolower($user->email) !== strtolower($invitation->email)) {
            return redirect('/app')
                ->with('error', 'This invitation was sent to a different email address.');
        }

        $invitation->accept($user);

        return redirect('/app')
            ->with('status', "You've been added to the project \"{$invitation->project->name}\"!");
    }
}
