<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests;

use Beste\Json;
use Kreait\Firebase\Util;
use RuntimeException;

require_once __DIR__.'/../vendor/autoload.php';

// Do not load tests/.env: emulator tests must never use a real signing key.
$projectId = Util::getenv('TEST_FIREBASE_PROJECT_ID') ?? 'demo-firebase-php';
Util::putenv('TEST_FIREBASE_PROJECT_ID', $projectId);
Util::putenv('TEST_FIREBASE_RTDB_URI', 'https://'.$projectId.'-default-rtdb.firebaseio.com');
Util::putenv('TEST_FIREBASE_TENANT_ID', 'demo-tenant');
Util::putenv('TEST_REGISTRATION_TOKENS', '[]');
Util::rmenv('TEST_FIREBASE_APP_ID');

// Custom tokens need a signer, but the emulator does not require a real service account.
$key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);

if ($key === false || !openssl_pkey_export($key, $privateKey)) {
    throw new RuntimeException('Unable to generate the emulator test signing key');
}

Util::putenv('GOOGLE_APPLICATION_CREDENTIALS', Json::encode([
    'type' => 'service_account',
    'project_id' => $projectId,
    'client_email' => 'emulator@'.$projectId.'.iam.gserviceaccount.com',
    'private_key' => $privateKey,
]));
