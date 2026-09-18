<?php

declare(strict_types=1);

namespace FelipeArnold\FilamentSteppedTabs\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use FelipeArnold\FilamentSteppedTabs\FilamentSteppedTabsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        // Filament\Support::packageRegistered() rebinds Livewire's DataStore
        // mechanism as a non-shared bind(). LivewireServiceProvider must be
        // registered last so its instance() binding wins and stays a
        // singleton (matches real apps, where composer alphabetically
        // discovers filament/* before livewire/*).
        return [
            BladeIconsServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            SupportServiceProvider::class,
            FilamentSteppedTabsServiceProvider::class,
            LivewireServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('app.key', 'base64:'.base64_encode(random_bytes(32)));

        $app['view']->addLocation(__DIR__.'/Fixtures/views');
    }
}
