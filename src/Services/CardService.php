<?php

namespace D4rk0s\Mangopay\Services;

use MangoPay\Card;
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

    public static function getUserCard(string $mangopayUserId, ?string $userCardId = null) : Card
    {
        $cards = self::getSDK()->Users->GetCards($mangopayUserId);
        $activeCards = array_filter($cards, function(Card $card) { return $card->Active === true;});
        $numActiveCards = count($activeCards);

        if($numActiveCards === 0) {
            throw new \Exception("No active cards found for this user (".$mangopayUserId.")");
        }

        if(is_null($userCardId)) {
            if($numActiveCards === 1)  {
                return current($activeCards);
            }
            throw new \Exception("Multiple active cards found and no userCardId specified.");
        }

        foreach($activeCards as $card) {
            if($card->Id === $userCardId) {
                return $card;
            }
        }

        throw new \Exception("No card with id (".$userCardId.") found for this user (".$mangopayUserId.")");
    }
}