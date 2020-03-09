# Simple Event Dispatcher for PHP

Basic event dispatcher based on the PSR-14 standard.

## Install

Via Composer

``` bash
$ composer require netolabs/simple-event-dispatcher
```

## Requirements

PHP version 7.3 and up is required.

## Usage

### Adding a listener

``` php
$listenerProvider = new ListenerProvider();
$dispatcher = new EventDispatcher($listenerProvider);

$listenerProvider->addListener(MyEvent::class, function() {
    // ...
});
```

### Emitting an event

``` php
$event = new MyEvent();

$dispatcher->dispatch($event);
```

## License

The MIT License (MIT). Please see the [License File](https://github.com/NetoECommerce/simple-container/blob/master/LICENSE) for more information.
