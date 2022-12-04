<?php

namespace D4rk0s\Mangopay\Components;

use Illuminate\Http\Request;
use Illuminate\View\Component;
use MangoPay\CardRegistration;

class CreditCardForm extends Component
{
    private CardRegistration $cardRegistration;
    public const SESSION_CARD_REGISTRATION_ID = "cardRegistrationId";

    public function __construct(CardRegistration $cardRegistration, Request $request)
    {
        $this->cardRegistration = $cardRegistration;
        $request->session()->put(self::SESSION_CARD_REGISTRATION_ID, $this->cardRegistration->Id);
    }

    public function render()
    {
        return view('mangopay::card-form')
          ->with('preRegistrationData', $this->cardRegistration->PreregistrationData)
          ->with('accessKey', $this->cardRegistration->AccessKey)
          ->with('returnUrl', route('d4rk0s_mangopay_card_registration_callback'));
    }
}