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
         <a href="https://github.com/kreait/firebase-php/actions/workflows/tests.yml"><img src="https://github.com/kreait/firebase-php/actions/workflows/tests.yml/badge.svg" alt="Tests"></a>
         <a href="https://github.com/kreait/firebase-php/actions/workflows/integration-tests.yml"><img src="https://github.com/kreait/firebase-php/actions/workflows/integration-tests.yml/badge.svg" alt="Integration Tests"></a>
         <a href="https://github.com/kreait/firebase-php/actions/workflows/emulator-tests.yml"><img src="https://github.com/kreait/firebase-php/actions/workflows/emulator-tests.yml/badge.svg" alt="Emulator Tests"></a>
         <a href="https://github.com/sponsors/jeromegamez"><img src="https://img.shields.io/static/v1?logo=GitHub&label=Sponsor&message=%E2%9D%A4&color=ff69b4" alt="Sponsor"></a>
      </p>

   </div>

.. important::
   **Support the project:** This SDK is downloaded 1M+ times monthly and powers thousands of applications. If it saves you or your team time, please consider `sponsoring its development <https://github.com/sponsors/jeromegamez>`_.

.. note::
    If you are interested in using the PHP Admin SDK as a client for end-user access
    (for example, in a web application), as opposed to admin access from a
    privileged environment (like a server), you should instead follow the
    `instructions for setting up the client JavaScript SDK <https://firebase.google.com/docs/web/setup>`_.

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

    composer require "kreait/firebase-php:^7.0"

Please continue to the :ref:`Setup section <setup>` to learn more about connecting your application to Firebase.

If you want to use the SDK within a Framework, please follow the installation instructions here:

- **Laravel**: `kreait/laravel-firebase <https://github.com/kreait/laravel-firebase>`_
- **Symfony**: `kreait/firebase-bundle <https://github.com/kreait/firebase-bundle>`_

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

*******
License
*******

Licensed using the `MIT license <https://opensource.org/license/MIT>`_.

    Copyright (c) Jérôme Gamez <https://github.com/jeromegamez> <jerome@gamez.name>

    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in
    all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
    THE SOFTWARE.

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
