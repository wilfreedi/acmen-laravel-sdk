<?php

namespace Wilfreedi\AcMen\Test;

use InvalidArgumentException;
use Wilfreedi\AcMen\Support\EndpointResolver;

class EndpointResolverTest extends TestCase
{
    /** @test */
    public function it_prefers_legacy_urls_for_backward_compatibility()
    {
        $resolver = new EndpointResolver([
            'url' => 'https://acmen.ru/api/v1/telegram/',
            'vk_url' => 'https://acmen.ru/api/v1/vk/',
            'email_url' => 'https://acmen.ru/api/v1/email',
            'base_url' => 'https://acmen.ru/api/v2',
            'endpoints' => [
                'telegram.send_message' => '/telegram/sendMessage',
                'vk.send_message' => '/vk/sendMessage',
                'email.send' => '/email',
            ],
        ]);

        $this->assertSame('https://acmen.ru/api/v1/telegram/sendMessage', $resolver->resolve('telegram.send_message'));
        $this->assertSame('https://acmen.ru/api/v1/vk/sendMessage', $resolver->resolve('vk.send_message'));
        $this->assertSame('https://acmen.ru/api/v1/email', $resolver->resolve('email.send'));
    }

    /** @test */
    public function it_builds_url_from_base_when_legacy_urls_are_missing()
    {
        $resolver = new EndpointResolver([
            'base_url' => 'https://acmen.ru/api/v1',
            'endpoints' => [
                'telegram.send_message' => '/telegram/sendMessage',
            ],
        ]);

        $this->assertSame('https://acmen.ru/api/v1/telegram/sendMessage', $resolver->resolve('telegram.send_message'));
    }

    /** @test */
    public function it_throws_for_unknown_endpoint_key()
    {
        $this->expectException(InvalidArgumentException::class);

        $resolver = new EndpointResolver([
            'base_url' => 'https://acmen.ru/api/v1',
            'endpoints' => [],
        ]);

        $resolver->resolve('max.send_message');
    }
}
