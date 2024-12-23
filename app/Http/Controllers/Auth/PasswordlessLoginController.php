<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordlessToken;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PasswordlessLoginController extends Controller
{
  public function create()
  {
    return Inertia::render('Auth/Login');
  }

  public function get_token()
  {
    return Inertia::render('Auth/VerifyToken');
  }

  public function login(Request $request)
  {
    $request->validate([
      'email' => 'required|email'
    ]);

    $token = PasswordlessToken::createForEmail($request->email);

    if (!$token) {
      return back()->withErrors([
        'error' => 'No account found with this email. Please login via Social Authentication.',
        'suggestSocialLogin' => true
      ]);
    }

    try {

      $this->sendVerificationEmail($token->user, $token->token);

      return Inertia::render('Auth/VerifyToken', [
        'message' => 'A verification token was sent to your email. Retrieve it and insert the characters in the box below',
      ]);

    } catch (\Exception $e) {

      \Log::error('Passwordless Login Error: ' . $e->getMessage());

      return back()->withErrors([
        'error' => 'Unable to send login token. Please try again.'
      ]);

    }
  }

  public function verifyToken(Request $request)
  {
    $request->validate([
      'token' => 'required|string',
      'email' => 'required|email'
    ]);

    try {

      $token = PasswordlessToken::verifyToken($request->email, $request->token);

      if (!$token) {
        return back()->withErrors([
          'token' => 'Invalid or expired token',
          'email' => $request->email
        ]);
      }

      $user = User::where('email', $request->email)->whereNotNull('provider')->first();

      if (!$user) {
        return Inertia::render('Auth/PasswordlessLogin', ['error' => 'User account not found', 'suggestSocialLogin' => true])->withStatus(404);
      }

      $token->delete();

      Auth::login($user, true);

      $this->ensureIsNotRateLimited();

      if (! Auth::attempt($user->only('email'), $token->boolean('remember'))) {

        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
          'email' => trans('auth.failed'),
        ]);

      }

      RateLimiter::clear($this->throttleKey());

      $request->session()->regenerate();

      return redirect()->intended(route('dashboard', absolute: false));

    } catch (\Exception $e) {

      \Log::error('Token Verification Error: ' . $e->getMessage());

      return back()->withErrors([
        'error' => 'Authentication failed. Please try again.'
      ]);

    }
  }

  /**
   * Destroy an authenticated session, forcing a user to be logged out.
   */
  public function destroy(Request $request): RedirectResponse
  {
    Auth::guard('web')->logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect('/');
  }

  protected function sendVerificationEmail(User $user, string $token)
  {
    Mail::send(
      'emails.send-token',
      [
        'name' => $user->name, 'token' => $token,
        'expires' => now()->addMinutes(10)->diffForHumans()
      ], function ($message) use ($user) {
      $message->to($user->email)->subject('Your Passwordless Login Token');
    });
  }

  /**
   * Ensure the login request is not rate limited.
   *
   * @throws \Illuminate\Validation\ValidationException
   */
  public function ensureIsNotRateLimited(): void
  {
    if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
      return;
    }

    event(new Lockout($this));

    $seconds = RateLimiter::availableIn($this->throttleKey());

    throw ValidationException::withMessages([
      'email' => trans('auth.throttle', [
        'seconds' => $seconds,
        'minutes' => ceil($seconds / 60),
      ]),
    ]);
  }

  /**
   * Get the rate limiting throttle key for the request.
   */
  public function throttleKey(): string
  {
    return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
  }
}
