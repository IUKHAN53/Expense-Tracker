<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\AccountInvitationMail;
use App\Models\Account;
use App\Models\AccountInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class InvitationController extends Controller
{
    /** Authenticated: list members + pending invitations for the current account. */
    public function index(Request $request)
    {
        $account = $request->user()->account;

        return response()->json([
            'members' => $account->users()
                ->orderBy('id')
                ->get(['id', 'name', 'email', 'created_at'])
                ->map(fn (User $u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'joined_at' => $u->created_at?->toIso8601String(),
                    'is_you' => $u->id === $request->user()->id,
                ]),
            'pending' => $account->pendingInvitations()
                ->orderBy('id')
                ->get(['id', 'email', 'expires_at', 'created_at'])
                ->map(fn (AccountInvitation $i) => [
                    'id' => $i->id,
                    'email' => $i->email,
                    'expires_at' => $i->expires_at->toIso8601String(),
                    'created_at' => $i->created_at->toIso8601String(),
                ]),
            'capacity' => [
                'used' => $account->memberUsage(),
                'max' => $account->maxMembers(),
                'plan' => $account->plan,
                'is_pro' => $account->isPro(),
            ],
        ]);
    }

    /** Authenticated: create a pending invitation, send it. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:120'],
        ]);

        $account = $request->user()->account;
        $email = strtolower(trim($data['email']));

        if (! $account->canInviteMore()) {
            throw ValidationException::withMessages([
                'email' => [
                    $account->isPro()
                        ? 'Your household is at the '.Account::PRO_MAX_MEMBERS.' member limit. Contact us to add more.'
                        : 'Free households are capped at '.Account::FREE_MAX_MEMBERS.' members. Upgrade to Pro for up to '.Account::PRO_MAX_MEMBERS.'.',
                ],
            ]);
        }

        if ($account->users()->where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => ['That email is already in this household.'],
            ]);
        }

        if ($account->pendingInvitations()->where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => ['An invitation to that email is already pending.'],
            ]);
        }

        $invitation = AccountInvitation::create([
            'account_id'         => $account->id,
            'invited_by_user_id' => $request->user()->id,
            'email'              => $email,
            'token'              => AccountInvitation::newToken(),
            'expires_at'         => now()->addDays(14),
        ]);

        try {
            Mail::to($email)->send(new AccountInvitationMail($invitation));
        } catch (\Throwable $e) {
            Log::warning('Invitation email failed', [
                'invitation_id' => $invitation->id,
                'email'         => $email,
                'error'         => $e->getMessage(),
            ]);
        }

        return response()->json([
            'message' => 'Invitation sent to '.$email,
            'invitation' => [
                'id' => $invitation->id,
                'email' => $invitation->email,
                'expires_at' => $invitation->expires_at->toIso8601String(),
            ],
        ], 201);
    }

    /** Authenticated: cancel a pending invitation owned by this account. */
    public function destroy(Request $request, AccountInvitation $invitation)
    {
        if ($invitation->account_id !== $request->user()->account_id) {
            abort(403);
        }
        if ($invitation->accepted_at) {
            abort(409, 'That invitation has already been accepted.');
        }

        $invitation->delete();

        return response()->json(['message' => 'Invitation revoked.']);
    }

    /** Authenticated: remove a household member (and delete them). */
    public function removeMember(Request $request, User $user)
    {
        $current = $request->user();

        if ($user->account_id !== $current->account_id) {
            abort(403);
        }
        if ($user->id === $current->id) {
            abort(422, 'You cannot remove yourself. Use account deletion instead.');
        }

        // Revoke their tokens then delete the row. The account stays.
        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'Member removed.']);
    }

    /** Public: fetch invitation details by token (so the accept page can render). */
    public function show(string $token)
    {
        $invitation = AccountInvitation::with(['account', 'invitedBy'])
            ->where('token', $token)
            ->first();

        if (! $invitation) {
            return response()->json(['message' => 'This invitation does not exist.'], 404);
        }
        if ($invitation->accepted_at) {
            return response()->json(['message' => 'This invitation has already been used.'], 410);
        }
        if ($invitation->isExpired()) {
            return response()->json(['message' => 'This invitation has expired.'], 410);
        }

        return response()->json([
            'invitation' => [
                'email' => $invitation->email,
                'household' => $invitation->account->name,
                'invited_by' => $invitation->invitedBy->name,
                'expires_at' => $invitation->expires_at->toIso8601String(),
            ],
            'invitee_has_account' => User::where('email', $invitation->email)->exists(),
        ]);
    }

    /**
     * Public: accept the invitation. Either logs in an existing user (and
     * moves them to the inviting account, deleting their old solo account)
     * or creates a brand-new user inside the inviting account.
     */
    public function accept(Request $request, string $token)
    {
        $invitation = AccountInvitation::where('token', $token)->first();

        if (! $invitation || $invitation->accepted_at || $invitation->isExpired()) {
            return response()->json(['message' => 'This invitation is no longer valid.'], 410);
        }

        $rules = ['password' => ['required', 'string']];
        $existing = User::where('email', $invitation->email)->first();
        if (! $existing) {
            // New-user path: also collect a name and apply the strong-password rule.
            $rules['name'] = ['required', 'string', 'max:80'];
            $rules['password'] = ['required', 'string', PasswordRule::min(8)];
        }
        $data = $request->validate($rules);

        $user = DB::transaction(function () use ($invitation, $existing, $data) {
            if ($existing) {
                if (! Hash::check($data['password'], $existing->password)) {
                    throw ValidationException::withMessages([
                        'password' => ['Incorrect password for '.$invitation->email],
                    ]);
                }

                // If they were the only user on their previous account, that
                // account becomes orphaned data. Delete it; the cascade clears
                // their old entries/lists/receipts/categories.
                $oldAccountId = $existing->account_id;
                $existing->forceFill(['account_id' => $invitation->account_id])->save();

                if ($oldAccountId && $oldAccountId !== $invitation->account_id) {
                    $stillUsed = User::where('account_id', $oldAccountId)->exists();
                    if (! $stillUsed) {
                        Account::query()->whereKey($oldAccountId)->delete();
                    }
                }

                $existing->tokens()->delete();

                return $existing;
            }

            return User::create([
                'name' => $data['name'],
                'email' => $invitation->email,
                'password' => Hash::make($data['password']),
                'account_id' => $invitation->account_id,
                'is_super_admin' => false,
            ]);
        });

        $invitation->forceFill([
            'accepted_at' => now(),
            'accepted_by_user_id' => $user->id,
        ])->save();

        $token = $user->createToken('invite-accept')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'account_id' => $user->account_id,
            ],
            'message' => 'Welcome to '.$invitation->account->name,
        ]);
    }
}
