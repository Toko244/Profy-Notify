<div x-data="{ step: 1 }" class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <ul class="nav nav-pills nav-justified flex-column flex-sm-row">
                <li class="nav-item">
                    <a class="nav-link d-flex flex-column align-items-center" :class="{ 'active': step === 1 }"
                        @click="step = 1" style="cursor: pointer;">
                        <span class="badge rounded-pill bg-primary mb-2">1</span>
                        <span class="d-none d-sm-block">Information</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex flex-column align-items-center" :class="{ 'active': step === 2 }"
                        @click="step = 2" style="cursor: pointer;">
                        <span class="badge rounded-pill bg-primary mb-2">2</span>
                        <span class="d-none d-sm-block">Trigger Options</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex flex-column align-items-center" :class="{ 'active': step === 3 }"
                        @click="step = 3" style="cursor: pointer;">
                        <span class="badge rounded-pill bg-primary mb-2">3</span>
                        <span class="d-none d-sm-block">Notification Type</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex flex-column align-items-center" :class="{ 'active': step === 4 }"
                        @click="step = 4" style="cursor: pointer;">
                        <span class="badge rounded-pill bg-primary mb-2">4</span>
                        <span class="d-none d-sm-block">Translations</span>
                    </a>
                </li>
                <li class="nav-item" style="    border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <a class="nav-link d-flex flex-column align-items-center" :class="{ 'active': step === 5 }"
                        @click="step = 5" style="cursor: pointer;">
                        <span class="badge rounded-pill bg-primary mb-2">5</span>
                        <span class="d-none d-sm-block">Criteria</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger shadow-sm mb-4" role="alert">
        <h5 class="alert-heading mb-2">Oops! There were some errors with your submission:</h5>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div x-show="step === 1" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-90">
        <div class="card shadow-sm p-4 mb-4">
            <h4 class="card-title mb-4">Notification Details</h4>
            <div class="row g-3">
                <div class="col-md-4">
                    @livewire('form.text', ['name' => 'title', 'label' => 'Notification Title', 'required' =>
                    'required', 'value' => $notification?->title ?? ''])
                </div>
                <div class="col-md-4">
                    @livewire('form.select', ['name' => 'trigger', 'label' => 'Trigger Event', 'options' => $triggers,
                    'listener' => 'changeTrigger', 'selected' => $notification?->trigger ?? ''], key('trigger'))
                </div>
                <div class="col-md-4">
                    @livewire('form.select', ['name' => 'category_id', 'nullable' => true, 'label' => 'Notification
                    Category', 'options' => $notificationCategories, 'selected' => $notification?->category_id ?? '',
                    'option_value' => 'key'], key('notification-category'))
                </div>
                <div class="col-md-4">
                    @livewire('form.checkbox', ['name' => 'active', 'label' => 'Active', 'required' =>
                    '', 'value' => $notification?->active ?? false])
                </div>
            </div>
        </div>
    </div>

    <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-90">
        <div class="card shadow-sm p-4 mb-4">
            <h4 class="card-title mb-4">Configure Trigger</h4>
            <div class="row g-3">
                @if (in_array($trigger, ['daily', 'weekly', 'monthly']))
                @if ($trigger === 'monthly')
                <div class="col-md-4">
                    @livewire('form.select', ['name' => 'additional[day]', 'label' => 'Day of Month', 'options' =>
                    $daysOfMonth, 'selected' => $notification->additional['day'] ?? '', 'option_value' => 'key'])
                </div>
                @endif

                @if ($trigger === 'weekly')
                <div class="col-md-4">
                    @livewire('form.select', ['name' => 'additional[week_day]', 'label' => 'Week Day', 'options' =>
                    $weekDays, 'selected' => $notification->additional['week_day'] ?? '', 'option_value' => 'key'])
                </div>
                @endif

                <div class="col-md-4">
                    @livewire('form.time', ['name' => 'additional[time]', 'label' => 'Time of Day', 'required' =>
                    'required', 'value' => $notification->additional['time'] ?? ''])
                </div>
                @elseif ($trigger === 'scheduled')
                <div class="col-md-6">
                    @livewire('form.date-time', ['name' => 'additional[time]', 'label' => 'Specific Date & Time',
                    'required' => 'required', 'value' => $notification->additional['time'] ?? ''])
                </div>
                @else
                <div class="col-md-4">
                    @livewire('form.number', ['name' => 'additional[delay_d]', 'label' => 'Delay (Days)', 'required' =>
                    'required', 'value' => $notification->additional['delay_d'] ?? 0])
                </div>
                <div class="col-md-4">
                    @livewire('form.number', ['name' => 'additional[delay_h]', 'label' => 'Delay (Hours)', 'required' =>
                    'required', 'value' => $notification->additional['delay_h'] ?? 0])
                </div>
                <div class="col-md-4">
                    @livewire('form.number', ['name' => 'additional[delay_m]', 'label' => 'Delay (Minutes)', 'required'
                    => 'required', 'value' => $notification->additional['delay_m'] ?? 0])
                </div>
                @endif
            </div>
        </div>
    </div>

    <div x-show="step === 3" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-90">
        <div class="card shadow-sm p-4 mb-4">
            <h4 class="card-title mb-4">Select Notification Types</h4>
            <div class="row">
                <div class="col-md-12">
                    @livewire('form.multi-select', ['name' => 'notification_type', 'label' => 'Delivery Type(s)',
                    'options' => $notificationTypes, 'listener' => 'changeType', 'selected' => $notification ? (array)
                    $notification->notification_type : []])
                </div>
            </div>
            <div class="row mt-4" x-show="Array.isArray($wire.type) && $wire.type.includes('email')" x-transition>
                <div class="col-md-6">
                    @livewire('form.select', ['name' => 'email_template', 'label' => 'Email Template', 'options' =>
                    $emailTemplates, 'selected' => $notification?->email_template ?? ''],
                    key('notification-email-template-field'))
                </div>
            </div>
        </div>
    </div>

    <div x-show="step === 4" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-90">
        <div class="card shadow-sm p-4 mb-4">
            <h4 class="card-title mb-4">Content Translations</h4>
            <div class="row g-3">
                @foreach ($languages as $index => $language)
                @php
                $translation = $notification?->translations->where('language_id', $language->id)->first();
                @endphp

                <input type="hidden" name="translations[{{ $index }}][language_id]" value="{{ $language->id }}">

                @if (in_array('email', $type ?? []) || in_array('push', $type ?? []))
                <div class="col-md-6">
                    @livewire('form.text', ['name' => "translations[{$index}][subject]", 'label' => "Subject
                    ({$language->name})", 'required' => 'required', 'value' => $translation?->subject ?? ''],
                    key('notification-subject-' . $language->locale))
                </div>
                @endif
                @endforeach

                @foreach ($languages as $index => $language)
                @php
                $translation = $notification?->translations->where('language_id', $language->id)->first();
                @endphp

                <div class="col-md-6">
                    @livewire('form.text-area', ['name' => "translations[{$index}][content]", 'label' => "Content
                    ({$language->name})", 'required' => 'required', 'value' => $translation?->content ?? '', 'class' =>
                    in_array('email', $type ?? []) ? 'tinymce' : ''], key('notification-content-' . $language->locale))
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div x-show="step === 5" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-90">
        <div class="card shadow-sm p-4 mb-4">
            <h4 class="card-title mb-4">Define Target Audience Criteria</h4>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Criterias</h5>
                <button type="button" class="btn btn-success" wire:click="addCriteria">
                    <i class="bi bi-plus-circle me-2"></i>Add Criteria
                </button>
            </div>
            <div class="row g-3">
                @forelse($this->criteria as $index => $criterion)
                <div class="col-12" wire:key="criterion-{{ $criterion['id'] }}">
                    <div class="card border-success shadow-sm">
                        <div class="card-header bg-success text-white">
                            Criterion #{{ $index + 1 }}
                        </div>
                        <div class="card-body row g-3">
                            <div class="col-md-4">
                                @livewire('form.select', [
                                'identifier' => $criterion['id'],
                                'listener' => 'changeCriteria',
                                'name' => 'criterion['.$index.'][type]',
                                'label' => 'Criterion Type',
                                'options' => $criteriaTypes,
                                'selected' => $criterion['type'] ?? $criteriaTypes[0]
                                ], key($criterion['id'].'-type'))
                            </div>

                            @if (in_array($criterion['type'], ['has_order', 'does_not_have_order',
                            'order_price_more_than', 'order_price_less_than', 'more_than_order_count',
                            'less_than_order_count']))
                            <div class="col-md-4">
                                @livewire('form.select', [
                                'name' => 'criterion['.$index.'][additional][order_type]',
                                'label' => 'Order Type',
                                'options' => $orderTypes,
                                'selected' => $criterion['additional']['order_type'] ?? $orderTypes[0]
                                ], key($criterion['id'].'-order-type'))
                            </div>
                            @endif

                            <div class="col-md-4">
                                @if (in_array($criterion['type'], ['has_order', 'does_not_have_order']))
                                @livewire('form.number', [
                                'name' => 'criterion['.$index.'][additional][duration]',
                                'label' => 'Duration (Days)',
                                'required' => 'required',
                                'value' => $criterion['additional']['duration'] ?? 0
                                ], key($criterion['id'].'-duration'))
                                @elseif (in_array($criterion['type'], ['order_price_more_than',
                                'order_price_less_than']))
                                @livewire('form.number', [
                                'name' => 'criterion['.$index.'][additional][price]',
                                'label' => 'Price (Value)',
                                'required' => 'required',
                                'value' => $criterion['additional']['price'] ?? 0
                                ], key($criterion['id'].'-price'))
                                @elseif (in_array($criterion['type'], ['more_than_order_count',
                                'less_than_order_count']))
                                @livewire('form.number', [
                                'name' => 'criterion['.$index.'][additional][count]',
                                'label' => 'Order Count',
                                'required' => 'required',
                                'value' => $criterion['additional']['count'] ?? 0
                                ], key($criterion['id'].'-count'))
                                @else
                                @livewire('form.number', [
                                'name' => 'criterion['.$index.'][additional][duration]',
                                'label' => 'Duration (Minutes)',
                                'required' => 'required',
                                'value' => $criterion['additional']['duration'] ?? 0
                                ], key($criterion['id'].'-duration'))
                                @endif
                            </div>

                            <div class="col-12 text-end mt-3 py-4">
                                <button type="button" class="btn btn-sm btn-danger" style="margin-right: 12px"
                                    wire:click="removeCriteria({{ $index }})" title="Remove Criteria">
                                    <i class="bi bi-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-4">
                    <p class="text-muted mb-0">No criteria added yet. Click "Add Criteria" to get started.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <button type="button" class="btn btn-secondary px-4 py-2" @click="step = step > 1 ? step - 1 : 1"
            :disabled="step === 1">
            <i class="bi bi-arrow-left me-2"></i>Previous
        </button>
        <button type="button" class="btn btn-primary px-4 py-2" @click="step = step < 5 ? step + 1 : step"
            :disabled="step === 5">
            Next<i class="bi bi-arrow-right ms-2"></i>
        </button>
    </div>
</div>

@section('scripts')
<script src="https://cdn.tiny.cloud/1/c8akqki7o15paneeceot45jev1u02hdnrehgex7uyqa2gzfi/tinymce/7/tinymce.min.js"
    referrerpolicy="origin"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('typeChanged', (selectedTypes) => {
            if (selectedTypes.includes('email')) {
                setTimeout(() => {
                    tinymce.init({
                        selector: '.tinymce textarea',
                        plugins: 'link image media codesample',
                        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline | link image media | align lineheight | checklist numlist bullist | removeformat | codesample',
                        tinycomments_mode: 'embedded',
                        tinycomments_author: 'Author name',
                        height: 300,
                        setup: function (editor) {
                            editor.on('change', function () {
                                let element = editor.getElement();
                                if (element && element.name) {
                                    @this.set(element.name, editor
                                        .getContent());
                                }
                            });
                        }
                    });
                }, 100);
            } else {
                if (tinymce.editors.length > 0) {
                    tinymce.editors.forEach(editor => editor.destroy());
                }
            }
        });

        Alpine.data('container', () => ({
            step: @js($step ?? 1),
            init() {
                this.$watch('step', (newStep) => {
                    if (newStep === 4 && Array.isArray(@this.type) && @this.type.includes(
                            'email')) {
                        setTimeout(() => {
                            tinymce.init({
                                selector: '.tinymce textarea',
                                plugins: 'link image media codesample',
                                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline | link image media | align lineheight | checklist numlist bullist | removeformat | codesample',
                                tinycomments_mode: 'embedded',
                                tinycomments_author: 'Author name',
                                height: 300,
                                setup: function (editor) {
                                    editor.on('change', function () {
                                        let element = editor
                                            .getElement();
                                        if (element && element
                                            .name) {
                                            @this.set(element
                                                .name,
                                                editor
                                                .getContent()
                                                );
                                        }
                                    });
                                }
                            });
                        }, 100);
                    } else if (newStep !== 4 && tinymce.editors.length > 0) {
                        tinymce.editors.forEach(editor => editor.destroy());
                    }
                });
            }
        }));
    });

</script>
@endsection
