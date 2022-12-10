<?php

namespace D4rk0s\Mangopay\Services;

use D4rk0s\Mangopay\Events\PaymentFailure;
use D4rk0s\Mangopay\Events\PaymentSuccessfull;
use Illuminate\Support\Str;
use MangoPay\BrowserInfo;
use MangoPay\Card;
use MangoPay\Money;
use MangoPay\PayIn;
use MangoPay\PayInExecutionDetailsDirect;
use MangoPay\PayInPaymentDetailsCard;
use MangoPay\PayInStatus;

class PaymentService extends MangopaySDK
{
    public const TRANSACTION_ID_IN_SESSION = 'transactionId';

    public static function make(
      string $mangopayUserId,
      string $walletId,
      Card $payerCard,
      Money $debitedFunds,
      ?Money $fees = null,
    )
    {
        $payIn = new PayIn();
        $payIn->AuthorId = $mangopayUserId;
        $payIn->CreditedWalletId = $walletId;
        $payIn->DebitedFunds = $debitedFunds;
        $payIn->PaymentDetails = new PayInPaymentDetailsCard();
        $payIn->PaymentDetails->CardType = $payerCard->CardType;
        $payIn->PaymentDetails->CardId = $payerCard->Id;
        $payIn->PaymentDetails->IpAddress = request()->ip();
        $payIn->PaymentDetails->BrowserInfo = new BrowserInfo;
        $payIn->PaymentDetails->BrowserInfo->AcceptHeader = implode(',',request()->getAcceptableContentTypes());
        $payIn->PaymentDetails->BrowserInfo->JavaEnabled = true;
        $payIn->PaymentDetails->BrowserInfo->Language = Str::limit(request()->server('HTTP_ACCEPT_LANGUAGE', 'fr'), 2, '');
        $payIn->PaymentDetails->BrowserInfo->ColorDepth = 32;
        $payIn->PaymentDetails->BrowserInfo->ScreenHeight = 1024;
        $payIn->PaymentDetails->BrowserInfo->ScreenWidth = 768;
        $payIn->PaymentDetails->BrowserInfo->TimeZoneOffset = '0';
        $payIn->PaymentDetails->BrowserInfo->UserAgent = request()->userAgent() ?: 'Unknown';
        $payIn->PaymentDetails->BrowserInfo->JavascriptEnabled = true;
        $payIn->ExecutionDetails = new PayInExecutionDetailsDirect();
        $payIn->ExecutionDetails->SecureModeReturnURL = route('mangopay-3ds2Callback');
        if(is_null($fees)) {
            $payIn->Fees = new Money();
            $payIn->Fees->Currency="EUR";
            $payIn->Fees->Amount = 0;
        } else {
            $payIn->Fees = $fees;
        }

        $payIn = self::getSDK()->PayIns->Create($payIn);
        if($payIn->Status === PayInStatus::Failed) {
            PaymentFailure::dispatch(
              $payIn->DebitedFunds->Amount,
              $payIn->Id,
              $mangopayUserId,
              $payIn->ResultCode
            );

            return redirect()->route(config('mangopay.paymentFailureRoute'))
                ->withErrors($payIn->ResultCode);

        }

        if ($payIn->ExecutionDetails->SecureModeNeeded === true) {
            request()->session()->put(self::TRANSACTION_ID_IN_SESSION, $payIn->Id);

            return redirect($payIn->ExecutionDetails->SecureModeRedirectURL);
        }

        PaymentSuccessfull::dispatch(
            $payIn->DebitedFunds->Amount,
            $payIn->Id,
            $mangopayUserId
        );

        return redirect()->route(config('mangopay.paymentSuccessRoute'));
    }

    public static function retrievePayIn(string $payInId) : ?PayIn
    {
        return self::getSDK()->PayIns->Get($payInId);
    }
}