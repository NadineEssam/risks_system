<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventType;
use App\Models\RiskEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** تصنيف بازل التفصيلي (EVENTS) */
class EventController extends Controller
{
    public function index(): View
    {
        $events = RiskEvent::with('eventType')->orderBy('event_name')->get();
        $eventTypes = EventType::active()->orderBy('type_name')->get();

        return view('admin.events.index', compact('events', 'eventTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'event_type_id' => ['required', 'integer', 'exists:event_types,id'],
            'event_name' => ['required', 'string', 'max:255'],
        ]);

        RiskEvent::create($data);

        return back()->with('success', 'تمت إضافة التصنيف التفصيلي بنجاح.');
    }

    public function toggle(RiskEvent $event): RedirectResponse
    {
        $event->update(['validity' => ! $event->validity]);

        return back()->with('success', 'تم تحديث حالة التصنيف.');
    }
}
