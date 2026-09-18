<?php

declare(strict_types=1);

namespace FelipeArnold\FilamentSteppedTabs\Tests\Fixtures;

use FelipeArnold\FilamentSteppedTabs\Concerns\InteractsWithSteppedTabs;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class DummySteppedComponent extends Component
{
    use InteractsWithSteppedTabs;

    public function getDefaultActiveStep(): ?string
    {
        return 'draft';
    }

    public function render(): View
    {
        return view('dummy');
    }
}
