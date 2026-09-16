<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CauseCategory;
use App\Models\CauseSubcategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** تصنيف الأسباب — الفرعي (CAUSE_SUBCATEGORY — L3) */
class CauseSubcategoryController extends Controller
{
    public function index(): View
    {
        $subcategories = CauseSubcategory::with('causeCategory')->orderBy('subcategory_name')->get();
        $categories = CauseCategory::active()->orderBy('category_name')->get();

        return view('admin.cause-subcategories.index', compact('subcategories', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'cause_category_id' => ['required', 'integer', 'exists:cause_categories,id'],
            'subcategory_code' => ['nullable', 'string', 'max:50'],
            'subcategory_name' => ['required', 'string', 'max:255'],
        ]);

        CauseSubcategory::create($data);

        return back()->with('success', 'تمت إضافة التصنيف الفرعي للسبب بنجاح.');
    }

    public function toggle(CauseSubcategory $causeSubcategory): RedirectResponse
    {
        $causeSubcategory->update(['validity' => ! $causeSubcategory->validity]);

        return back()->with('success', 'تم تحديث حالة التصنيف.');
    }
}
