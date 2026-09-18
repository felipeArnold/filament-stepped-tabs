<?php

declare(strict_types=1);

use FelipeArnold\FilamentSteppedTabs\StepTab;

it('renders all given steps with their count', function (): void {
    $steps = [
        StepTab::make('draft')->label('Drafts')->count(5)->toArray(),
        StepTab::make('sent')->label('In progress')->count(3)->toArray(),
    ];

    $html = view('filament-stepped-tabs::components.nav', [
        'steps' => $steps,
        'activeStep' => 'sent',
    ])->render();

    expect($html)
        ->toContain('Drafts')
        ->toContain('5 items')
        ->toContain('In progress')
        ->toContain('3 items');
});

it('marks the active step with a highlight class', function (): void {
    $steps = [
        StepTab::make('draft')->label('Drafts')->count(0)->toArray(),
        StepTab::make('sent')->label('In progress')->count(0)->toArray(),
    ];

    $html = view('filament-stepped-tabs::components.nav', [
        'steps' => $steps,
        'activeStep' => 'sent',
    ])->render();

    expect($html)->toContain('bg-blue-100');
});

it('renders the step icon when given', function (): void {
    $steps = [
        StepTab::make('sent')->label('In progress')->count(1)->icon('heroicon-o-paper-airplane')->toArray(),
    ];

    $html = view('filament-stepped-tabs::components.nav', [
        'steps' => $steps,
        'activeStep' => 'sent',
    ])->render();

    expect($html)->toContain('svg');
});

it('does not render an icon when none is given', function (): void {
    $steps = [
        StepTab::make('draft')->label('Drafts')->count(1)->toArray(),
    ];

    $html = view('filament-stepped-tabs::components.nav', [
        'steps' => $steps,
        'activeStep' => 'draft',
    ])->render();

    expect($html)->not->toContain('svg');
});
