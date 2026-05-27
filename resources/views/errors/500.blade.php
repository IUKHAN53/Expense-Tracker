@extends('errors.layout', ['title' => 'Server error · Kharcha'])

@section('status-label', '500 · Server error')

@section('display')500<span class="accent">,</span>@endsection

@section('lede')
    Something broke on our side. The error has been logged and we are looking into it. Try again in a moment.
@endsection

@section('cta')
    <a class="btn btn-primary" href="/">
        Back to home
        <span class="arrow" aria-hidden="true">→</span>
    </a>
    <a class="btn btn-ghost" href="mailto:hello@iukhan.tech?subject=Kharcha%20500%20error">Email support</a>
@endsection

@section('meta')
    <span>Logged · {{ now()->format('d M Y · H:i') }}</span>
    <span>If this keeps happening, write to <a href="mailto:hello@iukhan.tech">hello@iukhan.tech</a></span>
@endsection
