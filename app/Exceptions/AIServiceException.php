<?php

namespace App\Exceptions;

use Exception;

class AIServiceException extends Exception
{
  protected $context;

  public function __construct(string $message, $context = null)
  {
    parent::__construct($message);
    $this->context = $context;
  }

  public function getContext()
  {
    return $this->context;
  }
}
