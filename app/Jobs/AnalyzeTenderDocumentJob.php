<?php

namespace App\Jobs;

use App\Models\TenderDocument;
use App\Services\AIPromptService;
use App\Services\OpenAIService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class AnalyzeTenderDocumentJob implements ShouldQueue
{
  use Queueable, InteractsWithQueue, Dispatchable, SerializesModels;

  public $tries = 3;
  public $timeout = 300; // 5 minutes

  /**
   * Create a new job instance.
   */
  public function __construct(
    private TenderDocument $tenderDocument
  ) {}

  /**
   * Execute the job.
   */
  public function handle(
    OpenAIService $openAIService,
    AIPromptService $promptService
  ): void {
    try {
      $documentData = $this->tenderDocument->toArray();
      $prompts = $promptService->generateTenderAnalysisPrompt($documentData);

      $analysis = $openAIService->handleRequest(function() use ($prompts) {
        return OpenAI::chat()->create([
          'model' => 'gpt-4-turbo',
          'messages' => $prompts
        ]);
      }, "tender_analysis_{$this->tenderDocument->id}");

      // Process and store analysis
      $this->processTenderAnalysis($analysis);
    } catch (\Exception $e) {
      Log::channel('ai_service')->error('Tender Analysis Job Failed', [
        'tender_document_id' => $this->tenderDocument->id,
        'error' => $e->getMessage()
      ]);

      $this->fail($e);
    }
  }

  private function processTenderAnalysis($analysis)
  {
    // Implementation for processing the analysis result and storing it in the database
    $this->tenderDocument->analysis = $analysis->choices[0]->message->content;
    $this->tenderDocument->save();
  }
}
