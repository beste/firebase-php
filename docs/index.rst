.. title:: Firebase for PHP

################
Firebase for PHP
################

.. raw:: html

   <div align="center">

      <p><img src="_static/logo.svg" alt="Firebase for PHP Logo" width="120"></p>

      <p><em>An (unofficial) Firebase Admin SDK for PHP</em></p>

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

----

.. note::
    If you are interested in using the PHP Admin SDK as a client for end-user access
    (for example, in a web application), as opposed to admin access from a
    privileged environment (like a server), you should instead follow the
    `instructions for setting up the client JavaScript SDK <https://firebase.google.com/docs/web/setup>`_.

----

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

**********
User Guide
**********

.. toctree::

    overview
    setup
    cloud-messaging
    cloud-firestore
    cloud-storage
    realtime-database
    authentication
    user-management
    dynamic-links
    remote-config
    app-check
    framework-integrations
    testing
    troubleshooting
