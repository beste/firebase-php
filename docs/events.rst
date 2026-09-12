.. _events:

######
Events
######

The SDK can dispatch events through a `PSR-14 event dispatcher <https://www.php-fig.org/psr/psr-14/>`_. Provide the
dispatcher to the factory before creating a component:

.. code-block:: php

    $factory = $factory->withEventDispatcher($eventDispatcher);

Events are dispatched synchronously after an operation has completed. Exceptions thrown by event listeners are
passed to the caller.
