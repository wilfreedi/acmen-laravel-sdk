<?php

namespace Wilfreedi\AcMen\Test;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Wilfreedi\AcMen\AcMenServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    // Регистрируем сервис-провайдер
    protected function getPackageProviders($app) {
        return [
            AcMenServiceProvider::class,
        ];
    }

    // Регистрируем фасад
    protected function getPackageAliases($app) {
        return [
            'AcMen' => \Wilfreedi\AcMen\Facades\AcMen::class,
        ];
    }

    // Настраиваем конфигурацию для тестов
    protected function getEnvironmentSetUp($app) {
        $app['config']->set('acmen', [
            'url'     => 'https://acmen.ru/api/v1/telegram/',
            'token'   => 'test',
            'timeout' => 10,
        ]);
    }
}
