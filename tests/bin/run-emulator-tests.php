<?php

declare(strict_types=1);

namespace Kreait\Firebase;

require __DIR__.'/../../vendor/autoload.php';

use Dotenv\Dotenv;

// The CLI only needs the project ID; PHPUnit loads the other test settings itself.
$dotenv = Dotenv::createImmutable(__DIR__.'/..');
$dotenv->safeLoad();
$dotenv->required('TEST_FIREBASE_PROJECT_ID')->notEmpty();

putenv('FIREBASE_AUTH_EMULATOR_HOST=localhost:9099');
putenv('FIREBASE_DATABASE_EMULATOR_HOST=localhost:9100');

chdir(__DIR__.'/../..');

$command = sprintf(
    '%s emulators:exec --only auth,database --project %s %s',
    escapeshellarg(__DIR__.'/../../tools/.firebase/node_modules/.bin/firebase'),
    escapeshellarg(Util::getenv('TEST_FIREBASE_PROJECT_ID')),
    escapeshellarg('XDEBUG_MODE=off tools/phpunit --group=emulator'),
);

passthru($command, $exitCode);

exit($exitCode);
