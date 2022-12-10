<?php

namespace D4rk0s\Mangopay\Controllers;

use D4rk0s\Mangopay\Components\CreditCardForm;
use D4rk0s\Mangopay\Events\CardRegistrationFailure;
use D4rk0s\Mangopay\Events\CardRegistrationSuccessfull;
use D4rk0s\Mangopay\Models\MangopayErrorEnum;
use D4rk0s\Mangopay\Services\CardService;
use Illuminate\Http\Request;
use MangoPay\CardRegistrationStatus;

class CardRegistrationCallback
{
    public function __invoke(Request $request)
    {
        if(!$request->session()->has(CreditCardForm::SESSION_CARD_REGISTRATION_ID)) {
            abort(403);
        }

        // @todo: Check si la configuration du package est ok ?

        if($request->errorCode) {
            $request->session()->forget(CreditCardForm::SESSION_CARD_REGISTRATION_ID);

            $mangopayErrorEnum = MangopayErrorEnum::tryFrom($request->errorCode);
            $errorMessage = $mangopayErrorEnum ?
              $mangopayErrorEnum->getErrorMessage() :
              __("mangopay.error.unknown", ['errorCode' => $request->errorCode]);

            CardRegistrationFailure::dispatch($request->errorCode, $errorMessage);

            return redirect()->route(config('mangopay.cardInformationRoute'), ['locale'=>App()->getLocale()])
                ->withErrors($errorMessage);
        }

        if(is_null($request->data)) {
            abort(403);
        }

        $cardRegistrationId = $request->session()->get(CreditCardForm::SESSION_CARD_REGISTRATION_ID);
        $cardRegistration = CardService::updateCardRegistration($cardRegistrationId, $request->data);

        if($cardRegistration->Status !== CardRegistrationStatus::Validated || !isset($cardRegistration->CardId)) {
            CardRegistrationFailure::dispatch($cardRegistration->ResultCode, $cardRegistration->ResultMessage);

            return redirect()->route(config('mangopay.cardInformationRoute'), ['locale'=>App()->getLocale()])
                ->withErrors($cardRegistration->ResultMessage);
        }

        CardRegistrationSuccessfull::dispatch($cardRegistration);

        return redirect()->route(config('mangopay.paymentRoute'), [
            'locale' => App()->getLocale(),
            'cardId' => $cardRegistration->CardId
        ]);
    }
}