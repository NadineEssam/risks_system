<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CauseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** تصنيف الأسباب — الفئة (CAUSE_CATEGORY — L2) */
class CauseCategoryController extends Controller
{
    public function index(): View
    {
        $categories = CauseCategory::orderBy('category_name')->get();

        return view('admin.cause-categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_code' => ['nullable', 'string', 'max:50'],
            'category_name' => ['required', 'string', 'max:255'],
        ]);

        CauseCategory::create($data);

        return back()->with('success', 'تمت إضافة فئة السبب بنجاح.');
    }

    public function toggle(CauseCategory $causeCategory): RedirectResponse
    {
        $causeCategory->update(['validity' => ! $causeCategory->validity]);

        return back()->with('success', 'تم تحديث حالة التصنيف.');
    }
}
