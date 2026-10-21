<?php

declare(strict_types=1);

namespace FelipeArnold\FilamentSteppedTabs\Concerns;

use Closure;
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
     * @return array<int, array{key: string, label: string, count: int, icon: mixed, color: ?string, description: ?string}>
     */
    public function getSteps(): array
    {
        $tabs = $this->getCachedTabs();
        $counts = $this->countSteps($tabs);

        return collect($tabs)
            ->map(fn (Tab $tab, string|int $key): array => StepTab::make((string) $key)
                ->label((string) $tab->getLabel())
                ->icon($tab->getIcon())
                ->count($counts[$key] ?? 0)
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

    /**
     * Counts every tab in a single query, one `count(case when ...)` per tab. A tab that changes
     * the base query beyond appending `where` clauses (joins, scopes, `orWhere`) is counted on its own.
     *
     * @param  array<string|int, Tab>  $tabs
     * @return array<string|int, int>
     */
    protected function countSteps(array $tabs): array
    {
        $baseQuery = $this->getStepsBaseQuery();

        if (! $baseQuery) {
            return [];
        }

        $counts = [];
        $conditions = [];

        foreach ($tabs as $key => $tab) {
            $tabQuery = $tab->modifyQuery($baseQuery->clone());
            $condition = $this->getStepCondition($baseQuery, $tabQuery);

            if ($condition === null) {
                $counts[$key] = $tabQuery->count();
            } else {
                $conditions[$key] = $condition;
            }
        }

        if ($conditions === []) {
            return $counts;
        }

        $query = $baseQuery->toBase()
            ->cloneWithout(['columns', 'orders', 'limit', 'offset'])
            ->cloneWithoutBindings(['select', 'order']);
        $aliases = [];

        foreach (array_keys($conditions) as $index => $key) {
            [$sql, $bindings] = $conditions[$key];
            $aliases[$key] = "step_{$index}";

            $query->selectRaw(
                ($sql === '' ? 'count(*)' : "count(case when {$sql} then 1 end)").' as '.$query->getGrammar()->wrap($aliases[$key]),
                $bindings,
            );
        }

        $row = (array) $query->first();

        foreach ($aliases as $key => $alias) {
            $counts[$key] = (int) ($row[$alias] ?? 0);
        }

        return $counts;
    }

    /**
     * The `where` clauses the tab appended to the base query, as SQL and bindings; null when the
     * tab changed anything else, which a conditional count over the base rows cannot reproduce.
     *
     * @return array{0: string, 1: array<int, mixed>}|null
     */
    private function getStepCondition(Builder $baseQuery, Builder $tabQuery): ?array
    {
        $base = $baseQuery->getQuery();
        $tab = $tabQuery->getQuery();

        if ($base->distinct || $base->groups || $base->havings || $base->unions || $base->limit !== null || $base->offset !== null) {
            return null;
        }

        $scopes = static fn (Builder $query): array => [$query->scopes, $query->removedScopes];
        $scopes = Closure::bind($scopes, null, Builder::class);

        if ($scopes($baseQuery) !== $scopes($tabQuery)) {
            return null;
        }

        foreach (['from', 'joins', 'groups', 'havings', 'distinct', 'unions', 'limit', 'offset', 'lock', 'beforeQueryCallbacks'] as $property) {
            if ($base->{$property} !== $tab->{$property}) {
                return null;
            }
        }

        foreach ($tab->bindings as $type => $bindings) {
            if (! in_array($type, ['select', 'where', 'order'], true) && $bindings !== $base->bindings[$type]) {
                return null;
            }
        }

        $baseWhereCount = count($base->wheres);
        $baseBindingCount = count($base->bindings['where']);

        if (array_slice($tab->wheres, 0, $baseWhereCount) !== $base->wheres
            || array_slice($tab->bindings['where'], 0, $baseBindingCount) !== $base->bindings['where']) {
            return null;
        }

        // A top-level `or` ties the base filter and the tab condition into one expression; splitting them changes the result.
        foreach (array_slice($tab->wheres, 1) as $where) {
            if (! str_starts_with(strtolower($where['boolean']), 'and')) {
                return null;
            }
        }

        if (count($tab->wheres) === $baseWhereCount) {
            return ['', []];
        }

        $condition = $tab->forNestedWhere();
        $condition->wheres = array_slice($tab->wheres, $baseWhereCount);
        $condition->bindings['where'] = array_slice($tab->bindings['where'], $baseBindingCount);

        return [
            substr($tab->getGrammar()->compileWheres($condition), strlen('where ')),
            $condition->bindings['where'],
        ];
    }
}
