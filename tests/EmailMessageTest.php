<?php

namespace Wilfreedi\AcMen\Test;

use InvalidArgumentException;
use Wilfreedi\AcMen\DTO\EmailMessage;

class EmailMessageTest extends TestCase
{
    /** @test */
    public function it_requires_at_least_one_recipient()
    {
        $this->expectException(InvalidArgumentException::class);

        new EmailMessage([]);
    }

    /** @test */
    public function it_validates_email_format_for_recipients()
    {
        $this->expectException(InvalidArgumentException::class);

        new EmailMessage(['not-an-email']);
    }

    /** @test */
    public function it_builds_payload_with_optional_fields()
    {
        $message = new EmailMessage(
            to: ['user@example.com'],
            toHidden: ['audit@example.com'],
            email: 'bot@example.com',
            name: 'Support Bot',
            subject: 'Тест',
            message: '<b>Hello</b>',
            attach: 'https://example.com/file.pdf'
        );

        $this->assertSame([
            'to' => ['user@example.com'],
            'to_hidden' => ['audit@example.com'],
            'email' => 'bot@example.com',
            'name' => 'Support Bot',
            'subject' => 'Тест',
            'message' => '<b>Hello</b>',
            'attach' => 'https://example.com/file.pdf',
        ], $message->toArray());
    }
}
