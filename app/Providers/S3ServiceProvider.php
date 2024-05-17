<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use Aws\Laravel\AwsServiceProvider;
use Aws\S3\S3Client;

class S3ServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton(S3Client::class, function ($app) {
            return new S3Client([
                'region'      => env('AWS_DEFAULT_REGION'),
                'credentials' => [
                    'key'    => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
                'version'     => 'latest'
            ]);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
