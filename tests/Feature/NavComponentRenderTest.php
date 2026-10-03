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

it('marks only the active step as current', function (): void {
    $steps = [
        StepTab::make('draft')->label('Drafts')->count(0)->toArray(),
        StepTab::make('sent')->label('In progress')->count(0)->toArray(),
    ];

    $html = view('filament-stepped-tabs::components.nav', [
        'steps' => $steps,
        'activeStep' => 'sent',
    ])->render();

    expect(substr_count($html, 'aria-current="step"'))->toBe(1)
        ->and($html)->toMatch('/data-active aria-current="step"\s+class="[^"]*"\s+style="[^"]*"\s*>\s*(?:<svg|<span)[\s\S]*In progress/');
});

it('aligns step content to the start', function (): void {
    $html = view('filament-stepped-tabs::components.nav', [
        'steps' => [StepTab::make('draft')->label('Drafts')->toArray()],
        'activeStep' => 'draft',
    ])->render();

    expect($html)
        ->toContain('justify-start')
        ->not->toContain('justify-center');
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

it('cuts a notch on the left of the last step instead of an arrow', function (): void {
    $html = view('filament-stepped-tabs::components.nav', [
        'steps' => [StepTab::make('one')->toArray(), StepTab::make('two')->toArray()],
        'activeStep' => 'one',
    ])->render();

    expect($html)
        ->toContain('polygon(0 0, 100% 0, 100% 100%, 0 100%, 16px 50%)')
        ->not->toContain('polygon(16px 0, 100% 0, 100% 100%, 16px 100%, 0 50%)');
});
