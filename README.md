<div align="center">

# Firebase Admin SDK for PHP

<img src="docs/_static/logo.svg" alt="Firebase Admin PHP SDK Logo" width="120">

[![Current version](https://img.shields.io/packagist/v/kreait/firebase-php.svg?logo=composer)](https://packagist.org/packages/kreait/firebase-php)
[![Monthly Downloads](https://img.shields.io/packagist/dm/kreait/firebase-php.svg)](https://packagist.org/packages/kreait/firebase-php/stats)
[![Total Downloads](https://img.shields.io/packagist/dt/kreait/firebase-php.svg)](https://packagist.org/packages/kreait/firebase-php/stats)<br/>
[![CI](https://github.com/beste/firebase-php/actions/workflows/ci.yml/badge.svg)](https://github.com/beste/firebase-php/actions/workflows/ci.yml)
[![Secure Tests](https://github.com/beste/firebase-php/actions/workflows/secure-tests.yml/badge.svg)](https://github.com/beste/firebase-php/actions/workflows/secure-tests.yml)
[![Docs](https://github.com/beste/firebase-php/actions/workflows/docs.yml/badge.svg)](https://github.com/beste/firebase-php/actions/workflows/docs.yml)
[![Sponsor](https://img.shields.io/static/v1?logo=GitHub&label=Sponsor&message=%E2%9D%A4&color=ff69b4)](https://github.com/sponsors/jeromegamez)

</div>

> [!IMPORTANT]
> **Support the project:** This SDK is downloaded 1M+ times monthly and powers thousands of applications.
> If it saves you or your team time, please consider
> [sponsoring its development](https://github.com/sponsors/jeromegamez).

> [!NOTE]
> The project moved from the `kreait` to the `beste` GitHub Organization in January 2026.
> The namespace remains `Kreait\Firebase` and the package name remains `kreait/firebase-php`.
> Please update your remote URL if you have forked or cloned the repository.

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

- **Laravel**: https://packagist.org/packages/kreait/laravel-firebase
- **Symfony**: https://packagist.org/packages/kreait/firebase-bundle

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

## Sponsors

<div align="center" style="display: flex; justify-content: center; align-items: center; gap: 40px;">
  <a href="https://exitable.nl/"><img src="docs/_static/sponsors/logo-exitable.png" alt="Exitable" height="48"></a>
  <a href="https://jb.gg/OpenSourceSupport"><img src="docs/_static/sponsors/jetbrains.svg" alt="JetBrains" height="48"></a>
</div>

<div align="center">
  <sub>Thanks to <a href="https://exitable.nl/">Exitable</a> and <a href="https://www.jetbrains.com/">JetBrains</a> for supporting this project.</sub>
</div>

## License

Firebase Admin PHP SDK is licensed under the [MIT License](LICENSE).

Your use of Firebase is governed by the [Terms of Service for Firebase Services](https://firebase.google.com/terms/).
