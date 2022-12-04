<?php

use D4rk0s\Mangopay\Controllers\Card3DS2Callback;
use D4rk0s\Mangopay\Controllers\CardRegistrationCallback;
use Illuminate\Support\Facades\Route;

Route::get('mangopay/card-registration-callback', CardRegistrationCallback::class)
    ->name('mangopay-cardRegistrationCallback');

Route::get('mangopay/3ds2Callback', Card3DS2Callback::class)
    ->name("mangopay-3ds2Callback");