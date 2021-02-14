# Laravel Stateless Cart System for Web & API

[![Latest Version on Packagist](https://img.shields.io/packagist/v/webtamizhan/stateless-cart.svg?style=flat-square)](https://packagist.org/packages/webtamizhan/stateless-cart)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/webtamizhan/stateless-cart/run-tests?label=tests)](https://github.com/webtamizhan/stateless-cart/actions?query=workflow%3ATests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/webtamizhan/stateless-cart/Check%20&%20fix%20styling?label=code%20style)](https://github.com/webtamizhan/stateless-cart/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/webtamizhan/stateless-cart.svg?style=flat-square)](https://packagist.org/packages/webtamizhan/stateless-cart)


This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require webtamizhan/stateless-cart
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Webtamizhan\StatelessCart\StatelessCartServiceProvider" --tag="stateless-cart-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Webtamizhan\StatelessCart\StatelessCartServiceProvider" --tag="stateless-cart-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$stateless-cart = new Webtamizhan\StatelessCart();
echo $stateless-cart->echoPhrase('Hello, Webtamizhan!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Prabakaran T](https://github.com/webtamizhan)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
