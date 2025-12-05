<?php

declare(strict_types=1);

namespace Kreait\Firebase;

use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Exception\RuntimeException;
use Throwable;

/**
 * @internal
 */
final readonly class Firestore implements Contract\Firestore
{
    private function __construct(private FirestoreClient $client)
    {
    }

    /**
     * @param array<non-empty-string, mixed> $config
     *
     * @phpstan-ignore typePerfect.narrowReturnObjectType
     */
    public static function fromConfig(array $config): Contract\Firestore
    {
        try {
            return new self(new FirestoreClient($config));
        } catch (Throwable $e) {
            throw new RuntimeException(
                message: 'Unable to create a FirestoreClient: '.$e->getMessage(),
                previous: $e
            );
        }
    }

    public function database(): FirestoreClient
    {
        return $this->client;
    }
}
