@extends('emails.layouts.brand')

@section('title', ($subject ?? 'Notification') . ' — InfraHub')

@section('content')
    {{-- Optional Hero icon --}}
    @isset($icon)
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td align="center" style="padding-bottom:20px;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td
                                style="background:linear-gradient(135deg, #e8a229, #d4911e); width:56px; height:56px; border-radius:50%; text-align:center; line-height:56px; font-size:28px; color:white;">
                                {{ $icon }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    @endisset

    {{-- Heading --}}
    @isset($heading)
        <h1 style="margin:0 0 8px; font-size:24px; font-weight:800; color:#0f172a; text-align:center; letter-spacing:-0.3px;">
            {{ $heading }}
        </h1>
    @endisset

    @isset($subheading)
        <p style="margin:0 0 28px; font-size:14px; color:#64748b; text-align:center; line-height:1.6;">
            {{ $subheading }}
        </p>
    @endisset

    {{-- Greeting --}}
    @isset($greeting)
        <p style="margin:0 0 16px; font-size:15px; color:#334155; line-height:1.7;">
            {!! $greeting !!}
        </p>
    @endisset

    {{-- Body --}}
    @isset($body)
        <div style="margin:0 0 24px; font-size:14px; color:#475569; line-height:1.7;">
            {!! $body !!}
        </div>
    @endisset

    {{-- Info Box --}}
    @isset($infoBox)
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
            style="background:#fffbeb; border:1px solid #fde68a; border-radius:12px; overflow:hidden; margin-bottom:24px;">
            <tr>
                <td
                    style="height:4px; background:linear-gradient(90deg, #e8a229, #f5c563, #e8a229); line-height:4px; font-size:0;">
                    &nbsp;</td>
            </tr>
            <tr>
                <td style="padding:20px 24px;">
                    {!! $infoBox !!}
                </td>
            </tr>
        </table>
    @endisset

    {{-- CTA Button --}}
    @isset($actionUrl)
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td align="center">
                    <a href="{{ $actionUrl }}"
                        style="display:inline-block; padding:14px 36px; background:linear-gradient(135deg, #e8a229, #d4911e); color:#152d4a; font-size:15px; font-weight:700; border-radius:10px; text-decoration:none; box-shadow:0 4px 14px rgba(232,162,41,0.3);"
                        target="_blank">
                        {{ $actionLabel ?? 'Open InfraHub' }}
                    </a>
                </td>
            </tr>
        </table>
    @endisset

    {{-- Footer text --}}
    @isset($footerText)
        <p style="margin:24px 0 0; font-size:13px; color:#94a3b8; text-align:center; line-height:1.6;">
            {{ $footerText }}
        </p>
    @endisset
@endsection