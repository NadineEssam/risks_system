<?php

namespace App\Http\Controllers;

use App\Support\IncidentAccess;
use App\DataTables\IncidentDataTable;
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
    
    public function index(IncidentDataTable $dataTable)
    {
        return $dataTable->render('incidents.index');
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
            $centralCode = config('app.central_risk_sector_code');
            if ($centralCode && $centralRiskSector = Sector::where('sector_code', $centralCode)->first()) {
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

    public function edit(Incident $incident)
    {
        if ($error = $this->editError($incident)) {
            return redirect()->route('incidents.show', $incident)->with('error', $error);
        }

        $incident->load(['potentialRiskRegister', 'department', 'sectorResponsibilities']);

        return view('incidents.create', [
            'incident'        => $incident,
            'risks'           => collect(),
            'sectors'         => Sector::active()->orderBy('sector_ar')->get(),
            'department'      => $incident->department,
            'selectedSectors' => $incident->sectorResponsibilities->pluck('sectors_sec_id')->map(fn ($id) => (int) $id)->all(),
        ]);
    }

    public function update(StoreIncidentRequest $request, Incident $incident): RedirectResponse
    {
        if ($error = $this->editError($incident)) {
            return redirect()->route('incidents.show', $incident)->with('error', $error);
        }

        $data = $request->validated();

        DB::transaction(function () use ($data, $incident) {
            // الخطر المحتمل ثابت — التكرار ثابت — درجة الخطر = التكرار × الأثر الجديد
            $incident->update([
                'start_date'            => $data['start_date'] ?? null,
                'discovery_date'        => $data['discovery_date'],
                'impact_score'          => $data['impact_score'],
                'risk_degree'           => (int) $incident->frequency_score * (int) $data['impact_score'],
                'description'           => $data['description'] ?? null,
                'current_procedure'     => $data['current_procedure'] ?? null,
                'proposed_procedure'    => $data['proposed_procedure'] ?? null,
                'actual_impact_problem' => $data['actual_impact_problem'] ?? null,
            ]);

            // القطاعات المسؤولة: المختارة + القطاع المنشئ + القطاع المركزي (دايماً)
            $keep = collect($data['responsible_sectors'])->map(fn ($id) => (int) $id);

            if ($creatingSectorId = $incident->department?->sector?->sec_id) {
                $keep->push((int) $creatingSectorId);
            }

            $centralCode = config('app.central_risk_sector_code');
            if ($centralCode && $central = Sector::where('sector_code', $centralCode)->first()) {
                $keep->push((int) $central->sec_id);
            }

            $keep = $keep->unique()->values();

            foreach ($keep as $sectorId) {
                IncidentSectorResponsibility::firstOrCreate([
                    'incident_id'    => $incident->id,
                    'sectors_sec_id' => $sectorId,
                ]);
            }

            // القطاع اللي اتشال بيتحذف بس لو ملوش متابعات
            $incident->sectorResponsibilities()
                ->whereNotIn('sectors_sec_id', $keep->all())
                ->doesntHave('followups')
                ->delete();
        });

        return redirect()->route('incidents.show', $incident)->with('success', 'تم تعديل الحدث بنجاح.');
    }

    /** null = مسموح بالتعديل، غير كده رسالة السبب */
    private function editError(Incident $incident): ?string
    {
        if (! IncidentAccess::canEdit(Auth::user(), $incident)) {
            return 'لا يمكنك تعديل هذا الحدث — التعديل متاح للقطاع المنشئ للحدث وقطاع المخاطر فقط.';
        }

        if ($incident->isFollowupClosed()) {
            return 'لا يمكن تعديل حدث مغلق (آخر متابعة: إغلاق أو قبول الخطر).';
        }

        return null;
    }

    public function show(Incident $incident): View
    {
                abort_unless(\App\Support\IncidentAccess::canView(auth()->user(), $incident), 403, 'ليس لديك صلاحية على هذا الحدث.');
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
     * مقبول"). محمية بنفس صلاحية `incidents.status.update` الموجودة أصلاً (مُسندة
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
