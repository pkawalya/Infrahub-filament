@extends('emails.layouts.brand')

@section('title', 'Project Assignment — InfraHub')

@section('content')
    <!-- Heading -->
    <h1 style="margin:0 0 6px; font-size:24px; font-weight:800; color:#0f172a; letter-spacing:-0.3px;">
        You've been added to a project
    </h1>
    <p style="margin:0 0 28px; font-size:14px; color:#64748b; line-height:1.6;">
        {{ $assignedBy->name }} has assigned you to the following project.
    </p>

    <!-- Project Info Card -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="background:#fffbeb; border:1px solid #fde68a; border-radius:12px; overflow:hidden; margin-bottom:28px;">
        <tr>
            <td
                style="height:4px; background:linear-gradient(90deg, #e8a229, #f5c563, #e8a229); line-height:4px; font-size:0;">
                &nbsp;</td>
        </tr>
        <tr>
            <td style="padding:24px 28px;">
                <!-- Project name -->
                <p style="margin:0 0 8px; font-size:20px; font-weight:700; color:#152d4a;">
                    {{ $project->name }}
                </p>

                @if($project->description)
                    <p style="margin:0 0 16px; font-size:13px; color:#64748b; line-height:1.6;">
                        {!! Str::limit(strip_tags($project->description), 200) !!}
                    </p>
                @endif

                <!-- Assigned By badge -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="background:rgba(232,162,41,0.12); border-radius:8px; padding:10px 16px;">
                            <span
                                style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#e8a229; display:block; margin-bottom:2px;">Added
                                by</span>
                            <span style="font-size:14px; font-weight:600; color:#152d4a;">{{ $assignedBy->name }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- CTA Button -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td align="center">
                <a href="{{ $projectUrl }}"
                    style="display:inline-block; padding:14px 36px; background:linear-gradient(135deg, #e8a229, #d4911e); color:#152d4a; font-size:15px; font-weight:700; border-radius:10px; text-decoration:none; box-shadow:0 4px 14px rgba(232,162,41,0.3);"
                    target="_blank">
                    🚀&nbsp; Open Project
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:24px 0 0; font-size:13px; color:#94a3b8; text-align:center; line-height:1.6;">
        Click the button above to access the project and start collaborating with your team.
    </p>
@endsection