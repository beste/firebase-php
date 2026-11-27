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

.. _debug-tokens:

************
Debug Tokens
************

Debug tokens let apps running in debug environments, emulators or CI obtain valid App Check tokens without
a real attestation provider. You can register and manage debug tokens with the SDK; the app then uses the
secret with the App Check debug provider. See https://firebase.google.com/docs/app-check/web/debug-provider
for more information.

.. note::
   Debug token support is exposed through ``Kreait\Firebase\Contract\AppCheckWithDebugTokens`` as a
   transitional API to preserve backwards compatibility in the current major version.

.. code-block:: php

    use Kreait\Firebase\Contract\AppCheck;
    use Kreait\Firebase\Contract\AppCheckWithDebugTokens;

    /** @var AppCheck&AppCheckWithDebugTokens $appCheck */
    $appId = '1:123456789:ios:abcdef';

    // Register a debug token. If no secret is given, a random UUID4 is generated.
    // The secret is only available on the returned object; the API never returns it again.
    // Display names can be at most 50 characters long.
    $debugToken = $appCheck->createDebugToken($appId, 'CI');
    $secret = $debugToken->token; // Hand this to the app's App Check debug provider

    // Manage registered debug tokens (an app can have at most 20)
    $tokens = $appCheck->listDebugTokens($appId);
    $debugToken = $appCheck->getDebugToken($appId, $debugToken->id);
    $debugToken = $appCheck->updateDebugToken($appId, $debugToken->id, 'CI (renamed)');
    $appCheck->deleteDebugToken($appId, $debugToken->id);

