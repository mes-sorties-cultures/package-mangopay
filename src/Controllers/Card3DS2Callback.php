<?php

namespace D4rk0s\Mangopay\Controllers;

use D4rk0s\Mangopay\Events\PaymentFailure;
use D4rk0s\Mangopay\Events\PaymentSuccessfull;
use D4rk0s\Mangopay\Services\PaymentService;
use Illuminate\Http\Request;
use MangoPay\PayInStatus;

class Card3DS2Callback
{
    public function __invoke(Request $request)
    {
        $transactionIdInSession = $request->session()->get(PaymentService::TRANSACTION_ID_IN_SESSION);
        $request->session()->forget(PaymentService::TRANSACTION_ID_IN_SESSION);

        if(is_null($transactionIdInSession) || $transactionIdInSession !== $request->transactionId) {
            abort(403);
        }

        // On check si la transaction est ok
        $payIn = PaymentService::retrievePayIn($request->transactionId);

        if($payIn->Status === PayInStatus::Succeeded) {
            return PaymentSuccessfull::dispatch(
              $payIn->DebitedFunds->Amount,
              $payIn->Id,
              $payIn->AuthorId
            );
        }

        return PaymentFailure::dispatch(
          $payIn->DebitedFunds->Amount,
          $payIn->Id,
          $payIn->AuthorId,
          $payIn->ResultCode
        );
    }

}