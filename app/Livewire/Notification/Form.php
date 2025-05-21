<?php

namespace App\Livewire\Notification;

use App\Enums\CriteriaType;
use App\Enums\EmailTemplate;
use App\Enums\NotificationType;
use App\Enums\OrderType;
use App\Enums\Trigger;
use App\Models\NotificationCategory;
use Livewire\Component;

use function Termwind\render;

class Form extends Component
{
    public $notification = null;
    public $type = 'email';
    public $trigger = 'now';
    protected $listeners = ['changeType' => 'changeType', 'changeTrigger' => 'changeTrigger', 'changeCriteria' => 'changeCriteria'];
    public $criteria = [];
    public $criteriaTypes = [];

    public function changeType($value)
    {
        $this->type = $value;
    }

    public function changeCriteria($value)
    {
        $key = array_search($value['identifier'], array_column($this->criteria, 'id'));
        $this->criteria[$key]['type'] = $value['value'];
    }


    public function changeTrigger($value)
    {
        $this->trigger = $value;
    }

    public function addCriteria()
    {
        $this->criteria[] = [
            'id' => uniqid(),
            'type' => 'has_order'
        ];
    }

    public function removeCriteria($index)
    {
        unset($this->criteria[$index]);
        $this->criteria = array_values($this->criteria);
    }

    public function mount()
    {
        if ($this->notification) {
            $this->type = $this->notification->notification_type;
            $this->trigger = $this->notification->trigger;
            $this->criteria = $this->notification->criteria->toArray();
        }
    }


    public function render()
    {
        $triggers = Trigger::cases();
        $notificationTypes = NotificationType::cases();
        $emailTemplates = EmailTemplate::cases();
        $this->criteriaTypes = CriteriaType::cases();
        $orderTypes = OrderType::cases();
        $notificationCategories = NotificationCategory::latest()->pluck('title', 'id')->toArray();
        $weekDays = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday'
        ];
        $daysOfMonth = range(1, 31);

        return view('livewire.notification.form', [
            'triggers' => $triggers,
            'notificationTypes' => $notificationTypes,
            'emailTemplates' => $emailTemplates,
            'criteriaTypes' => $this->criteriaTypes,
            'trigger' => $this->trigger,
            'orderTypes' => $orderTypes,
            'notification' => $this->notification,
            'notificationCategories' => $notificationCategories,
            'weekDays' => $weekDays,
            'daysOfMonth' => $daysOfMonth
        ]);
    }
}
