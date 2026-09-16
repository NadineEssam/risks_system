<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventSubcategory;
use App\Models\RiskEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** تصنيف بازل الفرعي (EVENT_SUBCATEGORY — L3) */
class EventSubcategoryController extends Controller
{
    public function index(): View
    {
        $subcategories = EventSubcategory::with('event.eventType')->orderBy('subcategory_name')->get();
        $events = RiskEvent::active()->orderBy('event_name')->get();

        return view('admin.event-subcategories.index', compact('subcategories', 'events'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'events_id' => ['required', 'integer', 'exists:events,id'],
            'subcategory_code' => ['nullable', 'string', 'max:50'],
            'subcategory_name' => ['required', 'string', 'max:255'],
        ]);

        EventSubcategory::create($data);

        return back()->with('success', 'تمت إضافة التصنيف الفرعي بنجاح.');
    }

    public function toggle(EventSubcategory $eventSubcategory): RedirectResponse
    {
        $eventSubcategory->update(['validity' => ! $eventSubcategory->validity]);

        return back()->with('success', 'تم تحديث حالة التصنيف.');
    }
}
