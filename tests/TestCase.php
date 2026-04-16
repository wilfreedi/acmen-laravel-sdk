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
            'base_url' => 'https://acmen.ru/api/v1',
            'url'      => 'https://acmen.ru/api/v1/telegram/',
            'vk_url'   => 'https://acmen.ru/api/v1/vk/',
            'email_url'=> 'https://acmen.ru/api/v1/email',
            'endpoints' => [
                'telegram.send_message' => '/telegram/sendMessage',
                'vk.send_message'       => '/vk/sendMessage',
                'email.send'            => '/email',
            ],
            'channels' => [
                'telegram' => \Wilfreedi\AcMen\Channels\TelegramChannel::class,
                'vk'       => \Wilfreedi\AcMen\Channels\VkChannel::class,
                'email'    => \Wilfreedi\AcMen\Channels\EmailChannel::class,
            ],
            'token'    => 'test',
            'timeout'  => 10,
        ]);
    }
}
