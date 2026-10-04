<?php

namespace App\Http\Controllers;

use App\Models\FollowupStatus;
use App\Models\Incident;
use App\Models\IndicatorFollowup;
use App\Support\IncidentAccess;
use App\Support\RiskDegreeHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * لوحة التحكم — نفس تصميم ورسوم لوحة نظام الشكاوى ببيانات المخاطر.
 * كل أرقام الأحداث حسب رؤية القطاع (المركزي يشوف الكل).
 */
class DashboardController extends Controller
{
    public const STATUS_ORDER = ['جديد', 'جارى المتابعة', 'تم الحل', 'إغلاق', 'قبول الخطر'];

    public function index(Request $request)
    {
        $from  = $request->input('from');
        $to    = $request->input('to');
        $today = Carbon::today()->toDateString();

        // نفس تحقق الشكاوى
        if (($from && $from > $today) || ($to && $to > $today)) {
            return redirect()->route('dashboard')->with('error', 'لا يمكن اختيار تاريخ أكبر من تاريخ اليوم.');
        }
        if ($from && $to && $from > $to) {
            return redirect()->route('dashboard')->with('error', 'تاريخ البداية لا يمكن أن يكون بعد تاريخ النهاية.');
        }

        // ===== الأحداث المرئية للمستخدم + فلتر تاريخ الاكتشاف =====
        $incidents = IncidentAccess::scopeVisible(Incident::active(), Auth::user())
            ->when($from, fn ($q) => $q->whereDate('discovery_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('discovery_date', '<=', $to))
            ->with([
                'department.sector',
                'resolutionStatus',
                'potentialRiskRegister.eventDetail.eventSubcategory.event.eventType',
            ])
            ->get();

        // ===== آخر حالة متابعة لكل حدث (استعلام واحد لكل 900 حدث — حد Oracle للـ IN) =====
        $statusNames = FollowupStatus::pluck('status_name', 'id');
        $lastStatus  = collect();

        foreach ($incidents->pluck('id')->chunk(900) as $ids) {
            DB::table('incident_followups')
                ->join('incident_sectors_responsibilities', 'incident_sectors_responsibilities.id', '=', 'incident_followups.incident_sectors_responsibilities_id')
                ->whereIn('incident_sectors_responsibilities.incident_id', $ids->all())
                ->orderBy('incident_followups.id')
                ->get(['incident_sectors_responsibilities.incident_id', 'incident_followups.followup_status_id'])
                ->each(function ($row) use ($lastStatus, $statusNames) {
                    $row = (array) $row;
                    $row = array_change_key_case($row, CASE_LOWER);
                    // الترتيب تصاعدي — آخر واحدة بتكسب = آخر متابعة
                    $lastStatus[$row['incident_id']] = $statusNames[$row['followup_status_id']] ?? 'جديد';
                });
        }

        $statusOf = fn ($incident) => $lastStatus[$incident->id] ?? 'جديد';

        // ===== KPIs (زي كروت الشكاوى الخمسة) =====
        $byStatus = collect(self::STATUS_ORDER)->mapWithKeys(fn ($s) => [$s => 0]);
        foreach ($incidents as $incident) {
            $byStatus[$statusOf($incident)] = ($byStatus[$statusOf($incident)] ?? 0) + 1;
        }

        $kpis = [
            'total'      => $incidents->count(),
            'new'        => $byStatus['جديد'],
            'processing' => $byStatus['جارى المتابعة'],
            'solved'     => $byStatus['تم الحل'],
            'closed'     => $byStatus['إغلاق'] + $byStatus['قبول الخطر'],
        ];

        // ===== الرسوم =====
        $count = fn ($grouped) => $grouped->map->count()->sortDesc();

        $byEventType = $count($incidents->groupBy(fn ($i) =>
            $i->potentialRiskRegister?->eventDetail?->eventSubcategory?->event?->eventType?->type_name ?? 'غير مصنّف'));

        $byDegree = collect(['مقبول' => 0, 'متوسط' => 0, 'مرتفع' => 0]);
        foreach ($incidents as $incident) {
            if ($incident->risk_degree !== null) {
                $level = RiskDegreeHelper::classify((int) $incident->risk_degree)[0];
                $byDegree[$level]++;
            }
        }

        $byResolution = $count($incidents->groupBy(fn ($i) => $i->resolutionStatus?->status_name ?? 'غير محدد'));
        $bySector     = $count($incidents->groupBy(fn ($i) => $i->department?->sector?->sector_ar ?? 'غير معروف'));
        $byDepartment = $count($incidents->groupBy(fn ($i) => $i->department?->depname_ar ?? 'غير معروف'))->take(15);

        // الاتجاه الشهري (بالترتيب الزمني)
        $byMonth = $incidents
            ->filter(fn ($i) => $i->discovery_date)
            ->groupBy(fn ($i) => Carbon::parse($i->discovery_date)->format('Y-m'))
            ->map->count()
            ->sortKeys();

        // قياسات المؤشرات حسب المستوى (المؤشرات متاحة للكل)
        $byLevel = IndicatorFollowup::with('thresholdLevel')
            ->when($from, fn ($q) => $q->whereDate('measurement_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('measurement_date', '<=', $to))
            ->get()
            ->groupBy(fn ($f) => $f->thresholdLevel?->level_name ?? 'غير محدد')
            ->map->count();

        return view('dashboard.index', compact(
            'kpis', 'byStatus', 'byEventType', 'byDegree', 'byResolution',
            'bySector', 'byDepartment', 'byMonth', 'byLevel', 'from', 'to'
        ));
    }
}