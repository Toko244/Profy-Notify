<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'profy_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'allow_notification',
        'onesignal_player_id',
    ];

    protected $casts = [
        'allow_notification' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function segments()
    {
        return $this->belongsToMany(Segment::class, 'segment_customers', 'customer_id', 'segment_id');
    }
}
