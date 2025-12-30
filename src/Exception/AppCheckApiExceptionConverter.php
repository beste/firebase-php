<?php

declare(strict_types=1);

namespace Kreait\Firebase\Exception;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use GuzzleHttp\Exception\RequestException;
use Kreait\Firebase\Exception\AppCheck\ApiConnectionFailed;
use Kreait\Firebase\Exception\AppCheck\AppCheckError;
use Kreait\Firebase\Exception\AppCheck\PermissionDenied;
use Kreait\Firebase\Http\ErrorResponseParser;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * @internal
 */
final readonly class AppCheckApiExceptionConverter
{
    public function __construct(private ErrorResponseParser $responseParser)
    {
    }

    public function convertException(Throwable $exception): AppCheckException
    {
        if ($exception instanceof RequestException) {
            return $this->convertGuzzleRequestException($exception);
        }

        if ($exception instanceof NetworkExceptionInterface) {
            return new ApiConnectionFailed(
                message: 'Unable to connect to the API: '.$exception->getMessage(),
                previous: $exception
            );
        }

        return new AppCheckError(message: $exception->getMessage(), previous: $exception);
    }

    private function convertGuzzleRequestException(RequestException $e): AppCheckException
    {
        $message = $e->getMessage();
        $code = $e->getCode();
        $response = $e->getResponse();

        if ($response instanceof ResponseInterface) {
            $message = $this->responseParser->getErrorReasonFromResponse($response);
            $code = $response->getStatusCode();
        }

        return match ($code) {
            StatusCode::STATUS_UNAUTHORIZED, StatusCode::STATUS_FORBIDDEN => new PermissionDenied($message, $code, $e),
            default => new AppCheckError($message, $code, $e),
        };
    }
}
