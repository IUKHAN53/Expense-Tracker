@php
    use Illuminate\Support\Facades\Storage;
    $imageUrl = $receipt->image_path
        ? Storage::disk('public')->url($receipt->image_path)
        : null;
@endphp

<div style="display:flex; flex-direction:column; gap:18px;">
    @if ($imageUrl)
        <a href="{{ $imageUrl }}" target="_blank" rel="noopener"
           style="display:block; background:#2b1f12; border-radius:6px; overflow:hidden; text-align:center;">
            <img src="{{ $imageUrl }}"
                 alt="Receipt photo for {{ $receipt->merchant ?? 'receipt #'.$receipt->id }}"
                 style="display:block; max-width:100%; max-height:78vh; margin:0 auto; object-fit:contain;">
        </a>
        <p style="font-family: ui-monospace, 'SF Mono', Menlo, monospace; font-size: 11px; letter-spacing: 0.16em; text-transform: uppercase; color: #8a7558; margin: 0; text-align: center;">
            Tap the image to open the original
        </p>
    @else
        <div style="padding: 40px; border: 1px dashed #d8c7a3; border-radius: 4px; text-align: center; color: #8a7558; font-family: Georgia, serif; font-style: italic;">
            This receipt has no image on file.
        </div>
    @endif

    <dl style="display:grid; grid-template-columns: max-content 1fr; gap: 8px 24px; margin: 0; font-family: -apple-system, sans-serif; font-size: 13.5px;">
        <dt style="color:#8a7558; text-transform: uppercase; letter-spacing: 0.15em; font-size: 11px;">Merchant</dt>
        <dd style="margin:0; color:#2b1f12;">{{ $receipt->merchant ?? '·' }}</dd>

        <dt style="color:#8a7558; text-transform: uppercase; letter-spacing: 0.15em; font-size: 11px;">Type</dt>
        <dd style="margin:0; color:#2b1f12;">{{ $receipt->receipt_type ?? '·' }}</dd>

        <dt style="color:#8a7558; text-transform: uppercase; letter-spacing: 0.15em; font-size: 11px;">Total</dt>
        <dd style="margin:0; color:#2b1f12; font-variant-numeric: tabular-nums;">
            {{ $receipt->total !== null ? 'Rs '.number_format((float) $receipt->total, 0) : '·' }}
        </dd>

        <dt style="color:#8a7558; text-transform: uppercase; letter-spacing: 0.15em; font-size: 11px;">Purchased</dt>
        <dd style="margin:0; color:#2b1f12;">{{ $receipt->purchased_at?->format('d M Y, H:i') ?? '·' }}</dd>

        <dt style="color:#8a7558; text-transform: uppercase; letter-spacing: 0.15em; font-size: 11px;">Status</dt>
        <dd style="margin:0; color:#2b1f12;">{{ $receipt->status }}</dd>

        <dt style="color:#8a7558; text-transform: uppercase; letter-spacing: 0.15em; font-size: 11px;">Scanned</dt>
        <dd style="margin:0; color:#2b1f12;">{{ $receipt->created_at?->format('d M Y, H:i') }}</dd>

        @if ($receipt->createdBy)
            <dt style="color:#8a7558; text-transform: uppercase; letter-spacing: 0.15em; font-size: 11px;">Added by</dt>
            <dd style="margin:0; color:#2b1f12;">{{ $receipt->createdBy->name }} ({{ $receipt->createdBy->email }})</dd>
        @endif

        @if ($receipt->account)
            <dt style="color:#8a7558; text-transform: uppercase; letter-spacing: 0.15em; font-size: 11px;">Household</dt>
            <dd style="margin:0; color:#2b1f12;">{{ $receipt->account->name }}</dd>
        @endif

        @if ($receipt->error)
            <dt style="color:#a8341c; text-transform: uppercase; letter-spacing: 0.15em; font-size: 11px;">Error</dt>
            <dd style="margin:0; color:#a8341c;">{{ $receipt->error }}</dd>
        @endif
    </dl>
</div>
