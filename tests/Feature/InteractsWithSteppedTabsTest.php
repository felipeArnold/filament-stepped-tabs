<?php

declare(strict_types=1);

use FelipeArnold\FilamentSteppedTabs\Tests\Fixtures\DummySteppedComponent;
use Livewire\Livewire;

it('starts with the default defined by the component', function (): void {
    Livewire::test(DummySteppedComponent::class)
        ->assertSet('activeStep', 'draft');
});

it('changes activeStep when set via livewire', function (): void {
    Livewire::test(DummySteppedComponent::class)
        ->set('activeStep', 'signed')
        ->assertSet('activeStep', 'signed');
});
