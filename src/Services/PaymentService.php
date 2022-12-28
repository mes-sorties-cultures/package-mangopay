<?php

namespace D4rk0s\Mangopay\Services;

use D4rk0s\Mangopay\Events\PaymentFailure;
use D4rk0s\Mangopay\Events\PaymentSuccessfull;
use D4rk0s\Mangopay\Models\MangopayPaymentModel;
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

        $mangopayPayment = MangopayPaymentModel::load();

        $payIn = self::getSDK()->PayIns->Create($payIn);
        if($payIn->Status === PayInStatus::Failed) {
            PaymentFailure::dispatch(
              $payIn->DebitedFunds->Amount,
              $payIn->Id,
              $mangopayUserId,
              $payIn->ResultCode
            );

            return redirect()->route($mangopayPayment->getFailurePaymentRoute(), ['locale' => App()->getLocale()])
                ->withErrors($payIn->ResultCode);

        }

        $mangopayPayment = MangopayPaymentModel::load();
        $mangopayPayment
            ->setTransactionId($payIn->Id)
            ->save();
        
        if ($payIn->ExecutionDetails->SecureModeNeeded === true) {
            return redirect($payIn->ExecutionDetails->SecureModeRedirectURL);
        }

        PaymentSuccessfull::dispatch(
            $payIn->DebitedFunds->Amount,
            $payIn->Id,
            $mangopayUserId
        );

        return redirect()->route($mangopayPayment->getFailurePaymentRoute(), ['locale' => App()->getLocale()]);
    }

    public static function retrievePayIn(string $payInId) : PayIn
    {
        $payIn = self::getSDK()->PayIns->Get($payInId);
        if(is_null($payIn)) {
            abort(403);
        }

        return $payIn;
    }
}