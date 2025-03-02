<?php

namespace Wilfreedi\AcMen\Services;

use GuzzleHttp\Client;
use Wilfreedi\AcMen\Jobs\AcMenJob;

class AcMenService
{

    private string $token;
    private string $chatId;
    private ?string $message = null;
    private ?string $topicId = null;

    private bool $useQueue = false;


    public function __construct() {
        $this->token = config('acmen.token');
    }

    public function chat(int $chatId): self {
        $this->chatId = $chatId;
        return $this;
    }

    public function message(string $message): self {
        $this->message = $message;
        return $this;
    }

    public function topic(int $topicId): self {
        $this->topicId = $topicId;
        return $this;
    }

    public function queue(): void {
        $this->useQueue = true;
    }

    public function sendMessage(): array {
        $data = [
            'chat_id' => $this->chatId,
            'message' => $this->message
        ];
        if ($this->topicId) {
            $data['message_thread_id'] = $this->topicId;
        }
        return $this->sendRequest('sendMessage', $data);
    }

    public function sendRequest(string $method, array $data = [], string $type = 'POST'): array {
        if($this->useQueue) {
            AcMenJob::dispatch($method, $data, $type);
            return [
                'success' => 1,
                'message' => 'Запрос отправлен в очередь'
            ];
        } else {
            return $this->request($method, $data, $type);
        }
    }

    public function request(string $method, array $data = [], string $type = 'POST'): array {
        $rez = [
            'success' => 0,
            'message' => 'Ошибка'
        ];
        try {
            $url = config('acmen.url');

            $client = new Client();
            $headers = [
                'Authorization' => 'Bearer ' . $this->token,
                "Content-Type"  => "application/json",
                "Accept"        => "application/json"
            ];

            if ($type == 'GET') {
                $response = $client->request('GET', $url . $method . (count($data) ? '?' . http_build_query($data) : ''), [
                    'headers' => $headers,
                    'timeout' => config('acmen.timeout')
                ]);
            } else {
                $response = $client->request($type, $url . $method, [
                    'headers'   => $headers,
                    'body'      => json_encode($data),
                    'timeout'   => config('acmen.timeout')
                ]);

            }

            $rez = json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if ($e->hasResponse()) {
                $message = $e->getResponse()->getBody()->getContents();
                $message = json_decode($message, true);
                $message = $message['message'] ?? $e->getMessage();
            }
            $rez['message'] = $message;
        }
        return $rez;
    }

    public function setToken(string $token): void {
        $this->token = $token;
    }

}
