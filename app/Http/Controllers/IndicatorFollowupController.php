<?php

namespace App\Http\Controllers;

use App\DataTables\IndicatorFollowupDataTable;
use App\Http\Requests\StoreIndicatorFollowupRequest;
use App\Models\Indicator;
use App\Models\IndicatorFollowup;
use App\Models\ThresholdLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * متابعة المؤشر — نفس متابعة الحدث: سجل قياسات لكل مؤشر
 * + إضافة/تعديل/عرض/حذف، ومستوى حد الخطر بيتحسب تلقائياً.
 */
class IndicatorFollowupController extends Controller
{
    public function index(Indicator $indicator, IndicatorFollowupDataTable $dataTable)
    {
        $indicator->load(['potentialRiskRegister', 'nature', 'measurementUnit', 'thresholdDetails.thresholdLevel']);

        $lastFollowup = $indicator->followups()->with('thresholdLevel')
            ->orderByDesc('measurement_date')->orderByDesc('id')->first();

        return $dataTable->withIndicator($indicator)->render('indicator_followups.index', [
            'indicator'    => $indicator,
            'lastFollowup' => $lastFollowup,
        ]);
    }

    public function create(Indicator $indicator): View
    {
        return view('indicator_followups.create_edit', $this->formData($indicator));
    }

    public function store(StoreIndicatorFollowupRequest $request, Indicator $indicator): RedirectResponse
    {
        $data = $request->validated();
        $data['indicators_id']      = $indicator->id;
        // المستوى من السيرفر — مش من الفورم
        $data['threshold_level_id'] = $indicator->calculateThresholdLevel($data['actual_value'])->id;

        IndicatorFollowup::create($data);

        return redirect()->route('indicator-followups.index', $indicator)
            ->with('success', 'تم تسجيل القياس بنجاح.');
    }

    public function show(IndicatorFollowup $followup): View
    {
        $followup->load(['indicator.nature', 'indicator.measurementUnit', 'thresholdLevel']);
        $indicator = $followup->indicator;

        return view('indicator_followups.show', compact('followup', 'indicator'));
    }

    public function edit(IndicatorFollowup $followup): View
    {
        return view('indicator_followups.create_edit', $this->formData($followup->indicator, $followup));
    }

    public function update(StoreIndicatorFollowupRequest $request, IndicatorFollowup $followup): RedirectResponse
    {
        $indicator = $followup->indicator;
        $data = $request->validated();
        $data['threshold_level_id'] = $indicator->calculateThresholdLevel($data['actual_value'])->id;

        $followup->update($data);

        return redirect()->route('indicator-followups.index', $indicator)
            ->with('success', 'تم تعديل القياس بنجاح.');
    }

    // AJAX من زر 🗑 (delete-confirm.js)
    public function destroy(Request $request, IndicatorFollowup $followup)
    {
        $indicator = $followup->indicator;
        $followup->delete();
        $message = 'تم حذف القياس بنجاح.';

        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : redirect()->route('indicator-followups.index', $indicator)->with('success', $message);
    }

    /** بيانات الفورم + بيانات الحساب التلقائي للـ JS */
    private function formData(Indicator $indicator, ?IndicatorFollowup $followup = null): array
    {
        $indicator->loadMissing(['potentialRiskRegister', 'nature', 'measurementUnit', 'thresholdDetails.thresholdLevel']);

        $levels = ThresholdLevel::active()->get()->mapWithKeys(fn ($l) => [
            (int) $l->sort_order => ['id' => $l->id, 'name' => $l->level_name],
        ]);

        return [
            'indicator' => $indicator,
            'followup'  => $followup,
            'calc'      => array_merge($indicator->thresholdLimits(), [
                'decreasing' => $indicator->isDecreasing(),
                'levels'     => $levels,
            ]),
        ];
    }
}