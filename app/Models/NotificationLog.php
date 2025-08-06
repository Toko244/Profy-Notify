<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_id',
        'customer_id',
        'trigger',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
