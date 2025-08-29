<div>
    <!-- Filter buttons -->
    <div class="d-flex gap-2 mb-3">
        <button class="btn btn-sm {{ $filter === 'today' ? 'btn-primary' : 'btn-outline-primary' }}"
                wire:click="$set('filter', 'today')">
            Today
        </button>
        <button class="btn btn-sm {{ $filter === 'week' ? 'btn-primary' : 'btn-outline-primary' }}"
                wire:click="$set('filter', 'week')">
            Last Week
        </button>
        <button class="btn btn-sm {{ $filter === 'month' ? 'btn-primary' : 'btn-outline-primary' }}"
                wire:click="$set('filter', 'month')">
            Last Month
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0">{{ $totalSent }}</h3>
                </div>
                <button class="btn btn-sm btn-light collapse-toggle"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#analyticsDetails"
                        aria-expanded="false"
                        aria-controls="analyticsDetails">
                    <i class="bi bi-chevron-down"></i>
                </button>
            </div>

            <div class="collapse mt-3" id="analyticsDetails">
                <div class="border-top pt-2">
                    @forelse ($analytics as $analytic)
                        <div class="mb-2">
                            <strong>{{ $analytic['notification'] }}</strong> - Total: {{ $analytic['total_sent'] }}
                            <div class="mt-1">
                                @foreach ($analytic['channels'] as $channel => $count)
                                    <span class="badge bg-primary me-1">
                                        {{ ucfirst($channel) }}: {{ $count }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No notifications found for this period.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
