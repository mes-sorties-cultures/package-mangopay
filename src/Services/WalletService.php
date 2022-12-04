<?php

namespace D4rk0s\Mangopay\Services;

use MangoPay\User;
use MangoPay\Wallet;

class WalletService extends MangopaySDK
{
    public static function createWalletForUser(User $user)
    {
        // Création de son wallet
        $mangopayUserWallet = new Wallet();
        $mangopayUserWallet->Owners = [$user->Id];
        $mangopayUserWallet->Currency = "EUR";

        self::getSDK()->Wallets->create($mangopayUserWallet);
    }

    public static function getUserWallet(string $mangopayUserId, ?string $userWalletId = null) : Wallet
    {
        $wallets = self::getSDK()->Users->GetWallets($mangopayUserId);
        $numWallets = count($wallets);

        if($numWallets === 0) {
            throw new \Exception("No wallets found for this user (".$mangopayUserId.")");
        }

        if(is_null($userWalletId)) {
            if($numWallets === 1)  {
                return current($wallets);
            }
            throw new \Exception("Multiple wallets found and no userWalletId specified.");
        }

        foreach($wallets as $wallet) {
            if($wallet->Id === $userWalletId) {
                return $wallet;
            }
        }

        throw new \Exception("No wallets with id (".$userWalletId.") found for this user (".$mangopayUserId.")");
    }
}