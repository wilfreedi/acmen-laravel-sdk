<?php

namespace Wilfreedi\AcMen\Channels;

use Wilfreedi\AcMen\Contracts\ChannelContract;
use Wilfreedi\AcMen\Contracts\MessagePayload;
use Wilfreedi\AcMen\Services\AcMenService;

abstract class AbstractChannel implements ChannelContract
{
    protected AcMenService $service;

    public function __construct(AcMenService $service)
    {
        $this->service = $service;
    }

    abstract protected function endpointKey(): string;

    /**
     * @param MessagePayload|array<string, mixed> $payload
     */
    protected function post(MessagePayload|array $payload): array
    {
        $data = $payload instanceof MessagePayload ? $payload->toArray() : $payload;

        return $this->service->sendToEndpoint($this->endpointKey(), $data);
    }
}
