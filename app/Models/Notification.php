<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'title',
        'trigger',
        'notification_type',
        'email_template',
        'trigger',
        'subject',
        'content',
        'active',
        'additional'
    ];

    protected $casts = [
        'notification_type' => 'array',
        'active' => 'boolean',
        'additional' => 'array'
    ];

    public function criteria()
    {
        return $this->hasMany(Criterion::class);
    }

    public function category()
    {
        return $this->belongsTo(NotificationCategory::class, 'category_id', 'id');
    }
}
