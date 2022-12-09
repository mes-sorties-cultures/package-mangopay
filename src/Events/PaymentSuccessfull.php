<?php

namespace D4rk0s\Mangopay\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessfull
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $amount;
    public string $transactionId;
    public string $customerId;

    public function __construct(
      int $amount,
      string $transactionId,
      string $customerId
    )
    {
        $this->amount = $amount;
        $this->transactionId = $transactionId;
        $this->customerId = $customerId;
    }
}