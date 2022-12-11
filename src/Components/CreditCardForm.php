<?php

namespace D4rk0s\Mangopay\Components;

use D4rk0s\Mangopay\Models\MangopayPayment;
use D4rk0s\Mangopay\Services\CardService;
use Illuminate\View\Component;
use MangoPay\CardRegistration;

class CreditCardForm extends Component
{
    private CardRegistration $cardRegistration;

    public function __construct(
        string $mangopayUserId,
        float $amount,
        string $currency = "EUR",
        string $cardType = "CB_VISA_MASTERCARD"
    )
    {
        if($amount <= 0) {
            throw new \Exception("Invalid amount to pay.");
        }

        $cardRegistration = CardService::createCardRegistration(
          mangopayUserId: $mangopayUserId,
          currency: $currency,
          cardType: $cardType
        );

        $mangopayOrder = new MangopayPayment();
        $mangopayOrder
            ->setCardRegistration($cardRegistration)
            ->setUserId($mangopayUserId)
            ->setAmount($amount)
            ->save();

        $this->cardRegistration = $cardRegistration;
    }

    public function render()
    {
        return view('mangopay::card-form')
          ->with('preRegistrationData', $this->cardRegistration->PreregistrationData)
          ->with('cardRegistrationUrl', $this->cardRegistration->CardRegistrationURL)
          ->with('accessKey', $this->cardRegistration->AccessKey)
          ->with('returnUrl', route('mangopay-cardRegistrationCallback'));
    }
}