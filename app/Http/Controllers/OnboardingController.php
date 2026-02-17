<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class OnboardingController extends Controller
{
    /**
     * Show the onboarding / get-started page.
     */
    public function show()
    {
        $plans = Subscription::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('monthly_price')
            ->get();

        return view('onboarding', compact('plans'));
    }

    /**
     * Process the onboarding form.
     * Creates a pending company + admin user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Step 1 — Plan
            'subscription_id' => 'required|exists:subscriptions,id',
            // Step 2 — Company
            'company_name' => 'required|string|max:255',
            'industry' => 'required|string|max:100',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:50',
            'company_country' => 'required|string|max:10',
            'team_size' => 'required|string|max:20',
            // Step 3 — Account
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'job_title' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $plan = Subscription::findOrFail($validated['subscription_id']);

        // Create company in PENDING state (is_active = false) → awaiting admin approval
        $company = Company::create([
            'name' => $validated['company_name'],
            'slug' => Str::slug($validated['company_name']) . '-' . Str::random(4),
            'email' => $validated['company_email'] ?? null,
            'phone' => $validated['company_phone'] ?? null,
            'country' => $validated['company_country'],
            'subscription_id' => $plan->id,
            'billing_cycle' => 'monthly',
            'is_active' => false, // Pending approval
            'is_trial' => true,
            'trial_ends_at' => now()->addDays(14),
            'max_users' => $plan->max_users,
            'max_projects' => $plan->max_projects,
            'max_storage_gb' => $plan->max_storage_gb,
            'currency' => 'UGX',
            'currency_symbol' => 'UGX',
            'currency_position' => 'before',
            'timezone' => 'Africa/Kampala',
            'settings' => [
                'industry' => $validated['industry'],
                'team_size' => $validated['team_size'],
                'onboarding_status' => 'pending_approval',
            ],
        ]);

        // Sync modules from the chosen plan
        $company->syncModulesFromSubscription();

        // Create admin user (active but company not yet approved)
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'company_id' => $company->id,
            'user_type' => 'company_admin',
            'job_title' => $validated['job_title'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'is_active' => true,
        ]);

        // Assign role
        if ($role = \Spatie\Permission\Models\Role::where('name', 'company_admin')->first()) {
            $user->assignRole($role);
        }

        return redirect()->route('onboarding.success');
    }

    /**
     * Success / pending-approval page.
     */
    public function success()
    {
        return view('onboarding-success');
    }
}
