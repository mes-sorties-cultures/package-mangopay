<?php

namespace D4rk0s\Mangopay\Services;

use MangoPay\Card;
use MangoPay\CardRegistration;
use MangoPay\Pagination;

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
      CardRegistration $cardRegistration,
      string $registrationData
    ) : CardRegistration
    {
        $cardRegistration->RegistrationData = "data=".$registrationData;

        return self::getSDK()->CardRegistrations->Update(cardRegistration: $cardRegistration);
    }

    public static function getUserCard(string $mangopayUserId, ?string $userCardId = null) : Card
    {
        if($userCardId) {
            $card = self::getSDK()->Cards->Get($userCardId);
            if($card === null) {
                throw new \Exception("No card with id (".$userCardId.") found for this user (".$mangopayUserId.")");
            }

            return $card;
        }

        $pagination = new Pagination(1, 100);
        $cards = self::getSDK()->Users->GetCards($mangopayUserId, $pagination);
        $activeCards = array_filter($cards, function(Card $card) { return $card->Active === true;});
        $numActiveCards = count($activeCards);

        if($numActiveCards === 0) {
            throw new \Exception("No active cards found for this user (".$mangopayUserId.")");
        }

        if($numActiveCards === 1)  {
            return current($activeCards);
        }

        throw new \Exception("Multiple active cards found and no userCardId specified.");
    }
}