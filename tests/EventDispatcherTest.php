<?php declare(strict_types=1);

namespace Neto\Event\Test;

use Neto\Event\EventDispatcher;
use Neto\Event\ListenerProvider;
use Phake;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcherTest extends TestCase
{
    /** @var EventDispatcher */
    private $eventDispatcher;

    /** @var ListenerProvider */
    private $listenerProvider;

    /** @var callable */
    private $listener;

    /** @var object */
    private $event;

    public function setUp()
    {
        $this->event = new class implements StoppableEventInterface {
            public $called = 0;
            public $stopPropagation = false;
            public $stopPropagationAfter = 0;

            public function isPropagationStopped(): bool
            {
                $this->called++;
                return $this->stopPropagation && $this->called > $this->stopPropagationAfter;
            }
        };

        $this->listener = new class {
            public $called = 0;

            public function __invoke(object $event): void
            {
                $this->called++;
            }
        };

        $this->listenerProvider = Phake::mock(ListenerProvider::class);
        Phake::when($this->listenerProvider)->getListenersForEvent(Phake::anyParameters())
            ->thenReturn([$this->listener]);
        $this->eventDispatcher = new EventDispatcher($this->listenerProvider);
    }

    public function testImplementsEventDispatcherInterface()
    {
        $this->assertInstanceOf(EventDispatcherInterface::class, $this->eventDispatcher);
    }

    public function testDispatcherReturnsEventWithNoListeners()
    {
        Phake::when($this->listenerProvider)->getListenersForEvent(Phake::anyParameters())
            ->thenReturn([]);

        $actualEvent = $this->eventDispatcher->dispatch($this->event);
        $this->assertSame($this->event, $actualEvent);
        $this->assertEquals(0, $this->listener->called);
    }

    public function testDispatcherNotifiesListener()
    {
        $actualEvent = $this->eventDispatcher->dispatch($this->event);
        $this->assertSame($this->event, $actualEvent);
        $this->assertEquals(1, $this->listener->called);

        $this->eventDispatcher->dispatch($this->event);
        $this->assertEquals(2, $this->listener->called);
    }

    public function testEventCanBeStoppedMidIteration()
    {
        $this->event->stopPropagation = true;
        $this->event->stopPropagationAfter = 2;
        Phake::when($this->listenerProvider)->getListenersForEvent(Phake::anyParameters())
            ->thenReturn([$this->listener, $this->listener, $this->listener]);

        $this->eventDispatcher->dispatch($this->event);
        $this->assertEquals(2, $this->listener->called);
    }

    public function testStoppedEventReturnsEarly()
    {
        $this->event->stopPropagation = true;

        $actualEvent = $this->eventDispatcher->dispatch($this->event);
        $this->assertSame($this->event, $actualEvent);
        $this->assertEquals(0, $this->listener->called);
    }
}