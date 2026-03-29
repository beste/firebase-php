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

.. code-block:: php

   $appCheck = $factory->createAppCheck();

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
        $verification = $appCheck->verifyToken($appCheckTokenString);
    } catch (FailedToVerifyAppCheckToken $e) {
        // The token is invalid
    }

To enable replay protection for a security-critical endpoint, use the replay-protection contract method.
This performs an additional call to the App Check API and reports whether the token has already been consumed.

.. note::
   Replay protection is currently exposed through ``Kreait\Firebase\Contract\AppCheckWithReplayProtection`` as
   a transitional API to avoid a backwards-incompatible signature change in ``AppCheck::verifyToken()`` and
   preserve backwards compatibility in the current major version.
   In the next major release, this should be folded into ``AppCheck::verifyToken()``.

.. code-block:: php

    use Kreait\Firebase\Contract\AppCheck;
    use Kreait\Firebase\Contract\AppCheckWithReplayProtection;
    use Kreait\Firebase\Exception\AppCheck\FailedToVerifyAppCheckReplayProtection;

    /** @var AppCheck&AppCheckWithReplayProtection $appCheck */
    $verification = null;

    try {
        $verification = $appCheck->verifyTokenWithReplayProtection($appCheckTokenString);
    } catch (FailedToVerifyAppCheckReplayProtection $e) {
        // The token could not be consumed for replay protection.
    }

    if ($verification?->alreadyConsumed === true) {
        // Reject the request as a replay attempt.
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
