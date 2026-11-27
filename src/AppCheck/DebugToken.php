<?php

declare(strict_types=1);

namespace Kreait\Firebase\AppCheck;

use DateTimeImmutable;
use Kreait\Firebase\Util\DT;
use SensitiveParameter;

use function strrpos;
use function substr;

/**
 * A debug token registered for a Firebase App.
 *
 * The secret token value is only known when the debug token is created, the App Check API never returns it.
 *
 * @see https://firebase.google.com/docs/reference/appcheck/rest/v1/projects.apps.debugTokens
 *
 * @phpstan-type DebugTokenShape array{
 *     name: non-empty-string,
 *     displayName: string,
 *     updateTime?: string,
 *     etag?: string
 * }
 */
final readonly class DebugToken
{
    /**
     * @param non-empty-string $name
     * @param non-empty-string $id
     * @param non-empty-string|null $token
     */
    private function __construct(
        public string $name,
        public string $id,
        public string $displayName,
        public ?DateTimeImmutable $updateTime,
        #[SensitiveParameter]
        public ?string $token,
    ) {
    }

    /**
     * @param DebugTokenShape $data
     * @param non-empty-string|null $token
     */
    public static function fromArray(array $data, #[SensitiveParameter] ?string $token = null): self
    {
        $name = $data['name'];
        $lastSlash = strrpos($name, '/');
        $id = $lastSlash === false ? $name : substr($name, $lastSlash + 1);

        $updateTime = isset($data['updateTime']) ? DT::toUTCDateTimeImmutable($data['updateTime']) : null;

        return new self(
            $name,
            $id !== '' ? $id : $name,
            $data['displayName'],
            $updateTime,
            $token,
        );
    }
}
