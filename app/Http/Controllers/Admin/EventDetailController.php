<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventDetail;
use App\Models\EventSubcategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** تصنيف بازل الدقيق (EVENT_DETAIL — L4) */
class EventDetailController extends Controller
{
    public function index(): View
    {
        $details = EventDetail::with('eventSubcategory.event.eventType')->orderBy('detail_name')->get();
        $subcategories = EventSubcategory::active()->orderBy('subcategory_name')->get();

        return view('admin.event-details.index', compact('details', 'subcategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'event_subcategory_id' => ['required', 'integer', 'exists:event_subcategories,id'],
            'detail_code' => ['nullable', 'string', 'max:50'],
            'detail_name' => ['required', 'string', 'max:255'],
            'bank_example' => ['nullable', 'string'],
        ]);

        EventDetail::create($data);

        return back()->with('success', 'تمت إضافة التصنيف الدقيق بنجاح.');
    }

    public function toggle(EventDetail $eventDetail): RedirectResponse
    {
        $eventDetail->update(['validity' => ! $eventDetail->validity]);

        return back()->with('success', 'تم تحديث حالة التصنيف.');
    }
}
