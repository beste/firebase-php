#############################
Testing and Local Development
#############################

.. note::
    This page covers using the PHP SDK with emulators in your own application. To run this repository's tests, see the
    `contributor testing guide <https://github.com/beste/firebase-php/blob/8.x/TESTING.md>`_. Its
    ``composer test:emulator`` command starts and stops the emulators using a demo project. It requires no real
    Firebase project or credentials.

*****************
Integration Tests
*****************

The most reliable way of testing your project is to create a separate Firebase project and configure your tests to
use it instead of your production project. For example, you could have multiple Firebase projects depending on
your use-case:

    - ``my-project-dev``: used for developers while they develop new features
    - ``my-project-int``: used by CI/CD pipelines
    - ``my-project-staging``: used to preview upcoming changes for stakeholders
    - ``my-project``: used for production

*********************************
Using the Firebase Emulator Suite
*********************************

For an introduction to the Firebase Emulator suite, please visit the official documentation:
`https://firebase.google.com/docs/emulator-suite <https://firebase.google.com/docs/emulator-suite>`_

.. warning::
    Only the Auth and Realtime Database Emulators are currently supported in this PHP SDK.

To use the Firebase Emulator Suite, you must first `install it <https://firebase.google.com/docs/cli>`_.

See the `official documentation <https://firebase.google.com/docs/emulator-suite/install_and_configure#startup>`_
for instructions how to work with it.

Start the emulator suite before running your application so the PHP SDK can connect to it.

Auth Emulator
-------------

If not already present, create a ``firebase.json`` file in the root of your project and make sure that at least the
following fields are set (the port number can be changed to your requirements):

.. code-block:: js

    {
      "emulators": {
        "auth": {
          "port": 9099
        }
      }
    }

Firebase Admin SDKs automatically connect to the Authentication emulator when the
``FIREBASE_AUTH_EMULATOR_HOST`` environment variable is set.

.. code-block:: bash

    $ export FIREBASE_AUTH_EMULATOR_HOST="localhost:9099"

With the environment variable set, Firebase Admin SDKs will accept unsigned ID Tokens and session cookies issued by the
Authentication emulator (via ``verifyIdToken`` and ``createSessionCookie`` methods respectively) to facilitate local
development and testing. Please make sure not to set the environment variable in production.

When connecting to the Authentication emulator, you will need to specify a project ID. You can pass a project ID to
the Factory directly or set the ``GOOGLE_CLOUD_PROJECT`` environment variable. Note that you do not need to use your
real Firebase project ID; the Authentication emulator will accept any project ID.

This example uses ``demo-project`` in both the emulator command and ``Factory::withProjectId()``:

.. code-block:: bash

    $ firebase emulators:start --only auth --project demo-project

No service account or Google credentials are required for managing users, signing in, or verifying emulator tokens:

.. code-block:: php

    use Kreait\Firebase\Factory;

    $auth = (new Factory())->withProjectId('demo-project')->createAuth();
    $user = $auth->createUserWithEmailAndPassword('user@example.com', 'password123');
    $result = $auth->signInWithEmailAndPassword('user@example.com', 'password123');
    $token = $auth->verifyIdToken($result->idToken());
    $auth->deleteUser($user->uid);

Generating custom tokens still requires credentials that can sign them.

Realtime Database Emulator
--------------------------

If not already present, create a ``firebase.json`` file in the root of your project and make sure that at least the
following fields are set (the port number can be changed to your requirements):

.. code-block:: js

    {
      "emulators": {
        "database": {
          "port": 9100
        }
      }
    }

.. note::
    The Realtime Database Emulator uses port ``9000`` by default. This port is also used by PHP-FPM, so it is
    recommended to choose one that differs to avoid conflicts.

Firebase Admin SDKs automatically connect to the Realtime Database emulator when the
``FIREBASE_DATABASE_EMULATOR_HOST`` environment variable is set.

.. code-block:: bash

    $ export FIREBASE_DATABASE_EMULATOR_HOST="localhost:9100"
