<?php

namespace D4rk0s\Mangopay\Services;

use MangoPay\Libraries\Configuration;
use MangoPay\MangoPayApi;

use function env;

abstract class MangopaySDK
{
    protected static function getSDK()
    {
        $api = new MangoPayApi();
        $api->Config->ClientId = env('MANGOPAY_CLIENT_ID');
        $api->Config->ClientPassword = env('MANGOPAY_CLIENT_PASSWORD');
        $api->Config->TemporaryFolder = env('MANGOPAY_TEMP_DIR');

        return $api;
    }
}