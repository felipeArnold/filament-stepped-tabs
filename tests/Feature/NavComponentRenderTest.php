<?php

declare(strict_types=1);

use FelipeArnold\FilamentSteppedTabs\StepTab;

it('renderiza todos os steps recebidos com contador', function (): void {
    $steps = [
        StepTab::make('draft')->label('Rascunhos')->count(5)->toArray(),
        StepTab::make('sent')->label('Em andamento')->count(3)->toArray(),
    ];

    $html = view('filament-stepped-tabs::components.nav', [
        'steps' => $steps,
        'activeStep' => 'sent',
    ])->render();

    expect($html)
        ->toContain('Rascunhos')
        ->toContain('5 items')
        ->toContain('Em andamento')
        ->toContain('3 items');
});

it('marca o step ativo com classe de destaque', function (): void {
    $steps = [
        StepTab::make('draft')->label('Rascunhos')->count(0)->toArray(),
        StepTab::make('sent')->label('Em andamento')->count(0)->toArray(),
    ];

    $html = view('filament-stepped-tabs::components.nav', [
        'steps' => $steps,
        'activeStep' => 'sent',
    ])->render();

    expect($html)->toContain('bg-blue-100');
});
