<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CauseDetail;
use App\Models\CauseSubcategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** تصنيف الأسباب — الدقيق / السبب الجذري (CAUSE_DETAIL — L4) */
class CauseDetailController extends Controller
{
    public function index(): View
    {
        $details = CauseDetail::with('causeSubcategory.causeCategory')->orderBy('detail_name')->get();
        $subcategories = CauseSubcategory::active()->orderBy('subcategory_name')->get();

        return view('admin.cause-details.index', compact('details', 'subcategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'cause_subcategory_id' => ['required', 'integer', 'exists:cause_subcategories,id'],
            'detail_code' => ['nullable', 'string', 'max:50'],
            'detail_name' => ['required', 'string', 'max:255'],
        ]);

        CauseDetail::create($data);

        return back()->with('success', 'تمت إضافة السبب الجذري بنجاح.');
    }

    public function toggle(CauseDetail $causeDetail): RedirectResponse
    {
        $causeDetail->update(['validity' => ! $causeDetail->validity]);

        return back()->with('success', 'تم تحديث حالة التصنيف.');
    }
}
