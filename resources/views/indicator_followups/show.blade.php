@extends('layouts.app')

@php $fmt = fn ($v) => $v === null ? '—' : rtrim(rtrim((string) $v, '0'), '.'); @endphp

@section('title', 'عرض قياس')
@section('page-title', 'عرض قياس')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('indicators.index') }}">مؤشرات قياس المخاطر</a></li>
  <li class="breadcrumb-item"><a href="{{ route('indicator-followups.index', $indicator) }}">قياسات المؤشر #{{ $indicator->id }}</a></li>
  <li class="breadcrumb-item active">قياس #{{ $followup->id }}</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body pt-3">
    <h5 class="card-title"><i class="bx bx-show text-primary"></i> تفاصيل القياس #{{ $followup->id }}</h5>

    <div class="p-3 bg-light border rounded mb-3 small">
      <strong>المؤشر:</strong> {{ $indicator->indicator_name }}
      — <strong>الطبيعة:</strong> {{ $indicator->nature?->nature_name ?? '—' }}
    </div>

    <div class="row g-3">
      <div class="col-md-4"><strong>تاريخ القياس:</strong><br>{{ $followup->measurement_date?->format('Y-m-d') ?? '—' }}</div>
      <div class="col-md-4"><strong>القيمة الفعلية:</strong><br>{{ $fmt($followup->actual_value) }} {{ $indicator->measurementUnit?->unit_name }}</div>
      <div class="col-md-4"><strong>مستوى حد الخطر:</strong><br>{!! $followup->thresholdLevel?->badge() ?? '—' !!}</div>

      <div class="col-md-6">
        <strong>أسباب التغيّر:</strong>
        <div class="p-3 bg-light border rounded mt-2" style="white-space:pre-wrap;">{{ $followup->change_reason ?: '—' }}</div>
      </div>
      <div class="col-md-6">
        <strong>الإجراء المتخذ:</strong>
        <div class="p-3 bg-light border rounded mt-2" style="white-space:pre-wrap;">{{ $followup->action_taken ?: '—' }}</div>
      </div>
      @if($followup->notes)
        <div class="col-12">
          <strong>ملاحظات:</strong>
          <div class="p-3 bg-light border rounded mt-2" style="white-space:pre-wrap;">{{ $followup->notes }}</div>
        </div>
      @endif

      <div class="col-md-6 small text-muted"><i class="bx bx-user"></i> بواسطة: {{ $followup->created_by ?? '—' }}</div>
      @if($followup->updated_by)
        <div class="col-md-6 small text-muted"><i class="bx bx-edit"></i> آخر تعديل: {{ $followup->updated_by }}</div>
      @endif
    </div>

    <a href="{{ route('indicator-followups.index', $indicator) }}" class="btn btn-outline-secondary mt-4">
      <i class="bx bx-arrow-back"></i> رجوع لسجل القياسات
    </a>
  </div>
</div>
@endsection