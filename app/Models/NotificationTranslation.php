<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'notification_id',
        'language_id',
        'subject',
        'content',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
