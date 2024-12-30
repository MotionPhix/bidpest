<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PasswordlessToken extends Model
{
  protected $fillable = [
    'user_id',
    'email',
    'token',
    'remember',
    'expires_at',
    'attempts_count'
  ];

  protected $casts = [
    'expires_at' => 'datetime',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public static function createForEmail($email, $remember)
  {
    $user = User::where('email', $email)->whereNotNull('provider')->first();

    if (!$user) {
      return null;
    }

    if (!self::canRequestNewToken($user->id)) {
      return 'attempts';
    }

    $token = Str::random(6);

    return self::create([
      'user_id' => $user->id,
      'email' => $user->email,
      'remember' => $remember,
      'token' => $token,
      'expires_at' => now()->addMinutes(10),
    ]);
  }

  public static function verifyToken($email, $token)
  {
    return self::where('token', $token)
      ->where('email', $email)
      ->where('expires_at', '>', now())
      ->first();
  }

  public function isValid()
  {
    return $this->expires_at->isFuture();
  }

  public static function canRequestNewToken(int $userId)
  {
    // Limit token requests to prevent abuse
    $latestToken = self::where('user_id', $userId)
      ->orderBy('created_at', 'desc')->first();

    if (!$latestToken) {
      return true;
    }

    return $latestToken->attempts_count < 3
      && $latestToken->created_at->diffInMinutes(now()) >= 1;
  }

  public function incrementAttempts()
  {
    $this->increment('attempts_count');
  }
}
