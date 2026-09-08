<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Response;
use Inertia\ResponseFactory;

/**
 * Forgot/reset password over the `users` password broker (config/auth.php: 60-minute
 * token expiry, 60-second per-user resend throttle; tokens live in the
 * `password_reset_tokens` table created by the initial users migration).
 *
 * Enumeration contract: neither endpoint ever reveals whether an address is
 * registered. sendLink() discards the broker status entirely, and update() reports
 * INVALID_USER with the same message as INVALID_TOKEN. The broker timeboxes both
 * calls at 200ms, so the paths are indistinguishable by response time as well.
 */
class PasswordResetController extends Controller
{
    public function __construct(
        protected ResponseFactory $inertia,
    ) {}

    public function requestView(): Response
    {
        return $this->inertia->render('ForgotPassword');
    }

    /**
     * Always reports success. The broker returns RESET_LINK_SENT, INVALID_USER (no
     * such address) or RESET_THROTTLED (a link was issued inside the 60s window);
     * surfacing either of the last two turns this endpoint into an account oracle,
     * so all three collapse into one flash.
     */
    public function sendLink(ForgotPasswordRequest $request): RedirectResponse
    {
        Password::sendResetLink($request->only('email'));

        return back()->with('status', trans(Password::RESET_LINK_SENT));
    }

    public function resetView(Request $request, string $token): Response
    {
        return $this->inertia->render('ResetPassword', [
            'token' => $token,
            'email' => $request->string('email')->toString(),
        ]);
    }

    /**
     * Rotates remember_token so any surviving "remember me" cookie dies with the
     * old password. Deliberately does NOT log the user in: auto-login would hand a
     * session to whoever reaches the mailbox, bypassing the login path where the
     * deactivated-account check lives (and where a fork would add 2FA).
     */
    public function update(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            // INVALID_USER and INVALID_TOKEN share one message on purpose: reporting
            // them apart lets an attacker probe addresses with a junk token.
            return back()->withErrors([
                'email' => trans(Password::INVALID_TOKEN),
            ]);
        }

        return redirect()->route('login')->with('status', trans($status));
    }
}
