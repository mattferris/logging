Logging
-------

A PSR-3 compliant logger.

## Log to a file

```php
use MattFerris\Logging\Logger;
use MattFerris\Logging\Handlers\FileHandler;

$logger = new Logger([
    ['handler' => new FileHandler('log.txt')]
]);

$logger->error('an error occured');
```

In the above case, all log messages will recorded in the log file. To isolate
one or more log level's specifically, you can specify a `levels` key when
passing the handler.

```php

use MattFerris\Logging\Logger;
use MattFerris\Logging\Handlers\FileHandler;
use Psr\Log\LogLevel;

$logger = new Logger([
    [
        'handler' => new FileHandler('log.txt'),
        'levels' => [ LogLevel::EMERGENCY, LogLevel::CRITICAL, LogLevel::ERROR ]
    ]
]);
```

It's also possible to specify a `maxlevel` key so the handler will record
everything up to a specific level. The above example could be simplied like so:

```php
$logger = new Logger([
    [
        'handler' => new FileHandler('log.txt'),
        'maxlevel' => LogLevel::ERROR
    ]
]);
```

Multiple handlers can be used to log to multiple destinations. This could be
used to record different log levels to different files, or even a file and
a database. Currently, only the `FileHandler` is included, but any `callable`
or class implementing `MattFerris\Logging\Handlers\HandlerInterface` can be
used.

## Log message placeholders

As per PSR-3, placeholders can be used in log messages that are then populated
using the corresponding key in the provided `$context` array.

```php
$logger->error('the user {user} does not exist', ['user' => 'joe']);
```

This would result in the log message `the user joe does not exist`. This
substitution is handled by the `InterpolationHelper` helper class.

```php
use MattFerris\Logging\Helpers\InterpolationHelper;

$logger = new Logger($handlers, [ new InterpolationHelper() ]);
```
