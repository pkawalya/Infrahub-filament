<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function create()
    {
        return view('get-started');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'company' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'company_size' => 'nullable|string|max:50',
            'preferred_date' => 'required|date|after:today',
            'preferred_time' => 'required|string',
            'timezone' => 'required|string|max:100',
            'message' => 'nullable|string|max:1000',
        ]);

        Appointment::create($validated);

        return back()->with('success', 'Your call has been scheduled! We\'ll send a confirmation to your email shortly.');
    }
}
