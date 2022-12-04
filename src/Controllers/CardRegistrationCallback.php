<?php

namespace D4rk0s\Mangopay\Controllers;

use D4rk0s\Mangopay\Components\CreditCardForm;
use D4rk0s\Mangopay\Events\CreditCardRegistered;
use D4rk0s\Mangopay\Exceptions\ClientError;
use D4rk0s\Mangopay\Exceptions\MangopayError;
use D4rk0s\Mangopay\Models\MangopayErrorEnum;
use D4rk0s\Mangopay\Services\CardService;
use Illuminate\Http\Request;
use MangoPay\CardRegistrationStatus;

class CardRegistrationCallback
{
    public function __invoke(Request $request)
    {
        $this->errorChecks($request);

        $cardRegistrationId = $request->session()->get(CreditCardForm::SESSION_CARD_REGISTRATION_ID);
        $cardRegistration = CardService::updateCardRegistration($cardRegistrationId, $request->data);

        if ($cardRegistration->Status !== CardRegistrationStatus::Validated ||
          !isset($cardRegistration->CardId))
        {
            throw new MangopayError(__("mangopay.error.credit_card_registration_failed"));
        }

        CreditCardRegistered::dispatch($cardRegistration->CardId, $cardRegistration->CardType);
    }

    private function errorChecks(Request $request)
    {
        if(!$request->session()->has(CreditCardForm::SESSION_CARD_REGISTRATION_ID)) {
            abort(403);
        }

        if($request->errorCode) {
            $mangopayErrorEnum = MangopayErrorEnum::tryFrom($request->errorCode);
            $request->session()->forget(CreditCardForm::SESSION_CARD_REGISTRATION_ID);

            throw new MangopayError($mangopayErrorEnum ?
                                      $mangopayErrorEnum->getErrorMessage() :
                                      __("mangopay.error.unknown", ['errorCode' => $request->errorCode]));
        }

        if(is_null($request->data)) {
            $request->session()->forget(CreditCardForm::SESSION_CARD_REGISTRATION_ID);

            throw new ClientError(__("mangopay.client.error.request_data_missing"));
        }
    }
}