<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes, Translatable;

    protected $primaryKey = 'id'; // ✅ default
    public $incrementing = true;
    protected $keyType = 'int';

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
        'send_sms_if_push_disabled',
        'additional'
    ];

    protected $casts = [
        'notification_type' => 'array',
        'active' => 'boolean',
        'additional' => 'array'
    ];

    public function getTranslationModel()
    {
        return NotificationTranslation::class;
    }

    public function criteria()
    {
        return $this->hasMany(Criterion::class);
    }

    public function category()
    {
        return $this->belongsTo(NotificationCategory::class, 'category_id', 'id');
    }

    public function analytics()
    {
        return $this->hasMany(NotificationAnalytic::class);
    }
}
