<?php

declare(strict_types=1);

namespace FelipeArnold\FilamentSteppedTabs;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class FilamentSteppedTabsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-stepped-tabs';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasViews();
    }
}
