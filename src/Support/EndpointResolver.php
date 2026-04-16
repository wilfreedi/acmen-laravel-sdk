<?php

namespace Wilfreedi\AcMen\Support;

use InvalidArgumentException;

final class EndpointResolver
{
    public function __construct(private array $config)
    {
    }

    public function resolve(string $endpointKey): string
    {
        $legacyUrl = $this->resolveLegacyUrl($endpointKey);
        if (!is_null($legacyUrl)) {
            return $legacyUrl;
        }

        $endpoint = $this->config['endpoints'][$endpointKey] ?? null;
        if (!is_string($endpoint) || $endpoint === '') {
            throw new InvalidArgumentException("Не найден endpoint для ключа: {$endpointKey}");
        }

        $baseUrl = $this->config['base_url'] ?? null;
        if (!is_string($baseUrl) || $baseUrl === '') {
            throw new InvalidArgumentException('Не задан ACMEN_API_BASE_URL.');
        }

        return $this->joinUrl($baseUrl, $endpoint);
    }

    private function resolveLegacyUrl(string $endpointKey): ?string
    {
        if ($endpointKey === 'telegram.send_message') {
            return $this->legacyMethodUrl($this->config['url'] ?? null, 'sendMessage');
        }

        if ($endpointKey === 'vk.send_message') {
            return $this->legacyMethodUrl($this->config['vk_url'] ?? null, 'sendMessage');
        }

        if ($endpointKey === 'email.send') {
            $emailUrl = $this->config['email_url'] ?? null;
            return is_string($emailUrl) && $emailUrl !== '' ? rtrim($emailUrl, '/') : null;
        }

        return null;
    }

    private function legacyMethodUrl(?string $baseUrl, string $method): ?string
    {
        if (!is_string($baseUrl) || $baseUrl === '') {
            return null;
        }

        return $this->joinUrl($baseUrl, $method);
    }

    private function joinUrl(string $baseUrl, string $path): string
    {
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}
