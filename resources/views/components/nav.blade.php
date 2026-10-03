@php
    $stepsList = array_values($steps);
    $lastIndex = count($stepsList) - 1;
    $property ??= 'activeStep';
@endphp
@once
    <style>
        .fi-stepped-tabs { background-color: var(--gray-200); }
        .fi-stepped-tab { background-color: #fff; padding-inline: 2rem; }
        .fi-stepped-tab[data-first] { padding-inline-start: 1rem; }
        .fi-stepped-tab:not([data-first]) { margin-inline-start: calc(-1rem + 3px); }
        .fi-stepped-tab:hover { background-color: var(--gray-50); }
        .fi-stepped-tab[data-active] { background-color: var(--gray-200); }
        .fi-stepped-tab .fi-stepped-tab-icon { color: var(--gray-400); }
        .fi-stepped-tab[data-active] .fi-stepped-tab-icon { color: var(--gray-800); }
        .fi-stepped-tab[data-active] .fi-stepped-tab-label { color: var(--gray-950); }
        .dark .fi-stepped-tabs { background-color: var(--gray-700); }
        .dark .fi-stepped-tab { background-color: var(--gray-900); }
        .dark .fi-stepped-tab:hover { background-color: var(--gray-800); }
        .dark .fi-stepped-tab[data-active] { background-color: var(--gray-700); }
        .dark .fi-stepped-tab[data-active] .fi-stepped-tab-icon { color: var(--gray-200); }
        .dark .fi-stepped-tab[data-active] .fi-stepped-tab-label { color: #fff; }
    </style>
@endonce
<div class="w-full overflow-x-auto">
    <div class="fi-stepped-tabs flex min-w-max rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        @foreach ($stepsList as $index => $step)
            @php
                $isFirst = $index === 0;
                $isLast = $index === $lastIndex;
                $isActive = (string) $activeStep === $step['key'];
                $clipPath = match (true) {
                    $isFirst && $isLast => 'none',
                    $isFirst => 'polygon(0 0, calc(100% - 16px) 0, 100% 50%, calc(100% - 16px) 100%, 0 100%)',
                    $isLast => 'polygon(0 0, 100% 0, 100% 100%, 0 100%, 16px 50%)',
                    default => 'polygon(0 0, calc(100% - 16px) 0, 100% 50%, calc(100% - 16px) 100%, 0 100%, 16px 50%)',
                };
            @endphp
            <button
                type="button"
                wire:click="$set('{{ $property }}', '{{ $step['key'] }}')"
                @if ($isFirst) data-first @endif
                @if ($isActive) data-active aria-current="step" @endif
                class="fi-stepped-tab relative flex-1 flex items-center justify-start gap-3 py-3 min-w-[140px] whitespace-nowrap text-left transition-colors cursor-pointer"
                style="clip-path: {{ $clipPath }};"
            >
                @if ($step['icon'])
                    <x-filament::icon
                        :icon="$step['icon']"
                        class="fi-stepped-tab-icon w-5 h-5 shrink-0"
                    />
                @endif
                <span class="flex flex-col items-start">
                    <span class="fi-stepped-tab-label text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $step['label'] }}</span>
                    <span class="fi-stepped-tab-count text-xs text-gray-500 dark:text-gray-400 tabular-nums">{{ $step['count'] }} {{ \Illuminate\Support\Str::plural('item', $step['count']) }}</span>
                </span>
            </button>
        @endforeach
    </div>
</div>
