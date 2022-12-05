<?php

namespace D4rk0s\Mangopay\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MangoPay\CardRegistration;

class CardRegistrationSuccessfull
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $cardId;
    public string $cardType;

    public function __construct(CardRegistration $cardRegistration)
    {
        $this->cardId = $cardRegistration->CardId;
        $this->cardType = $cardRegistration->CardType;
    }
}