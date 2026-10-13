<?php

declare(strict_types=1);

namespace Kreait\Firebase;

if (!extension_loaded('grpc')) {
    fwrite(STDERR, 'Firestore emulator tests require the gRPC PHP extension.'.PHP_EOL);
    exit(1);
}

require __DIR__.'/../../vendor/autoload.php';

Util::rmenv('GOOGLE_APPLICATION_CREDENTIALS');
Util::rmenv('FIREBASE_TOKEN');
Util::putenv('FIREBASE_AUTH_EMULATOR_HOST', 'localhost:9099');
Util::putenv('FIREBASE_DATABASE_EMULATOR_HOST', 'localhost:9100');
Util::putenv('FIRESTORE_EMULATOR_HOST', 'localhost:8080');

chdir(__DIR__.'/../..');

$phpunit = 'tools/phpunit --bootstrap tests/bootstrap-emulator.php --testsuite integration --group emulator';
$phpunit .= ' '.implode(' ', array_map(escapeshellarg(...), array_slice($argv ?? [], 1)));

$command = sprintf(
    '%s emulators:exec --only auth,database,firestore --project demo-firebase-php --non-interactive %s',
    escapeshellarg(__DIR__.'/../../tools/.firebase/node_modules/.bin/firebase'),
    escapeshellarg($phpunit),
);

passthru($command, $exitCode);

exit($exitCode);
