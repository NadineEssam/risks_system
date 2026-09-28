@extends('layouts.app')

@php
  $isEdit = isset($followup) && $followup;
  $fmt = fn ($v) => $v === null ? '' : rtrim(rtrim((string) $v, '0'), '.');
@endphp

@section('title', $isEdit ? 'تعديل قياس' : 'إضافة قياس')
@section('page-title', $isEdit ? 'تعديل قياس' : 'إضافة قياس')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('indicators.index') }}">مؤشرات قياس المخاطر</a></li>
  <li class="breadcrumb-item"><a href="{{ route('indicator-followups.index', $indicator) }}">قياسات المؤشر #{{ $indicator->id }}</a></li>
  <li class="breadcrumb-item active">{{ $isEdit ? 'تعديل' : 'إضافة' }}</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body pt-3">
    <h5 class="card-title">
      <i class="bx {{ $isEdit ? 'bx-edit-alt' : 'bx-plus' }} text-primary"></i>
      {{ $isEdit ? 'تعديل قياس' : 'إضافة قياس' }} — المؤشر #{{ $indicator->id }}
    </h5>

    <div class="p-3 bg-light border rounded mb-4 small">
      <strong>المؤشر:</strong> {{ $indicator->indicator_name }}<br>
      <strong>الطبيعة:</strong> {{ $indicator->nature?->nature_name ?? '—' }}
      — <strong>الحد المتوسط:</strong> {{ $fmt($calc['medium']) ?: '—' }}
      — <strong>الحد المرتفع:</strong> {{ $fmt($calc['high']) ?: '—' }}
      @if($calc['medium'] === null || $calc['high'] === null)
        <div class="text-danger mt-2"><i class="bx bx-error"></i> حدود المؤشر غير مكتملة — لن يمكن حساب مستوى حد الخطر.</div>
      @endif
    </div>

    @if($errors->any())
      <div class="alert alert-danger">
        <strong><i class="bx bx-error"></i> يرجى تصحيح الأخطاء التالية:</strong>
        <ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif

    <form method="POST"
          action="{{ $isEdit ? route('indicator-followups.update', $followup) : route('indicator-followups.store', $indicator) }}">
      @csrf
      @if($isEdit) @method('PUT') @endif

      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">تاريخ القياس <span class="text-danger">*</span></label>
          <input type="date" name="measurement_date" max="{{ now()->toDateString() }}"
                 class="form-control @error('measurement_date') is-invalid @enderror"
                 value="{{ old('measurement_date', $followup?->measurement_date?->format('Y-m-d') ?? now()->toDateString()) }}">
          @error('measurement_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-4">
          <label class="form-label">القيمة الفعلية <span class="text-danger">*</span></label>
          <input type="number" step="any" name="actual_value" id="actual_value"
                 class="form-control @error('actual_value') is-invalid @enderror"
                 value="{{ old('actual_value', $fmt($followup?->actual_value)) }}">
          @error('actual_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- المستوى بيتحسب تلقائياً (للعرض بس — السيرفر بيحسبه تاني عند الحفظ) --}}
        <div class="col-md-4">
          <label class="form-label">مستوى حد الخطر <small class="text-muted">(يُحسب تلقائياً)</small></label>
          <div id="levelPreview" class="form-control bg-light d-flex align-items-center" style="min-height:38px;">
            <span class="text-muted small">أدخل القيمة الفعلية</span>
          </div>
        </div>

        <div class="col-md-6">
          <label class="form-label">أسباب التغيّر <span class="text-danger reason-star d-none">*</span></label>
          <textarea name="change_reason" rows="4" class="form-control @error('change_reason') is-invalid @enderror">{{ old('change_reason', $followup?->change_reason) }}</textarea>
          @error('change_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
          <label class="form-label">الإجراء المتخذ <span class="text-danger reason-star d-none">*</span></label>
          <textarea name="action_taken" rows="4" class="form-control @error('action_taken') is-invalid @enderror">{{ old('action_taken', $followup?->action_taken) }}</textarea>
          @error('action_taken')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-12">
          <label class="form-label">ملاحظات</label>
          <textarea name="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $followup?->notes) }}</textarea>
          @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button class="btn btn-primary"><i class="bx bx-save"></i> {{ $isEdit ? 'حفظ التعديل' : 'حفظ القياس' }}</button>
        <a href="{{ route('indicator-followups.index', $indicator) }}" class="btn btn-outline-secondary"><i class="bx bx-arrow-back"></i> رجوع</a>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // نفس قاعدة السيرفر (Indicator::calculateThresholdLevel):
  // متزايد: < متوسط = مقبول | ≥ متوسط و < مرتفع = متوسط | ≥ مرتفع = مرتفع
  // متناقص: > متوسط = مقبول | ≤ متوسط و > مرتفع = متوسط | ≤ مرتفع = مرتفع
  const calc = @json($calc);
  const colors = { 1: '#28a745', 2: '#f0ad4e', 3: '#dc3545' };

  const valueInput = document.getElementById('actual_value');
  const preview    = document.getElementById('levelPreview');
  const stars      = document.querySelectorAll('.reason-star');

  function levelOrder(raw) {
    if (raw === '' || isNaN(raw) || calc.medium === null || calc.high === null) return null;
    const v = parseFloat(raw);
    return calc.decreasing
      ? (v <= calc.high ? 3 : (v <= calc.medium ? 2 : 1))
      : (v >= calc.high ? 3 : (v >= calc.medium ? 2 : 1));
  }

  function refreshLevel() {
    const order = levelOrder(valueInput.value);
    const level = order ? calc.levels[order] : null;

    preview.innerHTML = level
      ? `<span class="badge" style="background:${colors[order]};color:#fff;font-size:13px;padding:6px 12px;">${level.name}</span>`
      : '<span class="text-muted small">أدخل القيمة الفعلية</span>';

    // غير "مقبول" → أسباب التغيّر والإجراء المتخذ إلزاميين
    stars.forEach((s) => s.classList.toggle('d-none', ! order || order === 1));
  }

  valueInput.addEventListener('input', refreshLevel);
  refreshLevel();
</script>
@endpush