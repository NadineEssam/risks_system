@extends('layouts.app')

@section('title', 'تفاصيل المؤشر')
@section('page-title', 'تفاصيل المؤشر')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('indicators.index') }}">مؤشرات قياس المخاطر</a></li>
  <li class="breadcrumb-item active">تفاصيل</li>
@endsection

@section('content')
<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="card-title mb-0">{{ $indicator->indicator_name }}</h5>
          @if($indicator->validity)
            <span class="badge bg-success">مفعل</span>
          @else
            <span class="badge bg-secondary">غير مفعل</span>
          @endif
        </div>

        <p class="text-muted">مرتبط بالخطر: {{ $indicator->potentialRiskRegister?->risk_description }}</p>

        <div class="row">
          <div class="col-md-3"><strong>الطبيعة:</strong> {{ $indicator->nature?->nature_name }}</div>
          <div class="col-md-3"><strong>وحدة القياس:</strong> {{ $indicator->measurementUnit?->unit_name }}</div>
          <div class="col-md-3"><strong>دورية الإبلاغ:</strong> {{ $indicator->reportingFrequency?->frequency_name }}</div>
          <div class="col-md-3"><strong>وحدة النشاط:</strong> {{ $indicator->activityUnit?->unit_name }}</div>
        </div>

        @if($indicator->related_actions)
          <p class="mt-3"><strong>الإجراءات ذات الصلة:</strong><br>{{ $indicator->related_actions }}</p>
        @endif
        @if($indicator->data_sources)
          <p><strong>مصادر البيانات:</strong><br>{{ $indicator->data_sources }}</p>
        @endif
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">مستويات الحدود</h5>
        <table class="table table-sm">
          <thead><tr><th>المستوى</th><th>القيمة الحدية</th><th>الإجراء عند التجاوز</th></tr></thead>
          <tbody>
            @foreach($indicator->thresholdDetails as $threshold)
              <tr>
                <td>{{ $threshold->thresholdLevel?->level_name }}</td>
                <td>{{ $threshold->threshold_value }}</td>
                <td>{{ $threshold->required_action ?? '—' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">سجل المتابعة ({{ $indicator->followups->count() }})</h5>
        <table class="table table-sm">
          <thead><tr><th>تاريخ القياس</th><th>القيمة الفعلية</th><th>المستوى</th></tr></thead>
          <tbody>
            @forelse($indicator->followups->sortByDesc('measurement_date') as $followup)
              <tr>
                <td>{{ $followup->measurement_date?->format('Y-m-d') }}</td>
                <td>{{ $followup->actual_value }}</td>
                <td>{{ $followup->thresholdLevel?->level_name }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="text-muted text-center py-2">لا توجد متابعات مسجلة بعد.</td></tr>
            @endforelse
          </tbody>
        </table>
        @can('create-indicator-followups')
          <a href="{{ route('indicator-followups.create') }}" class="btn btn-sm btn-outline-primary">تسجيل متابعة جديدة</a>
        @endcan
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">مسئولو المؤشر</h5>
        <ul class="list-group list-group-flush">
          @foreach($indicator->responsibles as $responsible)
            <li class="list-group-item px-0">
              <strong>{{ $responsible->full_name }}</strong><br>
              <span class="small text-muted">{{ $responsible->role?->role_name }} — {{ $responsible->job_title }}</span>
            </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection
