<?php

namespace Wilfreedi\AcMen\Services;

use GuzzleHttp\Client;

class AcMenService
{

    private string $token;
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
