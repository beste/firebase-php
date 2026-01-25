.. title:: Firebase Admin SDK for PHP

##########################
Firebase Admin SDK for PHP
##########################

.. raw:: html

   <div align="center">

      <p><img src="_static/logo.svg" alt="Firebase Admin SDK for PHP Logo" width="120"></p>

      <p>
         <a href="https://packagist.org/packages/kreait/firebase-php"><img src="https://img.shields.io/packagist/v/kreait/firebase-php.svg?logo=composer" alt="Current version"></a>
         <a href="https://packagist.org/packages/kreait/firebase-php/stats"><img src="https://img.shields.io/packagist/dm/kreait/firebase-php.svg" alt="Monthly Downloads"></a>
         <a href="https://packagist.org/packages/kreait/firebase-php/stats"><img src="https://img.shields.io/packagist/dt/kreait/firebase-php.svg" alt="Total Downloads"></a><br/>
         <a href="https://github.com/beste/firebase-php/actions/workflows/tests.yml"><img src="https://github.com/beste/firebase-php/actions/workflows/tests.yml/badge.svg" alt="Tests"></a>
         <a href="https://github.com/beste/firebase-php/actions/workflows/integration-tests.yml"><img src="https://github.com/beste/firebase-php/actions/workflows/integration-tests.yml/badge.svg" alt="Integration Tests"></a>
         <a href="https://github.com/beste/firebase-php/actions/workflows/emulator-tests.yml"><img src="https://github.com/beste/firebase-php/actions/workflows/emulator-tests.yml/badge.svg" alt="Emulator Tests"></a>
         <a href="https://github.com/sponsors/jeromegamez"><img src="https://img.shields.io/static/v1?logo=GitHub&label=Sponsor&message=%E2%9D%A4&color=ff69b4" alt="Sponsor"></a>
      </p>

   </div>

.. important::
   **Support the project:** This SDK is downloaded 1M+ times monthly and powers thousands of applications. If it saves you or your team time, please consider `sponsoring its development <https://github.com/sponsors/jeromegamez>`_.

.. note::
    The project moved from the ``kreait`` to the ``beste`` GitHub Organization in January 2026.
    The namespace remains ``Kreait\Firebase`` and the package name remains ``kreait/firebase-php``.
    Please update your remote URL if you have forked or cloned the repository.

********
Overview
********

`Firebase <https://firebase.google.com/>`_ provides the tools and infrastructure you need to develop your app,
grow your user base, and earn money. The Firebase Admin PHP SDK enables access to Firebase services from
privileged environments (such as servers or cloud) in PHP.

************
Installation
************

The recommended way to install the Firebase Admin SDK is with `Composer <https://getcomposer.org>`_.
Composer is a dependency management tool for PHP that allows you to declare the dependencies
your project needs and installs them into your project.

.. code-block:: bash

    composer require "kreait/firebase-php:^8.0"

Please continue to the :ref:`Setup section <setup>` to learn more about connecting your application to Firebase.

If you want to use the SDK within a Framework, please follow the installation instructions here:

- **Laravel**: `kreait/laravel-firebase <https://packagist.org/packages/kreait/laravel-firebase>`_
- **Symfony**: `kreait/firebase-bundle <https://packagist.org/packages/kreait/firebase-bundle>`_

***********
Quick Start
***********

.. code-block:: php

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

********
Sponsors
********

.. raw:: html

   <p style="display:flex; flex-wrap:wrap; gap:16px 32px; align-items:center; justify-content:flex-start; margin:0;">
     <a href="https://exitable.nl/" style="display:inline-flex; align-items:center;">
       <img src="_static/sponsors/logo-exitable.png" alt="Exitable logo" style="height:48px; width:auto; max-width:360px;">
     </a>
     <a href="https://jb.gg/OpenSourceSupport" style="display:inline-flex; align-items:center;">
       <img src="_static/sponsors/jetbrains.svg" alt="JetBrains logo" style="height:48px; width:auto; max-width:200px;">
     </a>
   </p>

Thanks to `Exitable <https://exitable.nl/>`_ and `JetBrains <https://www.jetbrains.com/>`_ for supporting the development of this project.

*******
License
*******

Licensed using the `MIT license <https://opensource.org/license/MIT>`_.

Your use of Firebase is governed by the `Terms of Service for Firebase Services <https://firebase.google.com/terms/>`_.

.. toctree::
   :hidden:

   setup
   cloud-messaging
   cloud-firestore
   cloud-storage
   realtime-database
   authentication
   user-management
   remote-config
   app-check
   testing
   troubleshooting
