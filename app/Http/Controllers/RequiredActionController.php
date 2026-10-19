<?php

namespace App\Http\Controllers;

use App\Models\PotentialRiskRegisterSectorDetail;
use App\Models\RequiredAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * خطوة "تسجيل الإجراءات المطلوبة من كل قطاع مسؤول" (خطوة 5 بالمسار).
 */
class RequiredActionController extends Controller
{
    public function store(Request $request, PotentialRiskRegisterSectorDetail $sectorDetail): RedirectResponse
    {
        abort_unless($request->user()->can('risks.edit') || $request->user()->can('risks.create'), 403);

        $data = $request->validate([
            'required_action' => ['required', 'string'],
            'expiration_date' => ['nullable', 'date'],
        ], [
            'required_action.required' => 'نص الإجراء المطلوب إلزامي.',
        ]);

        RequiredAction::create(array_merge($data, [
            'potential_risk_register_sector_details_id' => $sectorDetail->id,
        ]));

        return back()->with('success', 'تم تسجيل الإجراء المطلوب بنجاح.');
    }

    public function destroy(Request $request, RequiredAction $requiredAction): RedirectResponse
    {
        abort_unless($request->user()->can('risks.edit'), 403);

        $requiredAction->delete();

        return back()->with('success', 'تم حذف الإجراء المطلوب.');
    }
}
