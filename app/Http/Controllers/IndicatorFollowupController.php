<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIndicatorFollowupRequest;
use App\Models\Indicator;
use App\Models\IndicatorFollowup;
use App\Models\ThresholdLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\DataTables\IndicatorFollowupDataTable;

/**
 * المرحلة الخامسة: متابعة المؤشر (Indicator Follow-Up Workflow).
 *
 * 1) تحديد القطاع الإداري تلقائياً  2) اختيار مؤشر مرتبط بالقطاع ومفعّل
 * 3) القيمة الفعلية وتاريخ القياس ومستوى الحد  4) التحقق من مستوى الحد
 * 5) أسباب التغيّر والإجراء المتخذ (إلزامي إذا لم يكن الحد مقبولاً)  6) الحفظ.
 */
class IndicatorFollowupController extends Controller
{
        public function index(IndicatorFollowupDataTable $dataTable)
    {
        return $dataTable->render('indicator_followups.index');
    }

    public function create()
    {
        $user = Auth::user();
        $sectorId = $user->department?->sector?->sec_id;

        if (! $sectorId) {
            return back()->with('error', 'يجب أن يكون لديك قطاع/إدارة مرتبطة بحسابك لتسجيل متابعة مؤشر.');
        }

        $indicators = Indicator::active()->forSector($sectorId)->with('potentialRiskRegister')->get();
        $thresholdLevels = ThresholdLevel::active()->get();

        return view('indicator_followups.create', compact('indicators', 'thresholdLevels'));
    }

    public function store(StoreIndicatorFollowupRequest $request): RedirectResponse
    {
        IndicatorFollowup::create($request->validated());

        return redirect()->route('indicators.show', $request->validated()['indicators_id'])
            ->with('success', 'تم تسجيل متابعة المؤشر بنجاح.');
    }
}
