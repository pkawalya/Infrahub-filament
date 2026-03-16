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

        try {
            Mail::html($this->buildHtmlEmail($validated), function ($message) use ($validated) {
                $message->to('info@infrahub.click')
                    ->cc('appcellon@gmail.com')
                    ->replyTo($validated['email'], $validated['name'])
                    ->subject('New Call Request — ' . $validated['name']);
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

    private function buildHtmlEmail(array $data): string
    {
        $name = e($data['name']);
        $email = e($data['email']);
        $phone = e($data['phone'] ?? '');
        $company = e($data['company'] ?? '');
        $teamSize = e($data['team_size'] ?? '');
        $date = e(\Carbon\Carbon::parse($data['preferred_date'])->format('l, d F Y'));
        $time = e($data['preferred_time']);
        $message = e($data['message'] ?? '');

        $detailRows = '';

        // Contact details
        $detailRows .= $this->emailRow('Name', $name);
        $detailRows .= $this->emailRow('Email', "<a href=\"mailto:{$email}\" style=\"color:#e8a229;text-decoration:none;\">{$email}</a>");

        if ($phone) {
            $detailRows .= $this->emailRow('Phone', "<a href=\"tel:{$phone}\" style=\"color:#e8a229;text-decoration:none;\">{$phone}</a>");
        }
        if ($company) {
            $detailRows .= $this->emailRow('Company', $company);
        }
        if ($teamSize) {
            $detailRows .= $this->emailRow('Team Size', $teamSize . ' people');
        }

        // Schedule details
        $scheduleRows = '';
        $scheduleRows .= $this->emailRow('Date', $date);
        $scheduleRows .= $this->emailRow('Time', $time);

        // Message section
        $messageHtml = '';
        if ($message) {
            $messageHtml = <<<HTML
            <tr>
                <td style="padding:24px 32px 0;">
                    <p style="margin:0 0 8px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:#64748b;">Message</p>
                    <div style="background:#f1f5f9;border-radius:10px;padding:16px 20px;font-size:14px;line-height:1.7;color:#334155;">
                        {$message}
                    </div>
                </td>
            </tr>
            HTML;
        }

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
        <body style="margin:0;padding:0;background:#f1f5f9;font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
            <table role="presentation" width="100%" style="background:#f1f5f9;padding:40px 20px;">
                <tr>
                    <td align="center">
                        <table role="presentation" width="560" style="max-width:560px;width:100%;">

                            <!-- Logo Header -->
                            <tr>
                                <td align="center" style="padding-bottom:24px;">
                                    <img src="https://test.infrahub.click/logo/infrahub-logo-new.png" alt="InfraHub" height="44" style="height:44px;border-radius:12px;">
                                </td>
                            </tr>

                            <!-- Main Card -->
                            <tr>
                                <td style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.06);">
                                    <table role="presentation" width="100%">

                                        <!-- Accent Bar -->
                                        <tr>
                                            <td style="height:4px;background:linear-gradient(90deg,#e8a229,#f5c563,#e8a229);"></td>
                                        </tr>

                                        <!-- Title -->
                                        <tr>
                                            <td style="padding:32px 32px 8px;">
                                                <h1 style="margin:0;font-size:22px;font-weight:800;color:#0f172a;letter-spacing:-0.5px;">
                                                    New Call Request
                                                </h1>
                                                <p style="margin:6px 0 0;font-size:14px;color:#64748b;">
                                                    Someone wants to schedule a demo call with the team.
                                                </p>
                                            </td>
                                        </tr>

                                        <!-- Contact Info -->
                                        <tr>
                                            <td style="padding:24px 32px 0;">
                                                <p style="margin:0 0 12px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:#64748b;">Contact Information</p>
                                                <table role="presentation" width="100%" style="border-collapse:collapse;">
                                                    {$detailRows}
                                                </table>
                                            </td>
                                        </tr>

                                        <!-- Schedule Info -->
                                        <tr>
                                            <td style="padding:24px 32px 0;">
                                                <p style="margin:0 0 12px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:#64748b;">Preferred Schedule</p>
                                                <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:16px 20px;">
                                                    <table role="presentation" width="100%" style="border-collapse:collapse;">
                                                        {$scheduleRows}
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Message -->
                                        {$messageHtml}

                                        <!-- Reply CTA -->
                                        <tr>
                                            <td style="padding:28px 32px;">
                                                <table role="presentation" width="100%">
                                                    <tr>
                                                        <td align="center">
                                                            <a href="mailto:{$email}" style="display:inline-block;padding:14px 32px;background:linear-gradient(135deg,#e8a229,#d4911e);color:#152d4a;font-size:14px;font-weight:700;text-decoration:none;border-radius:12px;">
                                                                Reply to {$name}
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>

                                    </table>
                                </td>
                            </tr>

                            <!-- Footer -->
                            <tr>
                                <td style="padding:24px 0;text-align:center;">
                                    <p style="margin:0;font-size:12px;color:#94a3b8;">
                                        Sent from the <a href="https://test.infrahub.click/schedule-call" style="color:#e8a229;text-decoration:none;">Schedule a Call</a> form on InfraHub
                                    </p>
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        HTML;
    }

    private function emailRow(string $label, string $value): string
    {
        return <<<HTML
        <tr>
            <td style="padding:6px 0;font-size:13px;color:#94a3b8;font-weight:500;width:100px;vertical-align:top;">{$label}</td>
            <td style="padding:6px 0;font-size:14px;color:#0f172a;font-weight:600;">{$value}</td>
        </tr>
        HTML;
    }
}
