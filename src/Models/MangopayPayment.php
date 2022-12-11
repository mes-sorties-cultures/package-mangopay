<?php

namespace D4rk0s\Mangopay\Models;

use MangoPay\CardRegistration;

class MangopayPayment
{
    public const SESSION_MANGOPAY_ORDER = "mangopay_order";

    private string $userId;
    private CardRegistration $cardRegistration;
    private int $amount;
    private string $currency = "EUR";
    private string $transactionId;

    public function setUserId(string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function setCardRegistration(CardRegistration $cardRegistrationId): self
    {
        $this->cardRegistrationId = $cardRegistrationId;

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

    public static function load() : MangopayPayment
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