<?php

declare(strict_types=1);

use FelipeArnold\FilamentSteppedTabs\Tests\Fixtures\DummyTabsPage;
use FelipeArnold\FilamentSteppedTabs\Tests\Fixtures\Models\Order;
use Filament\Schemas\Components\View;
use Illuminate\Database\Schema\Blueprint;
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
    ]);
});

it('builds one step per tab with label, icon and count', function (): void {
    $steps = Livewire::test(DummyTabsPage::class)->instance()->getSteps();

    expect($steps)->toHaveCount(3)
        ->and($steps[0])->toMatchArray(['key' => 'open', 'label' => 'Open', 'count' => 2])
        ->and($steps[0]['icon'])->not->toBeNull()
        ->and($steps[1])->toMatchArray(['key' => 'paid', 'count' => 1])
        ->and($steps[2])->toMatchArray(['key' => 'all', 'count' => 3]);
});

it('counts every tab ignoring the active one but respecting the filters', function (): void {
    $component = Livewire::test(DummyTabsPage::class)
        ->set('activeTab', 'paid')
        ->set('customer', 'acme');

    expect(collect($component->instance()->getSteps())->pluck('count', 'key')->all())
        ->toBe(['open' => 1, 'paid' => 1, 'all' => 2])
        ->and($component->get('activeTab'))->toBe('paid');
});

it('binds the nav to the activeTab property', function (): void {
    Livewire::test(DummyTabsPage::class)
        ->assertSeeHtml("\$set('activeTab', 'paid')")
        ->set('activeTab', 'paid')
        ->assertSeeHtml('aria-current="step"');
});

it('re-evaluates the steps every render', function (): void {
    $component = Livewire::test(DummyTabsPage::class)->instance();
    $content = $component->getTabsContentComponent();

    expect($content)->toBeInstanceOf(View::class)
        ->and($content->getViewData()['activeStep'])->toBe('open');

    $component->activeTab = 'paid';

    expect($content->getViewData()['activeStep'])->toBe('paid');
});
