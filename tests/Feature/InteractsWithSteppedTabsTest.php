<?php

declare(strict_types=1);

use FelipeArnold\FilamentSteppedTabs\Tests\Fixtures\DummySteppedComponent;
use Livewire\Livewire;

it('inicia com o default definido pelo componente', function (): void {
    Livewire::test(DummySteppedComponent::class)
        ->assertSet('activeStep', 'draft');
});

it('activeStep muda ao setar via livewire', function (): void {
    Livewire::test(DummySteppedComponent::class)
        ->set('activeStep', 'signed')
        ->assertSet('activeStep', 'signed');
});
