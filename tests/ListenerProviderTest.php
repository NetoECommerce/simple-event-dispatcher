<?php declare(strict_types=1);

namespace Neto\Event\Test;

use Neto\Event\ListenerProvider;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\StoppableEventInterface;

class ListenerProviderTest extends TestCase
{
    /** @var ListenerProvider */
    private $listenerProvider;

    /** @var object */
    private $event;

    public function setUp()
    {
        $this->listenerProvider = new ListenerProvider();
        $this->event = new class implements StoppableEventInterface {
            public function isPropagationStopped(): bool
            {
                return false;
            }
        };
    }

    public function testListenersAreEmptyByDefault()
    {
        $listeners = $this->listenerProvider->getListenersForEvent($this->event);
        $this->assertEmpty(iterator_to_array($listeners));
    }

    public function testAddingAndFetchingListeners()
    {
        $stoppableListener = function(StoppableEventInterface $event) {};
        $stdClassListener = function(\stdClass $event) {};
        $stdClassEvent = new \stdClass();
        $this->listenerProvider->addListener(StoppableEventInterface::class, $stoppableListener);
        $this->listenerProvider->addListener(\stdClass::class, $stdClassListener);

        $this->assertSame(
            [$stoppableListener],
            iterator_to_array($this->listenerProvider->getListenersForEvent($this->event))
        );

        $this->assertSame(
            [$stdClassListener],
            iterator_to_array($this->listenerProvider->getListenersForEvent($stdClassEvent))
        );
    }
}