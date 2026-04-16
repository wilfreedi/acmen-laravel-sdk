<?php

namespace Wilfreedi\AcMen\DTO;

use InvalidArgumentException;
use Wilfreedi\AcMen\Contracts\MessagePayload;

final class EmailMessage implements MessagePayload
{
    /**
     * @param array<int, string> $to
     * @param array<int, string> $toHidden
     */
    public function __construct(
        private array $to,
        private array $toHidden = [],
        private ?string $email = null,
        private ?string $name = null,
        private ?string $subject = null,
        private ?string $message = null,
        private ?string $attach = null
    ) {
        $this->to = $this->normalizeRecipients($this->to);
        $this->toHidden = $this->normalizeRecipients($this->toHidden);

        if ($this->to === []) {
            throw new InvalidArgumentException('Поле "to" обязательно и не может быть пустым.');
        }
    }

    public function toArray(): array
    {
        $payload = [
            'to' => $this->to,
        ];

        if ($this->toHidden !== []) {
            $payload['to_hidden'] = $this->toHidden;
        }

        if (!is_null($this->email)) {
            $payload['email'] = $this->email;
        }

        if (!is_null($this->name)) {
            $payload['name'] = $this->name;
        }

        if (!is_null($this->subject)) {
            $payload['subject'] = $this->subject;
        }

        if (!is_null($this->message)) {
            $payload['message'] = $this->message;
        }

        if (!is_null($this->attach)) {
            $payload['attach'] = $this->attach;
        }

        return $payload;
    }

    /**
     * @param array<int, string> $emails
     * @return array<int, string>
     */
    private function normalizeRecipients(array $emails): array
    {
        $emails = array_values(array_filter(array_map(
            static fn ($email) => is_string($email) ? trim($email) : '',
            $emails
        )));

        foreach ($emails as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("Некорректный email: {$email}");
            }
        }

        return $emails;
    }
}
