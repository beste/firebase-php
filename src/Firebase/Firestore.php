<?php

declare(strict_types=1);

namespace Kreait\Firebase;

use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Exception\RuntimeException;
use Throwable;

/**
 * @internal
 */
final class Firestore implements Contract\Firestore
{
    private function __construct(private readonly FirestoreClient $client)
    {
    }

    /**
     * @param array<non-empty-string, mixed> $config
     */
    public static function fromConfig(array $config): Contract\Firestore
    {
        try {
            return new self(new FirestoreClient($config));
        } catch (Throwable $e) {
            throw new RuntimeException('Unable to create a FirestoreClient: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    public function database(): FirestoreClient
    {
        return $this->client;
    }
}
