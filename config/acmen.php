<?php

return [
    'url'     => env('ACMEN_API_URL', 'https://acmen.ru/api/v1/telegram'), // URL внешнего API
    'token'   => env('ACMEN_API_TOKEN', null), // Токен для авторизации
    'timeout' => env('ACMEN_API_TIMEOUT', 10), // Таймаут запросов (в секундах)
];
