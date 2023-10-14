<?php

namespace Foodticket\FilesystemCloudflareImages;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

class FilesystemCloudflareImagesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Storage::extend('cloudflare-images', function (Application $app, array $config) {
            $adapter = new CloudflareImagesAdapter($config);

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        //
    }
}
