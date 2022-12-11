<?php

namespace D4rk0s\Mangopay\Controllers;

use D4rk0s\Mangopay\Events\CardRegistrationFailure;
use D4rk0s\Mangopay\Events\CardRegistrationSuccessfull;
use D4rk0s\Mangopay\Models\MangopayErrorEnum;
use D4rk0s\Mangopay\Models\MangopayPayment;
use D4rk0s\Mangopay\Services\CardService;
use D4rk0s\Mangopay\Services\PaymentService;
use D4rk0s\Mangopay\Services\WalletService;
use Illuminate\Http\Request;
use MangoPay\CardRegistrationStatus;
use MangoPay\Money;

class CardRegistrationCallback
{
    public function __invoke(Request $request)
    {
        $mangopayPayment = MangopayPayment::load();

        if($request->errorCode) {
            $mangopayErrorEnum = MangopayErrorEnum::tryFrom($request->errorCode);
            if(is_null($mangopayErrorEnum)) {
                $errorMessage = __("mangopay.error.unknown", ['errorCode' => $request->errorCode]);
            } else {
                $errorMessage = $mangopayErrorEnum->getErrorMessage();
            }

            CardRegistrationFailure::dispatch($request->errorCode, $errorMessage);
            MangopayPayment::remove();

            return redirect()->route(config('mangopay.cardInformationRoute'), ['locale'=>App()->getLocale()])
                ->withErrors($errorMessage);
        }

        if(is_null($request->data)) {
            abort(403);
        }

        $cardRegistration = CardService::updateCardRegistration($mangopayPayment->getCardRegistration(), $request->data);

        if($cardRegistration->Status !== CardRegistrationStatus::Validated || !isset($cardRegistration->CardId)) {
            CardRegistrationFailure::dispatch($cardRegistration->ResultCode, $cardRegistration->ResultMessage);
            MangopayPayment::remove();

            return redirect()->route(config('mangopay.cardInformationRoute'), ['locale'=>App()->getLocale()])
                ->withErrors($cardRegistration->ResultMessage);
        }

        CardRegistrationSuccessfull::dispatch($cardRegistration);

        // Construction de l'objet mangopay pour le paiement
        $debitedFunds = new Money();
        $debitedFunds->Amount = $mangopayPayment->getAmount();
        $debitedFunds->Currency = $mangopayPayment->getCurrency();

        $wallet = WalletService::getUserWallet($mangopayPayment->getUserId());
        $card = CardService::getUserCard($mangopayPayment->getUserId(), $request->cardId);

        return PaymentService::make(
            mangopayUserId: $mangopayPayment->getUserId(),
            walletId: $wallet->Id,
            payerCard: $card,
            debitedFunds: $debitedFunds
        );
    }
}