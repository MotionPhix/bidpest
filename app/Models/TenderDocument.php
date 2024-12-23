<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TenderDocument extends Model
{
  use HasUuids;

  protected $fillable = [
    'title',
    'description',
    'client_name',
    'submission_deadline',
    'requirements',
    'status',
    'user_id'
  ];

  protected $casts = [
    'requirements' => 'array',
    'submission_deadline' => 'date'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function bids()
  {
    return $this->hasMany(Bid::class);
  }
}
