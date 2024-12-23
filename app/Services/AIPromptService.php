<?php

namespace App\Services;

use App\Models\Bid;
use App\Models\TenderDocument;

class AIPromptService
{
  /*public function generateTenderAnalysisPrompt(array $documentData)
  {
    return [
      [
        'role' => 'system',
        'content' => 'You are an expert bid analyst specialized in creating comprehensive and compliant tender responses.'
      ],
      [
        'role' => 'user',
        'content' => $this->constructDetailedPrompt($documentData)
      ]
    ];
  }

  private function constructDetailedPrompt(array $documentData)
  {
    $prompt = "Analyze the following tender document with extreme precision:\n\n";

    $prompt .= "Document Title: {$documentData['title']}\n";
    $prompt .= "Client Name: {$documentData['client_name']}\n";
    $prompt .= "Submission Deadline: {$documentData['submission_deadline']}\n\n";

    $prompt .= "Detailed Description:\n{$documentData['description']}\n\n";

    if (!empty($documentData['requirements'])) {
      $prompt .= "Key Requirements:\n";
      foreach ($documentData['requirements'] as $requirement) {
        $prompt .= "- {$requirement}\n";
      }
    }

    $prompt .= "\nProvide a structured analysis with the following considerations:
        1. Comprehensive breakdown of requirements
        2. Potential challenges and mitigation strategies
        3. Recommended response structure
        4. Critical compliance points
        5. Suggested technical and commercial approach

        Response should be:
        - Highly detailed
        - Directly addressing each requirement
        - Demonstrating clear understanding of the tender
        - Highlighting our competitive advantages
        ";

    return $prompt;
  }*/

  public function generateBidGenerationPrompt(TenderDocument $tenderDocument)
  {
    return [
      [
        'role' => 'system',
        'content' => 'You are an expert bid writer specializing in creating winning, comprehensive tender responses.'
      ],
      [
        'role' => 'user',
        'content' => $this->constructDetailedBidPrompt($tenderDocument)
      ]
    ];
  }

  private function constructDetailedBidPrompt(TenderDocument $tenderDocument)
  {
    return "Generate a comprehensive, winning bid response for the following tender:

        Tender Details:
        - Title: {$tenderDocument->title}
        - Client: {$tenderDocument->client_name}
        - Submission Deadline: {$tenderDocument->submission_deadline}

        Detailed Requirements:
        " . implode("\n", $tenderDocument->requirements ?? []) . "

        Bid Generation Guidelines:
        1. Provide a structured, professional response
        2. Directly address all tender requirements
        3. Highlight unique value propositions
        4. Demonstrate technical and commercial expertise
        5. Include risk mitigation strategies
        6. Provide clear, concise, and compelling content

        Response Format:
        - Executive Summary
        - Company Overview
        - Technical Solution
        - Commercial Proposal
        - Implementation Approach
        - Risk Management
        - Compliance Matrix

        Tone: Professional, confident, and solution-oriented
        Length: Comprehensive but concise
        ";
  }

  public function extractBidSections(string $generatedContent)
  {
    // Use AI to break down the generated content into structured sections
    // This is a simplified example - you'd want more sophisticated parsing
    return [
      [
        'title' => 'Executive Summary',
        'content' => $this->extractSection($generatedContent, 'Executive Summary'),
        'order' => 1
      ],
      [
        'title' => 'Technical Solution',
        'content' => $this->extractSection($generatedContent, 'Technical Solution'),
        'order' => 2
      ],
      // Add more sections
    ];
  }

  private function extractSection(string $content, string $sectionTitle)
  {
    // Implement more robust section extraction logic
    $pattern = "/#{$sectionTitle}(.*?)(?=#|$)/s";
    preg_match($pattern, $content, $matches);
    return $matches[1] ?? '';
  }

  public function calculateBidCompliance(Bid $bid)
  {
    // Implement complex compliance scoring
    return [
      'technical_compliance' => 85,
      'commercial_compliance' => 90,
      'overall_compliance' => 87.5
    ];
  }
}
