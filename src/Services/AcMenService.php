<?php

namespace Wilfreedi\AcMen\Services;

use GuzzleHttp\Client;
use Wilfreedi\AcMen\Jobs\AcMenJob;

class AcMenService
{

    private string $token;

    private bool $useQueue = false;


    public function __construct() {
        $this->token = config('acmen.token');
    }

    public function queue(): self {
        $this->useQueue = true;
        return $this;
    }

    /**
     * @param int $chatId ид чата, для групп с -
     * @param string $message сообщение
     * @param int|null $topicId ид топика(темы), если есть
     * @return array
     */
    public function sendMessage(int $chatId, string $message, ?int $topicId = null): array {
        $data = [
            'chat_id' => $chatId,
            'message' => $message
        ];
        if ($topicId) {
            $data['message_thread_id'] = $topicId;
        }
        return $this->sendRequest('sendMessage', $data);
    }

    public function sendRequest(string $method, array $data = [], string $type = 'POST'): array {
        $token = $this->token;
        if($this->useQueue) {
            AcMenJob::dispatch($method, $data, $type, $token);
            return [
                'success' => 1,
                'message' => 'Запрос отправлен в очередь'
            ];
        } else {
            return $this->request($method, $data, $type, $token);
        }
    }

    public function request(string $method, array $data, string $type, string $token): array {
        $rez = [
            'success' => 0,
            'message' => 'Ошибка'
        ];
        try {
            $url = config('acmen.url');

            $client = new Client();
            $headers = [
                'Authorization' => 'Bearer ' . $token,
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

            if ($e instanceof \GuzzleHttp\Exception\RequestException && $e->hasResponse()) {
                $body = (string) $e->getResponse()->getBody();
                $decoded = json_decode($body, true);
                $message = is_array($decoded) ? ($decoded['message'] ?? $message) : $message;
            }

            $rez['message'] = $message;
        }
        return $rez;
    }

    public function setToken(string $token): void {
        $this->token = $token;
    }

}
