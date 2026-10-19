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
use App\DataTables\IndicatorDataTable;
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
        public function index(IndicatorDataTable $dataTable)
    {
        return $dataTable->render('indicators.index');
    }

    public function create(Request $request): View
    {
        return view('indicators.create_edit', array_merge($this->formLookups(), [
            'indicator'      => null,
            'selectedRiskId' => $request->get('risk'),
        ]));
    }

    public function edit(Indicator $indicator): View
    {
        $indicator->load(['thresholdDetails', 'responsibles']);

        return view('indicators.create_edit', array_merge($this->formLookups(), [
            'indicator'      => $indicator,
            'selectedRiskId' => $indicator->potential_risk_register_id,
        ]));
    }

    public function update(StoreIndicatorRequest $request, Indicator $indicator): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $indicator) {
            $indicator->update([
                'potential_risk_register_id' => $data['potential_risk_register_id'],
                'indicator_nature_id'        => $data['indicator_nature_id'],
                'measurement_unit_id'        => $data['measurement_unit_id'],
                'reporting_frequency_id'     => $data['reporting_frequency_id'],
                'activity_unit_id'           => $data['activity_unit_id'],
                'indicator_name'             => $data['indicator_name'],
                'related_actions'            => $data['related_actions'] ?? null,
                'data_sources'               => $data['data_sources'] ?? null,
            ]);

            // الحدود: تحديث قيمة كل مستوى
            foreach ($data['thresholds'] as $threshold) {
                IndicatorThresholdDetail::updateOrCreate(
                    ['indicators_id' => $indicator->id, 'threshold_level_id' => $threshold['threshold_level_id']],
                    ['threshold_value' => $threshold['threshold_value'], 'required_action' => $threshold['required_action'] ?? null]
                );
            }

            // المسئولين: نستبدلهم بالقائمة الجديدة
            $indicator->responsibles()->delete();
            foreach ($data['responsibles'] as $responsible) {
                IndicatorResponsible::create(array_merge($responsible, ['indicators_id' => $indicator->id]));
            }

            // إعادة حساب مستوى كل القياسات القديمة بالحدود/الطبيعة الجديدة
            $indicator->refresh()->load('nature');
            foreach ($indicator->followups as $followup) {
                $level = $indicator->calculateThresholdLevel($followup->actual_value);
                if ($level && (int) $followup->threshold_level_id !== (int) $level->id) {
                    $followup->forceFill(['threshold_level_id' => $level->id])->saveQuietly();
                }
            }
        });

        return redirect()->route('indicators.show', $indicator)->with('success', 'تم تعديل المؤشر بنجاح.');
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
                'approval_date'              => $data['approval_date'],
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
