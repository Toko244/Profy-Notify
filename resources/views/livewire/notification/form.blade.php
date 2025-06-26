<div>
    <div class="row g-3">
        {{-- Title --}}
        <div class="col-md-4">
            @livewire('form.text', [
            'name' => 'title',
            'label' => 'Title',
            'required' => 'required',
            'value' => $notification?->title ?? ''
            ])
        </div>

        {{-- Trigger --}}
        <div class="col-md-4">
            @livewire('form.select', [
            'name' => 'trigger',
            'label' => 'Trigger',
            'options' => $triggers,
            'listener' => 'changeTrigger',
            'selected' => $notification?->trigger ?? ''
            ], key('trigger'))
        </div>

        {{-- Category --}}
        <div class="col-md-4">
            @livewire('form.select', [
            'name' => 'category_id',
            'nullable' => true,
            'label' => 'Category',
            'options' => $notificationCategories,
            'selected' => $notification?->category_id ?? '',
            'option_value' => 'key'
            ], key('notification-category'))
        </div>
    </div>

    {{-- Trigger-specific options --}}
    <div class="row g-3 mt-3">
        @if (in_array($trigger, ['daily', 'weekly', 'monthly']))
        @if ($trigger === 'monthly')
        <div class="col-md-4">
            @livewire('form.select', [
            'name' => 'additional[day]',
            'label' => 'Day of Month',
            'options' => $daysOfMonth,
            'selected' => $notification->additional['day'] ?? '',
            'option_value' => 'key'
            ])
        </div>
        @endif

        @if ($trigger === 'weekly')
        <div class="col-md-4">
            @livewire('form.select', [
            'name' => 'additional[week_day]',
            'label' => 'Week Day',
            'options' => $weekDays,
            'selected' => $notification->additional['week_day'] ?? '',
            'option_value' => 'key'
            ])
        </div>
        @endif

        <div class="col-md-4">
            @livewire('form.time', [
            'name' => 'additional[time]',
            'label' => 'Time',
            'required' => 'required',
            'value' => $notification->additional['time'] ?? ''
            ])
        </div>
        @elseif ($trigger === 'scheduled')
        <div class="col-md-6">
            @livewire('form.date-time', [
            'name' => 'additional[time]',
            'label' => 'Time',
            'required' => 'required',
            'value' => $notification->additional['time'] ?? ''
            ])
        </div>
        @else
        <div class="col-md-4">
            @livewire('form.number', [
            'name' => 'additional[delay_d]',
            'label' => 'Delay Days',
            'required' => 'required',
            'value' => $notification->additional['delay_d'] ?? 0
            ])
        </div>
        <div class="col-md-4">
            @livewire('form.number', [
            'name' => 'additional[delay_h]',
            'label' => 'Delay Hours',
            'required' => 'required',
            'value' => $notification->additional['delay_h'] ?? 0
            ])
        </div>
        <div class="col-md-4">
            @livewire('form.number', [
            'name' => 'additional[delay_m]',
            'label' => 'Delay Minutes',
            'required' => 'required',
            'value' => $notification->additional['delay_m'] ?? 0
            ])
        </div>
        @endif
    </div>

    {{-- Notification Type --}}
    <div class="row mt-4">
        <div class="col-md-12">
            @livewire('form.multi-select', [
            'name' => 'notification_type',
            'label' => 'Type',
            'options' => $notificationTypes,
            'listener' => 'changeType',
            'selected' => $notification ? (array) $notification->notification_type : []
            ])
        </div>
    </div>

    {{-- Translations --}}
    <hr class="my-4">
    <div class="row g-3">
        {{-- SUBJECTS SECTION --}}
        @foreach ($languages as $index => $language)
        @php
        $translation = $notification
        ? $notification->translations->where('language_id', $language->id)->first()
        : null;
        @endphp

        <input type="hidden" name="translations[{{ $index }}][language_id]" value="{{ $language->id }}">

        @if (in_array('email', $type ?? []) || in_array('push', $type ?? []))
        <div class="col-md-6">
            @livewire('form.text', [
            'name' => "translations[{$index}][subject]",
            'label' => "Subject ({$language->name})",
            'required' => 'required',
            'value' => $translation?->subject ?? ''
            ], key('notification-subject-' . $language->locale))
        </div>
        @endif
        @endforeach

        {{-- CONTENTS SECTION --}}
        @foreach ($languages as $index => $language)
        @php
        $translation = $notification
        ? $notification->translations->where('language_id', $language->id)->first()
        : null;
        @endphp

        <div class="col-md-6">
            @livewire('form.text-area', [
            'name' => "translations[{$index}][content]",
            'label' => "Content ({$language->name})",
            'required' => 'required',
            'value' => $translation?->content ?? ''
            ], key('notification-content-' . $language->locale))
        </div>
        @endforeach
    </div>

    {{-- Email Template --}}
    <div class="row mt-4" @if (!in_array('email', $type)) style="display:none;" @endif>
        <div class="col-md-6">
            @livewire('form.select', [
            'name' => 'email_template',
            'label' => 'Email Template',
            'options' => $emailTemplates,
            'selected' => $notification?->email_template ?? ''
            ], key('notification-email-template-field'))
        </div>
    </div>

    {{-- Active --}}
    <div class="row mt-4">
        <div class="col-md-12">
            @livewire('form.checkbox', [
            'name' => 'active',
            'label' => 'Active',
            'value' => 1,
            'checked' => $notification ? $notification->active : 1
            ])
        </div>
    </div>
    <hr>

    <div class="col-md-12 mt-5">
        <div class="row">
            <div class="col-sm-6">
                <h4 class="mb-0 mt-0">
                    Criterias
                </h4>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn btn-success float-end" wire:click="addCriteria">
                    ADD
                </button>
            </div>
        </div>
        <div class="row">
            @foreach($this->criteria as $index => $criterion)
            <div class="col-12 mt-3" wire:key="criterion-{{ $criterion['id'] }}">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"></h3>
                        <button type="button" class="btn btn-danger float-end"
                            wire:click="removeCriteria({{ $index }})">
                            REMOVE
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="col-md-4">
                            <div class="row">
                                @livewire('form.select', ['identifier' => $criterion['id'], 'listener' =>
                                'changeCriteria', 'name' => 'criterion['.$index.'][type]', 'label' => 'Type', 'options'
                                => $criteriaTypes, 'selected' => isset($criterion['type']) ? $criterion['type'] :
                                $criteriaTypes[0]], key($criterion['id'].'-type'))
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                @livewire('form.select', ['name' => 'criterion['.$index.'][additional][order_type]',
                                'label' => 'Type', 'options' => $orderTypes, 'selected' =>
                                isset($criterion['additional']['order_type']) ? $criterion['additional']['order_type'] :
                                $orderTypes[0]], key($criterion['id'].'-order-type'))
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                @if (in_array($criterion['type'], ['has_order', 'does_not_have_order']))
                                @livewire('form.number', ['name' => 'criterion['.$index.'][additional][duration]',
                                'label' => 'Days', 'required' => 'required', 'value' =>
                                isset($criterion['additional']['duration']) ? $criterion['additional']['duration'] : 0],
                                key($criterion['id'].'-duration'))
                                @elseif (in_array($criterion['type'], ['order_price_more_than',
                                'order_price_less_than']))
                                @livewire('form.number', ['name' => 'criterion['.$index.'][additional][price]', 'label'
                                => 'Price', 'required' => 'required', 'value' =>
                                isset($criterion['additional']['price']) ? $criterion['additional']['price'] : 0],
                                key($criterion['id'].'-price'))
                                @else
                                @livewire('form.number', ['name' => 'criterion['.$index.'][additional][duration]',
                                'label' => 'Minutes', 'required' => 'required', 'value' =>
                                isset($criterion['additional']['duration']) ? $criterion['additional']['duration'] : 0],
                                key($criterion['id'].'-duration'))
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@section('scripts')

<script src="https://cdn.tiny.cloud/1/c8akqki7o15paneeceot45jev1u02hdnrehgex7uyqa2gzfi/tinymce/7/tinymce.min.js"
    referrerpolicy="origin"></script>
@if (in_array('email', $type))
<script>
    tinymce.init({
        selector: '.tinymce textarea',
        plugins: '',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        tinycomments_mode: 'embedded',
        tinycomments_author: 'Author name',
        mergetags_list: [{
                value: 'First.Name',
                title: 'First Name'
            },
            {
                value: 'Email',
                title: 'Email'
            },
        ],
        ai_request: (request, respondWith) => respondWith.string(() => Promise.reject(
            "See docs to implement AI Assistant")),
    });

</script>
@endif

@endsection
