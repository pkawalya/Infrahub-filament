<?php

namespace App\Http\Controllers;

use App\Models\ProjectInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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

        // Check if a user already exists with this email
        $existingUser = User::where('email', $invitation->email)->first();

        return view('invitations.project-accept', [
            'invitation' => $invitation,
            'project' => $invitation->project,
            'company' => $invitation->company,
            'existingUser' => $existingUser,
            'needsRegistration' => !$existingUser,
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
                // Store the token in session and redirect to the appropriate login
                session(['project_invite_token' => $token]);

                $loginUrl = $existingUser->user_type === 'client' ? '/client/login' : '/app/login';

                return redirect($loginUrl)
                    ->with('status', 'Please log in to accept the project invitation.');
            }

            // No user exists — they need to register via the register route
            return redirect()->route('project-invitation.accept', $token)
                ->with('error', 'Please create your account below to accept the invitation.');
        }

        // User is authenticated — accept the invitation
        if (strtolower($user->email) !== strtolower($invitation->email)) {
            return redirect('/app')
                ->with('error', 'This invitation was sent to a different email address.');
        }

        $invitation->accept($user);

        $redirectUrl = $user->user_type === 'client' ? '/client' : '/app';

        return redirect($redirectUrl)
            ->with('status', "You've been added to the project \"{$invitation->project->name}\"!");
    }

    /**
     * Register a new user from a project invitation and accept the invitation.
     */
    public function register(Request $request, string $token)
    {
        $invitation = ProjectInvitation::findByToken($token);

        if (!$invitation) {
            return redirect('/app/login')
                ->with('error', 'This invitation link is invalid or has expired.');
        }

        // Prevent registration if user already exists
        if (User::where('email', $invitation->email)->exists()) {
            return redirect()->route('project-invitation.accept', $token)
                ->with('error', 'An account already exists for this email. Please log in instead.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        // Determine user type based on invitation role
        $userType = $invitation->role === 'client' ? 'client' : 'member';

        // Create the user account
        $user = User::create([
            'name' => $request->name,
            'email' => $invitation->email,
            'password' => Hash::make($request->password),
            'company_id' => $invitation->company_id,
            'user_type' => $userType,
            'is_active' => true,
            'email_verified_at' => now(), // Verified via invitation link
        ]);

        // Accept the invitation (adds user to project)
        $invitation->accept($user);

        // Log the user in
        Auth::login($user);

        $redirectUrl = $userType === 'client' ? '/client' : '/app';

        return redirect($redirectUrl)
            ->with('status', "Welcome! Your account has been created and you've been added to \"{$invitation->project->name}\".");
    }
}
