<?php

declare(strict_types=1);

use FelipeArnold\FilamentSteppedTabs\Tests\Fixtures\DummyTabsPage;
use FelipeArnold\FilamentSteppedTabs\Tests\Fixtures\Models\Order;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

beforeEach(function (): void {
    Schema::create('orders', function (Blueprint $table): void {
        $table->id();
        $table->string('status');
        $table->string('customer');
    });

    Order::query()->insert([
        ['status' => 'open', 'customer' => 'acme'],
        ['status' => 'open', 'customer' => 'globex'],
        ['status' => 'paid', 'customer' => 'acme'],
        ['status' => 'void', 'customer' => 'acme'],
    ]);
});

afterEach(function (): void {
    DummyTabsPage::$tabsUsing = null;
    DummyTabsPage::$queryUsing = null;
});

/**
 * @return array{0: array<string, int>, 1: int}
 */
function countStepsWithQueryLog(DummyTabsPage $page): array
{
    DB::flushQueryLog();
    DB::enableQueryLog();

    $counts = collect($page->getSteps())->pluck('count', 'key')->all();

    DB::disableQueryLog();

    return [$counts, count(DB::getQueryLog())];
}

it('counts every step in a single query', function (): void {
    $page = Livewire::test(DummyTabsPage::class)->set('customer', 'acme')->instance();

    [$counts, $queries] = countStepsWithQueryLog($page);

    expect($counts)->toBe(['open' => 1, 'paid' => 1, 'all' => 3])
        ->and($queries)->toBe(1);
});

it('keeps nested, negated and multi-value conditions in the single query', function (): void {
    DummyTabsPage::$tabsUsing = fn (): array => [
        'nested' => Tab::make('Nested')->modifyQueryUsing(fn (Builder $query): Builder => $query
            ->where(fn (Builder $query): Builder => $query->where('status', 'paid')->orWhere('status', 'void'))),
        'not_open' => Tab::make('Not open')->modifyQueryUsing(fn (Builder $query): Builder => $query->whereNot('status', 'open')),
        'in' => Tab::make('In')->modifyQueryUsing(fn (Builder $query): Builder => $query->whereIn('status', ['open', 'void'])),
        'none' => Tab::make('None')->modifyQueryUsing(fn (Builder $query): Builder => $query->whereIn('status', [])),
        'all' => Tab::make('All'),
    ];

    $page = Livewire::test(DummyTabsPage::class)->set('activeTab', 'all')->set('customer', 'acme')->instance();

    [$counts, $queries] = countStepsWithQueryLog($page);

    expect($counts)->toBe(['nested' => 2, 'not_open' => 2, 'in' => 2, 'none' => 0, 'all' => 3])
        ->and($queries)->toBe(1);
});

it('applies the global scopes once to the shared query and counts a tab that removes one on its own', function (): void {
    DummyTabsPage::$queryUsing = fn (): Builder => Order::query()
        ->withGlobalScope('acme', fn (Builder $query): Builder => $query->where('customer', 'acme'));
    DummyTabsPage::$tabsUsing = fn (): array => [
        'open' => Tab::make('Open')->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'open')),
        'everyone' => Tab::make('Everyone')->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutGlobalScope('acme')),
        'all' => Tab::make('All'),
    ];

    $page = Livewire::test(DummyTabsPage::class)->set('activeTab', 'all')->instance();

    [$counts, $queries] = countStepsWithQueryLog($page);

    expect($counts)->toBe(['open' => 1, 'everyone' => 4, 'all' => 3])
        ->and($queries)->toBe(2);
});

it('counts on its own a tab that changes more than the where clauses', function (): void {
    DummyTabsPage::$tabsUsing = fn (): array => [
        'or_globex' => Tab::make('Or globex')->modifyQueryUsing(fn (Builder $query): Builder => $query->orWhere('customer', 'globex')),
        'grouped' => Tab::make('Grouped')->modifyQueryUsing(fn (Builder $query): Builder => $query->groupBy('status')->select('status')),
        'all' => Tab::make('All'),
    ];

    $page = Livewire::test(DummyTabsPage::class)->set('activeTab', 'all')->set('customer', 'acme')->instance();

    [$counts, $queries] = countStepsWithQueryLog($page);

    expect($counts)->toBe([
        'or_globex' => 4,
        'grouped' => Order::query()->where('customer', 'acme')->groupBy('status')->select('status')->count(),
        'all' => 3,
    ])->and($queries)->toBe(3);
});
