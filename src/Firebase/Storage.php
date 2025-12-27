<?php

declare(strict_types=1);

namespace Kreait\Firebase;

use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageClient;

use function array_key_exists;

/**
 * @internal
 */
final class Storage implements Contract\Storage
{
    /**
     * @var Bucket[]
     */
    private array $buckets = [];

    public function __construct(
        private readonly StorageClient $storageClient,
        private readonly string $defaultBucket,
    ) {
    }

    public function getStorageClient(): StorageClient
    {
        return $this->storageClient;
    }

    public function getBucket(?string $name = null): Bucket
    {
        $name ??= $this->defaultBucket;

        if (!array_key_exists($name, $this->buckets)) {
            $this->buckets[$name] = $this->storageClient->bucket($name);
        }

        return $this->buckets[$name];
    }
}
