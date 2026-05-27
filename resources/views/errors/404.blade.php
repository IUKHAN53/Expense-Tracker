@extends('errors.layout', ['title' => 'Not found · Kharcha'])

@section('status-label', '404 · Not found')

@section('display')404<span class="accent">.</span>@endsection

@section('lede')
    This page is not in the ledger. Either it never existed, or it has been retired.
@endsection

@section('cta')
    <a class="btn btn-primary" href="/">
        Back to home
        <span class="arrow" aria-hidden="true">→</span>
    </a>
    <a class="btn btn-ghost" href="/admin/login">Sign in</a>
@endsection
