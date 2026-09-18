<?php

declare(strict_types=1);

use FelipeArnold\FilamentSteppedTabs\StepTab;

it('monta step via fluent api com label, contador e icone', function (): void {
    $step = StepTab::make('sent')
        ->label('Em andamento')
        ->count(3)
        ->icon('heroicon-o-paper-airplane');

    expect($step->toArray())->toBe([
        'key' => 'sent',
        'label' => 'Em andamento',
        'count' => 3,
        'icon' => 'heroicon-o-paper-airplane',
        'color' => null,
    ]);
});

it('usa a key como label quando nenhum label e informado', function (): void {
    $step = StepTab::make('draft');

    expect($step->toArray()['label'])->toBe('draft');
});

it('rejeita key com caracteres invalidos', function (): void {
    StepTab::make('em andamento!');
})->throws(InvalidArgumentException::class);

it('aceita key com letras minusculas, numeros, hifen e underscore', function (): void {
    $step = StepTab::make('step-2_final');

    expect($step->toArray()['key'])->toBe('step-2_final');
});
