<?php

namespace Wilfreedi\AcMen\Channels;

use Wilfreedi\AcMen\DTO\VkMessage;

final class VkChannel extends AbstractChannel
{
    public function name(): string
    {
        return 'vk';
    }

    protected function endpointKey(): string
    {
        return 'vk.send_message';
    }

    public function send(VkMessage $message): array
    {
        return $this->post($message);
    }

    /**
     * @throws \Exception
     */
    public function sendMessage(int $peerId, string $message, ?int $fromId = null, ?int $randomId = null): array
    {
        return $this->send(new VkMessage($peerId, $message, $fromId, $randomId));
    }
}
