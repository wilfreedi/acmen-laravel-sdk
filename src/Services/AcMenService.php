<?php

namespace Wilfreedi\AcMen\Services;

use GuzzleHttp\Client;
use InvalidArgumentException;
use Wilfreedi\AcMen\Channels\EmailChannel;
use Wilfreedi\AcMen\Channels\TelegramChannel;
use Wilfreedi\AcMen\Channels\VkChannel;
use Wilfreedi\AcMen\Contracts\ChannelContract;
use Wilfreedi\AcMen\Jobs\AcMenJob;
use Wilfreedi\AcMen\Support\EndpointResolver;

class AcMenService
{
    private string $token;
    private bool $useQueue = false;
    private EndpointResolver $endpointResolver;
    /** @var array<string, ChannelContract> */
    private array $channels = [];

    public function __construct()
    {
        $this->token = (string) config('acmen.token', '');
        $this->endpointResolver = new EndpointResolver((array) config('acmen', []));
    }

    public function queue(): self
    {
        $clone = clone $this;
        $clone->useQueue = true;
        $clone->channels = [];

        return $clone;
    }

    public function channel(string $name): ChannelContract
    {
        if (isset($this->channels[$name])) {
            return $this->channels[$name];
        }

        $className = config("acmen.channels.{$name}");
        if (!is_string($className) || $className === '' || !class_exists($className)) {
            throw new InvalidArgumentException("Канал '{$name}' не зарегистрирован.");
        }

        $channel = new $className($this);
        if (!$channel instanceof ChannelContract) {
            throw new InvalidArgumentException("Канал '{$name}' должен реализовывать ChannelContract.");
        }

        $this->channels[$name] = $channel;

        return $channel;
    }

    public function telegram(): TelegramChannel
    {
        $channel = $this->channel('telegram');
        if (!$channel instanceof TelegramChannel) {
            throw new InvalidArgumentException('Канал telegram имеет неверный тип.');
        }

        return $channel;
    }

    public function vk(): VkChannel
    {
        $channel = $this->channel('vk');
        if (!$channel instanceof VkChannel) {
            throw new InvalidArgumentException('Канал vk имеет неверный тип.');
        }

        return $channel;
    }

    public function email(): EmailChannel
    {
        $channel = $this->channel('email');
        if (!$channel instanceof EmailChannel) {
            throw new InvalidArgumentException('Канал email имеет неверный тип.');
        }

        return $channel;
    }

    /**
     * @param int $chatId ид чата, для групп с -
     * @param string $message сообщение
     * @param int|null $topicId ид топика(темы), если есть
     * @return array
     */
    public function sendMessage(int $chatId, string $message, ?int $topicId = null): array
    {
        return $this->telegram()->sendMessage($chatId, $message, $topicId);
    }

    /**
     * @param int $peerId идентификатор диалога/чата VK
     * @param string $message текст сообщения
     * @param int|null $fromId ID VK-интеграции
     * @param int|null $randomId уникальный ID сообщения для дедупликации VK
     * @return array
     * @throws \Exception
     */
    public function sendVkMessage(int $peerId, string $message, ?int $fromId = null, ?int $randomId = null): array
    {
        return $this->vk()->sendMessage($peerId, $message, $fromId, $randomId);
    }

    /**
     * @param array<int, string> $to
     * @param array<int, string> $toHidden
     */
    public function sendEmail(
        array $to,
        array $toHidden = [],
        ?string $email = null,
        ?string $name = null,
        ?string $subject = null,
        ?string $message = null,
        ?string $attach = null
    ): array {
        return $this->email()->sendEmail($to, $toHidden, $email, $name, $subject, $message, $attach);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function sendToEndpoint(string $endpointKey, array $data, string $type = 'POST'): array
    {
        $url = $this->endpointResolver->resolve($endpointKey);

        return $this->sendRequest($url, $data, $type);
    }

    public function sendRequest(string $method, array $data = [], string $type = 'POST', ?string $url = null): array
    {
        $token = $this->token;

        if ($this->useQueue) {
            AcMenJob::dispatch($method, $data, $type, $token, $url);

            return [
                'success' => 1,
                'message' => 'Запрос отправлен в очередь'
            ];
        }

        return $this->request($method, $data, $type, $token, $url);
    }

    public function request(string $method, array $data, string $type, string $token, ?string $url = null): array
    {
        $rez = [
            'success' => 0,
            'message' => 'Ошибка'
        ];

        try {
            $requestUrl = $this->resolveRequestUrl($method, $url);

            $client = new Client();
            $headers = [
                'Authorization' => 'Bearer ' . $token,
                "Content-Type"  => "application/json",
                "Accept"        => "application/json"
            ];

            if ($type == 'GET') {
                $separator = str_contains($requestUrl, '?') ? '&' : '?';
                $response = $client->request('GET', $requestUrl . (count($data) ? $separator . http_build_query($data) : ''), [
                    'headers' => $headers,
                    'timeout' => config('acmen.timeout')
                ]);
            } else {
                $response = $client->request($type, $requestUrl, [
                    'headers'   => $headers,
                    'body'      => json_encode($data),
                    'timeout'   => config('acmen.timeout')
                ]);
            }

            $rez = json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if ($e instanceof \GuzzleHttp\Exception\RequestException && $e->hasResponse()) {
                $body = (string) $e->getResponse()->getBody();
                $decoded = json_decode($body, true);
                $message = is_array($decoded) ? ($decoded['message'] ?? $message) : $message;
            }

            $rez['message'] = $message;
        }

        return $rez;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    private function resolveRequestUrl(string $method, ?string $url): string
    {
        if (filter_var($method, FILTER_VALIDATE_URL)) {
            return $method;
        }

        $baseUrl = $url ?? config('acmen.url');
        if (!is_string($baseUrl) || $baseUrl === '') {
            throw new InvalidArgumentException('Не настроен URL API для запроса.');
        }

        return rtrim($baseUrl, '/') . '/' . ltrim($method, '/');
    }
}
