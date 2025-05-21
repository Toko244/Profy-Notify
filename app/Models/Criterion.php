<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criterion extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'additional',
        'notification_id'
    ];

    protected $casts = [
        'additional' => 'array'
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }
}
