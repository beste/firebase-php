<?php

declare(strict_types=1);

use Boundwize\StructArmed\Architecture;
use Boundwize\StructArmed\Preset\Preset;

return Architecture::define()
    // Each component owns its implementation, contract, and specific exceptions.
    ->layer('AppCheck', [
        'src/AppCheck.php',
        'src/Contract/AppCheck.php',
        'src/AppCheck/',
        'src/Exception/AppCheck/',
        'src/Exception/AppCheckException.php',
        'src/Exception/AppCheckApiExceptionConverter.php',
        'src/Contract/AppCheckWithReplayProtection.php',
    ])
    ->layer('Auth', [
        'src/Auth.php',
        'src/Contract/Auth.php',
        'src/Auth/',
        'src/Exception/Auth/',
        'src/Exception/AuthException.php',
        'src/Exception/AuthApiExceptionConverter.php',
    ])
    ->layer('Database', [
        'src/Database.php',
        'src/Contract/Database.php',
        'src/Database/',
        'src/Exception/Database/',
        'src/Exception/DatabaseException.php',
        'src/Exception/DatabaseApiExceptionConverter.php',
    ])
    ->layer('Messaging', [
        'src/Messaging.php',
        'src/Contract/Messaging.php',
        'src/Messaging/',
        'src/Exception/Messaging/',
        'src/Exception/MessagingException.php',
        'src/Exception/MessagingApiExceptionConverter.php',
    ])
    ->layer('RemoteConfig', [
        'src/RemoteConfig.php',
        'src/Contract/RemoteConfig.php',
        'src/RemoteConfig/',
        'src/Exception/RemoteConfig/',
        'src/Exception/RemoteConfigException.php',
        'src/Exception/RemoteConfigApiExceptionConverter.php',
    ])
    ->layer('Firestore', [
        'src/Firestore.php',
        'src/Contract/Firestore.php',
    ])
    ->layer('Storage', [
        'src/Storage.php',
        'src/Contract/Storage.php',
    ])
    // The third argument excludes exceptions and converters already assigned to their specific component layers.
    ->layer('Exception', 'src/Exception/', [
        'src/Exception/AppCheck/',
        'src/Exception/AppCheckException.php',
        'src/Exception/AppCheckApiExceptionConverter.php',
        'src/Exception/Auth/',
        'src/Exception/AuthException.php',
        'src/Exception/AuthApiExceptionConverter.php',
        'src/Exception/Database/',
        'src/Exception/DatabaseException.php',
        'src/Exception/DatabaseApiExceptionConverter.php',
        'src/Exception/Messaging/',
        'src/Exception/MessagingException.php',
        'src/Exception/MessagingApiExceptionConverter.php',
        'src/Exception/RemoteConfig/',
        'src/Exception/RemoteConfigException.php',
        'src/Exception/RemoteConfigApiExceptionConverter.php',
    ])
    ->layer('Http', 'src/Http/')
    ->layer('Request', 'src/Request/')
    ->layer('Valinor', 'src/Valinor/')
    ->layer('Value', 'src/Value/')
    ->layer('Util', [
        'src/Util/',
        'src/Util.php',
    ])
    ->layer('Factory', 'src/Factory.php')
    // The third argument excludes files and directories already assigned to other layers.
    ->layer('Core', 'src/', [
        'src/AppCheck/',
        'src/Auth/',
        'src/Contract/',
        'src/Database/',
        'src/Exception/',
        'src/Http/',
        'src/Messaging/',
        'src/RemoteConfig/',
        'src/Request/',
        'src/Util/',
        'src/Valinor/',
        'src/Value/',
        'src/AppCheck.php',
        'src/Auth.php',
        'src/Database.php',
        'src/Messaging.php',
        'src/RemoteConfig.php',
        'src/Firestore.php',
        'src/Storage.php',
        'src/Factory.php',
        'src/Util.php',
    ])
    ->layer('Tests', 'tests/')
    // The third argument excludes contracts already assigned to their specific component layers.
    ->layer('Contract', 'src/Contract/', [
        'src/Contract/AppCheck.php',
        'src/Contract/AppCheckWithReplayProtection.php',
        'src/Contract/Auth.php',
        'src/Contract/Database.php',
        'src/Contract/Messaging.php',
        'src/Contract/RemoteConfig.php',
        'src/Contract/Firestore.php',
        'src/Contract/Storage.php',
    ])
    // +Layer grants access to that layer and its allowed dependencies.
    ->ruleset([
        'Core' => [],
        'Exception' => [],
        'Contract' => [],
        'Value' => ['+Exception'],
        'Util' => ['+Exception'],
        'Http' => ['+Exception'],
        'Valinor' => ['+Exception'],
        'Request' => ['+Value'],
        'AppCheck' => ['+Http'],
        // AuthError references Factory in its diagnostic message; its dependencies are not inherited.
        'Auth' => ['+Http', '+Request', '+Util', 'Factory'],
        'Database' => ['+Http', '+Util'],
        'Messaging' => ['+Http', '+Util'],
        'RemoteConfig' => ['+Http', '+Util'],
        'Firestore' => ['+Exception'],
        'Storage' => [],
        'Factory' => [
            '+Core',
            '+AppCheck',
            '+Auth',
            '+Database',
            '+Messaging',
            '+RemoteConfig',
            '+Firestore',
            '+Storage',
            '+Valinor',
            'Contract',
        ],
        'Tests' => ['+Factory'],
    ])
    ->withPresets(Preset::PSR4(), Preset::CODEQUALITY());
