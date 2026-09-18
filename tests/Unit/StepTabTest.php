<?php

declare(strict_types=1);

use FelipeArnold\FilamentSteppedTabs\StepTab;

it('builds a step via the fluent api with label, count and icon', function (): void {
    $step = StepTab::make('sent')
        ->label('In progress')
        ->count(3)
        ->icon('heroicon-o-paper-airplane');

    expect($step->toArray())->toBe([
        'key' => 'sent',
        'label' => 'In progress',
        'count' => 3,
        'icon' => 'heroicon-o-paper-airplane',
        'color' => null,
    ]);
});

it('uses the key as the label when no label is given', function (): void {
    $step = StepTab::make('draft');

    expect($step->toArray()['label'])->toBe('draft');
});

it('rejects a key with invalid characters', function (): void {
    StepTab::make('in progress!');
})->throws(InvalidArgumentException::class);

it('accepts a key with lowercase letters, numbers, hyphen and underscore', function (): void {
    $step = StepTab::make('step-2_final');

    expect($step->toArray()['key'])->toBe('step-2_final');
});
