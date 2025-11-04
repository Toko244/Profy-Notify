<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between gap-4 mb-4">
            <div>
                <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                    🏆 Notification Performance Ranking
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $this->getFilterLabel() }}
                </p>
            </div>

            @if($this->getFilters())
                <div class="flex gap-2">
                    @foreach($this->getFilters() as $filterKey => $filterLabel)
                        <button
                            wire:click="$set('filter', '{{ $filterKey }}')"
                            type="button"
                            class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
                                {{ $this->filter === $filterKey
                                    ? 'bg-primary-600 text-white'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700'
                                }}"
                        >
                            {{ $filterLabel }}
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div style="height: 280px; overflow-y: auto; overflow-x: hidden;" class="rounded-lg border border-gray-200 dark:border-gray-700">
            {{ $this->table }}
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
