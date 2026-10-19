@extends('layouts.app')

@section('title', 'متابعات الحدث')
@section('page-title', 'متابعات الحدث')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('incidents.index') }}">الأحداث التشغيلية</a></li>
  <li class="breadcrumb-item active">متابعات الحدث #{{ $incident->id }}</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body pt-3">

    {{-- الهيدر + زرار الإضافة (يختفي لو الحدث مقفول — زي الشكاوى) --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="card-title m-0 p-0">
        <i class="bx bx-message-square-detail text-primary"></i>
        سجل متابعات الحدث #{{ $incident->id }}
      </h5>
      @if(PerUser('incident-followups.create') && ! $isClosed)
        <a href="{{ route('incident-followups.create', $incident) }}" class="btn btn-primary">
          <i class="bx bx-plus"></i> إضافة متابعة
        </a>
      @endif
    </div>

    {{-- بيانات الحدث + آخر حالة --}}
    <div class="card border mb-4">
      <div class="card-body pt-3">
        <div class="row align-items-start">
          <div class="col-md-8 mb-3">
            <label class="fw-bold text-muted mb-2 d-block"><i class="bx bx-error-circle"></i> الخطر المحتمل</label>
            <div class="p-3 bg-light border rounded mb-3" style="white-space:pre-wrap;word-break:break-word;">{{ $incident->potentialRiskRegister?->risk_description ?? '—' }}</div>

            <label class="fw-bold text-muted mb-2 d-block"><i class="bx bx-message-detail"></i> وصف الحدث</label>
            <div class="p-3 bg-light border rounded" style="min-height:70px;max-height:200px;overflow-y:auto;white-space:pre-wrap;word-break:break-word;">{{ $incident->description ?? 'لا يوجد وصف للحدث' }}</div>
          </div>

          <div class="col-md-4 mb-3">
            <label class="fw-bold text-muted mb-2 d-block"><i class="bx bx-buildings"></i> الإدارة</label>
            <p>{{ $incident->department?->depname_ar ?? '—' }}</p>

            <label class="fw-bold text-muted mb-2 d-block"><i class="bx bx-calendar"></i> تاريخ الاكتشاف</label>
            <p>{{ $incident->discovery_date?->format('Y-m-d') ?? '—' }}</p>

            <label class="fw-bold text-muted mb-2 d-block"><i class="bx bx-info-circle"></i> آخر حالة</label>
            {!! \App\Support\IncidentAccess::statusBadge($lastFollowup?->followupStatus?->status_name) !!}
            @if($isClosed)
              <div class="small text-danger mt-2"><i class="bx bx-lock-alt"></i> الحدث مغلق — لا يمكن إضافة أو حذف متابعات.</div>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="table-responsive">
      {{ $dataTable->table(['class' => 'table text-center align-middle datatable-custom', 'style' => 'width:100%']) }}
    </div>

  </div>
</div>
@endsection

@push('scripts')
  {{ $dataTable->scripts() }}
@endpush