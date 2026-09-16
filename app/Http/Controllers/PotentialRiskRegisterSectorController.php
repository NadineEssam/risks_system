<?php

namespace App\Http\Controllers;

use App\Models\PotentialRiskRegister;
use App\Models\PotentialRiskRegisterSectorDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * خطوة "ربط القطاعات الإدارية المسؤولة" عن الخطر المحتمل (خطوة 4 بالمسار).
 */
class PotentialRiskRegisterSectorController extends Controller
{
    public function store(Request $request, PotentialRiskRegister $risk): RedirectResponse
    {
        abort_unless($request->user()->can('edit-risks') || $request->user()->can('create-risks'), 403);

        $data = $request->validate([
            'sectors_sec_id' => ['required', 'integer', 'exists:sectors,sec_id'],
        ], [
            'sectors_sec_id.required' => 'يجب اختيار القطاع الإداري المسؤول.',
        ]);

        $exists = $risk->sectorDetails()->where('sectors_sec_id', $data['sectors_sec_id'])->exists();

        if ($exists) {
            return back()->with('error', 'هذا القطاع مرتبط بالفعل بهذا الخطر المحتمل.');
        }

        PotentialRiskRegisterSectorDetail::create([
            'potential_risk_register_id' => $risk->id,
            'sectors_sec_id' => $data['sectors_sec_id'],
        ]);

        return back()->with('success', 'تم ربط القطاع الإداري بنجاح. يمكنك الآن تسجيل الإجراءات المطلوبة منه.');
    }

    public function destroy(Request $request, PotentialRiskRegister $risk, PotentialRiskRegisterSectorDetail $sectorDetail): RedirectResponse
    {
        abort_unless($request->user()->can('edit-risks'), 403);
        abort_unless($sectorDetail->potential_risk_register_id === $risk->id, 404);

        $sectorDetail->delete();

        return back()->with('success', 'تم إلغاء ربط القطاع الإداري وكل الإجراءات المرتبطة به.');
    }
}
