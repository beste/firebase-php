#########
App Check
#########

The Firebase Admin SDK for PHP provides an API for verifying custom backends using Firebase App Check.

Before you start, please read about Firebase App Check in the official documentation:

* `Introduction to Firebase App Check <https://firebase.google.com/docs/app-check>`_
* `Verify App Check tokens from a custom backend (Client-side) <https://firebase.google.com/docs/app-check/custom-resource-backend>`_
* `Implement a custom App Check provider <https://firebase.google.com/docs/app-check/custom-provider>`_

************************************
Initializing the App Check component
************************************

.. tab-set::

   .. tab-item:: SDK

      .. code-block:: php

         $appCheck = $factory->createAppCheck();

   .. tab-item:: Symfony

      See the `Symfony Bundle documentation <https://github.com/kreait/firebase-bundle>`_ for configuration details.

      .. code-block:: php

         use Kreait\Firebase\Contract\AppCheck;

         class MyService
         {
             public function __construct(AppCheck $appCheck)
             {
                 $this->appCheck = $appCheck;
             }
         }

   .. tab-item:: Laravel

      See the `Laravel Package documentation <https://github.com/kreait/laravel-firebase>`_ for configuration details.

      .. code-block:: php

         $appCheck = app('firebase.app_check');


.. _verify-app-check-tokens:

***********************
Verify App Check Tokens
***********************

The Firebase Admin SDK has a built-in method for validating App Check tokens.

See https://firebase.google.com/docs/app-check/custom-resource-backend for more information.

.. code-block:: php

    use Kreait\Firebase\Exception\AppCheck\FailedToVerifyAppCheckToken;

    $appCheckTokenString = '...';

    try {
        $appCheck->verifyToken($appCheckTokenString);
    } catch (FailedToVerifyAppCheckToken $e) {
        // The token is invalid
    }

.. _create-a-custom-provider:

************************
Create a Custom Provider
************************

The Firebase Admin SDK has a built-in method for creating custom provider of Firebase App Check tokens.
It creates a custom token and then exchanges it for Firebase App Check token that can be sent back to the client.

See https://firebase.google.com/docs/app-check/custom-provider for more information.

.. code-block:: php

    $token = $appCheck->createToken("com.example.app-id");
