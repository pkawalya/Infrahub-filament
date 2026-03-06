@extends('emails.layouts.brand')

@section('title', $templateName ?? 'Notification')

@section('content')
    {!! $body !!}
@endsection