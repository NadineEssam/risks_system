<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** SECTORS — القطاعات الإدارية (بيانات مرجعية محلية) */
class SectorController extends Controller
{
    public function index(): View
    {
        $sectors = Sector::orderBy('sector_ar')->get();

        return view('admin.sectors.index', compact('sectors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'sector_code' => ['required', 'string', 'max:50', 'unique:sectors,sector_code'],
            'sector_ar' => ['required', 'string', 'max:255'],
            'sector_en' => ['nullable', 'string', 'max:255'],
        ]);

        Sector::create($data);

        return back()->with('success', 'تمت إضافة القطاع بنجاح.');
    }

    public function toggle(Sector $sector): RedirectResponse
    {
        $sector->update(['validity' => ! $sector->validity]);

        return back()->with('success', 'تم تحديث حالة تفعيل القطاع.');
    }
}
