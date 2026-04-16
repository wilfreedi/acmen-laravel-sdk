<?php

return [
    // Единый базовый URL API (новая схема для всех каналов)
    'base_url' => env('ACMEN_API_BASE_URL', 'https://acmen.ru/api/v1'),

    // Legacy URL (сохранены для обратной совместимости)
    'url'       => env('ACMEN_API_URL', 'https://acmen.ru/api/v1/telegram/'),
    'vk_url'    => env('ACMEN_VK_API_URL', 'https://acmen.ru/api/v1/vk/'),
    'email_url' => env('ACMEN_EMAIL_API_URL', 'https://acmen.ru/api/v1/email'),

    // Маршруты для универсальной маршрутизации по каналам
    'endpoints' => [
        'telegram.send_message' => '/telegram/sendMessage',
        'vk.send_message'       => '/vk/sendMessage',
        'email.send'            => '/email',
    ],

    // Реестр каналов для расширяемости (например, будущий max)
    'channels' => [
        'telegram' => \Wilfreedi\AcMen\Channels\TelegramChannel::class,
        'vk'       => \Wilfreedi\AcMen\Channels\VkChannel::class,
        'email'    => \Wilfreedi\AcMen\Channels\EmailChannel::class,
    ],

    'token'   => env('ACMEN_API_TOKEN'),
    'timeout' => env('ACMEN_API_TIMEOUT', 10),
];
