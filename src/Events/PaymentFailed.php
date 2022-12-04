<?php

namespace D4rk0s\Mangopay\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentFailed
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