<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IndicatorFollowup;
use App\Models\PotentialRiskRegister;
use App\Models\ThresholdLevel;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $incidents = Incident::active()->get(['risk_degree', 'resolution_status_id']);

        $degreeBuckets = [
            'مقبول (أقل من 8)' => $incidents->where('risk_degree', '<', 8)->count(),
            'متوسط (8 إلى 14)' => $incidents->whereBetween('risk_degree', [8, 14])->count(),
            'مرتفع (15 فأكثر)' => $incidents->where('risk_degree', '>=', 15)->count(),
        ];

        $statusBreakdown = Incident::active()
            ->join('resolution_statuses', 'incidents.resolution_status_id', '=', 'resolution_statuses.id')
            ->selectRaw('resolution_statuses.status_name as status_name, count(*) as total')
            ->groupBy('resolution_statuses.status_name')
            ->pluck('total', 'status_name');

        $topRisks = PotentialRiskRegister::active()
            ->withCount(['incidents', 'sectorDetails'])
            ->orderByDesc('incidents_count')
            ->limit(10)
            ->with('eventDetail.eventSubcategory.event.eventType')
            ->get();

        $breachedIndicators = IndicatorFollowup::whereHas('thresholdLevel', fn ($q) => $q->where('level_name', '!=', ThresholdLevel::ACCEPTABLE))
            ->with(['indicator', 'thresholdLevel'])
            ->latest('measurement_date')
            ->limit(15)
            ->get();

        return view('reports.index', compact('degreeBuckets', 'statusBreakdown', 'topRisks', 'breachedIndicators'));
    }
}
