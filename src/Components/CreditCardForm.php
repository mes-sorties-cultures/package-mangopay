<?php

namespace D4rk0s\Mangopay\Components;

use D4rk0s\Mangopay\Services\CardService;
use Illuminate\View\Component;
use MangoPay\CardRegistration;

class CreditCardForm extends Component
{
    public const SESSION_CARD_REGISTRATION_ID = "cardRegistrationId";
    private CardRegistration $cardRegistration;

    public function __construct(
        string $mangopayUserId,
        string $currency = "EUR",
        string $cardType = "CB_VISA_MASTERCARD"
    )
    {
        $cardRegistration = CardService::createCardRegistration(
          mangopayUserId: $mangopayUserId,
          currency: $currency,
          cardType: $cardType
        );

        request()->session()->put(self::SESSION_CARD_REGISTRATION_ID, $cardRegistration->Id);
        $this->cardRegistration = $cardRegistration;
    }

    public function render()
    {
        return view('mangopay::card-form')
          ->with('preRegistrationData', $this->cardRegistration->PreregistrationData)
          ->with('accessKey', $this->cardRegistration->AccessKey)
          ->with('returnUrl', route('d4rk0s_mangopay_card_registration_callback'));
    }
}