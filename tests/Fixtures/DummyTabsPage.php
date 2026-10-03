<?php

declare(strict_types=1);

namespace FelipeArnold\FilamentSteppedTabs\Tests\Fixtures;

use FelipeArnold\FilamentSteppedTabs\Concerns\HasSteppedTabs;
use FelipeArnold\FilamentSteppedTabs\Tests\Fixtures\Models\Order;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

final class DummyTabsPage extends Component
{
    use HasSteppedTabs;

    public ?string $activeTab = 'open';

    public ?string $customer = null;

    /**
     * @return array<string, Tab>
     */
    public function getCachedTabs(): array
    {
        return [
            'open' => Tab::make('Open')
                ->icon(Heroicon::OutlinedClock)
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'open')),
            'paid' => Tab::make('Paid')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'paid')),
            'all' => Tab::make('All'),
        ];
    }

    public function getFilteredTableQuery(): Builder
    {
        $query = Order::query()->when($this->customer, fn (Builder $query, string $customer): Builder => $query->where('customer', $customer));

        $tabs = $this->getCachedTabs();

        return $this->activeTab && isset($tabs[$this->activeTab])
            ? $tabs[$this->activeTab]->modifyQuery($query)
            : $query;
    }

    public function render(): View
    {
        return view('dummy-tabs', ['steps' => $this->getSteps()]);
    }
}
