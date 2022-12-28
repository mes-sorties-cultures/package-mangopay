<?php

namespace D4rk0s\Mangopay\Controllers;

use D4rk0s\Mangopay\Events\PaymentFailure;
use D4rk0s\Mangopay\Events\PaymentSuccessfull;
use D4rk0s\Mangopay\Models\MangopayPaymentModel;
use D4rk0s\Mangopay\Services\PaymentService;
use Illuminate\Http\Request;
use MangoPay\PayInStatus;

class Card3DS2Callback
{
    public function __invoke(Request $request)
    {
        $mangopayPayment = MangopayPaymentModel::load();

        if($mangopayPayment->getTransactionId() !== $request->transactionId) {
            abort(403);
        }

        // On check si la transaction est ok
        $payIn = PaymentService::retrievePayIn($request->transactionId);

        if($payIn->Status === PayInStatus::Succeeded) {
            PaymentSuccessfull::dispatch(
              $payIn->DebitedFunds->Amount,
              $payIn->Id,
              $payIn->AuthorId
            );

            return redirect()->route($mangopayPayment->getSuccessPaymentRoute(), ['locale' => App()->getLocale()]);
        }

        PaymentFailure::dispatch(
          $payIn->DebitedFunds->Amount,
          $payIn->Id,
          $payIn->AuthorId,
          $payIn->ResultCode
        );

        return redirect()->route($mangopayPayment->getFailurePaymentRoute(), ['locale' => App()->getLocale()])
            ->withErrors($payIn->ResultCode);
    }

}