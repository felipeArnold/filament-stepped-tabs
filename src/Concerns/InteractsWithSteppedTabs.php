<?php

declare(strict_types=1);

namespace FelipeArnold\FilamentSteppedTabs\Concerns;

trait InteractsWithSteppedTabs
{
    public ?string $activeStep = null;

    public function getDefaultActiveStep(): ?string
    {
        return null;
    }

    public function mountInteractsWithSteppedTabs(): void
    {
        if (filled($this->activeStep)) {
            return;
        }

        $this->activeStep = $this->getDefaultActiveStep();
    }
}
