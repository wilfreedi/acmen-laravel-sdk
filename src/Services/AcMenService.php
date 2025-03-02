<?php

namespace Wilfreedi\AcMen\Services;

use App\Jobs\AcMenJob;
use GuzzleHttp\Client;

class AcMenService
{

    private string $token;
    private bool $useQueue = false;

    public function __construct() {
        $this->token = config('acmen.token');
    }

    public function sendMessage(int $chatId, string $message, int $topicId = null): array {
        $data = [
            'chat_id' => $chatId,
            'message' => $message
        ];
        if ($topicId) {
            $data['message_thread_id'] = $topicId;
        }
        return $this->request('sendMessage', $data);
    }

    public function sendRequest(string $method, array $data = [], string $type = 'POST') {
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

    public function queue(): void {
        $this->useQueue = true;
    }

}
