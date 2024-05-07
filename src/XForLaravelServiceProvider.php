<?php

namespace ClaraLeigh\XForLaravel;

use Abraham\TwitterOAuth\TwitterOAuth;
use ClaraLeigh\XForLaravel\Services\TwitterService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class XForLaravelServiceProvider extends PackageServiceProvider
{
    public static $userModel = 'App\\Models\\User';

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->app
            ->when([TwitterService::class])
            ->needs(TwitterOAuth::class)
            ->give(function () {
                $api = new TwitterOAuth(
                    consumerKey: config('x-for-laravel.client_id'),
                    consumerSecret: config('x-for-laravel.client_secret')
                );
                $api->setApiVersion('2');

                return $api;
            });

        return parent::boot();
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('x-for-laravel')
            ->hasConfigFile()
            ->hasRoute('web')
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
