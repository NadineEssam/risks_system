<?php

namespace App\Http\Controllers;

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
    public function index(Request $request): View
    {
        $query = PotentialRiskRegister::with([
            'eventDetail.eventSubcategory.event.eventType',
            'latestResolutionStatus.resolutionStatus',
        ])
            ->withCount(['sectorDetails', 'incidents', 'indicators']);

        if ($search = $request->get('q')) {
            $query->where('risk_description', 'like', "%{$search}%");
        }

        $risks = $query->latest('creation_date')->get();

        return view('risks.index', compact('risks', 'search'));
    }

    public function create(): View
    {
        return view('risks.create', $this->formLookups());
    }

    public function store(StorePotentialRiskRegisterRequest $request): RedirectResponse
    {
        $risk = PotentialRiskRegister::create($request->validated());

        RiskResolutionStatusDetail::create([
            'potential_risk_register_id' => $risk->id,
            'resolution_status_id' => $request->validated()['resolution_status_id'],
        ]);

        return redirect()->route('risks.show', $risk)
            ->with('success', 'تم تسجيل الخطر المحتمل بنجاح. يمكنك الآن ربط القطاعات المسؤولة وتسجيل الإجراءات المطلوبة.');
    }

    public function show(PotentialRiskRegister $risk): View
    {
        $risk->load([
            'eventDetail.eventSubcategory.event.eventType',
            'sectorDetails.sector',
            'sectorDetails.requiredActions',
            'resolutionStatusDetails.resolutionStatus',
            'incidents.department',
            'indicators',
        ]);

        $allSectors = Sector::active()->orderBy('sector_ar')->get();
        $resolutionStatuses = ResolutionStatus::active()->orderBy('status_name')->get();

        return view('risks.show', compact('risk', 'allSectors', 'resolutionStatuses'));
    }

    public function edit(PotentialRiskRegister $risk): View
    {
        return view('risks.edit', array_merge(['risk' => $risk], $this->formLookups()));
    }

    public function update(UpdatePotentialRiskRegisterRequest $request, PotentialRiskRegister $risk): RedirectResponse
    {
        $risk->update($request->validated());

        return redirect()->route('risks.show', $risk)->with('success', 'تم تحديث بيانات الخطر المحتمل بنجاح.');
    }

    public function toggle(PotentialRiskRegister $risk): RedirectResponse
    {
        abort_unless(request()->user()->can('edit-risks'), 403);

        $risk->update(['validity' => ! $risk->validity]);

        return back()->with('success', $risk->validity ? 'تم تفعيل الخطر.' : 'تم إلغاء تفعيل الخطر.');
    }

    public function updateStatus(Request $request, PotentialRiskRegister $risk): RedirectResponse
    {
        abort_unless($request->user()->can('approve-risks'), 403);

        $data = $request->validate([
            'resolution_status_id' => ['required', 'integer', 'exists:resolution_statuses,id'],
        ]);

        RiskResolutionStatusDetail::create([
            'potential_risk_register_id' => $risk->id,
            'resolution_status_id' => $data['resolution_status_id'],
        ]);

        return back()->with('success', 'تم اعتماد حالة الخطر المحدَّثة.');
    }

    private function formLookups(): array
    {
        return [
            'eventTypes' => EventType::active()->orderBy('type_name')->get(),
            'events' => RiskEvent::active()->orderBy('event_name')->get(['id', 'event_type_id', 'event_name']),
            'eventSubcategories' => EventSubcategory::active()->orderBy('subcategory_name')->get(['id', 'events_id', 'subcategory_name']),
            'eventDetails' => EventDetail::active()->orderBy('detail_name')->get(['id', 'event_subcategory_id', 'detail_name']),
            'resolutionStatuses' => ResolutionStatus::active()->orderBy('status_name')->get(),
        ];
    }
}
