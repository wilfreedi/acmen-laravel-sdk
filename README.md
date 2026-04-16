# AcMen Laravel SDK

SDK для отправки сообщений через коммуникационные каналы AcMen API.

Поддерживаемые каналы:
- Telegram
- VK
- Email

Архитектура SDK построена как расширяемый реестр каналов (`channel registry`), поэтому новые каналы (например, `max`) добавляются без переписывания ядра.

## Установка

```bash
composer require wilfreedi/acmen-laravel-sdk
```

## Публикация конфига

```bash
php artisan vendor:publish --provider="Wilfreedi\AcMen\AcMenServiceProvider"
```

## Конфигурация

```php
return [
    // Новая универсальная схема
    'base_url' => env('ACMEN_API_BASE_URL', 'https://acmen.ru/api/v1'),

    // Legacy (для обратной совместимости)
    'url'       => env('ACMEN_API_URL', 'https://acmen.ru/api/v1/telegram/'),
    'vk_url'    => env('ACMEN_VK_API_URL', 'https://acmen.ru/api/v1/vk/'),
    'email_url' => env('ACMEN_EMAIL_API_URL', 'https://acmen.ru/api/v1/email'),

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

    'token'   => env('ACMEN_API_TOKEN'),
    'timeout' => env('ACMEN_API_TIMEOUT', 10),
];
```

## Использование

### 1) Универсальный доступ по каналу

```php
use Wilfreedi\AcMen\Facades\AcMen;

AcMen::channel('telegram')->sendMessage(-1001234567890, 'Привет');
AcMen::channel('vk')->sendMessage(2000000015, 'Привет из VK');
AcMen::channel('email')->sendEmail(['user@example.com'], subject: 'Тест', message: '<b>Hello</b>');
```

### 2) Удобные shortcut-методы

```php
use Wilfreedi\AcMen\Facades\AcMen;

// Telegram
$telegramResponse = AcMen::sendMessage(
    chatId: -1001234567890,
    message: 'Привет из Telegram API',
    topicId: 15
);

// VK
$vkResponse = AcMen::sendVkMessage(
    peerId: 2000000015,
    message: 'Привет из VK API',
    fromId: 1,
    randomId: 123456 // optional, если не передан, генерируется автоматически
);

// Email
$emailResponse = AcMen::sendEmail(
    to: ['user1@example.com', 'user2@example.com'],
    toHidden: ['audit@example.com'],
    email: 'bot@example.com',
    name: 'Support Bot',
    subject: 'Тест',
    message: '<b>Hello</b>',
    attach: 'https://example.com/file.pdf'
);
```

### 3) Отправка через очередь

```php
use Wilfreedi\AcMen\Facades\AcMen;

AcMen::queue()->vk()->sendMessage(2000000015, 'Сообщение через очередь');
```

## Контракты API

### VK: POST `/api/v1/vk/sendMessage`

Успешный ответ:

```json
{
  "success": true,
  "data": 981,
  "message": "VK сообщение отправлено"
}
```

Ошибки:
- `403`: нет доступных VK ботов
- `403`: VK бот не найден
- `403`: VK бот отключен
- `400`: ошибка отправки от VK API

### Email: POST `/api/v1/email`

Тело запроса:
- `email` (string, optional): username конкретного Email-аккаунта
- `to` (array, required): получатели
- `to_hidden` (array, optional): BCC
- `name` (string, optional): имя отправителя
- `subject` (string, optional): тема письма
- `message` (string, optional): HTML-тело
- `attach` (string, optional): URL вложения

Успешный ответ:

```json
{
  "success": true,
  "data": [],
  "message": "Email отправлен"
}
```

Ошибки:
- `403`: нет привязанных email-каналов к API токену
- `403`: выбранный email не найден

Примечание: `subject` и `message` формально optional, но в рабочем сценарии рекомендуется всегда передавать их строками.

## Расширение под будущий канал (например, max)

1. Создать класс канала (по аналогии с `VkChannel`/`EmailChannel`).
2. Добавить endpoint в `acmen.endpoints`.
3. Зарегистрировать канал в `acmen.channels`.
4. Использовать `AcMen::channel('max')`.
