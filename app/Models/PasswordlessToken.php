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

  public static function createForEmail($email)
  {
    $user = User::where('email', $email)->whereNotNull('provider')->first();

    if (!$user) {
      return null;
    }

    $token = Str::random(6);

    return self::create([
      'user_id' => $user->id,
      'email' => $user->email,
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

  public function canRequestNewToken()
  {
    // Limit token requests to prevent abuse
    return $this->attempts_count < 3 &&
      $this->created_at->diffInMinutes(now()) >= 1;
  }
}
