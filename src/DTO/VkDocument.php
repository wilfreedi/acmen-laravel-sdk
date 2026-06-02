<?php

namespace Wilfreedi\AcMen\DTO;

use Wilfreedi\AcMen\Contracts\MessagePayload;

final class VkDocument implements MessagePayload
{
    public function __construct(
        private int $peerId,
        private string $file,
        private ?string $message = null,
        private ?int $fromId = null
    ) {
        if (!file_exists($this->file)) {
            throw new \InvalidArgumentException("Файл '{$this->file}' не найден.");
        }
    }

    public function getPeerId(): int
    {
        return $this->peerId;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getFromId(): ?int
    {
        return $this->fromId;
    }

    public function toArray(): array
    {
        $payload = [
            'peer_id' => $this->peerId,
            'file'    => $this->file,
        ];

        if (!is_null($this->message)) {
            $payload['message'] = $this->message;
        }

        if (!is_null($this->fromId)) {
            $payload['from_id'] = $this->fromId;
        }

        return $payload;
    }
}
