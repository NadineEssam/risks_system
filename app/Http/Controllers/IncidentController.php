<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidentRequest;
use App\Models\Incident;
use App\Models\IncidentSectorResponsibility;
use App\Models\PotentialRiskRegister;
use App\Models\ResolutionStatus;
use App\Models\Sector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * المرحلة الثانية: الحدث (Incident Workflow).
 *
 * خطوات المسار:
 * 1) تحديد القطاع الإداري تلقائياً وفق قطاع المستخدم المسجل دخوله
 * 2) اختيار الخطر المحتمل المرتبط بالقطاع الإداري المحدد
 * 3) احتساب عدد مرات التكرار تلقائياً (حد أقصى 5)
 * 4) إدخال تاريخ الاكتشاف  5) درجة الأثر (1-5)
 * 6) احتساب درجة الخطر = التكرار × الأثر
 * 7) تحديد القطاعات المسؤولة عن الحل  8) الحفظ (مع إضافة قطاع الإنشاء وقطاع
 *    المخاطر المركزي تلقائياً ضمن القطاعات المسؤولة عن المتابعة).
 */
class IncidentController extends Controller
{
    public function index(): View
    {
        $incidents = Incident::with(['potentialRiskRegister', 'department', 'resolutionStatus'])
            ->latest('creation_date')
            ->get();

        return view('incidents.index', compact('incidents'));
    }

    public function create()
    {
        $user = Auth::user();

        if (! $user->department_id) {
            return back()->with('error', 'يجب أن يكون لديك قطاع/إدارة مرتبطة بحسابك لتسجيل حدث جديد. برجاء التواصل مع مسئول النظام.');
        }

        $userSectorId = $user->department?->sector?->sec_id;

        $risks = $userSectorId
            ? PotentialRiskRegister::active()->forSector($userSectorId)
                ->with('eventDetail.eventSubcategory.event.eventType')->orderByDesc('creation_date')->get()
            : collect();

        $sectors = Sector::active()->orderBy('sector_ar')->get();

        return view('incidents.create', [
            'risks' => $risks,
            'sectors' => $sectors,
            'department' => $user->department,
        ]);
    }

    public function store(StoreIncidentRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $department = $user->department;

        abort_unless($department, 403, 'لا يوجد قطاع/إدارة مرتبطة بحسابك.');

        $data = $request->validated();
        $userSectorId = $department->sector?->sec_id;

        $repetitionCount = Incident::actualRepetitionCount($data['potential_risk_register_id'], $userSectorId ?? 0);
        $repetitionNumber = $repetitionCount + 1;
        $frequencyScore = Incident::cappedFrequencyScore($repetitionNumber);
        $riskDegree = $frequencyScore * (int) $data['impact_score'];

        $incident = DB::transaction(function () use ($data, $department, $frequencyScore, $riskDegree) {
            $incident = Incident::create([
                'potential_risk_register_id' => $data['potential_risk_register_id'],
                'departments_dep_id' => $department->dep_id,
                'start_date' => $data['start_date'] ?? null,
                'discovery_date' => $data['discovery_date'],
                'impact_score' => $data['impact_score'],
                'frequency_score' => $frequencyScore,
                'risk_degree' => $riskDegree,
                'description' => $data['description'] ?? null,
                'current_procedure' => $data['current_procedure'] ?? null,
                'proposed_procedure' => $data['proposed_procedure'] ?? null,
                'actual_impact_problem' => $data['actual_impact_problem'] ?? null,
            ]);

            $sectorIds = collect($data['responsible_sectors'])->map(fn ($id) => (int) $id);

            // القطاع المنشئ للحدث وقطاع المخاطر المركزي يكونان دائماً ضمن
            // القطاعات المسؤولة عن المتابعة، طبقاً لخطوة الحفظ بالوثيقة.
            if ($creatingSectorId = $department->sector?->sec_id) {
                $sectorIds->push($creatingSectorId);
            }
            if ($centralRiskSector = Sector::where('sector_code', 'SEC-RISK')->first()) {
                $sectorIds->push($centralRiskSector->sec_id);
            }

            foreach ($sectorIds->unique() as $sectorId) {
                IncidentSectorResponsibility::create([
                    'incident_id' => $incident->id,
                    'sectors_sec_id' => $sectorId,
                ]);
            }

            return $incident;
        });

        return redirect()->route('incidents.show', $incident)
            ->with('success', "تم حفظ الحدث بنجاح. درجة الخطر المحتسبة: {$riskDegree} (تكرار {$frequencyScore} × أثر {$data['impact_score']}).");
    }

    public function show(Incident $incident): View
    {
        $incident->load([
            'potentialRiskRegister.eventDetail.eventSubcategory.event.eventType',
            'department.sector',
            'resolutionStatus',
            'sectorResponsibilities.sector',
            'sectorResponsibilities.followups.followupStatus',
            'sectorResponsibilities.followups.followupEntryType',
        ]);

        $resolutionStatuses = ResolutionStatus::active()->get();

        return view('incidents.show', compact('incident', 'resolutionStatuses'));
    }

    /**
     * تحديد/تحديث "حالة حل الحدث" الخاصة بالحدث نفسه (resolution_status_id
     * على جدول incidents) — منفصلة تماماً عن "حالة حل الخطر" الخاصة بالخطر
     * المحتمل المرتبط (potential_risk_registers، عبر risk_status_details).
     *
     * أُضيفت هذه الدالة بتاريخ 2026-09-10: لم يكن هناك أي وسيلة في الواجهة
     * لتحديد حالة الحدث من الأصل — عمود `resolution_status_id` كان يبقى
     * NULL دائماً منذ `store()`، ما كان يمنع أي حدث نهائياً من الظهور في
     * "تسجيل متابعة حدث" (اللي شرطها يشمل: حالة الحدث "حل جزئي"/"غير
     * مقبول"). محمية بنفس صلاحية `edit-incidents` الموجودة أصلاً (مُسندة
     * لدور incident-officer) لكن لم تكن مربوطة بأي مسار من قبل.
     */
    public function updateStatus(Request $request, Incident $incident): RedirectResponse
    {
        $data = $request->validate([
            'resolution_status_id' => ['required', 'integer', 'exists:resolution_statuses,id'],
        ], [
            'resolution_status_id.required' => 'يجب اختيار حالة حل الحدث.',
        ]);

        $incident->update(['resolution_status_id' => $data['resolution_status_id']]);

        return back()->with('success', 'تم تحديث حالة الحدث بنجاح.');
    }
}
