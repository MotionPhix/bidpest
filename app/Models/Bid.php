<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
  use HasUuids;

  protected $fillable = [
    'tender_document_id',
    'user_id',
    'status',
    'compliance_score',
    'total_price',
    'notes'
  ];

  protected $casts = [
    'compliance_score' => 'array',
    'total_price' => 'decimal:2'
  ];

  public function tenderDocument()
  {
    return $this->belongsTo(TenderDocument::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function sections()
  {
    return $this->hasMany(BidSection::class);
  }
}
