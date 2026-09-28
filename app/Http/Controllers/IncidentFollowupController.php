<?php

namespace App\Http\Controllers;

use App\DataTables\IncidentFollowupDataTable;
use App\Http\Requests\StoreIncidentFollowupRequest;
use App\Models\FollowupEntryType;
use App\Models\FollowupStatus;
use App\Models\Incident;
use App\Models\IncidentFollowup;
use App\Models\IncidentSectorResponsibility;
use App\Support\IncidentAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * متابعة الحدث — نفس نظام "الرد على البيان" في الشكاوى:
 * سجل متابعات لكل حدث + إضافة/تعديل/عرض/حذف بنفس القواعد.
 */
class IncidentFollowupController extends Controller
{
    // سجل متابعات حدث معيّن
    public function index(Incident $incident, IncidentFollowupDataTable $dataTable)
    {
        $this->ensureCanView($incident);
        $incident->load(['potentialRiskRegister', 'department']);

        return $dataTable->withIncident($incident)->render('incident_followups.index', [
            'incident'     => $incident,
            'lastFollowup' => $incident->lastFollowup(),
            'isClosed'     => $incident->isFollowupClosed(),
        ]);
    }

    public function create(Incident $incident)
    {
        $this->ensureCanView($incident);

        if ($incident->isFollowupClosed()) {
            return redirect()->route('incident-followups.index', $incident)
                ->with('error', 'لا يمكن إضافة متابعة على حدث مغلق.');
        }

        if (! IncidentAccess::sector(Auth::user())) {
            return redirect()->route('incident-followups.index', $incident)
                ->with('error', 'يجب أن يكون لديك قطاع مرتبط بحسابك لتسجيل متابعة.');
        }

        return view('incident_followups.create_edit', $this->formData($incident));
    }

    public function store(StoreIncidentFollowupRequest $request, Incident $incident): RedirectResponse
    {
        $this->ensureCanView($incident);
        abort_if($incident->isFollowupClosed(), 403, 'لا يمكن إضافة متابعة على حدث مغلق.');

        $sector = IncidentAccess::sector(Auth::user());
        abort_unless($sector, 403, 'لا يوجد قطاع مرتبط بحسابك.');

        // المتابعة بتتسجل باسم قطاع المستخدم
        $responsibility = IncidentSectorResponsibility::firstOrCreate([
            'incident_id'    => $incident->id,
            'sectors_sec_id' => $sector->sec_id,
        ]);

        $responsibility->followups()->create($request->validated());

        return redirect()->route('incident-followups.index', $incident)
            ->with('success', 'تم تسجيل المتابعة بنجاح.');
    }

    public function show(IncidentFollowup $followup): View
    {
        $followup->load([
            'incidentSectorResponsibility.incident.potentialRiskRegister',
            'incidentSectorResponsibility.sector',
            'followupStatus',
            'followupEntryType',
        ]);

        $incident = $followup->incidentSectorResponsibility->incident;
        $this->ensureCanView($incident);

        return view('incident_followups.show', compact('followup', 'incident'));
    }

    public function edit(IncidentFollowup $followup)
    {
        [$incident, $error] = $this->checkModify($followup, forEdit: true);

        if ($error) {
            return redirect()->route('incident-followups.index', $incident)->with('error', $error);
        }

        return view('incident_followups.create_edit', $this->formData($incident, $followup));
    }

    public function update(StoreIncidentFollowupRequest $request, IncidentFollowup $followup): RedirectResponse
    {
        [$incident, $error] = $this->checkModify($followup, forEdit: true);

        if ($error) {
            return redirect()->route('incident-followups.index', $incident)->with('error', $error);
        }

        $followup->update($request->validated());

        return redirect()->route('incident-followups.index', $incident)
            ->with('success', 'تم تعديل المتابعة بنجاح.');
    }

    // AJAX من زر 🗑 (delete-confirm.js)
    public function destroy(Request $request, IncidentFollowup $followup)
    {
        [$incident, $error] = $this->checkModify($followup, forEdit: false);

        if ($error) {
            return $request->expectsJson()
                ? response()->json(['message' => $error], 422)
                : back()->with('error', $error);
        }

        $followup->delete();
        $message = 'تم حذف المتابعة بنجاح.';

        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : redirect()->route('incident-followups.index', $incident)->with('success', $message);
    }

    /**
     * نفس قواعد الشكاوى:
     * - التعديل/الحذف لقطاع المتابعة نفسه (أو المركزي/مدير النظام)
     * - الحدث المقفول: مفيش حذف، والتعديل لآخر متابعة بس
     */
    private function checkModify(IncidentFollowup $followup, bool $forEdit): array
    {
        $incident = $followup->incidentSectorResponsibility->incident;
        $this->ensureCanView($incident);

        if (! IncidentAccess::canModify(Auth::user(), $followup)) {
            return [$incident, 'لا يمكنك تعديل أو حذف متابعة سجّلها قطاع آخر.'];
        }

        if ($incident->isFollowupClosed()) {
            if (! $forEdit) {
                return [$incident, 'لا يمكن حذف متابعات حدث مغلق.'];
            }

            if ((int) $incident->lastFollowup()?->id !== (int) $followup->id) {
                return [$incident, 'لا يمكن تعديل متابعات حدث مغلق إلا آخر متابعة.'];
            }
        }

        return [$incident, null];
    }

    /** بيانات الفورم: الحالات المتاحة (من غير المستخدمة، ما عدا جارى المتابعة) */
    private function formData(Incident $incident, ?IncidentFollowup $followup = null): array
    {
        $used = $incident->followups()
            ->when($followup, fn ($q) => $q->where('incident_followups.id', '!=', $followup->id))
            ->pluck('followup_status_id');

        $statuses = FollowupStatus::active()->get()->filter(
            fn ($status) => in_array($status->status_name, IncidentAccess::REPEATABLE_STATUSES, true)
                || ! $used->contains($status->id)
        );

        return [
            'incident'   => $incident->loadMissing(['potentialRiskRegister', 'department']),
            'followup'   => $followup,
            'statuses'   => $statuses,
            'entryTypes' => FollowupEntryType::active()->get(),
        ];
    }

    private function ensureCanView(Incident $incident): void
    {
        abort_unless(IncidentAccess::canView(Auth::user(), $incident), 403, 'ليس لديك صلاحية على هذا الحدث.');
    }
}