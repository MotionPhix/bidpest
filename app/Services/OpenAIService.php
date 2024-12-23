<?php

namespace App\Services;

use App\Exceptions\AIServiceException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
  protected $maxRetries = 3;
  protected $retryDelay = 2; // seconds

  public function handleRequest(callable $apiCall, string $cacheKey = null)
  {
    // Check cache first if cache key is provided
    if ($cacheKey && Cache::has($cacheKey)) {
      return Cache::get($cacheKey);
    }

    $attempts = 0;
    while ($attempts < $this->maxRetries) {
      try {
        $response = $apiCall();

        // Cache the response if cache key is provided
        if ($cacheKey) {
          Cache::put($cacheKey, $response, now()->addHours(24));
        }

        return $response;
      } catch (\OpenAI\Exceptions\ErrorException $e) {
        // Handle specific OpenAI errors
        $this->logError($e, 'OpenAI API Error');

        // Check for rate limit error
        if ($this->isRateLimitError($e)) {
          // Exponential backoff
          sleep($this->retryDelay * (2 ** $attempts));
          $attempts++;
          continue;
        }

        // For non-rate limit errors, throw exception
        throw new AIServiceException(
          'AI Service Request Failed: ' . $e->getMessage(),
          [
            'error_code' => $e->getCode(),
            'error_message' => $e->getMessage()
          ]
        );
      } catch (\Exception $e) {
        // Log and rethrow unexpected errors
        $this->logError($e, 'Unexpected AI Service Error');
        throw $e;
      }
    }

    // If max retries exceeded
    throw new AIServiceException('Max retries exceeded for AI service');
  }

  protected function isRateLimitError(\Exception $e): bool
  {
    return strpos($e->getMessage(), 'rate limit') !== false;
  }

  protected function logError(\Exception $e, string $context)
  {
    Log::channel('ai_service')->error($context, [
      'message' => $e->getMessage(),
      'trace' => $e->getTraceAsString(),
      'context' => method_exists($e, 'getContext') ? $e->getContext() : null
    ]);
  }
}
