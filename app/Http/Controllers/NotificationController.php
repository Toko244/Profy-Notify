<?php

namespace App\Http\Controllers;

use App\Enums\Trigger;
use App\Http\Requests\Dashboard\NotificationRequest;
use App\Jobs\Triggers\ScheduledJob;
use App\Models\EmailNotification;
use App\Models\Notification;
use App\Models\NotificationCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notifications = Notification::when(request()->get('category_id'), function ($query) {
            $query->where('category_id', request()->get('category_id'));
        })->when(request()->get('title'), function ($query) {
            $query->where('title', 'like', '%' . request()->get('title') . '%');
        })
            ->latest()->paginate(20);
        $notificationCategories = NotificationCategory::latest()->pluck('title', 'id')->toArray();

        return view('pages.notifications.index', [
            'notifications' => $notifications,
            'notificationCategories' => $notificationCategories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.notifications.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NotificationRequest $request)
    {
        $notification = Notification::create($request->validated());
        $notification->translations()->createMany($request->translations);

        if ($request->criterion) {
            foreach ($request->criterion as $criterion) {
                $notification->criteria()->create([
                    'type' => $criterion['type'],
                    'additional' => $criterion['additional'] ?? [],
                ]);
            }
        }

        if ($notification->trigger == 'scheduled') {
            ScheduledJob::dispatch($notification)->delay(Carbon::create($notification->additional['time']));
        }

        return redirect()->route('notifications.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification)
    {
        $notification->load('criteria');
        return view('pages.notifications.edit', [
            'notification' => $notification
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NotificationRequest $request, Notification $notification)
    {
        $notification->update($request->validated());

        $notification->translations()->delete();
        $notification->translations()->createMany($request->translations);

        $notification->criteria()->delete();
        if ($request->criterion) {
            foreach ($request->criterion as $criterion) {
                $notification->criteria()->create([
                    'type' => $criterion['type'],
                    'additional' => $criterion['additional'] ?? [],
                ]);
            }
        }

        if ($notification->trigger === 'scheduled' && isset($notification->additional['time'])) {
            ScheduledJob::dispatch($notification)->delay(Carbon::create($notification->additional['time']));
        }

        return redirect()->route('notifications.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();

        return redirect()->route('notifications.index');
    }
}
