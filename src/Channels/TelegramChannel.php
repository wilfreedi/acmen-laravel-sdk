<?php

namespace Wilfreedi\AcMen\Channels;

use Wilfreedi\AcMen\DTO\TelegramMessage;

final class TelegramChannel extends AbstractChannel
{
    public function name(): string
    {
        return 'telegram';
    }

    protected function endpointKey(): string
    {
        return 'telegram.send_message';
    }

    public function send(TelegramMessage $message): array
    {
        return $this->post($message);
    }

    public function sendMessage(int $chatId, string $message, ?int $topicId = null): array
    {
        return $this->send(new TelegramMessage($chatId, $message, $topicId));
    }
}
