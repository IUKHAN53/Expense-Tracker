<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Support\Fx;
use Illuminate\Http\Request;

class FxController extends Controller
{
    /**
     * Return the conversion rate from one supported currency to another, using
     * the server's daily-refreshed reference rates. `rate` is null when the
     * table has no data for a leg yet — the app then asks the user to enter it.
     */
    public function show(Request $request)
    {
        $data = $request->validate([
            'from' => ['required', 'string', 'size:3', 'in:'.implode(',', Account::SUPPORTED_CURRENCIES)],
            'to' => ['required', 'string', 'size:3', 'in:'.implode(',', Account::SUPPORTED_CURRENCIES)],
        ]);

        return response()->json([
            'from' => strtoupper($data['from']),
            'to' => strtoupper($data['to']),
            'rate' => Fx::rate($data['from'], $data['to']),
            'as_of' => Fx::asOf(),
        ]);
    }
}
