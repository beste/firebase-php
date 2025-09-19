.. _setup:

#####
Setup
#####

**********************
Google Service Account
**********************

In order to access a Firebase project using a server SDK, you must authenticate your requests to Firebase with
`Service Account credentials <https://developers.google.com/identity/protocols/OAuth2ServiceAccount>`_.

The SDK is able to auto-discover the Service Account for your project in the following conditions:

#. Your application runs on Google Cloud Engine.

#. The path to the JSON key file or a JSON string (not recommended) is provided by a
   ``GOOGLE_APPLICATION_CREDENTIALS`` variable.

#. The JSON Key file is located in Google's "well known path"

   * on Linux/MacOS: ``$HOME/.config/gcloud/application_default_credentials.json``
   * on Windows: ``$APPDATA/gcloud/application_default_credentials.json``

If auto-discovery is not wanted, you can generate a private key file in JSON format and provide it to
the factory directly. To generate a private key file for your service account:

1. Open https://console.firebase.google.com/project/_/settings/serviceaccounts/adminsdk and select
   the project you want to generate a private key file for.
2. Click **Generate New Private Key**, then confirm by clicking **Generate Key**
3. Securely store the JSON file containing the key.

.. note::
    You should store the JSON file outside of your code repository to avoid accidentally exposing it
    to the outside world.

You can then configure the SDK to use this Service Account:

.. tab-set::

   .. tab-item:: SDK

      .. code-block:: php

         use Kreait\Firebase\Factory;

         $factory = (new Factory)->withServiceAccount('/path/to/firebase_credentials.json');

   .. tab-item:: Symfony

      See the `Symfony configuration documentation <https://github.com/kreait/firebase-bundle#configuration>`_ for setup details.

   .. tab-item:: Laravel

      See the `Laravel configuration documentation <https://github.com/kreait/laravel-firebase#configuration>`_ for setup details.

**********
Project ID
**********

.. note::
    It is not necessary to explicitly configure the project ID in most cases.

Service Account credentials usually include the ID of the Google Cloud Project your Firebase project belongs to.

If you use another type of credential, it might be necessary to provide it manually to the Firebase Factory.

.. code-block:: php

    use Kreait\Firebase\Factory;

    $factory = (new Factory())
        ->withProjectId('my-project')
        ->withDatabaseUri('https://my-project.firebaseio.com');

You can also set a ``GOOGLE_CLOUD_PROJECT=<project-id>`` environment variable before instantiating a component with
the factory.


*********************
Realtime Database URI
*********************

.. note::
    You can find the URI for your Realtime Database at
    `https://console.firebase.google.com/project/_/database <https://console.firebase.google.com/project/_/database>`_.
    For recently created Firebase projects the default database URI usually has the format
    ``https://<project-id>-default-rtdb.firebaseio.com``. Databases in projects created before September 2020 had the
    default database URI ``https://<project-id>.firebaseio.com``.

    For backward compatibility reasons, if you don't specify a database URI, the SDK will use the project ID defined
    in the Service Account JSON file to automatically generate it.


.. code-block:: php

    use Kreait\Firebase\Factory;

    $factory = (new Factory())
        ->withDatabaseUri('https://my-project.firebaseio.com');

*******
Caching
*******

The SDK, by default, uses a single in-memory cache for all components that support caching, so you can re-use them
within the current process.

If you want to cache data more effectively, you can provide any
`implementation of psr/cache <https://packagist.org/providers/psr/cache-implementation>`_ to the Firebase factory. By
using a persistent cache, you can avoid unnecessary API requests.

.. code-block:: php

    use Symfony\Component\Cache\Simple\FilesystemCache;

    // One Cache instance for all components
    $factory = $factory->withDefaultCache(new FilesystemCache());

    // Cache used for authentication tokens, so that they requests don't have to be re-authenticated
    $factory = $factory->withAuthTokenCache(new FilesystemCache());

    // Cache used to store JWKS (JSON Web Key Sets), so that they don't have to be fetched again
    $factory = $factory->withKeySetCache(new FilesystemCache());

    // Cache used to store verified ID tokens and session cookies, so that they don't have to be verified again
    $factory = $factory->withVerifierCache(new FilesystemCache());

.. note::
    The example uses the `Symfony Cache Component <https://symfony.com/doc/current/components/cache.html>`_, but you
    are, of course, free to use any other PSR-6 cache implementation.

********************
End User Credentials
********************

.. note::
    While theoretically possible, it's not recommended to use end user credentials in the context
    of a Server-to-Server backend application.

When using End User Credentials (for example if you set you application default credentials locally
with ``gcloud auth application-default login``), you need to provide the ID of the project you
want to access directly and suppress warnings triggered by the Google Auth Component:

.. code-block:: php

    use Kreait\Firebase\Factory;

    putenv('SUPPRESS_GCLOUD_CREDS_WARNING=true');

    // This will use the project defined in the Service Account
    // credentials files by default
    $base = (new Factory())->withProjectId('firebase-project-id');

.. _http-client-options:

*******************
HTTP Client Options
*******************

You can configure the behavior of the Guzzle HTTP Client performing the API requests by passing an
instance of ``Kreait\Firebase\Http\HttpClientOptions`` to the factory before creating a
service.

.. code-block:: php

    use Kreait\Firebase\Http\HttpClientOptions;

    $options = HttpClientOptions::default();

    // Set the maximum amount of seconds (float) that can pass before
    // a request is considered timed out
    // (default: indefinitely)
    $options = $options->withTimeOut(3.5);

    // Use a proxy that all API requests should be passed through.
    // (default: none)
    $options = $options->withProxy('tcp://<host>:<port>');

    // Use a custom handler
    $options = $options->withGuzzleHandler(...);

    $factory = $factory->withHttpClientOptions($options);

    // Newly created services will now use the new HTTP options
    $realtimeDatabase = $factory->createDatabase();

Setting Guzzle Config Options
=============================

In addition to the explicit settings above, you can fully customize the configuration of the Guzzle HTTP Client:

.. code-block:: php

    use Kreait\Firebase\Http\HttpClientOptions;

    $options = HttpClientOptions::default()
        ->withGuzzleConfigOption('single', 'value')
        ->withGuzzleConfigOptions([
            'first' => 'value',
            'second' => 'value',
        ]);

.. note::
    You can find all Guzzle Config Options at
    `Guzzle: Request Options <https://docs.guzzlephp.org/en/stable/request-options.html>`_

Adding Guzzle Middlewares
=========================

You can also add middlewares to the Guzzle HTTP Client:

.. code-block:: php

    use Kreait\Firebase\Http\HttpClientOptions;

    $options = HttpClientOptions::default();

    # Adding a single middleware
    $options = $options->withGuzzleMiddleware($myMiddleware, 'my_middleware'); // The name can be omitted

    # Adding multiple middlewares
    $options = $options->withGuzzleMiddlewares([
        # Just providing the middleware
        $myMiddleware,
        # Alternative notation:
        ['middleware' => $myMiddleware]
        # Providing a named middleware
        ['middleware' => $myMiddleware, 'name' => 'my_middleware'],
    ]);

.. note::
    You can find more information about Guzzle Middlewares at
    `Guzzle: Handlers and Middleware <https://docs.guzzlephp.org/en/stable/handlers-and-middleware.html>`_

*******
Logging
*******

In order to log API requests to the Firebase APIs, you can provide the factory with loggers
implementing ``Psr\Log\LoggerInterface``.

The following examples use the `Monolog <https://github.com/Seldaek/monolog>`_ logger, but
work with any `PSR-3 log implementation <https://packagist.org/providers/psr/log-implementation>`_.

.. code-block:: php

    use GuzzleHttp\MessageFormatter;
    use Kreait\Firebase\Factory;
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;

    $httpLogger = new Logger('firebase_http_logs');
    $httpLogger->pushHandler(new StreamHandler('path/to/firebase_api.log', Logger::INFO));

    // Without further arguments, requests and responses will be logged with basic
    // request and response information. Successful responses will be logged with
    // the 'info' log level, failures (Status code >= 400) with 'notice'
    $factory = $factory->withHttpLogger($httpLogger);

    // You can configure the message format and log levels individually
    $messageFormatter = new MessageFormatter(MessageFormatter::SHORT);
    $factory = $factory->withHttpLogger(
        $httpLogger, $messageFormatter, $successes = 'debug', $errors = 'warning'
    );

    // You can provide a separate logger for detailed HTTP message logs
    $httpDebugLogger = new Logger('firebase_http_debug_logs');
    $httpDebugLogger->pushHandler(
        new StreamHandler('path/to/firebase_api_debug.log',
        Logger::DEBUG)
    );

    // Logs will include the full request and response headers and bodies
    $factory = $factory->withHttpDebugLogger($httpDebugLogger)

