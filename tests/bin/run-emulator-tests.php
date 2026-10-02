<?php

declare(strict_types=1);

namespace Kreait\Firebase;

require __DIR__.'/../../vendor/autoload.php';

Util::rmenv('GOOGLE_APPLICATION_CREDENTIALS');
Util::rmenv('FIREBASE_TOKEN');
Util::putenv('TEST_FIREBASE_PROJECT_ID', Util::getenv('TEST_FIREBASE_PROJECT_ID') ?? 'demo-firebase-php');
Util::putenv('FIREBASE_AUTH_EMULATOR_HOST', 'localhost:9099');
Util::putenv('FIREBASE_DATABASE_EMULATOR_HOST', 'localhost:9100');

chdir(__DIR__.'/../..');

$phpunit = 'tools/phpunit --bootstrap tests/bootstrap-emulator.php --testsuite integration --group emulator';
$phpunit .= ' '.implode(' ', array_map(escapeshellarg(...), array_slice($argv ?? [], 1)));

$command = sprintf(
    '%s emulators:exec --only auth,database --project %s --non-interactive %s',
    escapeshellarg(__DIR__.'/../../tools/.firebase/node_modules/.bin/firebase'),
    escapeshellarg(Util::getenv('TEST_FIREBASE_PROJECT_ID')),
    escapeshellarg($phpunit),
);

passthru($command, $exitCode);

exit($exitCode);
