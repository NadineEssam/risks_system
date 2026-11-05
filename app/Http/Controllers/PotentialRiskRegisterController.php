<?php

namespace App\Http\Controllers;

use App\DataTables\PotentialRiskRegisterDataTable;
use App\Http\Requests\StorePotentialRiskRegisterRequest;
use App\Http\Requests\UpdatePotentialRiskRegisterRequest;
use App\Models\EventDetail;
use App\Models\EventSubcategory;
use App\Models\EventType;
use App\Models\PotentialRiskRegister;
use App\Models\ResolutionStatus;
use App\Models\RiskEvent;
use App\Models\RiskResolutionStatusDetail;
use App\Models\Sector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Support\RiskNotifications;
use App\Models\CauseDetail;
use App\Models\ControlCategory;
use App\Models\ImpactItem;
use App\Models\RiskAppetiteDetail;
use App\Models\ThresholdLevel;

/**
 * المرحلة الأولى: سجل المخاطر المحتملة (Potential Risk Register Workflow).
 *
 * خطوات المسار كما وردت في وثيقة المتطلبات:
 * 1) اختيار تصنيف بازل العام  2) اختيار تصنيف بازل التفصيلي
 * 3) تسجيل بيانات الخطر المحتمل  4) ربط القطاعات الإدارية المسؤولة
 * 5) تسجيل الإجراءات المطلوبة من كل قطاع  6) تحديد الحالة الأولية  7) الحفظ
 */
class PotentialRiskRegisterController extends Controller
{
    public function index(PotentialRiskRegisterDataTable $dataTable)
    {
        return $dataTable->render('risks.index');
    }

    public function create(): View
    {
        return view('risks.create', $this->formLookups());
    }

    public function store(StorePotentialRiskRegisterRequest $request): RedirectResponse
    {
        // نسجّل قطاع وإدارة اللي أنشأ الخطر — عشان يظهر لقطاعه في فورم الحدث
        $user = $request->user();
        $risk = PotentialRiskRegister::create(array_merge($request->validated(), [
            // قطاع الإدارة أولاً (نفس اللي فورم الحدث بيقارن بيه) — وبعدها قطاع المستخدم
            'created_sector_id'     => $user->department?->sector?->sec_id ?? $user->sector_id,
            'created_department_id' => $user->department_id,
        ]));

        // حالتين مستقلتين: قبول الخطر + معالجة الخطر
        foreach (['acceptance_status_id', 'treatment_status_id'] as $field) {
            RiskResolutionStatusDetail::create([
                'potential_risk_register_id' => $risk->id,
                'resolution_status_id'       => $request->validated()[$field],
            ]);
        }

        // الأثر والضوابط: أكتر من اختيار
        $risk->impactItems()->sync($request->input('impact_items', []));
        // شهية المخاطر: حد لكل مستوى + الإجراء المطلوب
        foreach ($request->input('appetite', []) as $row) {
            RiskAppetiteDetail::updateOrCreate(
                ['potential_risk_register_id' => $risk->id, 'threshold_level_id' => $row['threshold_level_id']],
                ['threshold_value' => (int) $row['threshold_value'], 'required_action' => $row['required_action'] ?? null]
            );
        }
        // الأسباب + نسبة كل سبب
        $risk->causeDetails()->sync(collect($request->input('causes', []))
            ->mapWithKeys(fn ($c) => [(int) $c['cause_detail_id'] => ['percentage' => (float) $c['percentage']]])
            ->all());
        $risk->controlCategories()->sync($request->input('control_categories', []));

        return redirect()->route('risks.show', $risk)
            ->with('success', 'تم تسجيل الخطر المحتمل بنجاح. يمكنك الآن ربط القطاعات المسؤولة وتسجيل الإجراءات المطلوبة.');
    }

    public function show(PotentialRiskRegister $risk): View
    {
        $risk->load([
            'eventDetail.eventSubcategory.event.eventType',
            'causeDetails.causeSubcategory',
            'impactItems.impactCategory',
            'controlCategories.controlDomain',
            'appetiteDetails.thresholdLevel',
            'sectorDetails.sector',
            'sectorDetails.requiredActions',
            'resolutionStatusDetails.resolutionStatus',
            'incidents.department',
            'indicators',
        ]);

        $allSectors = Sector::active()->orderBy('sector_ar')->get();
        $acceptanceStatuses = ResolutionStatus::active()->acceptance()->orderBy('status_name')->get();
        $treatmentStatuses  = ResolutionStatus::active()->treatment()->orderBy('status_name')->get();

        return view('risks.show', compact('risk', 'allSectors', 'acceptanceStatuses', 'treatmentStatuses'));
    }

    public function edit(PotentialRiskRegister $risk): View
    {
        $risk->load(['impactItems', 'controlCategories', 'causeDetails', 'appetiteDetails']);

        return view('risks.edit', array_merge(['risk' => $risk], $this->formLookups()));
    }

    public function update(UpdatePotentialRiskRegisterRequest $request, PotentialRiskRegister $risk): RedirectResponse
    {
        $risk->update($request->validated());
        $risk->impactItems()->sync($request->input('impact_items', []));
        // شهية المخاطر: حد لكل مستوى + الإجراء المطلوب
        foreach ($request->input('appetite', []) as $row) {
            RiskAppetiteDetail::updateOrCreate(
                ['potential_risk_register_id' => $risk->id, 'threshold_level_id' => $row['threshold_level_id']],
                ['threshold_value' => (int) $row['threshold_value'], 'required_action' => $row['required_action'] ?? null]
            );
        }
        // الأسباب + نسبة كل سبب
        $risk->causeDetails()->sync(collect($request->input('causes', []))
            ->mapWithKeys(fn ($c) => [(int) $c['cause_detail_id'] => ['percentage' => (float) $c['percentage']]])
            ->all());
        $risk->controlCategories()->sync($request->input('control_categories', []));

        return redirect()->route('risks.show', $risk)->with('success', 'تم تحديث بيانات الخطر المحتمل بنجاح.');
    }

    public function toggle(PotentialRiskRegister $risk): RedirectResponse
    {
        abort_unless(request()->user()->can('risks.edit'), 403);

        $risk->update(['validity' => ! $risk->validity]);

        return back()->with('success', $risk->validity ? 'تم تفعيل الخطر.' : 'تم إلغاء تفعيل الخطر.');
    }

    public function updateStatus(Request $request, PotentialRiskRegister $risk): RedirectResponse
    {
        abort_unless($request->user()->can('risks.status.update'), 403);

        $data = $request->validate([
            'resolution_status_id' => ['required', 'integer', 'exists:resolution_statuses,id'],
        ]);

        // التكرار ممنوع جوه نفس المجموعة (قبول ← قبول / معالجة ← معالجة)
        $status = ResolutionStatus::findOrFail($data['resolution_status_id']);
        $groupLabel = $status->status_group === ResolutionStatus::GROUP_ACCEPTANCE ? 'قبول الخطر' : 'معالجة الخطر';

        $currentStatusId = $risk->resolutionStatusDetails()
            ->whereHas('resolutionStatus', fn ($q) => $q->where('status_group', $status->status_group))
            ->orderByDesc('id')
            ->value('resolution_status_id');

        if ((string) $currentStatusId === (string) $status->id) {
            return back()->with('error', "هذه هي الحالة الحالية لـ ({$groupLabel}) بالفعل — اختر حالة مختلفة.");
        }

        RiskResolutionStatusDetail::create([
            'potential_risk_register_id' => $risk->id,
            'resolution_status_id' => $data['resolution_status_id'],
        ]);

        RiskNotifications::riskStatusApproved($risk, "{$groupLabel}: {$status->status_name}");

        return back()->with('success', "تم اعتماد ({$groupLabel}): {$status->status_name}.");
    }

    private function formLookups(): array
    {
        return [
            'eventTypes' => EventType::active()->orderBy('type_name')->get(),
            'events' => RiskEvent::active()->orderBy('event_name')->get(['id', 'event_type_id', 'event_name']),
            'eventSubcategories' => EventSubcategory::active()->orderBy('subcategory_name')->get(['id', 'events_id', 'subcategory_name']),
            'eventDetails' => EventDetail::active()->orderBy('detail_name')->get(['id', 'event_subcategory_id', 'detail_name']),
            'resolutionStatuses' => ResolutionStatus::active()->orderBy('status_name')->get(),
            'acceptanceStatuses' => ResolutionStatus::active()->acceptance()->orderBy('status_name')->get(),
            'treatmentStatuses'  => ResolutionStatus::active()->treatment()->orderBy('status_name')->get(),
            'thresholdLevels'    => ThresholdLevel::active()->orderBy('sort_order')->get(),
            // تصنيفات ERM (النشطة بس)
            'causeDetails'      => CauseDetail::active()->with('causeSubcategory')->orderBy('detail_code')->get(),
            'impactItems'       => ImpactItem::active()->with('impactCategory')->orderBy('item_code')->get(),
            'controlCategories' => ControlCategory::active()->with('controlDomain')->orderBy('category_code')->get(),
        ];
    }
}
