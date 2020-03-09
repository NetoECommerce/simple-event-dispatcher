<?php declare(strict_types=1);

namespace Neto\Event;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Class ListenerProvider
 * @package Neto\Event
 */
class ListenerProvider implements ListenerProviderInterface
{
    /** @var callable[] */
    private $listeners = [];

    /**
     * Fetch all listeners matching the event's type
     *
     * @param  object $event
     * @return iterable
     */
    public function getListenersForEvent(object $event): iterable
    {
        foreach ($this->listeners as $type => $listeners) {
            if (!$event instanceof $type) {
                continue;
            }
            foreach ($listeners as $listener) {
                yield $listener;
            }
        }
    }

    /**
     * Push a listener on to the array
     *
     * @param string $type
     * @param callable $listener
     * @return void
     */
    public function addListener(string $type, callable $listener)
    {
        $this->listeners[$type][] = $listener;
    }
}
