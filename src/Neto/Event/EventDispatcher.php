<?php declare(strict_types=1);

namespace Neto\Event;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Event dispatcher based on the PSR-14 standard.
 *
 * @package Neto\Event
 */
class EventDispatcher implements EventDispatcherInterface
{
    /** @var ListenerProviderInterface */
    private $listenerProvider;

    /**
     * EventDispatcher constructor.
     * @param ListenerProviderInterface $listenerProvider
     */
    public function __construct(ListenerProviderInterface $listenerProvider)
    {
        $this->listenerProvider = $listenerProvider;
    }

    /**
     * Provide all relevant listeners with an event to process
     *
     * @param  object $event The event to process
     * @return object The processed event
     */
    public function dispatch(object $event)
    {
        $eventIsStoppable = ($event instanceof StoppableEventInterface);
        if ($eventIsStoppable && $event->isPropagationStopped()) {
            return $event;
        }

        $listeners = $this->listenerProvider->getListenersForEvent($event);
        foreach ($listeners as $listener) {
            call_user_func($listener, $event);

            if ($eventIsStoppable && $event->isPropagationStopped()) {
                break;
            }
        }

        return $event;
    }
}
