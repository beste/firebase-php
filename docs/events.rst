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

For example, with the `Symfony EventDispatcher <https://symfony.com/doc/current/components/event_dispatcher.html>`_:

.. code-block:: php

    use Kreait\Firebase\Messaging\Event\MessagesSent;
    use Symfony\Component\EventDispatcher\EventDispatcher;

    $eventDispatcher = new EventDispatcher();

    $eventDispatcher->addListener(
        MessagesSent::class,
        function (MessagesSent $event): void {
            $report = $event->report;
            $validateOnly = $event->validateOnly;
        },
    );

    $messaging = $factory
        ->withEventDispatcher($eventDispatcher)
        ->createMessaging();
