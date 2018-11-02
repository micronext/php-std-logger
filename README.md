# PHP StdOut Logger for CLI Apps.

## **Purpose:** App Logs outputs to **stdout/stderr** according to **LogLevel** and in **user-defined** format. **IN RUNTIME**!

> And for the hail of Satan, of cource 😈

## **Motivation:** Its might be very helpful for 🐳 Docker fans

> Designed as simple as possible, following [php-fig](https://www.php-fig.org) PSR's and [codestyle](https://en.wikipedia.org/wiki/Programming_style) tools

## ⚠️ WARNING! Today it's for `PHP_CLI` mode only!

### Tiny and _simple-first_ **PHP Logger** library that output data to console's `stdout`/`stderr` in depends of `LogLevel`.

### Implements interfaces defined by [PSR-3](https://www.php-fig.org/psr/psr-3/).

## Install

```sh
composer require micronext/php-std-logger
```

## Development

Clone this repo

```sh
git clone https://github.com/micronext/php-std-logger
cd php-std-logger
composer install
```

## Usage:

```php
<?php

namespace YourAmazingApp;

use Psr\Log\LogLevel;
use MicroNext\StdOut\Logger;

$logger = new Logger;

$logger->emergency("emergency test");
$logger->alert("alert test");
$logger->critical("critical test");
$logger->error("error test");
$logger->warning("warning test");
$logger->notice("notice test");
$logger->info("info test");
$logger->debug("debug test");

// Also, you can call ->log() directly:
$logger->log(LogLevel::NOTICE, "notice throught log test";
$logger->log(null, "log test");
```

## Currently `std output` separated to 2 targets:

> [LogLevel definition @ PSR-3](https://www.php-fig.org/psr/psr-3/#5-psrlogloglevel)

```c
stderr << log
when "LogLevel"
     ::EMERGENCY // System is unusable.
     ::ALERT // Action must be taken immediately.
     ::CRITICAL  // Critical conditions.
     ::ERROR // Runtime errors that do not require immediate action but should typically be logged and monitored.

stdout << log
when LogLevel
     ::WARNING // Exceptional occurrences that are not errors.
     ::NOTICE // Normal but significant events.
     ::INFO // Interesting events.
     ::DEBUG // Detailed debug information.
```

## MIT LICENSE

```
Copyright 2018 (c) MicroNext

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
```

2018 © [MicroNext](https://github.com/micronext) (represented by [Yevhenii Ivanets](https://github.com/ivanets))