@extends('emails.layouts.brand')

@section('title', $templateName ?? 'Notification')

{{-- Override the header with company branding if available --}}
@section('header_action')
    @if(!empty($companyWebsite))
        <a href="{{ $companyWebsite }}"
            style="display:inline-block; padding:8px 20px; font-size:13px; font-weight:600; color:#374151; border:1px solid #d1d5db; border-radius:8px; text-decoration:none; font-family:'Inter',sans-serif;">
            Visit Website
        </a>
    @else
        <a href="{{ config('app.url') }}/app"
            style="display:inline-block; padding:8px 20px; font-size:13px; font-weight:600; color:#374151; border:1px solid #d1d5db; border-radius:8px; text-decoration:none; font-family:'Inter',sans-serif;">
            Log in
        </a>
    @endif
@endsection

@section('content')
    {{-- Company logo at top of content if available --}}
    @if(!empty($companyLogoUrl))
        <div style="text-align:center; margin-bottom:24px;">
            <img src="{{ $companyLogoUrl }}" alt="{{ $companyName }}" style="max-height:48px; max-width:200px;">
        </div>
    @endif

    {!! $body !!}
@endsection