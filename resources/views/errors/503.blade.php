@extends('errors.layout', ['title' => 'Briefly down · Kharcha'])

@section('status-label', '503 · Maintenance')

@section('display')Be<br>right<br>back<span class="accent">.</span>@endsection

@section('lede')
    Kharcha is briefly down for a small update. <em>Back in a moment</em>, with the ledger exactly as you left it.
@endsection

@section('cta')
    <a class="btn btn-primary" href="/" onclick="event.preventDefault(); window.location.reload();">
        Try again
        <span class="arrow" aria-hidden="true">↺</span>
    </a>
    <a class="btn btn-ghost" href="mailto:hello@iukhan.tech?subject=Kharcha%20still%20down">Tell us it is taking long</a>
@endsection
