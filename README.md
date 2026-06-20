# Auto tweets for Laravel using OAuth

[![Latest Version on Packagist](https://img.shields.io/packagist/v/claraleigh/autotweet-for-laravel.svg?style=flat-square)](https://packagist.org/packages/claraleigh/autotweet-for-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/claraleigh/autotweet-for-laravel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/claraleigh/autotweet-for-laravel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/claraleigh/autotweet-for-laravel/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/claraleigh/autotweet-for-laravel/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/claraleigh/autotweet-for-laravel.svg?style=flat-square)](https://packagist.org/packages/claraleigh/autotweet-for-laravel)

This package provides a Twitter channel for Laravel notifications, allowing you to send tweets from your application.

## Requirements

- PHP 8.2 or higher
- Laravel 10 or 11

## Support us

Hi there! If you're using this package, please consider supporting me on [GitHub Sponsors](https://github.com/sponsors/ClaraLeigh). It would mean a lot to me.

## Installation

You can install the package via composer:

```bash
composer require claraleigh/autotweet-for-laravel
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="autotweet-for-laravel-migrations"
php artisan migrate
```

Update your user model's casts to include the `twitter_token` field:

```php
$casts = [
    // Existing casts
    'twitter_token' => 'object',
];
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="autotweet-for-laravel-config"
```

## Usage

Update your Notification file's `via` method to include the Twitter channel:

```php
public function via(object $notifiable): array
{
    return [TwitterChannel::class];
}
```

Add a `toTwitter` method to your Notification file:

```php
public function toTwitter($notifiable): TwitterMessage
{
    $post = (new TwitterStatusUpdate(
        __('Come see visit profile :url ❤️', ['url' => 'https://google.com/'])
    ));
   
    // Optional: Add an image to the tweet
    $post->withImage('path/to/image.jpg');

    return $post;
}
```

## Alternative User Model

To change the default user model, update the table used in the migration file and add the following code to your service provider:

```php
AutotweetForLaravelServiceProvider::useUserModel(ExampleModel::class);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Clara Leigh](https://github.com/ClaraLeigh)
- [All Contributors](../../contributors)

Initially based on [Laravel Twitter Channel](https://github.com/laravel-notification-channels/twitter)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
