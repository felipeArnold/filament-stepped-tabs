<?php

declare(strict_types=1);

namespace FelipeArnold\FilamentSteppedTabs\Tests\Fixtures;

use FelipeArnold\FilamentSteppedTabs\Concerns\InteractsWithSteppedTabs;
use Livewire\Component;

final class DummySteppedComponent extends Component
{
    use InteractsWithSteppedTabs;

    public function getDefaultActiveStep(): ?string
    {
        return 'draft';
    }

    public function render(): string
    {
        return '<div>{{ $activeStep }}</div>';
    }
}
