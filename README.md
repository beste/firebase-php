<div align="center">

# Firebase for PHP

<img src="docs/_static/logo.svg" alt="Firebase Admin PHP SDK Logo" width="120">

<p><em>Firebase Admin SDK for PHP</em></p>

[![Current version](https://img.shields.io/packagist/v/kreait/firebase-php.svg?logo=composer)](https://packagist.org/packages/kreait/firebase-php)
[![Monthly Downloads](https://img.shields.io/packagist/dm/kreait/firebase-php.svg)](https://packagist.org/packages/kreait/firebase-php/stats)
[![Total Downloads](https://img.shields.io/packagist/dt/kreait/firebase-php.svg)](https://packagist.org/packages/kreait/firebase-php/stats)<br/>
[![Tests](https://github.com/kreait/firebase-php/actions/workflows/tests.yml/badge.svg)](https://github.com/kreait/firebase-php/actions/workflows/tests.yml)
[![Integration Tests](https://github.com/kreait/firebase-php/actions/workflows/integration-tests.yml/badge.svg)](https://github.com/kreait/firebase-php/actions/workflows/integration-tests.yml)
[![Emulator Tests](https://github.com/kreait/firebase-php/actions/workflows/emulator-tests.yml/badge.svg)](https://github.com/kreait/firebase-php/actions/workflows/emulator-tests.yml)
[![Sponsor](https://img.shields.io/static/v1?logo=GitHub&label=Sponsor&message=%E2%9D%A4&color=ff69b4)](https://github.com/sponsors/jeromegamez)

</div>

> [!IMPORTANT]
> **Support the project:** This SDK is downloaded 1M+ times monthly and powers thousands of applications.
> If it saves you or your team time, please consider
> [sponsoring its development](https://github.com/sponsors/jeromegamez).

> [!NOTE]
> If you are interested in using the PHP Admin SDK as a client for end-user access (for example, in a web application),
> as opposed to admin access from a privileged environment (like a server), you should instead follow the
> [instructions for setting up the client JavaScript SDK](https://firebase.google.com/docs/web/setup).

## Overview

[Firebase](https://firebase.google.com/) provides the tools and infrastructure you need to develop your app, grow your user base, and earn money. The Firebase Admin PHP SDK enables access to Firebase services from privileged environments (such as servers or cloud) in PHP.

For more information, visit the [Firebase Admin PHP SDK documentation](https://firebase-php.readthedocs.io/).

## Installation

The recommended way to install the Firebase Admin SDK is with [Composer](https://getcomposer.org).
Composer is a dependency management tool for PHP that allows you to declare the dependencies
your project needs and installs them into your project.

```bash
composer require "kreait/firebase-php:^8.0"
```

Please continue to the [Setup section](docs/setup.rst) to learn more about connecting your application to Firebase.

If you want to use the SDK within a Framework, please follow the installation instructions here:

- **Laravel**: https://github.com/kreait/laravel-firebase
- **Symfony**: https://github.com/kreait/firebase-bundle

## Quickstart

```php
use Kreait\Firebase\Factory;

$factory = (new Factory)
    ->withServiceAccount('/path/to/firebase_credentials.json')
    ->withDatabaseUri('https://my-project-default-rtdb.firebaseio.com');

$auth = $factory->createAuth();
$realtimeDatabase = $factory->createDatabase();
$cloudMessaging = $factory->createMessaging();
$remoteConfig = $factory->createRemoteConfig();
$cloudStorage = $factory->createStorage();
$firestore = $factory->createFirestore();
```

## Powered by

[![JetBrains logo.](https://resources.jetbrains.com/storage/products/company/brand/logos/jetbrains.svg)](https://jb.gg/OpenSourceSupport)

Thanks to [JetBrains](https://www.jetbrains.com/) credits for providing [a free PhpStorm license](https://jb.gg/OpenSourceSupport) for the development of this open-source package.

## License

Firebase Admin PHP SDK is licensed under the [MIT License](LICENSE).

Your use of Firebase is governed by the [Terms of Service for Firebase Services](https://firebase.google.com/terms/).
