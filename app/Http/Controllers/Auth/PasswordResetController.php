<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

/**
 * Forgot-password / reset-password for all portals (staff, admin, identity).
 * The user may identify themselves with their Staff No. or e-mail address.
 */
class PasswordResetController extends Controller
{
    public function request()
    {
        return view('auth.forgot-password');
    }

    public function email(Request $request)
    {
        $request->validate(['login' => ['required', 'string', 'max:255']]);

        $login = trim($request->login);
        $user  = User::where('email', $login)->orWhere('fileno', strtoupper($login))->first();

        if ($user) {
            Password::sendResetLink(['email' => $user->email]);
        }

        // Same answer whether or not the account exists (no account probing).
        return back()->with('success',
            'If an account matches what you entered, a password reset link has been sent to its e-mail address.');
    }

    public function reset(Request $request, string $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $user  = User::where('email', $request->email)->first();
            $route = match ($user?->role) {
                'admin'    => 'admin.auth.index',
                'identity' => 'identity.auth.index',
                default    => 'welcome',
            };
            return redirect()->route($route)->with('success', 'Your password has been reset. Please sign in.');
        }

        return back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }
}
