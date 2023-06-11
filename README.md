# logger
Simple logger, use SQLITE3 - Database.

## Installation
Logger uses Composer to install and update:

```bash
composer require it-m-h/logger
```
## Konfiguration

Change the file: vendor/it-m-h/logger/src/config.php

```php
    return [
        "DB" => './../data/logging.sqlite3',
        "TABLE" => 'log'
    ];
```

## Use
```php
$log = new \logger\Logger();
$log->write('Hello World!');

``` 


## show log
```php
 // TODO: show array with all log entries
```