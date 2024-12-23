<?php

namespace App\Jobs;

use App\Models\TenderDocument;
use App\Notifications\BidGeneratedNotification;
use App\Services\BidGenerationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateBidJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $tenderDocument;
  protected $user;

  public $tries = 3;
  public $timeout = 600; // 10 minutes

  public function __construct(TenderDocument $tenderDocument, $user)
  {
    $this->tenderDocument = $tenderDocument;
    $this->user = $user;
  }

  public function handle(BidGenerationService $bidGenerationService)
  {
    try {
      $bid = $bidGenerationService->generateComprehensiveBid(
        $this->tenderDocument,
        $this->user
      );

      // Optionally send notification about bid generation
      $this->notifyBidGeneration($bid);
    } catch (\Exception $e) {
      Log::error('Bid Generation Job Failed', [
        'tender_document_id' => $this->tenderDocument->id,
        'error' => $e->getMessage()
      ]);

      $this->fail($e);
    }
  }

  private function notifyBidGeneration($bid)
  {
    $this->user->notify(new BidGeneratedNotification($bid));
  }
}
