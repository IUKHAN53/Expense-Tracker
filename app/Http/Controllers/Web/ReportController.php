<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Support\Money;
use App\Support\ReportData;
use App\Support\ReportPeriod;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * A print-friendly report for the current account + period. Opened in a new
     * tab from the Reports page; the user prints it / saves as PDF from the
     * browser. Account-scoped via the authenticated session user.
     */
    public function print(Request $request)
    {
        $period = ReportPeriod::resolve($request->query('period'));
        $start = $period['start'];
        $end = $period['end'];

        $account = $request->user()->account;
        $currency = $account?->currency ?: 'USD';

        return view('reports.print', [
            'account' => $account,
            'symbol' => Money::symbol($currency),
            'currency' => $currency,
            'periodLabel' => $period['label'],
            'months' => $period['months'],
            'start' => $start,
            'end' => $end,
            'totals' => ReportData::totals($start, $end),
            'byCategory' => ReportData::byCategory($start, $end),
            'byList' => ReportData::byList($start, $end),
            'topExpenses' => ReportData::topExpenses($start, $end, 25),
        ]);
    }
}
