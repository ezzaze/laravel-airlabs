<?php

namespace Ezzaze\Airlabs;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Ezzaze\Airlabs\Commands\AirlabsCommand;

class AirlabsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('airlabs')
            ->hasConfigFile();
        // ->hasViews()
        // ->hasMigration('create_airlabs_table')
        // ->hasCommand(AirlabsCommand::class);
    }

    public function packageBooted()
    {
        $this->app->singleton(Airlabs::class, fn () => new Airlabs());
    }
}
