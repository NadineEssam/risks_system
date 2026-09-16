<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Sector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** DEPARTMENTS — الإدارات (بيانات مرجعية محلية) */
class DepartmentController extends Controller
{
    public function index(): View
    {
        $departments = Department::with('sector')->orderBy('depname_ar')->get();
        $sectors = Sector::active()->orderBy('sector_ar')->get();

        return view('admin.departments.index', compact('departments', 'sectors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'sector_code' => ['required', 'string', 'max:50', 'exists:sectors,sector_code'],
            'dep_code' => ['required', 'string', 'max:50', 'unique:departments,dep_code'],
            'depname_ar' => ['required', 'string', 'max:255'],
            'depname_en' => ['nullable', 'string', 'max:255'],
        ]);

        Department::create($data);

        return back()->with('success', 'تمت إضافة الإدارة بنجاح.');
    }

    public function toggle(Department $department): RedirectResponse
    {
        $department->update(['validity' => ! $department->validity]);

        return back()->with('success', 'تم تحديث حالة تفعيل الإدارة.');
    }
}
