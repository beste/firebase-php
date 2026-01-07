<?php

declare(strict_types=1);

namespace Kreait\Firebase\Contract;

use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageClient;

/**
 * Thin wrapper around Google Cloud Storage that provides Firebase-specific conveniences.
 *
 * This contract exists to maintain consistent Factory integration patterns across all Firebase
 * components while adding conveniences like default bucket management and bucket instance caching.
 */
interface Storage
{
    public function getStorageClient(): StorageClient;

    /**
     * @param non-empty-string|null $name
     */
    public function getBucket(?string $name = null): Bucket;
}
