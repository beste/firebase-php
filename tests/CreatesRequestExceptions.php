<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ResponseException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
trait CreatesRequestExceptions
{
    private static function createGuzzleRequestException(string $message, RequestInterface $request, ResponseInterface $response): RequestException
    {
        if (class_exists(ResponseException::class)) {
            // Guzzle 8: only the ResponseException branch of RequestException carries a response.
            return new ResponseException($message, $request, $response);
        }

        // Guzzle 7: any RequestException can carry a response.
        return new RequestException($message, $request, $response);
    }
}
