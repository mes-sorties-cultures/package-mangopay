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
        $config = new Configuration();
        $config->ClientId = env('MANGOPAY_CLIENT_ID');
        $config->ClientPassword = env('MANGOPAY_CLIENT_PASSWORD');
        $config->TemporaryFolder = env('MANGOPAY_TEMP_DIR');
        $api->setConfig($config);

        return $api;
    }
}