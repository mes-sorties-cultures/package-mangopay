<?php

namespace D4rk0s\Mangopay\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CardRegistrationFailure
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $resultCode;
    public string $resultMessage;

    public function __construct(
      string $errorCode,
      string $errorMessage
    )
    {
        $this->resultCode = $errorCode;
        $this->resultMessage = $errorMessage;
    }
}