<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Indicator;
use App\Models\IndicatorFollowup;
use App\Models\PotentialRiskRegister;
use App\Models\ThresholdLevel;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $stats = [
            'potential_risks' => PotentialRiskRegister::active()->count(),
            'open_incidents' => Incident::active()
                ->whereHas('resolutionStatus', fn ($q) => $q->whereIn('status_name', ['حل جزئي', 'غير مقبول']))
                ->count(),
            'active_indicators' => Indicator::active()->count(),
            'high_risk_incidents' => Incident::active()->where('risk_degree', '>=', 15)->count(),
        ];

        $recentIncidents = Incident::with(['potentialRiskRegister', 'department', 'resolutionStatus'])
            ->latest('creation_date')
            ->limit(8)
            ->get();

        $breachedIndicators = IndicatorFollowup::with(['indicator', 'thresholdLevel'])
            ->whereHas('thresholdLevel', fn ($q) => $q->where('level_name', '!=', ThresholdLevel::ACCEPTABLE))
            ->latest('creation_date')
            ->limit(8)
            ->get();

        return view('dashboard.index', compact('stats', 'recentIncidents', 'breachedIndicators', 'user'));
    }
}
