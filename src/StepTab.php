<?php

declare(strict_types=1);

namespace FelipeArnold\FilamentSteppedTabs;

use InvalidArgumentException;

final class StepTab
{
    private ?string $label = null;

    private int $count = 0;

    private ?string $icon = null;

    private ?string $color = null;

    private function __construct(private readonly string $key)
    {
        if (preg_match('/^[a-z0-9_-]+$/', $key) !== 1) {
            throw new InvalidArgumentException("Invalid step key \"{$key}\": only lowercase letters, numbers, \"_\" and \"-\" are allowed.");
        }
    }

    public static function make(string $key): self
    {
        return new self($key);
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function count(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    public function icon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function color(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return array{key: string, label: string, count: int, icon: ?string, color: ?string}
     */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label ?? $this->key,
            'count' => $this->count,
            'icon' => $this->icon,
            'color' => $this->color,
        ];
    }
}
