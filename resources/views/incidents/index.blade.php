@extends('layouts.app')

@section('title', 'الأحداث التشغيلية')
@section('page-title', 'الأحداث التشغيلية')
@section('breadcrumbs')
  <li class="breadcrumb-item active">الأحداث التشغيلية</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">

    <div class="d-flex justify-content-end mb-2">
      @can('create-incidents')
        <a href="{{ route('incidents.create') }}" class="btn btn-primary">
          <i class="bi bi-plus-lg"></i> تسجيل حدث جديد
        </a>
      @endcan
    </div>

    @if($incidents->isEmpty())
      <p class="text-center text-muted py-2">لا توجد أحداث مسجلة بعد.</p>
    @else
      <div class="table-responsive">
        <table class="table table-striped datatable">
          <thead>
            <tr>
              <th>الخطر المحتمل المرتبط</th>
              <th>الإدارة</th>
              <th>تاريخ الاكتشاف</th>
              <th>التكرار</th>
              <th>الأثر</th>
              <th>درجة الخطر</th>
              <th>الحالة</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($incidents as $incident)
              <tr>
                <td>{{ \Illuminate\Support\Str::limit($incident->potentialRiskRegister?->risk_description, 40) }}</td>
                <td>{{ $incident->department?->depname_ar }}</td>
                <td>{{ $incident->discovery_date?->format('Y-m-d') }}</td>
                <td>{{ $incident->frequency_score }}</td>
                <td>{{ $incident->impact_score }}</td>
                <td class="risk-degree-cell">@riskDegreeBadge($incident->risk_degree)</td>
                <td>{{ $incident->resolutionStatus?->status_name ?? '—' }}</td>
                <td><a href="{{ route('incidents.show', $incident) }}" class="btn btn-sm btn-outline-primary">عرض</a></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
@endsection
