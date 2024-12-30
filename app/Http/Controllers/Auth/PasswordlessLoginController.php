<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordlessToken;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

  public function get_token(User $user)
  {
    if (! $user->uuid) {
      return back()->withErrors([
        'error' => 'We could not attribute your authenticity'
      ]);
    }

    return Inertia::render('Auth/VerifyToken');
  }

  public function login(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'remember' => 'required'
    ]);

    $token = PasswordlessToken::createForEmail($request->email, $request->remember);

    if (!$token) {
      return back()->withErrors([
        'error' => 'No matching account found with this email.',
      ]);
    } elseif ($token === 'attempts') {
      return back()->withErrors([
        'error' => 'Too many attempts made. Please try again later'
      ]);
    }

    try {

      $this->sendTokenEmail($token->user, $token->token);

      return redirect(
        route(
          'auth.get.token',
          ['user' => $token->user->uuid]
        )
      );

    } catch (\Exception $e) {

      Log::error('Passwordless Login Error: ' . $e->getMessage());

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
    ], [
      'token.required' => 'Enter the token you received from your email',
      'email.email' => 'Enter a valid email address',
      'email.required' => 'Enter your email address'
    ]);

    try {

      $token = PasswordlessToken::verifyToken($request->email, $request->token);

      if (is_null($token)) {

        $this->incrementAttempts($token->email);

        return back()->withErrors([
          'token' => 'Invalid or expired token',
        ]);

      }

      $user = User::where('email', $request->email)->whereNotNull('provider')->first();

      if (!$user) {
        return back()->withErrors([
          'error' => 'User account not found'
        ]);
      }

      $token->delete();

      Auth::login($user, true);

      $this->ensureIsNotRateLimited();

      if (!Auth::attempt($user->only('email'), $token->boolean('remember'))) {

        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
          'email' => trans('auth.failed'),
        ]);

      }

      RateLimiter::clear($this->throttleKey());

      $request->session()->regenerate();

      return redirect()->intended(route('dashboard', absolute: false));

    } catch (\Exception $e) {

      Log::error('Token Verification Error: ' . $e->getMessage());

      return back()->withErrors([
        'error' => 'Invalid or expired token.'
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

    Inertia::clearHistory();

    return redirect('/');
  }

  protected function sendTokenEmail(User $user, string $token)
  {
    Mail::send(
      'emails.send-token',
      [
        'name' => $user->name, 'token' => $token,
        'expires' => now()->addMinutes(10)->diffForHumans()
      ], function ($message) use ($user) {
      $message->to($user->email)->subject('Your OTP Token');
    });
  }

  protected function incrementAttempts($email)
  {
    $latestToken = PasswordlessToken::where('email', $email)
      ->orderBy('created_at', 'desc')->first();

    if ($latestToken) {
      $latestToken->incrementAttempts();
    }
  }

  public function ensureIsNotRateLimited(): void
  {
    if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
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
    return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
  }
}
