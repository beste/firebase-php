<?php

declare(strict_types=1);

namespace Kreait\Firebase\Exception;

use Beste\Clock\SystemClock;
use DateInterval;
use DateTimeImmutable;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use GuzzleHttp\Exception\RequestException;
use Kreait\Firebase\Exception\Messaging\ApiConnectionFailed;
use Kreait\Firebase\Exception\Messaging\AuthenticationError;
use Kreait\Firebase\Exception\Messaging\InvalidMessage;
use Kreait\Firebase\Exception\Messaging\MessagingError;
use Kreait\Firebase\Exception\Messaging\NotFound;
use Kreait\Firebase\Exception\Messaging\QuotaExceeded;
use Kreait\Firebase\Exception\Messaging\ServerError;
use Kreait\Firebase\Exception\Messaging\ServerUnavailable;
use Kreait\Firebase\Http\ErrorResponseParser;
use Psr\Clock\ClockInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

use function is_numeric;

/**
 * @internal
 */
class MessagingApiExceptionConverter
{
    private readonly ErrorResponseParser $responseParser;

    private readonly ClockInterface $clock;

    public function __construct(?ClockInterface $clock = null)
    {
        $this->responseParser = new ErrorResponseParser();
        $this->clock = $clock ?? SystemClock::create();
    }

    public function convertException(Throwable $exception): MessagingException
    {
        if ($exception instanceof RequestException) {
            return $this->convertGuzzleRequestException($exception);
        }

        if ($exception instanceof NetworkExceptionInterface) {
            return new ApiConnectionFailed('Unable to connect to the API: '.$exception->getMessage(), $exception->getCode(), $exception);
        }

        return new MessagingError($exception->getMessage(), $exception->getCode(), $exception);
    }

    public function convertResponse(ResponseInterface $response, ?Throwable $previous = null): MessagingException
    {
        $code = $response->getStatusCode();

        if ($code < StatusCode::STATUS_BAD_REQUEST) {
            throw new InvalidArgumentException('Cannot convert a non-failed response to an exception');
        }

        $errors = $this->responseParser->getErrorsFromResponse($response);
        $message = $this->responseParser->getErrorReasonFromResponse($response);

        switch ($code) {
            case StatusCode::STATUS_BAD_REQUEST:
                $convertedError = new InvalidMessage($message, previous: $previous);

                break;

            case StatusCode::STATUS_UNAUTHORIZED:
            case StatusCode::STATUS_FORBIDDEN:
                $convertedError = new AuthenticationError($message, previous: $previous);

                break;

            case StatusCode::STATUS_NOT_FOUND:
                $convertedError = new NotFound($message, previous: $previous);

                break;

            case StatusCode::STATUS_TOO_MANY_REQUESTS:
                $convertedError = new QuotaExceeded($message, previous: $previous);
                $retryAfter = $this->getRetryAfter($response);

                if ($retryAfter !== null) {
                    $convertedError = $convertedError->withRetryAfter($retryAfter);
                }

                break;

            case StatusCode::STATUS_INTERNAL_SERVER_ERROR:
                $convertedError = new ServerError($message, previous: $previous);

                break;

            case StatusCode::STATUS_BAD_GATEWAY:
                $contentType = mb_strtolower($response->getHeaderLine('Content-Type'));
                $retryAfter = $this->getRetryAfter($response);

                if (!str_contains($contentType, 'json')) {
                    // Adding 30 seconds as a fallback retry after because the HTML Response suggests it
                    // See https://github.com/kreait/firebase-php/issues/988
                    $retryAfter ??= ($this->clock->now()->add(new DateInterval('PT30S')));
                    $message = 'The server encountered a temporary error and could not complete your request.';
                }

                $convertedError = new ServerUnavailable($message, previous: $previous);

                if ($retryAfter !== null) {
                    $convertedError = $convertedError->withRetryAfter($retryAfter);
                }

                break;

            case StatusCode::STATUS_SERVICE_UNAVAILABLE:
                $convertedError = new ServerUnavailable($message, previous: $previous);
                $retryAfter = $this->getRetryAfter($response);

                if ($retryAfter !== null) {
                    $convertedError = $convertedError->withRetryAfter($retryAfter);
                }

                break;

            default:
                $convertedError = new MessagingError($message, $code, $previous);

                break;
        }

        return $convertedError->withErrors($errors);
    }

    private function convertGuzzleRequestException(RequestException $e): MessagingException
    {
        $response = $e->getResponse();

        if ($response !== null) {
            return $this->convertResponse($response, $e);
        }

        return new MessagingError($e->getMessage(), $e->getCode(), $e);
    }

    private function getRetryAfter(ResponseInterface $response): ?DateTimeImmutable
    {
        $retryAfter = $response->getHeaderLine('Retry-After');

        if ($retryAfter === '') {
            return null;
        }

        if (is_numeric($retryAfter)) {
            return $this->clock->now()->modify("+{$retryAfter} seconds");
        }

        try {
            return new DateTimeImmutable($retryAfter);
        } catch (Throwable) {
            // We can't afford to throw exceptions in an exception handler :)
            // Here, if the Retry-After header doesn't have a numeric value
            // or a date that can be handled by DateTimeImmutable, we just
            // throw it away, sorry not sorry ¯\_(ツ)_/¯
            return null;
        }
    }
}
