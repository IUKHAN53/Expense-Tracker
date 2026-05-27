@extends('errors.layout', ['title' => 'Session expired · Kharcha'])

@section('status-label', '419 · Session expired')

@section('display')Expired<span class="accent">.</span>@endsection

@section('lede')
    The page sat open long enough that its security token timed out. Reload and continue where you were.
@endsection

@section('cta')
    <a class="btn btn-primary" href="javascript:history.back()">
        Reload the page
        <span class="arrow" aria-hidden="true">↺</span>
    </a>
    <a class="btn btn-ghost" href="/">Back to home</a>
@endsection
