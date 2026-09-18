@php
    $stepsList = array_values($steps);
    $lastIndex = count($stepsList) - 1;
@endphp
<div class="w-full overflow-x-auto">
    <div class="flex min-w-max rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-900">
        @foreach ($stepsList as $index => $step)
            @php
                $isFirst = $index === 0;
                $isLast = $index === $lastIndex;
                $isActive = $activeStep === $step['key'];
                $clipPath = match (true) {
                    $isFirst && $isLast => 'none',
                    $isFirst => 'polygon(0 0, calc(100% - 16px) 0, 100% 50%, calc(100% - 16px) 100%, 0 100%)',
                    $isLast => 'polygon(16px 0, 100% 0, 100% 100%, 16px 100%, 0 50%)',
                    default => 'polygon(0 0, calc(100% - 16px) 0, 100% 50%, calc(100% - 16px) 100%, 0 100%, 16px 50%)',
                };
            @endphp
            <button
                type="button"
                wire:click="$set('activeStep', '{{ $step['key'] }}')"
                @class([
                    'relative flex-1 flex items-center justify-center gap-2 px-4 py-3 min-w-[140px] whitespace-nowrap transition-colors cursor-pointer',
                    'bg-blue-100 dark:bg-blue-500/20' => $isActive,
                    'bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800' => ! $isActive,
                    '-ml-4' => ! $isFirst,
                ])
                style="clip-path: {{ $clipPath }};"
            >
                @if ($step['icon'])
                    <x-filament::icon
                        :icon="$step['icon']"
                        class="w-4 h-4 shrink-0 {{ $isActive ? 'text-blue-900 dark:text-blue-200' : 'text-gray-500 dark:text-gray-400' }}"
                    />
                @endif
                <span class="flex flex-col items-start">
                    <span @class([
                        'text-sm font-semibold truncate',
                        'text-blue-900 dark:text-blue-200' => $isActive,
                        'text-gray-900 dark:text-gray-100' => ! $isActive,
                    ])>{{ $step['label'] }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 tabular-nums">{{ $step['count'] }} {{ \Illuminate\Support\Str::plural('item', $step['count']) }}</span>
                </span>
            </button>
        @endforeach
    </div>
</div>
