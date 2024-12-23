<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use App\Models\TenderDocument;

class AIBidAnalysisService
{
  public function analyzeTenderDocument(TenderDocument $document)
  {
    try {
      $response = OpenAI::chat()->create([
        'model' => 'gpt-4-turbo',
        'messages' => [
          [
            'role' => 'system',
            'content' => 'You are an expert bid analyst. Break down the tender requirements into structured sections.'
          ],
          [
            'role' => 'user',
            'content' => "Analyze this tender document and provide a detailed breakdown of requirements:\n\n{$document->description}"
          ]
        ]
      ]);

      return $this->processAnalysisResult($response->choices[0]->message->content);
    } catch (\Exception $e) {
      \Log::error('AI Analysis Error: ' . $e->getMessage());
      return null;
    }
  }

  private function processAnalysisResult($analysis)
  {
    // Parse AI response into structured sections
    return [
      'key_requirements' => $this->extractKeyRequirements($analysis),
      'suggested_sections' => $this->suggestBidSections($analysis)
    ];
  }

  private function extractKeyRequirements($analysis)
  {
    // Implement more sophisticated parsing
    return [
      'main_objectives' => $analysis,
      // Add more structured parsing
    ];
  }

  private function suggestBidSections($analysis)
  {
    // Suggest bid sections based on analysis
    return [
      ['title' => 'Executive Summary', 'order' => 1],
      ['title' => 'Technical Approach', 'order' => 2],
      ['title' => 'Pricing', 'order' => 3],
      ['title' => 'Company Profile', 'order' => 4],
    ];
  }
}
