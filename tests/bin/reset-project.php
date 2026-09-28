<?php

declare(strict_types=1);

namespace Kreait\Firebase;

use Dotenv\Dotenv;
use Kreait\Firebase\Auth\UserRecord;
use Kreait\Firebase\Database\RuleSet;

require __DIR__.'/../../vendor/autoload.php';

// Unsafe is needed because google/auth uses getenv/putenv to determine the Application Credentials
$dotenv = Dotenv::createUnsafeImmutable(__DIR__.'/..');
$dotenv->safeLoad();
$dotenv->required('GOOGLE_APPLICATION_CREDENTIALS');

$factory = (new Factory())->withDatabaseUri(Util::getenv('TEST_FIREBASE_RTDB_URI'));

$auth = $factory->createAuth();
$db = $factory->createDatabase();

echo 'Resetting database rules... ';
$db->updateRules(RuleSet::private());
echo 'DONE'.PHP_EOL;

echo 'Purging database... ';
$db->getReference('/')->remove();
echo 'DONE'.PHP_EOL;

echo 'Purging auth users... ';
do {
    $users = iterator_to_array($auth->listUsers());
    $uids = array_map(fn(UserRecord $record): string => $record->uid, $users);

    if (count($uids) > 0) {
        $auth->deleteUsers($uids, true);
    }
} while (count($uids) > 0);
echo 'DONE'.PHP_EOL;
