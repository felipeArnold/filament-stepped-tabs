<?php

declare(strict_types=1);

namespace FelipeArnold\FilamentSteppedTabs\Concerns;

use FelipeArnold\FilamentSteppedTabs\StepTab;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\View;
use Illuminate\Database\Eloquent\Builder;

/**
 * Renders the Filament `getTabs()` of a page that uses `HasTabs` (e.g. `ListRecords`)
 * as stepped tabs. Each step counts the records of its tab with the table filters applied.
 */
trait HasSteppedTabs
{
    public function getTabsContentComponent(): Component
    {
        return View::make('filament-stepped-tabs::components.nav')
            ->viewData(fn (): array => [
                'steps' => $this->getSteps(),
                'activeStep' => (string) $this->activeTab,
                'property' => 'activeTab',
            ])
            ->hidden(empty($this->getCachedTabs()));
    }

    /**
     * @return array<int, array{key: string, label: string, count: int, icon: mixed, color: ?string}>
     */
    public function getSteps(): array
    {
        $baseQuery = $this->getStepsBaseQuery();

        return collect($this->getCachedTabs())
            ->map(fn (Tab $tab, string|int $key): array => StepTab::make((string) $key)
                ->label((string) $tab->getLabel())
                ->icon($tab->getIcon())
                ->count($baseQuery ? $tab->modifyQuery($baseQuery->clone())->count() : 0)
                ->toArray())
            ->values()
            ->all();
    }

    protected function getStepsBaseQuery(): ?Builder
    {
        $activeTab = $this->activeTab;
        $this->activeTab = null;

        try {
            return $this->getFilteredTableQuery();
        } finally {
            $this->activeTab = $activeTab;
        }
    }
}
