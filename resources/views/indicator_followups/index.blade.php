@extends('layouts.app')

@section('title', 'سجل قياسات المؤشر')
@section('page-title', 'سجل قياسات المؤشر')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('indicators.index') }}">مؤشرات قياس المخاطر</a></li>
  <li class="breadcrumb-item active">قياسات المؤشر #{{ $indicator->id }}</li>
@endsection

@php
  $fmt = fn ($v) => $v === null ? '—' : rtrim(rtrim((string) $v, '0'), '.');
@endphp

@section('content')
<div class="card">
  <div class="card-body pt-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="card-title m-0 p-0">
        <i class="bx bx-line-chart text-primary"></i>
        سجل قياسات المؤشر #{{ $indicator->id }}
      </h5>
      @if(PerUser('indicator-followups.create'))
        <a href="{{ route('indicator-followups.create', $indicator) }}" class="btn btn-primary">
          <i class="bx bx-plus"></i> إضافة قياس
        </a>
      @endif
    </div>

    {{-- بيانات المؤشر + الحدود + آخر مستوى --}}
    <div class="card border mb-4">
      <div class="card-body pt-3">
        <div class="row align-items-start">
          <div class="col-md-7 mb-3">
            <label class="fw-bold text-muted mb-2 d-block"><i class="bx bx-bar-chart-alt-2"></i> اسم المؤشر</label>
            <div class="p-3 bg-light border rounded mb-3" style="white-space:pre-wrap;word-break:break-word;">{{ $indicator->indicator_name }}</div>

            <label class="fw-bold text-muted mb-2 d-block"><i class="bx bx-error-circle"></i> الخطر المرتبط</label>
            <div class="p-3 bg-light border rounded" style="white-space:pre-wrap;word-break:break-word;">{{ $indicator->potentialRiskRegister?->risk_description ?? '—' }}</div>
          </div>

          <div class="col-md-5 mb-3">
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="fw-bold text-muted d-block">طبيعة المؤشر</label>
                <span class="badge bg-secondary">{{ $indicator->nature?->nature_name ?? '—' }}</span>
              </div>
              <div class="col-6">
                <label class="fw-bold text-muted d-block">وحدة القياس</label>
                {{ $indicator->measurementUnit?->unit_name ?? '—' }}
              </div>
            </div>

            <label class="fw-bold text-muted mb-2 d-block"><i class="bx bx-slider-alt"></i> حدود المؤشر</label>
            <table class="table table-sm table-bordered text-center mb-3">
              <tbody>
                @forelse($indicator->thresholdDetails->sortBy(fn ($d) => $d->thresholdLevel?->sort_order) as $detail)
                  <tr>
                    <td>{!! $detail->thresholdLevel?->badge() !!}</td>
                    <td class="fw-bold">{{ $fmt($detail->threshold_value) }}</td>
                  </tr>
                @empty
                  <tr><td class="text-danger">لم يتم تحديد حدود لهذا المؤشر</td></tr>
                @endforelse
              </tbody>
            </table>

            <label class="fw-bold text-muted mb-2 d-block"><i class="bx bx-info-circle"></i> آخر مستوى</label>
            {!! $lastFollowup?->thresholdLevel?->badge() ?? \App\Models\ThresholdLevel::emptyBadge() !!}
            @if($lastFollowup)
              <span class="small text-muted ms-2">({{ $lastFollowup->measurement_date?->format('Y-m-d') }} — القيمة {{ $fmt($lastFollowup->actual_value) }})</span>
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