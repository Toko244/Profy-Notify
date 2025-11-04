<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-4">
            {{-- Filter Buttons --}}
            <div class="flex flex-wrap gap-2">
                <button
                    wire:click="$set('filter', 'total')"
                    class="px-3 py-1.5 text-sm font-medium rounded-lg {{ $filter === 'total' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}"
                >
                    Total
                </button>
                <button
                    wire:click="$set('filter', 'today')"
                    class="px-3 py-1.5 text-sm font-medium rounded-lg {{ $filter === 'today' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}"
                >
                    Today
                </button>
                <button
                    wire:click="$set('filter', 'week')"
                    class="px-3 py-1.5 text-sm font-medium rounded-lg {{ $filter === 'week' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}"
                >
                    Last Week
                </button>
                <button
                    wire:click="$set('filter', 'month')"
                    class="px-3 py-1.5 text-sm font-medium rounded-lg {{ $filter === 'month' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}"
                >
                    Last Month
                </button>
                <button
                    wire:click="$set('filter', 'custom')"
                    class="px-3 py-1.5 text-sm font-medium rounded-lg {{ $filter === 'custom' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}"
                >
                    Custom
                </button>
            </div>

            {{-- Custom Date Range --}}
            @if($filter === 'custom')
                <div class="flex gap-2">
                    <input
                        type="date"
                        wire:model.live="startDate"
                        class="block w-full px-3 py-2 text-sm border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Start date"
                    >
                    <input
                        type="date"
                        wire:model.live="endDate"
                        class="block w-full px-3 py-2 text-sm border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="End date"
                    >
                </div>
            @endif

            {{-- Total Sent --}}
            <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Notifications Sent</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalSent) }}</p>
                    </div>
                </div>
            </div>

            {{-- Analytics Details --}}
            <div class="space-y-3">
                @forelse ($analytics as $analytic)
                    <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ $analytic['notification'] }}
                            </span>
                            <span class="text-sm font-semibold text-primary-600 dark:text-primary-400">
                                Total: {{ number_format($analytic['total_sent']) }}
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($analytic['channels'] as $channel => $count)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $channel === 'email' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : '' }}
                                    {{ $channel === 'sms' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : '' }}
                                    {{ $channel === 'push' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300' : '' }}
                                ">
                                    {{ ucfirst($channel) }}: {{ number_format($count) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                        No notifications found for this period.
                    </div>
                @endforelse
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
