<?php

namespace ClaraLeigh\XForLaravel;

use Abraham\TwitterOAuth\TwitterOAuth;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class XForLaravelServiceProvider extends PackageServiceProvider
{
    public static $userModel = 'App\\Models\\User';

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->app
            ->when([TwitterChannel::class])
            ->needs(TwitterOAuth::class)
            ->give(function () {
                $api = new TwitterOAuth(
                    consumerKey: config('services.x-for-laravel.consumer_key'),
                    consumerSecret: config('services.x-for-laravel.consumer_secret'),
                    oauthToken: config('services.x-for-laravel.access_token'),
                    oauthTokenSecret: config('services.x-for-laravel.access_secret'),
                );
                $api->setApiVersion('2');

                return $api;
            });

        return parent::boot();
    }

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('x-for-laravel')
            ->hasConfigFile()
            ->hasMigration('create_x_for_laravel_table');
    }

    /**
     * Set the user model class name.
     *
     * @param  string  $userModel
     * @return void
     */
    public static function useUserModel($userModel)
    {
        static::$userModel = $userModel;
    }
}
