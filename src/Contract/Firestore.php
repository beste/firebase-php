<?php

declare(strict_types=1);

namespace Kreait\Firebase\Contract;

use Google\Cloud\Firestore\FirestoreClient;

/**
 * Thin wrapper around Google Cloud Firestore that provides Firebase-specific error handling.
 *
 * This contract exists to maintain consistent Factory integration patterns across all Firebase
 * components while wrapping Google Cloud client instantiation errors in Firebase exceptions.
 */
interface Firestore
{
    public function database(): FirestoreClient;
}
