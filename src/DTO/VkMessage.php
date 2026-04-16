<?php

namespace Wilfreedi\AcMen\DTO;

use Wilfreedi\AcMen\Contracts\MessagePayload;

final class VkMessage implements MessagePayload
{
    public function __construct(
        private int $peerId,
        private string $message,
        private ?int $fromId = null,
        private ?int $randomId = null
    ) {
        if (is_null($this->randomId)) {
            $this->randomId = random_int(1, min(PHP_INT_MAX, 2147483647));
        }
    }

    public function toArray(): array
    {
        $payload = [
            'peer_id'   => $this->peerId,
            'message'   => $this->message,
            'random_id' => $this->randomId,
        ];

        if (!is_null($this->fromId)) {
            $payload['from_id'] = $this->fromId;
        }

        return $payload;
    }
}
