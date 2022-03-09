<?php

namespace Ezzaze\Airlabs;

use Ezzaze\Airlabs\Commands\AirlabsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_airlabs_table')
            ->hasCommand(AirlabsCommand::class);
    }
}
