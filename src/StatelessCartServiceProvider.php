<?php

namespace Webtamizhan\StatelessCart;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Webtamizhan\StatelessCart\Commands\StatelessCartCommand;

class StatelessCartServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('stateless-cart')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_stateless_cart_table')
            ->hasCommand(StatelessCartCommand::class);
    }
}
