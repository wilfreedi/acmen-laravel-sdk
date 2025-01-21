<?php

namespace Wilfreedi\AcMen;

use Illuminate\Support\ServiceProvider;

class AcMenServiceProvider extends ServiceProvider
{
    public function register() {
        // Объединяем конфигурацию с конфигами Laravel
        $this->mergeConfigFrom(__DIR__ . '/../config/acmen.php', 'acmen');

        $this->app->singleton('acmen', function ($app) {
            return new \Wilfreedi\AcMen\Services\AcMenService(config('acmen'));
        });

    }

    public function boot() {
        // Публикуем конфигурацию для переопределения пользователем
        $this->publishes([
            __DIR__ . '/../config/acmen.php' => config_path('acmen.php'),
        ], 'config');
    }
}
