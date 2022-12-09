<?php

namespace D4rk0s\Mangopay\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentFailure
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $amount;
    public string $transactionId;
    public string $customerId;
    public string $errorCode;

    public function __construct(
      int $amount,
      string $transactionId,
      string $customerId,
      string $errorCode
    )
    {
        $this->amount = $amount;
        $this->transactionId = $transactionId;
        $this->customerId = $customerId;
        $this->errorCode = $errorCode;
    }
}