<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidentFollowupRequest;
use App\Models\FollowupEntryType;
use App\Models\FollowupStatus;
use App\Models\Incident;
use App\Models\IncidentFollowup;
use App\Models\IncidentSectorResponsibility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * المرحلة الثالثة: متابعة الحدث (Incident Follow-Up Workflow).
 *
 * 1) تحديد القطاع الإداري تلقائياً  2) اختيار الحدث (المحوَّل لقطاع
 * المستخدم بحالة "حل جزئي/غير مقبول" أو كانت آخر متابعة له "جارى المتابعة")
 * 3) تسجيل نص المتابعة  4) تصنيف المتابعة (توصية/رد/رأي)
 * 5) حالة المتابعة (يحددها القطاع المركزي للمخاطر)  6) الحفظ.
 */
class IncidentFollowupController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $sectorId = $user->department?->sector?->sec_id;

        $query = IncidentFollowup::with([
            'incidentSectorResponsibility.incident.potentialRiskRegister',
            'incidentSectorResponsibility.sector',
            'followupStatus',
            'followupEntryType',
        ])->latest('creation_date');

        if ($sectorId && ! $user->hasRole('super-admin') && ! $user->can('incident-followups.decide')) {
            $query->whereHas('incidentSectorResponsibility', fn ($q) => $q->where('sectors_sec_id', $sectorId));
        }

        $followups = $query->get();

        return view('incident_followups.index', compact('followups'));
    }

    public function create()
    {
        $user = Auth::user();
        $sectorId = $user->department?->sector?->sec_id;

        if (! $sectorId) {
            return back()->with('error', 'يجب أن يكون لديك قطاع/إدارة مرتبطة بحسابك لتسجيل متابعة.');
        }

        $eligibleIncidents = Incident::whereHas('sectorResponsibilities', function ($q) use ($sectorId) {
            $q->where('sectors_sec_id', $sectorId);
        })->where(function ($q) use ($sectorId) {
            $q->whereHas('resolutionStatus', fn ($qq) => $qq->whereIn('status_name', ['حل جزئي', 'غير مقبول']))
                ->orWhereHas('sectorResponsibilities', function ($qq) use ($sectorId) {
                    $qq->where('sectors_sec_id', $sectorId)
                        ->whereHas('latestFollowup.followupStatus', fn ($s) => $s->where('status_name', 'جارى المتابعة'));
                });
        })->with('potentialRiskRegister')->get();

        return view('incident_followups.create', [
            'incidents' => $eligibleIncidents,
            'entryTypes' => FollowupEntryType::active()->get(),
            'statuses' => FollowupStatus::active()->get(),
            'canDecide' => $user->can('incident-followups.decide'),
        ]);
    }

    public function store(StoreIncidentFollowupRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $sectorId = $user->department?->sector?->sec_id;

        abort_unless($sectorId, 403, 'لا يوجد قطاع/إدارة مرتبطة بحسابك.');

        $data = $request->validated();

        $responsibility = IncidentSectorResponsibility::firstOrCreate([
            'incident_id' => $data['incident_id'],
            'sectors_sec_id' => $sectorId,
        ]);

        IncidentFollowup::create([
            'incident_sectors_responsibilities_id' => $responsibility->id,
            'followup_status_id' => $data['followup_status_id'],
            'followup_entry_type_id' => $data['followup_entry_type_id'],
            'followup_date' => $data['followup_date'],
            'entry_text' => $data['entry_text'],
        ]);

        return redirect()->route('incidents.show', $data['incident_id'])
            ->with('success', 'تم تسجيل متابعة الحدث بنجاح.');
    }
}
