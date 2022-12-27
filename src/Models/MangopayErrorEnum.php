<?php

namespace D4rk0s\Mangopay\Models;

enum MangopayErrorEnum : string
{
    case INVALID_CARD_NUMBER = "02625";
    case INVALID_DATE = "02626";
    case INVALID_CCV_NUMBER = "02627";
    case TOKENISATION_SERVER_ERROR = "02101";
    case INACTIVE_CARD = "01902";
    case EXPIRED_CARD = "02624";
    case TIMEOUT = "02631";

    public function getErrorMessage()
    {
        return match($this)
        {
            self::INVALID_CARD_NUMBER => __('mangopay::messages.error.invalid_card_number'),
            self::INVALID_DATE => __('mangopay::messages.error.invalid_date'),
            self::INVALID_CCV_NUMBER => __('mangopay::messages.error.invalid_cvv'),
            self::TOKENISATION_SERVER_ERROR => __('mangopay::messages.error.tokenisation_server_failure'),
            self::INACTIVE_CARD => __('mangopay::messages.error.inactive_card'),
            self::EXPIRED_CARD => __('mangopay::messages.error.expired_card'),
            self::TIMEOUT => __('mangopay::messages.error.timeout')
        };
    }
}