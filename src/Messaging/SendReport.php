<?php

declare(strict_types=1);

namespace Kreait\Firebase\Messaging;

use Kreait\Firebase\Exception\Messaging\InvalidMessage;
use Kreait\Firebase\Exception\Messaging\NotFound;
use Kreait\Firebase\Exception\MessagingException;

use function preg_match;

final class SendReport
{
    /**
     * @var array<array-key, scalar>|null
     */
    private ?array $result = null;

    private ?Message $message = null;

    private ?MessagingException $error = null;

    private function __construct(private readonly MessageTarget $target)
    {
    }

    /**
     * @param array<array-key, scalar> $response
     */
    public static function success(MessageTarget $target, array $response, ?Message $message = null): self
    {
        $report = new self($target);
        $report->result = $response;
        $report->message = $message;

        return $report;
    }

    public static function failure(MessageTarget $target, MessagingException $error, ?Message $message = null): self
    {
        $report = new self($target);
        $report->error = $error;
        $report->message = $message;

        return $report;
    }

    public function target(): MessageTarget
    {
        return $this->target;
    }

    public function isSuccess(): bool
    {
        return !($this->error instanceof MessagingException);
    }

    public function isFailure(): bool
    {
        return $this->error instanceof MessagingException;
    }

    public function messageTargetWasInvalid(): bool
    {
        $errorMessage = $this->error instanceof MessagingException ? $this->error->getMessage() : '';

        return preg_match('/((not.+valid)|invalid).+token/i', $errorMessage) === 1;
    }

    public function messageWasInvalid(): bool
    {
        return $this->error instanceof InvalidMessage;
    }

    public function messageWasSentToUnknownToken(): bool
    {
        return $this->error instanceof NotFound;
    }

    /**
     * @return array<array-key, scalar>|null
     */
    public function result(): ?array
    {
        return $this->result;
    }

    public function error(): ?MessagingException
    {
        return $this->error;
    }

    public function message(): ?Message
    {
        return $this->message;
    }
}
