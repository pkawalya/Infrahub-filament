<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    public function create()
    {
        return view('schedule-call');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'company' => 'nullable|string|max:255',
            'team_size' => 'nullable|string|max:50',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|string',
            'message' => 'nullable|string|max:2000',
        ]);

        // Send email to info@infrahub.click
        try {
            Mail::raw($this->buildEmailBody($validated), function ($message) use ($validated) {
                $message->to('info@infrahub.click')
                    ->cc('appcellon@gmail.com')
                    ->replyTo($validated['email'], $validated['name'])
                    ->subject('📞 New Call Request from ' . $validated['name']);
            });

            Log::info('Schedule call request submitted', [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send schedule-call email: ' . $e->getMessage());
        }

        return back()->with('success', 'Your call has been scheduled! We\'ll send a confirmation to your email shortly.');
    }

    private function buildEmailBody(array $data): string
    {
        $lines = [
            '📞 NEW CALL SCHEDULING REQUEST',
            str_repeat('─', 40),
            '',
            '👤 Name: ' . $data['name'],
            '📧 Email: ' . $data['email'],
        ];

        if (!empty($data['phone'])) {
            $lines[] = '📱 Phone: ' . $data['phone'];
        }
        if (!empty($data['company'])) {
            $lines[] = '🏢 Company: ' . $data['company'];
        }
        if (!empty($data['team_size'])) {
            $lines[] = '👥 Team Size: ' . $data['team_size'];
        }

        $lines[] = '';
        $lines[] = '📅 Preferred Date: ' . $data['preferred_date'];
        $lines[] = '🕐 Preferred Time: ' . $data['preferred_time'];

        if (!empty($data['message'])) {
            $lines[] = '';
            $lines[] = '💬 Message:';
            $lines[] = $data['message'];
        }

        $lines[] = '';
        $lines[] = str_repeat('─', 40);
        $lines[] = 'Sent from InfraHub Schedule-a-Call form';
        $lines[] = 'Reply directly to this email to respond to ' . $data['name'];

        return implode("\n", $lines);
    }
}
