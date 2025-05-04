# phpseclib3_rector

Rector rules to upgrade a phpseclib v2.0 install to phpseclib v3.0

## Overview

You can use [phpseclib2_compat](https://github.com/phpseclib/phpseclib2_compat) to make all your phpseclib v2.0 calls use phpseclib v3.0, internally, under the hood, or you can use this [Rector](https://getrector.com/) rule to upgrade your phpseclib v2.0 calls to phpseclib v3.0 calls.

## Installation

With [Composer](https://getcomposer.org/):

```
composer require phpseclib/phpseclib2_rector:~1.0
```

## Usage

Create a rector.php file with the following contents:

```php
<?php
use Rector\Config\RectorConfig;
use phpseclib\phpseclib3Rector\Set;

return RectorConfig::configure()
    ->withSets([Set::PATH]);
```
In the same directory where you created that file you can then run Rector by doing either of these commands:

```
vendor/bin/rector process src --dry-run
vendor/bin/rector process src
```
The files in the `src/` directory will either be full on modified or (in the case of `--dry-run`) the changes that would be made will be previewed.
