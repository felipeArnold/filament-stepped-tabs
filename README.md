# Filament Stepped Tabs

Chevron-style stepped tabs navigation with counters, for Filament (and any Livewire) pages.

<img width="2048" height="768" alt="9160b522-8e35-4136-99a2-28116d3ceea1" src="https://github.com/user-attachments/assets/719a297d-9535-488f-b88f-87ad0fecb584" />


## Why

Filament's built-in `Tabs` component renders a pill-style tab bar. This package renders the same
"steps"/breadcrumb look — chevron-shaped segments, a bold label, a small counter underneath, active
step highlighted — and plugs into any Livewire component, not just Filament resource pages.

The core (`StepTab` builder + `InteractsWithSteppedTabs` trait) has **no dependency on Filament**
internals — it only requires Livewire. The Blade view uses `<x-filament::icon>` (from
`filament/support`, present in every Filament major) for the optional per-step icon.

## Installation

```bash
composer require felipearnold/filament-stepped-tabs
```

## Usage

### 1. Add the trait to your Livewire component

```php
use FelipeArnold\FilamentSteppedTabs\Concerns\InteractsWithSteppedTabs;

class ListEnvelopes extends ListRecords
{
    use InteractsWithSteppedTabs;

    public function getDefaultActiveStep(): ?string
    {
        return 'sent';
    }
}
```

### 2. Build your steps with `StepTab`

```php
use FelipeArnold\FilamentSteppedTabs\StepTab;

public function getSteps(): array
{
    $counts = Envelope::query()->where('tenant_id', $tenant->id)
        ->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

    return [
        StepTab::make('draft')->label('Rascunhos')->icon('heroicon-o-pencil-square')->count($counts->get('draft', 0))->toArray(),
        StepTab::make('cancelled')->label('Cancelados')->icon('heroicon-o-x-circle')->count($counts->get('cancelled', 0))->toArray(),
        StepTab::make('expired')->label('Expirados')->icon('heroicon-o-calendar')->count($counts->get('expired', 0))->toArray(),
        StepTab::make('sent')->label('Em andamento')->icon('heroicon-o-paper-airplane')->count($counts->get('sent', 0))->toArray(),
        StepTab::make('signed')->label('Assinados')->icon('heroicon-o-check-circle')->count($counts->get('signed', 0))->toArray(),
        StepTab::make('all')->label('Todos')->icon('heroicon-o-inbox')->count($counts->sum())->toArray(),
    ];
}
```

### 3. Render it and filter your query by `$this->activeStep`

**Filament v4/v5** (inside a `ListRecords` page, replacing `getTabsContentComponent()`):

```php
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\View;

public function getTabsContentComponent(): Component
{
    return View::make('filament-stepped-tabs::components.nav')
        ->viewData(['steps' => $this->getSteps(), 'activeStep' => $this->activeStep]);
}

protected function getTableQuery(): Builder
{
    $query = parent::getTableQuery();

    return $this->activeStep && $this->activeStep !== 'all'
        ? $query->where('status', $this->activeStep)
        : $query;
}
```

**Filament v3** (no `Schemas` package — render inside the page's Blade view or via a `renderHook`):

```blade
{{-- resources/views/filament/resources/envelopes/pages/list-envelopes.blade.php --}}
<x-filament-panels::page>
    @include('filament-stepped-tabs::components.nav', ['steps' => $this->getSteps(), 'activeStep' => $this->activeStep])

    {{ $this->table }}
</x-filament-panels::page>
```

**Plain Blade / any Livewire component**:

```blade
<x-filament-stepped-tabs::nav :steps="$this->getSteps()" :active-step="$activeStep" />
```

## Step options

| Method | Description |
|---|---|
| `StepTab::make(string $key)` | Unique key (`[a-z0-9_-]+`), used for `activeStep` matching |
| `->label(string $label)` | Display label (defaults to the key) |
| `->count(int $count)` | Counter shown under the label |
| `->icon(?string $icon)` | Optional heroicon name, rendered via `x-filament::icon` |
| `->color(?string $color)` | Reserved for future theming |

## Testing

```bash
composer test
```

## License

MIT.
