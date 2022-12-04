<?php

namespace D4rk0s\Mangopay\Services;

use App\Models\Customer;
use MangoPay\UserNatural;

class UserService extends MangopaySDK
{
    public static function createNaturalPayerUser(Customer $customer) : UserNatural
    {
        $user = $customer->user;
        $sdk = self::getSDK();

        $mangopayUser = new UserNatural();
        $mangopayUser->FirstName = $user->firstname;
        $mangopayUser->LastName = $user->lastname;
        $mangopayUser->Email = $user->email;
        $mangopayUser->UserCategory = 'PAYER';
        $mangopayUser->TermsAndConditionsAccepted = true;
        $mangopayUser = $sdk->Users->Create($mangopayUser);

        $user->mangopay_user_id = $mangopayUser->Id;
        $user->save();

        return $mangopayUser;
    }

    /** Later... */
    public static function createLegalOwnerUser(Customer $customer)
    {

    }
}