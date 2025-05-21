<?php

namespace App\Http\Controllers;

use App\Http\Requests\Dashboard\NotificationCategoryRequest;
use App\Models\NotificationCategory;
use Illuminate\Http\Request;

class NotificationCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notificationCategories = NotificationCategory::withCount('notifications')->latest()->paginate(20);

        return view('pages.notification-categories.index', compact('notificationCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.notification-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NotificationCategoryRequest $request)
    {
        NotificationCategory::create($request->validated());

        return redirect()->route('notification-categories.index')->with('success', 'Notification category created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NotificationCategory $notificationCategory)
    {
        return view('pages.notification-categories.edit', compact('notificationCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NotificationCategoryRequest $request, NotificationCategory $notificationCategory)
    {
        $notificationCategory->update($request->validated());

        return redirect()->route('notification-categories.index')->with('success', 'Notification category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NotificationCategory $notificationCategory)
    {
        $notificationCategory->delete();

        return redirect()->route('notification-categories.index')->with('success', 'Notification category deleted successfully.');
    }
}
