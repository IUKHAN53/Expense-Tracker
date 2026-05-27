@extends('errors.layout', ['title' => 'Not for you · Kharcha'])

@section('status-label', '403 · Reserved')

@section('display')Not<br>for you<span class="accent">.</span>@endsection

@section('lede')
    This corner of Kharcha is reserved. If you think you should be here, sign in with the right account.
@endsection

@section('cta')
    <a class="btn btn-primary" href="/">
        Back to home
        <span class="arrow" aria-hidden="true">→</span>
    </a>
    <a class="btn btn-ghost" href="/admin/login">Sign in</a>
@endsection
