<?php

namespace Wilfreedi\AcMen\Channels;

use Wilfreedi\AcMen\DTO\EmailMessage;

final class EmailChannel extends AbstractChannel
{
    public function name(): string
    {
        return 'email';
    }

    protected function endpointKey(): string
    {
        return 'email.send';
    }

    public function send(EmailMessage $message): array
    {
        return $this->post($message);
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
        return $this->send(new EmailMessage($to, $toHidden, $email, $name, $subject, $message, $attach));
    }
}
