@extends('layouts.app')

@section('title', 'عرض متابعة')
@section('page-title', 'عرض متابعة')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('incidents.index') }}">الأحداث التشغيلية</a></li>
  <li class="breadcrumb-item"><a href="{{ route('incident-followups.index', $incident) }}">متابعات الحدث #{{ $incident->id }}</a></li>
  <li class="breadcrumb-item active">متابعة #{{ $followup->id }}</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body pt-3">
    <h5 class="card-title"><i class="bx bx-show text-primary"></i> تفاصيل المتابعة #{{ $followup->id }}</h5>

    <div class="row g-3">
      <div class="col-md-3"><strong>تاريخ المتابعة:</strong><br>{{ $followup->followup_date?->format('Y-m-d') ?? '—' }}</div>
      <div class="col-md-3"><strong>القطاع:</strong><br>{{ $followup->incidentSectorResponsibility?->sector?->sector_ar ?? '—' }}</div>
      <div class="col-md-3"><strong>نوع الإدخال:</strong><br><span class="badge bg-info">{{ $followup->followupEntryType?->type_name ?? '—' }}</span></div>
      <div class="col-md-3"><strong>الحالة:</strong><br>{!! \App\Support\IncidentAccess::statusBadge($followup->followupStatus?->status_name) !!}</div>

      <div class="col-12">
        <strong>نص المتابعة:</strong>
        <div class="p-3 bg-light border rounded mt-2" style="white-space:pre-wrap;word-break:break-word;">{{ $followup->entry_text }}</div>
      </div>

      <div class="col-md-6 small text-muted">
        <i class="bx bx-user"></i> بواسطة: {{ $followup->created_by ?? '—' }}
        @if($followup->creation_date) — {{ $followup->creation_date->format('Y-m-d H:i') }} @endif
      </div>
      @if($followup->updated_by)
        <div class="col-md-6 small text-muted">
          <i class="bx bx-edit"></i> آخر تعديل: {{ $followup->updated_by }}
          @if($followup->update_date) — {{ $followup->update_date->format('Y-m-d H:i') }} @endif
        </div>
      @endif
    </div>

    <a href="{{ route('incident-followups.index', $incident) }}" class="btn btn-outline-secondary mt-4">
      <i class="bx bx-arrow-back"></i> رجوع لسجل المتابعات
    </a>
  </div>
</div>
@endsection