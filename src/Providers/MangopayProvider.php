<?php

namespace D4rk0s\Mangopay\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class MangopayProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__."/../Routes/web.php");
        $this->loadViewsFrom(__DIR__."/../Views","mangopay");
        $this->publishes([
                           __DIR__.'/../Views' => resource_path('views/vendor/mangopay'),
                         ]);
        $this->loadTranslationsFrom(__DIR__."/../Lang", "mangopay");
        $this->publishes([
                           __DIR__.'/../Lang' => $this->app->langPath('vendor/mangopay'),
                         ]);
        Blade::componentNamespace('D4rk0s\\Mangopay\\Components', 'mangopay');
    }
}