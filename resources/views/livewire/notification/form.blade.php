<div>
    <div class="col-md-4">
        <div class="row">
            @livewire('form.text', ['name' => 'title', 'label' => 'Title', 'required' => 'required', 'value' => $notification ? $notification->title : ''])
        </div>
    </div>
    <div class="col-md-4">
        <div class="row">
            @livewire('form.select', ['name' => 'trigger', 'label' => 'Trigger', 'options' => $triggers, 'listener' => 'changeTrigger', 'selected' => $notification ? $notification->trigger : ''], key('trigger'))
        </div>
    </div>
    <div class="col-md-4">
        <div class="row">
            @livewire('form.select', ['name' => 'category_id', 'nullable' => true, 'label' => 'Category', 'options' => $notificationCategories, 'selected' => $notification ? $notification->category_id : '', 'option_value' => 'key'], key('notification-category'))
        </div>
    </div>
    @if (in_array($trigger, ['daily', 'weekly', 'monthly']))
        <div class="col-md-6">
            <div class="row">

                @if (in_array($trigger, ['monthly']))
                    <div class="col-md-6">
                        <div class="row">
                            @livewire('form.select', ['name' => 'additional[day]', 'label' => 'Day of Month', 'options' => $daysOfMonth, 'selected' =>  $notification && $notification->additional && isset($notification->additional['day']) ? $notification->additional['day'] : '',  'option_value' => 'key'])
                        </div>
                    </div>
                @endif
                @if (in_array($trigger, ['weekly']))
                    <div class="col-md-6">
                        <div class="row">
                            @livewire('form.select', ['name' => 'additional[week_day]', 'label' => 'Week Day', 'options' => $weekDays, 'selected' =>  $notification && $notification->additional && isset($notification->additional['week_day']) ? $notification->additional['week_day'] : '',  'option_value' => 'key'])
                        </div>
                    </div>
                @endif
                <div class="col-md-6">
                    <div class="row">
                        @livewire('form.time', ['name' => 'additional[time]', 'label' => 'Time', 'required' => 'required', 'value' =>  $notification && $notification->additional && isset($notification->additional['time']) ? $notification->additional['time'] : 0])
                    </div>
                </div>
            </div>
        </div>
    @elseif (in_array($trigger, ['scheduled']))
        <div class="col-md-6">
            <div class="row">
                @livewire('form.date-time', ['name' => 'additional[time]', 'label' => 'Time', 'required' => 'required', 'value' =>  $notification && $notification->additional && isset($notification->additional['time']) ? $notification->additional['time'] : 0])
            </div>
        </div>
    @else
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-4">
                <div class="row">
                    @livewire('form.number', ['name' => 'additional[delay_d]', 'label' => 'Delay Days', 'required' => 'required', 'value' => $notification && $notification->additional && isset($notification->additional['delay_d']) ? $notification->additional['delay_d'] : 0])
                </div>
            </div>

            <div class="col-md-4">
                <div class="row">
                    @livewire('form.number', ['name' => 'additional[delay_m]', 'label' => 'Delay Minutes', 'required' => 'required', 'value' =>  $notification && $notification->additional && isset($notification->additional['delay_m']) ? $notification->additional['delay_m'] : 0])
                </div>
            </div>

            <div class="col-md-4">
                <div class="row">
                    @livewire('form.number', ['name' => 'additional[delay_h]', 'label' => 'Delay Hours', 'required' => 'required', 'value' =>  $notification && $notification->additional && isset($notification->additional['delay_h']) ? $notification->additional['delay_h'] : 0])
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="col-md-6">
        <div class="row">
            @livewire('form.select', ['name' => 'notification_type', 'label' => 'Type', 'options' => $notificationTypes, 'listener' => 'changeType', 'selected' => $notification ? $notification->notification_type : ''])
        </div>
    </div>
    @if ($type == 'email' || $type == 'push')
        <div class="col-md-6">
            <div class="row">
                @livewire('form.text', ['name' => 'subject', 'label' => 'Subject', 'required' => 'required', 'value' => $notification ? $notification->subject : ''])
            </div>
        </div>
    @endif
    @if ($type == 'email')
        <div class="col-md-6">
            <div class="row">
                @livewire('form.select', ['name' => 'email_template', 'label' => 'Email Template', 'options' => $emailTemplates, 'selected' => $notification ? $notification->email_template : ''])
            </div>
        </div>
    @endif


        <div class="col-md-12">
            <div class="row">
                @livewire('form.text-area', ['name' => 'content', 'label' => 'Message', 'value' => $notification ? $notification->content : ''])
            </div>
        </div>
    <div class="col-md-12">
        <div class="row">
            @livewire('form.checkbox', ['name' => 'active', 'label' => 'Active', 'value' => 1, 'checked' => $notification ? $notification->active : 1])
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
                            <button type="button" class="btn btn-danger float-end" wire:click="removeCriteria({{ $index }})">
                                REMOVE
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="col-md-4">
                                <div class="row">
                                    @livewire('form.select', ['identifier' => $criterion['id'], 'listener' => 'changeCriteria', 'name' => 'criterion['.$index.'][type]', 'label' => 'Type', 'options' => $criteriaTypes, 'selected' => isset($criterion['type']) ? $criterion['type'] : $criteriaTypes[0]], key($criterion['id'].'-type'))
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    @livewire('form.select', ['name' => 'criterion['.$index.'][additional][order_type]', 'label' => 'Type', 'options' => $orderTypes, 'selected' => isset($criterion['additional']['order_type']) ? $criterion['additional']['order_type'] : $orderTypes[0]], key($criterion['id'].'-order-type'))
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    @if (in_array($criterion['type'], ['has_order', 'does_not_have_order']))
                                        @livewire('form.number', ['name' => 'criterion['.$index.'][additional][duration]', 'label' => 'Days', 'required' => 'required', 'value' => isset($criterion['additional']['duration']) ? $criterion['additional']['duration'] : 0], key($criterion['id'].'-duration'))
                                    @elseif (in_array($criterion['type'], ['order_price_more_than', 'order_price_less_than']))
                                        @livewire('form.number', ['name' => 'criterion['.$index.'][additional][price]', 'label' => 'Price', 'required' => 'required', 'value' => isset($criterion['additional']['price']) ? $criterion['additional']['price'] : 0], key($criterion['id'].'-price'))
                                    @else
                                        @livewire('form.number', ['name' => 'criterion['.$index.'][additional][duration]', 'label' => 'Minutes', 'required' => 'required', 'value' => isset($criterion['additional']['duration']) ? $criterion['additional']['duration'] : 0], key($criterion['id'].'-duration'))
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

<script src="https://cdn.tiny.cloud/1/c8akqki7o15paneeceot45jev1u02hdnrehgex7uyqa2gzfi/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
@if ($type == 'email')
<script>
  tinymce.init({
    selector: '.tinymce textarea',
    plugins: '',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    tinycomments_mode: 'embedded',
    tinycomments_author: 'Author name',
    mergetags_list: [
      { value: 'First.Name', title: 'First Name' },
      { value: 'Email', title: 'Email' },
    ],
    ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
  });
</script>
@endif

@endsection
