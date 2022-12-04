<?php

namespace D4rk0s\Mangopay\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreditCardRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $cardId;
    public string $cardType;

    public function __construct(string $cardId, string $cardType)
    {
        $this->cardId = $cardId;
        $this->cardType = $cardType;
    }
}