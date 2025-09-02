<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_id',
        'date',
        'total_sent',
        'channel_breakdown',
    ];

    protected $casts = [
        'date' => 'date',
        'channel_breakdown' => 'array',
    ];

    /**
     * Filter analytics for a specific day.
    */
    public function scopeForDay($query, Carbon $day)
    {
        return $query->where('date', $day->toDateString());
    }

    /**
     * Filter analytics for the current week.
    */
    public function scopeForWeek($query, Carbon $date = null)
    {
        $date = $date ?? Carbon::now();
        return $query->whereBetween('date', [$date->startOfWeek()->toDateString(), $date->endOfWeek()->toDateString()]);
    }

    /**
     * Filter analytics for the current month.
    */
    public function scopeForMonth($query, Carbon $date = null)
    {
        $date = $date ?? Carbon::now();
        return $query->whereBetween('date', [$date->startOfMonth()->toDateString(), $date->endOfMonth()->toDateString()]);
    }

    /**
     * Filter analytics for the custom dates.
    */
    public function scopeForCustom($query, $start_date, $end_date)
    {
        if ($start_date && $end_date) {
            $start = Carbon::parse($start_date)->toDateString();
            $end = Carbon::parse($end_date)->toDateString();

            return $query->whereDate('date', '>=', $start)
                        ->whereDate('date', '<=', $end);
        }

        return $query;
    }

    /**
     * Optional: Increment sent count and update channel breakdown.
     */
    public function incrementSent(string $channel)
    {
        $this->increment('total_sent');

        $breakdown = $this->channel_breakdown ?? [];
        $breakdown[$channel] = ($breakdown[$channel] ?? 0) + 1;

        $this->channel_breakdown = $breakdown;
        $this->save();
    }

    /**
     * Relation to Notification.
     */
    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }
}
