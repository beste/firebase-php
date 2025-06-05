<?php
declare(strict_types = 1);

use Composer\InstalledVersions;
use Composer\Semver\VersionParser;

$config = ['parameters' => ['ignoreErrors' => []]];

if (PHP_VERSION_ID < 80200) {
//    $config['parameters']['ignoreErrors'][] = [
//        'message' => "#Call to method.+assertIsCallable.+will always evaluate to true#",
//        'path' => __DIR__,
//    ];
}

return $config;
