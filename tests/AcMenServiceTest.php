<?php

namespace Wilfreedi\AcMen\Test;

use InvalidArgumentException;
use Wilfreedi\AcMen\Channels\EmailChannel;
use Wilfreedi\AcMen\Channels\TelegramChannel;
use Wilfreedi\AcMen\Channels\VkChannel;
use Wilfreedi\AcMen\Services\AcMenService;

class AcMenServiceTest extends TestCase
{
    /** @test */
    public function it_resolves_service_from_container()
    {
        $service = $this->app->make('acmen');

        $this->assertInstanceOf(AcMenService::class, $service);
    }

    /** @test */
    public function it_resolves_registered_channels()
    {
        $service = new AcMenService();

        $this->assertInstanceOf(TelegramChannel::class, $service->telegram());
        $this->assertInstanceOf(VkChannel::class, $service->vk());
        $this->assertInstanceOf(EmailChannel::class, $service->email());
    }

    /** @test */
    public function it_uses_clone_for_queue_mode()
    {
        $service = new AcMenService();
        $queuedService = $service->queue();

        $this->assertNotSame($service, $queuedService);
        $this->assertInstanceOf(AcMenService::class, $queuedService);
    }

    /** @test */
    public function it_throws_for_unknown_channel()
    {
        $this->expectException(InvalidArgumentException::class);

        $service = new AcMenService();
        $service->channel('max');
    }

    /** @test */
    public function it_sends_telegram_message_via_legacy_endpoint_url()
    {
        $service = $this->fakeService();

        $service->sendMessage(12345, 'Telegram message', 15);

        $this->assertSame('https://acmen.ru/api/v1/telegram/sendMessage', $service->captured['method']);
        $this->assertSame('POST', $service->captured['type']);
        $this->assertSame('test', $service->captured['token']);
        $this->assertSame([
            'chat_id' => 12345,
            'message' => 'Telegram message',
            'message_thread_id' => 15,
        ], $service->captured['data']);
    }

    /** @test */
    public function it_sends_vk_message_with_all_parameters()
    {
        $service = $this->fakeService();

        $service->sendVkMessage(2000000015, 'Привет из VK API', 1, 123456);

        $this->assertSame('https://acmen.ru/api/v1/vk/sendMessage', $service->captured['method']);
        $this->assertSame([
            'peer_id'   => 2000000015,
            'message'   => 'Привет из VK API',
            'random_id' => 123456,
            'from_id'   => 1,
        ], $service->captured['data']);
    }

    /** @test */
    public function it_generates_random_id_for_vk_when_missing()
    {
        $service = $this->fakeService();

        $service->vk()->sendMessage(2000000015, 'Привет из VK API');

        $this->assertSame('https://acmen.ru/api/v1/vk/sendMessage', $service->captured['method']);
        $this->assertArrayHasKey('random_id', $service->captured['data']);
        $this->assertIsInt($service->captured['data']['random_id']);
        $this->assertGreaterThan(0, $service->captured['data']['random_id']);
        $this->assertArrayNotHasKey('from_id', $service->captured['data']);
    }

    /** @test */
    public function it_sends_email_with_documented_payload()
    {
        $service = $this->fakeService();

        $service->sendEmail(
            to: ['user1@example.com', 'user2@example.com'],
            toHidden: ['audit@example.com'],
            email: 'bot@example.com',
            name: 'Support Bot',
            subject: 'Тест',
            message: '<b>Hello</b>',
            attach: 'https://example.com/file.pdf'
        );

        $this->assertSame('https://acmen.ru/api/v1/email', $service->captured['method']);
        $this->assertSame([
            'to' => ['user1@example.com', 'user2@example.com'],
            'to_hidden' => ['audit@example.com'],
            'email' => 'bot@example.com',
            'name' => 'Support Bot',
            'subject' => 'Тест',
            'message' => '<b>Hello</b>',
            'attach' => 'https://example.com/file.pdf',
        ], $service->captured['data']);
    }

    private function fakeService(): AcMenService
    {
        return new class extends AcMenService {
            public array $captured = [];

            public function request(string $method, array $data, string $type, string $token, ?string $url = null): array
            {
                $this->captured = compact('method', 'data', 'type', 'token', 'url');

                return ['success' => 1];
            }
        };
    }
}
