<?php

namespace App\Services;

use App\Models\Bid;
use App\Models\TenderDocument;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class BidGenerationService
{
  /*protected $aiAnalysisService;

  public function __construct(AIBidAnalysisService $aiAnalysisService)
  {
    $this->aiAnalysisService = $aiAnalysisService;
  }

  public function generateBidForTender(TenderDocument $document, User $user)
  {
    // Analyze tender document
    $analysis = $this->aiAnalysisService->analyzeTenderDocument($document);

    // Create bid
    $bid = Bid::create([
      'tender_document_id' => $document->id,
      'user_id' => $user->id,
      'status' => 'draft',
      'compliance_score' => $analysis['key_requirements'] ?? null
    ]);

    // Generate sections
    $this->generateBidSections($bid, $analysis);

    return $bid;
  }

  private function generateBidSections(Bid $bid, $analysis)
  {
    foreach ($analysis['suggested_sections'] as $section) {
      $bid->sections()->create([
        'title' => $section['title'],
        'order' => $section['order'],
        'content' => null, // Content can be filled later
        'metadata' => null // Additional metadata if needed
      ]);
    }
  }*/

  protected $aiPromptService;
  protected $openAIService;

  public function __construct(
    AIPromptService $aiPromptService,
    OpenAIService   $openAIService
  )
  {
    $this->aiPromptService = $aiPromptService;
    $this->openAIService = $openAIService;
  }

  public function generateComprehensiveBid(TenderDocument $tenderDocument, $user)
  {
    try {
      // Prepare detailed prompt for bid generation
      $prompt = $this->aiPromptService->generateBidGenerationPrompt($tenderDocument);

      // Use OpenAI service to handle API calls with retry and error handling
      $bidContent = $this->openAIService->handleRequest(function () use ($prompt) {
        return OpenAI::chat()->create([
          'model' => 'gpt-4-turbo',
          'messages' => $prompt,
          'max_tokens' => 4000,
          'temperature' => 0.7
        ]);
      }, "bid_generation_{$tenderDocument->id}");

      // Create bid with generated content
      $bid = Bid::create([
        'tender_document_id' => $tenderDocument->id,
        'user_id' => $user->id,
        'status' => 'draft',
        'generated_content' => $bidContent->choices[0]->message->content
      ]);

      // Generate structured bid sections
      $this->createBidSections($bid, $bidContent->choices[0]->message->content);

      // Calculate compliance score
      $complianceScore = $this->calculateComplianceScore($bid);
      $bid->update(['compliance_score' => $complianceScore]);

      return $bid;
    } catch (\Exception $e) {
      Log::error('Bid Generation Failed', [
        'tender_document_id' => $tenderDocument->id,
        'error' => $e->getMessage()
      ]);

      throw $e;
    }
  }

  protected function createBidSections(Bid $bid, string $generatedContent)
  {
    // Use AI to break down the generated content into structured sections
    $sections = $this->aiPromptService->extractBidSections($generatedContent);

    foreach ($sections as $sectionData) {
      $bid->sections()->create([
        'title' => $sectionData['title'],
        'content' => $sectionData['content'],
        'order' => $sectionData['order'] ?? 0
      ]);
    }
  }

  protected function calculateComplianceScore(Bid $bid)
  {
    // Implement complex compliance scoring logic
    return $this->aiPromptService->calculateBidCompliance($bid);
  }
}
