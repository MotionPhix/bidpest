<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BidSection extends Model
{
  protected $fillable = [
    'bid_id',
    'title',
    'content',
    'metadata',
    'order'
  ];

  protected $casts = [
    'metadata' => 'array'
  ];

  public function bid()
  {
    return $this->belongsTo(Bid::class);
  }
}
