# SSO Powered Access Control for Filament

[![Latest Version on Packagist](https://img.shields.io/packagist/v/chrisreedio/bastion.svg?style=flat-square)](https://packagist.org/packages/chrisreedio/bastion)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/chrisreedio/bastion/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/chrisreedio/bastion/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/chrisreedio/bastion/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/chrisreedio/bastion/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/chrisreedio/bastion.svg?style=flat-square)](https://packagist.org/packages/chrisreedio/bastion)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require chrisreedio/bastion
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="bastion-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="bastion-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="bastion-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$bastion = new ChrisReedIO\Bastion();
echo $bastion->echoPhrase('Hello, ChrisReedIO!');
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

Special thanks to @Althinect and @bezhanSalleh for their packages and hard work. 
This is both inspired by and based on their work. This package would not be possible without them.

- [Chris Reed](https://github.com/chrisreedio)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
