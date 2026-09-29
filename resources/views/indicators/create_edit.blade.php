@extends('layouts.app')

@php
  $isEdit = isset($indicator) && $indicator;
  $fmt = fn ($v) => $v === null || $v === '' ? '' : rtrim(rtrim((string) $v, '0'), '.');
  $thresholdValues = $isEdit ? $indicator->thresholdDetails->keyBy('threshold_level_id') : collect();
  $responsibleRows = old('responsibles', $isEdit
      ? $indicator->responsibles->map->only(['full_name', 'job_title', 'email', 'responsible_role_id'])->values()->all()
      : []);
  if (empty($responsibleRows)) { $responsibleRows = [[]]; }
@endphp

@section('title', $isEdit ? 'تعديل مؤشر' : 'إضافة مؤشر جديد')
@section('page-title', $isEdit ? 'تعديل مؤشر قياس مخاطر' : 'إضافة مؤشر قياس مخاطر جديد')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('indicators.index') }}">مؤشرات قياس المخاطر</a></li>
  <li class="breadcrumb-item active">{{ $isEdit ? 'تعديل #'.$indicator->id : 'إضافة جديد' }}</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ $isEdit ? route('indicators.update', $indicator) : route('indicators.store') }}" data-wizard>
      @csrf
      @if($isEdit) @method('PUT') @endif

      {{-- 1 الخطر المحتمل المرتبط --}}
      <div class="wizard-step" data-step-title="الخطر المحتمل المرتبط">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">الخطر المحتمل <span class="required-mark">*</span></label>
            <select name="potential_risk_register_id" class="form-select @error('potential_risk_register_id') is-invalid @enderror" required>
              <option value="">-- اختر الخطر المحتمل --</option>
              @foreach($risks as $risk)
                <option value="{{ $risk->id }}"
                    title="{{ $risk->classification_label }} — {{ $risk->risk_description }}"
                    @selected(old('potential_risk_register_id') == $risk->id)>
                    {{ \Illuminate\Support\Str::limit($risk->risk_description, 70) }}@if($risk->eventDetail) ({{ \Illuminate\Support\Str::limit($risk->eventDetail->detail_name, 35) }})@endif
                </option>
              @endforeach
            </select>
            @error('potential_risk_register_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>

      {{-- 2 خصائص المؤشر --}}
      <div class="wizard-step" data-step-title="خصائص المؤشر">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">اسم / وصف المؤشر <span class="required-mark">*</span></label>
            <textarea name="indicator_name" rows="2" class="form-control @error('indicator_name') is-invalid @enderror" required>{{ old('indicator_name', $indicator?->indicator_name) }}</textarea>
            @error('indicator_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          @foreach([
            ['indicator_nature_id',    'طبيعة المؤشر', $natures,       'nature_name'],
            ['measurement_unit_id',    'وحدة القياس',  $units,         'unit_name'],
            ['reporting_frequency_id', 'دورية الإبلاغ', $frequencies,   'frequency_name'],
            ['activity_unit_id',       'وحدة النشاط',  $activityUnits, 'unit_name'],
          ] as [$field, $label, $options, $column])
            <div class="col-md-3">
              <label class="form-label">{{ $label }} <span class="required-mark">*</span></label>
              <select name="{{ $field }}" class="form-select @error($field) is-invalid @enderror" required>
                <option value="">-- اختر --</option>
                @foreach($options as $option)
                  <option value="{{ $option->id }}" @selected(old($field, $indicator?->$field) == $option->id)>{{ $option->$column }}</option>
                @endforeach
              </select>
              @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          @endforeach

          <div class="col-md-6">
            <label class="form-label">الإجراءات ذات الصلة</label>
            <textarea name="related_actions" rows="2" class="form-control">{{ old('related_actions', $indicator?->related_actions) }}</textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">مصادر البيانات</label>
            <textarea name="data_sources" rows="2" class="form-control">{{ old('data_sources', $indicator?->data_sources) }}</textarea>
          </div>
        </div>
      </div>

      {{-- 3 مستويات حدود المؤشر --}}
      <div class="wizard-step" data-step-title="مستويات حدود المؤشر">
        <div class="alert alert-info small mb-3">
          <i class="bx bx-info-circle"></i>
          <strong>متزايد:</strong> المقبول &lt; المتوسط &lt; المرتفع —
          <strong>متناقص:</strong> المقبول &gt; المتوسط &gt; المرتفع
        </div>
        @error('thresholds')
          <div class="alert alert-danger is-invalid"><i class="bx bx-error"></i> {{ $message }}</div>
        @enderror

        <div class="row g-3">
          @foreach($thresholdLevels as $i => $level)
            <input type="hidden" name="thresholds[{{ $i }}][threshold_level_id]" value="{{ $level->id }}">
            <div class="col-md-4">
              <div class="border rounded p-3 h-100">
                <label class="form-label fw-bold">{!! $level->badge() !!}</label>
                <input type="number" step="any" name="thresholds[{{ $i }}][threshold_value]"
                       class="form-control mb-2 @error("thresholds.$i.threshold_value") is-invalid @enderror"
                       placeholder="القيمة الحدية" required
                       value="{{ old("thresholds.$i.threshold_value", $fmt($thresholdValues[$level->id]->threshold_value ?? null)) }}">
                @error("thresholds.$i.threshold_value")<div class="invalid-feedback">{{ $message }}</div>@enderror
                <textarea name="thresholds[{{ $i }}][required_action]" rows="2" class="form-control"
                          placeholder="الإجراء عند تجاوز هذا المستوى">{{ old("thresholds.$i.required_action", $thresholdValues[$level->id]->required_action ?? '') }}</textarea>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      {{-- 4 مسئولو المؤشر --}}
      <div class="wizard-step" data-step-title="مسئولو المؤشر">
        <div id="responsibles-wrapper">
          @foreach($responsibleRows as $r => $row)
            <div class="row g-3 mb-2 responsible-row">
              <div class="col-md-3">
                <input type="text" name="responsibles[{{ $r }}][full_name]" class="form-control" placeholder="الاسم الكامل" required value="{{ $row['full_name'] ?? '' }}">
              </div>
              <div class="col-md-3">
                <input type="text" name="responsibles[{{ $r }}][job_title]" class="form-control" placeholder="المسمى الوظيفي" value="{{ $row['job_title'] ?? '' }}">
              </div>
              <div class="col-md-3">
                <input type="email" name="responsibles[{{ $r }}][email]" class="form-control" placeholder="البريد الإلكتروني" value="{{ $row['email'] ?? '' }}">
              </div>
              <div class="col-md-2">
                <select name="responsibles[{{ $r }}][responsible_role_id]" class="form-select" required>
                  @foreach($roles as $role)
                    <option value="{{ $role->id }}" @selected(($row['responsible_role_id'] ?? null) == $role->id)>{{ $role->role_name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-1 d-flex align-items-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-responsible {{ $r === 0 ? 'invisible' : '' }}" title="حذف"><i class="bx bx-trash"></i></button>
              </div>
            </div>
          @endforeach
        </div>
        <button type="button" id="add-responsible" class="btn btn-sm btn-outline-primary">
          <i class="bx bx-plus"></i> إضافة مسئول آخر
        </button>
      </div>

      <div class="wizard-controls d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
        <div>
          <button type="button" class="btn btn-outline-secondary wizard-prev d-none"><i class="bi bi-arrow-right"></i> السابق</button>
          <a href="{{ $isEdit ? route('indicators.show', $indicator) : route('indicators.index') }}" class="btn btn-link text-muted">إلغاء</a>
        </div>
        <div>
          <button type="button" class="btn btn-primary wizard-next">التالي <i class="bi bi-arrow-left"></i></button>
          <button type="submit" class="btn btn-success wizard-submit d-none">{{ $isEdit ? 'حفظ التعديل' : 'حفظ المؤشر' }}</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // إضافة/حذف مسئول
  const wrapper = document.getElementById('responsibles-wrapper');
  let responsibleIndex = wrapper.querySelectorAll('.responsible-row').length;

  document.getElementById('add-responsible').addEventListener('click', function () {
    const row = wrapper.querySelector('.responsible-row').cloneNode(true);
    row.querySelectorAll('input, select').forEach((el) => {
      el.name = el.name.replace(/\[\d+\]/, `[${responsibleIndex}]`);
      el.classList.remove('is-invalid');
      if (el.tagName === 'INPUT') el.value = '';
    });
    row.querySelectorAll('.server-error, .invalid-feedback').forEach((el) => el.remove());
    row.querySelector('.remove-responsible').classList.remove('invisible');
    wrapper.appendChild(row);
    responsibleIndex++;
  });

  wrapper.addEventListener('click', function (e) {
    const btn = e.target.closest('.remove-responsible');
    if (btn && wrapper.querySelectorAll('.responsible-row').length > 1) {
      btn.closest('.responsible-row').remove();
    }
  });
</script>
@endpush