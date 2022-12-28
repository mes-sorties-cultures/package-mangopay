<?php

namespace D4rk0s\Mangopay\Models;

use MangoPay\CardRegistration;

class MangopayPaymentModel
{
    public const SESSION_MANGOPAY_ORDER = "mangopay_order";

    private string $userId;
    private CardRegistration $cardRegistration;
    private string $successPaymentRoute;
    private string $failurePaymentRoute;
    private string $cardDetailsRoute;
    private int $amount;
    private string $currency = "EUR";
    private string $transactionId;


    public function setUserId(string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function setCardRegistration(CardRegistration $cardRegistration): self
    {
        $this->cardRegistration = $cardRegistration;

        return $this;
    }

    public function setAmount(float $amount) : self
    {
        $this->amount = $amount * 100;

        return $this;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function setTransactionId(string $transactionId): self
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    public function setSuccessPaymentRoute(string $successPaymentRoute): self
    {
        $this->successPaymentRoute = $successPaymentRoute;

        return $this;
    }

    public function setFailurePaymentRoute(string $failurePaymentRoute): self
    {
        $this->failurePaymentRoute = $failurePaymentRoute;

        return $this;
    }

    public function setCardDetailsRoute(string $cardDetailsRoute): self
    {
        $this->cardDetailsRoute = $cardDetailsRoute;

        return $this;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getCardRegistration(): CardRegistration
    {
        return $this->cardRegistration;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getSuccessPaymentRoute(): string
    {
        return $this->successPaymentRoute;
    }

    public function getFailurePaymentRoute(): string
    {
        return $this->failurePaymentRoute;
    }

    public function getCardDetailsRoute(): string
    {
        return $this->cardDetailsRoute;
    }

    public static function load() : MangopayPaymentModel
    {
        if(!session()->has(self::SESSION_MANGOPAY_ORDER)) {
            throw new \Exception("Mangopay Order not found");
        }

        return session()->get(self::SESSION_MANGOPAY_ORDER);
    }

    public function save() : void
    {
        session()->put(self::SESSION_MANGOPAY_ORDER, $this);
    }

    public static function remove() : void
    {
        session()->forget(self::SESSION_MANGOPAY_ORDER);
    }

}