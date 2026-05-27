<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    /**
     * Set or change the account's display currency. The mobile app calls
     * this from the first-launch picker and from the Settings → Currency
     * row. Amounts already stored on the account are NOT converted; this
     * is a relabel, not a re-conversion.
     */
    public function setCurrency(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'currency' => ['required', 'string', 'size:3', 'in:'.implode(',', \App\Models\Account::SUPPORTED_CURRENCIES)],
        ]);

        $account = $request->user()->account;
        $account->forceFill(['currency' => strtoupper($data['currency'])])->save();

        return response()->json([
            'message' => 'Currency set to '.$account->currency,
            'currency' => $account->currency,
        ]);
    }


    /**
     * Hard-delete the current user and their household account.
     *
     * Required by Google Play (Data Safety): every app that lets users
     * create an account must let them delete it from inside the app.
     *
     * The user re-enters their password to prove possession (defends
     * against a stolen unlocked phone). Cascading FKs remove every
     * SpendingList, Entry, Receipt and Category in one transaction.
     */
    public function destroy(Request $request)
    {
        $data = $request->validate([
            'password'     => ['required', 'string'],
            'confirmation' => ['required', 'string', 'in:DELETE'],
        ], [
            'confirmation.in' => 'Type DELETE exactly to confirm.',
        ]);

        /** @var User $user */
        $user = $request->user();

        if (! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Incorrect password.'],
            ]);
        }

        $userId    = $user->id;
        $accountId = $user->account_id;
        $email     = $user->email;

        DB::transaction(function () use ($user, $accountId) {
            // Revoke every token so any other signed-in device drops immediately.
            $user->tokens()->delete();

            // Remove the user before the account so the FK has nothing to nullify.
            $user->delete();

            // Cascades to spending_lists, entries, receipts, categories
            // via the account_id FK with cascadeOnDelete.
            if ($accountId) {
                Account::query()->whereKey($accountId)->delete();
            }
        });

        Log::info('Account deleted', [
            'user_id'    => $userId,
            'account_id' => $accountId,
            'email'      => $email,
        ]);

        return response()->json([
            'message' => 'Your account and every entry tied to it have been deleted.',
        ]);
    }
}
