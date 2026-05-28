@if (session()->has(\App\Support\Impersonation::SESSION_KEY))
    <div style="position:sticky;top:0;z-index:50;display:flex;align-items:center;justify-content:center;gap:.75rem;
                padding:.5rem 1rem;background:#b14430;color:#fff;font-family:'Geist',sans-serif;font-size:.875rem;">
        <span>
            Viewing as
            <strong>{{ auth()->user()?->email ?? 'user' }}</strong>
            — you are impersonating this account.
        </span>
        <a href="{{ route('impersonate.leave') }}"
           style="background:#fff;color:#b14430;padding:.2rem .7rem;border-radius:9999px;font-weight:600;text-decoration:none;">
            Return to SuperAdmin
        </a>
    </div>
@endif
