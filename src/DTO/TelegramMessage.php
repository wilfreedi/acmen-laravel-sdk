<?php

namespace Wilfreedi\AcMen\DTO;

use Wilfreedi\AcMen\Contracts\MessagePayload;

final class TelegramMessage implements MessagePayload
{
    public function __construct(
        private int $chatId,
        private string $message,
        private ?int $topicId = null
    ) {
    }

    public function toArray(): array
    {
        $payload = [
            'chat_id' => $this->chatId,
            'message' => $this->message,
        ];

        if (!is_null($this->topicId)) {
            $payload['message_thread_id'] = $this->topicId;
        }

        return $payload;
    }
}
