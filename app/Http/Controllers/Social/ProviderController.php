<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

class ProviderController extends Controller
{
  protected $providers = ['google', 'facebook'];

  public function redirectToProvider($provider)
  {
    if (!in_array($provider, $this->providers)) {
      return Inertia::render('Auth/Login', [
        'error' => 'Invalid authentication provider'
      ])->withStatus(400);
    }

    return Socialite::driver($provider)->redirect();
  }

  public function handleProviderCallback($provider)
  {
    try {
      $socialiteUser = Socialite::driver($provider)->user();
      $user = $this->findOrCreateUser($socialiteUser, $provider);

      // Log in the user
      Auth::login($user, true);

      // Redirect to dashboard or intended page
      return Inertia::location(route('dashboard'));
    } catch (\Exception $e) {
      // Log the error
      \Log::error('Socialite Authentication Error: ' . $e->getMessage());

      // Redirect back with error
      return Inertia::render('Auth/Login', [
        'error' => 'Authentication failed. Please try again.'
      ]);
    }
  }

  protected function findOrCreateUser($socialiteUser, $provider)
  {
    // First, check if social account exists
    $socialAccount = User::where('provider', $provider)
      ->where('provider_id', $socialiteUser->getId())
      ->first();

    // If social account exists, return the user
    if ($socialAccount) {
      return $socialAccount->user;
    }

    // Check if user exists by email
    $user = User::where('email', $socialiteUser->getEmail())->first();

    if (!$user) {
      $data = [
        'name' => $socialiteUser->getName(),
        'email' => $socialiteUser->getEmail(),
        'avatar' => $socialiteUser->getAvatar()
      ];

      if ($provider !== 'facebook') $data['email_verified_at'] = now();

      // Create new user
      $user = User::create($data);

      // Assign default role
      $user->assignRole('user');
    }

    return $user;
  }

}
