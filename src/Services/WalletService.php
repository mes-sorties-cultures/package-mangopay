<?php

namespace D4rk0s\Mangopay\Services;

use MangoPay\Money;
use MangoPay\Transfer;
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
        if($userWalletId) {
            $wallet = self::getSDK()->Wallets->Get($userWalletId);
            if($wallet === null) {
                throw new \Exception("No wallet with id (".$userWalletId.") found for this user (".$mangopayUserId.")");
            }

            return $wallet;
        }

        $wallets = self::getSDK()->Users->GetWallets($mangopayUserId);
        $numWallets = count($wallets);

        if($numWallets === 0) {
            throw new \Exception("No wallets found for this user (".$mangopayUserId.")");
        }

        if($numWallets === 1)  {
            return current($wallets);
        }

        throw new \Exception("Multiple wallets found and no userWalletId specified.");
    }

    public static function transfertMoneyBetweenWallets(
        string $mangopayUserId,
        string $walletIdFrom,
        string $walletIdTo,
        string $amount,
        int $feeAmount = 0,
        string $currency = "EUR",
        string $metadata = ""
    ) {
        $debitedFunds = new Money();
        $debitedFunds->Currency = $currency;
        $debitedFunds->Amount = $amount * 100;

        $fees = new Money();
        $fees->Currency = $currency;
        $fees->Amount = $feeAmount * 100;

        $transfert = new Transfer();
        $transfert->AuthorId = $mangopayUserId;
        $transfert->DebitedFunds = $debitedFunds;
        $transfert->Fees = $fees;
        $transfert->CreditedWalletId = $walletIdTo;
        $transfert->DebitedWalletId = $walletIdFrom;
        $transfert->Tag = $metadata;

        return self::getSDK()->Transfers->Create($transfert);
    }
}