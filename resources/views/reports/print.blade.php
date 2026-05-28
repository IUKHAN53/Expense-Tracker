<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kharcha report · {{ $periodLabel }}</title>
    <style>
        :root { --ink:#2b1f12; --soft:#7c6a52; --rule:#e8dcc1; --accent:#c9621f; --bg:#fdf8ee; }
        * { box-sizing: border-box; }
        body { font-family: -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
               color: var(--ink); background: #fff; margin: 0; padding: 32px; font-size: 13px; }
        .wrap { max-width: 820px; margin: 0 auto; }
        header { display:flex; justify-content:space-between; align-items:flex-end;
                 border-bottom: 2px solid var(--ink); padding-bottom: 12px; margin-bottom: 20px; }
        h1 { font-size: 22px; margin: 0; letter-spacing: -0.5px; }
        .muted { color: var(--soft); }
        .sub { font-size: 12px; color: var(--soft); margin-top: 4px; }
        .cards { display:flex; gap:12px; margin: 18px 0 26px; }
        .card { flex:1; border:1px solid var(--rule); border-radius:8px; padding:12px 14px; }
        .card .k { font-size:10px; letter-spacing:1px; text-transform:uppercase; color:var(--soft); }
        .card .v { font-size:22px; font-weight:600; margin-top:4px; }
        h2 { font-size: 14px; margin: 26px 0 8px; }
        table { width:100%; border-collapse: collapse; }
        th, td { text-align:left; padding:7px 8px; border-bottom:1px solid var(--rule); }
        th { font-size:10px; letter-spacing:0.6px; text-transform:uppercase; color:var(--soft); }
        td.num, th.num { text-align:right; font-variant-numeric: tabular-nums; }
        tfoot td { font-weight:600; border-top:2px solid var(--ink); border-bottom:none; }
        .toolbar { text-align:center; margin-bottom:18px; }
        .btn { background: var(--accent); color:#fff; border:none; border-radius:6px;
               padding:9px 18px; font-size:13px; cursor:pointer; }
        @media print { .toolbar { display:none; } body { padding:0; } }
    </style>
</head>
<body>
<div class="wrap">
    <div class="toolbar">
        <button class="btn" onclick="window.print()">Print / Save as PDF</button>
    </div>

    <header>
        <div>
            <h1>Kharcha — Spending report</h1>
            <div class="sub">{{ $account?->name ?? 'Household' }} · {{ $periodLabel }}
                ({{ $start->format('d M Y') }} – {{ $end->format('d M Y') }})</div>
        </div>
        <div class="sub">Generated {{ now()->format('d M Y, H:i') }}</div>
    </header>

    <div class="cards">
        <div class="card"><div class="k">Total spent</div><div class="v">{{ $symbol }} {{ number_format($totals['total']) }}</div></div>
        <div class="card"><div class="k">Entries</div><div class="v">{{ number_format($totals['count']) }}</div></div>
        <div class="card"><div class="k">Average / entry</div><div class="v">{{ $symbol }} {{ number_format($totals['average']) }}</div></div>
    </div>

    <h2>By category</h2>
    <table>
        <thead><tr><th>Category</th><th class="num">Entries</th><th class="num">Total</th></tr></thead>
        <tbody>
        @forelse ($byCategory as $row)
            <tr><td>{{ $row['name'] }}</td><td class="num">{{ $row['count'] }}</td>
                <td class="num">{{ $symbol }} {{ number_format($row['total']) }}</td></tr>
        @empty
            <tr><td colspan="3" class="muted">No spending in this period.</td></tr>
        @endforelse
        </tbody>
    </table>

    <h2>By list</h2>
    <table>
        <thead><tr><th>List</th><th>Type</th><th class="num">Budget / mo</th><th class="num">Spent</th></tr></thead>
        <tbody>
        @foreach ($byList as $row)
            <tr><td>{{ $row['name'] }}</td><td class="muted">{{ ucfirst($row['type']) }}</td>
                <td class="num">{{ $row['budget'] !== null ? $symbol.' '.number_format($row['budget']) : '—' }}</td>
                <td class="num">{{ $symbol }} {{ number_format($row['total']) }}</td></tr>
        @endforeach
        </tbody>
    </table>

    <h2>Largest expenses</h2>
    <table>
        <thead><tr><th>Date</th><th>Item</th><th>List</th><th>Category</th><th class="num">Amount</th></tr></thead>
        <tbody>
        @forelse ($topExpenses as $e)
            <tr>
                <td>{{ $e->purchased_at?->format('d M Y') }}</td>
                <td>{{ $e->item_name }}</td>
                <td class="muted">{{ $e->spendingList?->name }}</td>
                <td class="muted">{{ $e->category?->name ?? '—' }}</td>
                <td class="num">{{ $symbol }} {{ number_format((float) $e->amount, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="muted">No expenses in this period.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
</body>
</html>
