# PSR-6 Cache for Craft CMS
[![Join the chat at https://gitter.im/flipboxfactory/craft-psr6](https://badges.gitter.im/flipboxfactory/craft-psr6.svg)](https://gitter.im/flipboxfactory/craft-psr6?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Latest Version](https://img.shields.io/github/release/flipboxfactory/craft-psr6.svg?style=flat-square)](https://github.com/flipboxfactory/craft-psr6/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/flipboxfactory/craft-psr6/master.svg?style=flat-square)](https://travis-ci.org/flipboxfactory/craft-psr6)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/flipboxfactory/craft-psr6.svg?style=flat-square)](https://scrutinizer-ci.com/g/flipboxfactory/craft-psr6/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/flipboxfactory/craft-psr6.svg?style=flat-square)](https://scrutinizer-ci.com/g/flipboxfactory/craft-psr6)
[![Total Downloads](https://img.shields.io/packagist/dt/flipboxfactory/craft-psr6.svg?style=flat-square)](https://packagist.org/packages/flipboxfactory/craft-psr6)

This package provides simple mechanism for PSR-6 Cache via Craft CMS.

## Installation

To install, use composer:

```
composer require flipboxfactory/craft-psr6
```

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Usage
Define it as a component in your plugin
```php 
'components' => [
    'psr6cache' => [
        'class' => flipbox\craft\psr6\Cache::class
     ]
]
```
or via your composer as an 'extra' definition
```json
"components": {
  "psr6cache": "flipbox\\craft\\psr6\\Cache"
}
```

## Contributing

Please see [CONTRIBUTING](https://github.com/flipboxfactory/craft-psr6/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Flipbox Digital](https://github.com/flipbox)

## License

The MIT License (MIT). Please see [License File](https://github.com/flipboxfactory/craft-psr6/blob/master/LICENSE) for more information.
