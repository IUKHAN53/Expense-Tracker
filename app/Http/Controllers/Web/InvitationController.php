<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AccountInvitation;
use App\Models\User;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    /** Land the email link here; render the accept page with whichever state matches. */
    public function show(Request $request)
    {
        $token = (string) $request->query('token', '');

        $invitation = AccountInvitation::with(['account', 'invitedBy'])
            ->where('token', $token)
            ->first();

        if (! $invitation) {
            return view('invitations.show', ['state' => 'invalid']);
        }
        if ($invitation->accepted_at) {
            return view('invitations.show', ['state' => 'accepted', 'invitation' => $invitation]);
        }
        if ($invitation->isExpired()) {
            return view('invitations.show', ['state' => 'expired', 'invitation' => $invitation]);
        }

        return view('invitations.show', [
            'state' => 'pending',
            'invitation' => $invitation,
            'inviteeHasAccount' => User::where('email', $invitation->email)->exists(),
        ]);
    }
}
