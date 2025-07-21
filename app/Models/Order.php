<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number',
        'customer_id',
        'finished_at',
        'service_finished_at',
        'price',
        'status',
        'type'
    ];

    protected $casts = [
        'finished_at' => 'datetime',
        'service_finished_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
