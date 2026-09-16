@extends('layouts.app')

@section('title', 'تفاصيل الحدث')
@section('page-title', 'تفاصيل الحدث')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('incidents.index') }}">الأحداث التشغيلية</a></li>
  <li class="breadcrumb-item active">تفاصيل</li>
@endsection

@section('content')
<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">{{ $incident->potentialRiskRegister?->classification_label }}</h5>
        <p>{{ $incident->potentialRiskRegister?->risk_description }}</p>

        <div class="row">
          <div class="col-md-4"><strong>الإدارة:</strong> {{ $incident->department?->depname_ar }}</div>
          <div class="col-md-4"><strong>تاريخ الاكتشاف:</strong> {{ $incident->discovery_date?->format('Y-m-d') }}</div>
          <div class="col-md-4">
            <strong>حالة الحدث:</strong> {{ $incident->resolutionStatus?->status_name ?? '—' }}
            @can('edit-incidents')
              <form method="POST" action="{{ route('incidents.status.update', $incident) }}" class="d-flex gap-2 mt-1">
                @csrf
                <select name="resolution_status_id" class="form-select form-select-sm" style="max-width: 200px;">
                  <option value="">-- تحديد الحالة --</option>
                  @foreach($resolutionStatuses as $status)
                    <option value="{{ $status->id }}" @selected($incident->resolution_status_id === $status->id)>{{ $status->status_name }}</option>
                  @endforeach
                </select>
                <button class="btn btn-sm btn-outline-primary">حفظ</button>
              </form>
            @endcan
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-md-4"><strong>عدد مرات التكرار:</strong> {{ $incident->frequency_score }}</div>
          <div class="col-md-4"><strong>درجة الأثر:</strong> {{ $incident->impact_score }}</div>
          <div class="col-md-4"><strong>درجة الخطر:</strong> @riskDegreeBadge($incident->risk_degree)</div>
        </div>

        @if($incident->description)
          <p class="mt-3"><strong>وصف الحدث:</strong><br>{{ $incident->description }}</p>
        @endif
        @if($incident->current_procedure)
          <p><strong>الإجراء الحالي:</strong><br>{{ $incident->current_procedure }}</p>
        @endif
        @if($incident->proposed_procedure)
          <p><strong>الإجراء المقترح:</strong><br>{{ $incident->proposed_procedure }}</p>
        @endif
        @if($incident->actual_impact_problem)
          <p><strong>الأثر الفعلي للمشكلة:</strong><br>{{ $incident->actual_impact_problem }}</p>
        @endif
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">القطاعات المسؤولة عن المتابعة</h5>
        <ul class="list-group list-group-flush">
          @foreach($incident->sectorResponsibilities as $resp)
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
              {{ $resp->sector?->sector_ar }}
              <span class="badge bg-light text-dark">{{ $resp->followups->count() }} متابعة</span>
            </li>
          @endforeach
        </ul>
        @can('view-incident-followups')
          <a href="{{ route('incident-followups.index', ['incident' => $incident->id]) }}" class="btn btn-sm btn-outline-primary mt-2">
            عرض المتابعات
          </a>
        @endcan
      </div>
    </div>
  </div>
</div>
@endsection
