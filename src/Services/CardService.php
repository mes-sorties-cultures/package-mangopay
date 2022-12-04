<?php

namespace D4rk0s\Mangopay\Services;

use MangoPay\CardRegistration;

class CardService extends MangopaySDK
{
    public static function createCardRegistration(
      int $mangopayUserId,
      string $currency = "EUR",
      string $cardType = "CB_VISA_MASTERCARD"
    ) : ?CardRegistration
    {
        $cardRegistration = new CardRegistration();
        $cardRegistration->UserId = $mangopayUserId;
        $cardRegistration->Currency = $currency;
        $cardRegistration->CardType = $cardType;

        return self::getSDK()->CardRegistrations->Create(cardRegistration: $cardRegistration);
    }

    public static function updateCardRegistration(
      string $cardRegistrationId,
      string $registrationData
    ) : CardRegistration
    {
        $cardRegistration = self::getSDK()->CardRegistrations->Get(cardRegistrationId: $cardRegistrationId);
        if(is_null($cardRegistration)) {
            throw new \Exception("Unable to retrieve the CardRegistration object with id : ".$cardRegistrationId);
        }
        $cardRegistration->RegistrationData = $registrationData;

        return self::getSDK()->CardRegistrations->Update(cardRegistration: $cardRegistration);
    }
}