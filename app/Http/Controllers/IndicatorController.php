<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIndicatorRequest;
use App\Models\ActivityUnit;
use App\Models\Indicator;
use App\Models\IndicatorNature;
use App\Models\IndicatorResponsible;
use App\Models\IndicatorThresholdDetail;
use App\Models\MeasurementUnit;
use App\Models\PotentialRiskRegister;
use App\Models\ReportingFrequency;
use App\Models\ResponsibleRole;
use App\Models\ThresholdLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * المرحلة الرابعة: المؤشر (Indicator / KRI Workflow).
 *
 * 1) اختيار الخطر المحتمل  2) خصائص المؤشر (طبيعة/وحدة قياس/دورية إبلاغ/وحدة نشاط)
 * 3) مستويات الحدود الثلاثة  4) إجراءات تجاوز المستوى (متوسط/مرتفع)
 * 5) تعيين مسئولي المؤشر وأدوارهم  6) الحفظ بالحالة "مفعل" تلقائياً.
 */
class IndicatorController extends Controller
{
    public function index(): View
    {
        $indicators = Indicator::with(['potentialRiskRegister', 'nature', 'measurementUnit', 'reportingFrequency'])
            ->latest('creation_date')
            ->get();

        return view('indicators.index', compact('indicators'));
    }

    public function create(Request $request): View
    {
        return view('indicators.create', array_merge($this->formLookups(), [
            'selectedRiskId' => $request->get('risk'),
        ]));
    }

    public function store(StoreIndicatorRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $indicator = DB::transaction(function () use ($data) {
            $indicator = Indicator::create([
                'potential_risk_register_id' => $data['potential_risk_register_id'],
                'indicator_nature_id' => $data['indicator_nature_id'],
                'measurement_unit_id' => $data['measurement_unit_id'],
                'reporting_frequency_id' => $data['reporting_frequency_id'],
                'activity_unit_id' => $data['activity_unit_id'],
                'indicator_name' => $data['indicator_name'],
                'related_actions' => $data['related_actions'] ?? null,
                'data_sources' => $data['data_sources'] ?? null,
            ]);

            foreach ($data['thresholds'] as $threshold) {
                IndicatorThresholdDetail::create([
                    'indicators_id' => $indicator->id,
                    'threshold_level_id' => $threshold['threshold_level_id'],
                    'threshold_value' => $threshold['threshold_value'],
                    'required_action' => $threshold['required_action'] ?? null,
                ]);
            }

            foreach ($data['responsibles'] as $responsible) {
                IndicatorResponsible::create(array_merge($responsible, [
                    'indicators_id' => $indicator->id,
                ]));
            }

            return $indicator;
        });

        return redirect()->route('indicators.show', $indicator)->with('success', 'تم حفظ المؤشر بنجاح بالحالة "مفعل".');
    }

    public function show(Indicator $indicator): View
    {
        $indicator->load([
            'potentialRiskRegister.eventDetail.eventSubcategory.event.eventType',
            'nature', 'measurementUnit', 'reportingFrequency', 'activityUnit',
            'thresholdDetails.thresholdLevel',
            'responsibles.role',
            'followups.thresholdLevel',
        ]);

        return view('indicators.show', compact('indicator'));
    }

    private function formLookups(): array
    {
        return [
            'risks' => PotentialRiskRegister::active()->with('eventDetail.eventSubcategory.event.eventType')->orderByDesc('creation_date')->get(),
            'natures' => IndicatorNature::active()->get(),
            'units' => MeasurementUnit::active()->get(),
            'frequencies' => ReportingFrequency::active()->get(),
            'activityUnits' => ActivityUnit::active()->get(),
            'thresholdLevels' => ThresholdLevel::active()->get(),
            'roles' => ResponsibleRole::active()->get(),
        ];
    }
}
