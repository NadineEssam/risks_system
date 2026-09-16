@extends('layouts.app')

@section('title', 'تسجيل متابعة مؤشر')
@section('page-title', 'تسجيل متابعة مؤشر')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('indicator-followups.index') }}">متابعة المؤشرات</a></li>
  <li class="breadcrumb-item active">تسجيل جديد</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">

    @if($indicators->isEmpty())
      <div class="alert alert-warning mb-0">
        لا توجد مؤشرات مفعّلة مرتبطة بقطاعك الإداري حالياً.
      </div>
    @else
      <form method="POST" action="{{ route('indicator-followups.store') }}" data-wizard>
        @csrf

        <div class="wizard-step" data-step-title="المؤشر">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">المؤشر <span class="required-mark">*</span></label>
              <select name="indicators_id" class="form-select @error('indicators_id') is-invalid @enderror" required>
                <option value="">-- اختر المؤشر --</option>
                @foreach($indicators as $indicator)
                  <option value="{{ $indicator->id }}" @selected(old('indicators_id') == $indicator->id)>
                    {{ \Illuminate\Support\Str::limit($indicator->indicator_name, 70) }}
                  </option>
                @endforeach
              </select>
              @error('indicators_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>

        <div class="wizard-step" data-step-title="بيانات القياس">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">تاريخ القياس <span class="required-mark">*</span></label>
              <input type="date" name="measurement_date" value="{{ old('measurement_date', now()->format('Y-m-d')) }}" class="form-control @error('measurement_date') is-invalid @enderror" required>
              @error('measurement_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
              <label class="form-label">القيمة الفعلية <span class="required-mark">*</span></label>
              <input type="number" step="0.0001" name="actual_value" value="{{ old('actual_value') }}" class="form-control @error('actual_value') is-invalid @enderror" required>
              @error('actual_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
              <label class="form-label">مستوى حد المؤشر <span class="required-mark">*</span></label>
              <select name="threshold_level_id" id="threshold_level_id" class="form-select @error('threshold_level_id') is-invalid @enderror" required>
                <option value="">-- اختر --</option>
                @foreach($thresholdLevels as $level)
                  <option value="{{ $level->id }}" data-acceptable="{{ $level->isAcceptable() ? '1' : '0' }}" @selected(old('threshold_level_id') == $level->id)>
                    {{ $level->level_name }}
                  </option>
                @endforeach
              </select>
              @error('threshold_level_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>

        <div class="wizard-step" data-step-title="أسباب التغيّر والإجراء">
          <div class="alert alert-info small" id="acceptable-hint">
            أسباب التغيّر والإجراء المتخذ إلزاميان فقط إذا كان مستوى الحد المختار غير "مقبول".
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">أسباب التغيّر <span class="required-mark conditional-required" style="display:none">*</span></label>
              <textarea name="change_reason" id="change_reason" rows="3" class="form-control @error('change_reason') is-invalid @enderror">{{ old('change_reason') }}</textarea>
              @error('change_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">الإجراء المتخذ <span class="required-mark conditional-required" style="display:none">*</span></label>
              <textarea name="action_taken" id="action_taken" rows="3" class="form-control @error('action_taken') is-invalid @enderror">{{ old('action_taken') }}</textarea>
              @error('action_taken')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
              <label class="form-label">ملاحظات</label>
              <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
            </div>
          </div>
        </div>

        <div class="wizard-controls d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
          <div>
            <button type="button" class="btn btn-outline-secondary wizard-prev d-none"><i class="bi bi-arrow-right"></i> السابق</button>
            <a href="{{ route('indicator-followups.index') }}" class="btn btn-link text-muted">إلغاء</a>
          </div>
          <div>
            <button type="button" class="btn btn-primary wizard-next">التالي <i class="bi bi-arrow-left"></i></button>
            <button type="submit" class="btn btn-success wizard-submit d-none">حفظ المتابعة</button>
          </div>
        </div>
      </form>
    @endif

  </div>
</div>

@push('scripts')
<script>
  const levelSelect = document.getElementById('threshold_level_id');
  const requiredMarks = document.querySelectorAll('.conditional-required');
  const changeReason = document.getElementById('change_reason');
  const actionTaken = document.getElementById('action_taken');

  function toggleRequirement() {
    const selected = levelSelect.options[levelSelect.selectedIndex];
    const acceptable = selected ? selected.dataset.acceptable === '1' : false;
    const isRequired = selected && selected.value && !acceptable;

    requiredMarks.forEach(m => m.style.display = isRequired ? 'inline' : 'none');
    changeReason.required = isRequired;
    actionTaken.required = isRequired;
  }

  levelSelect.addEventListener('change', toggleRequirement);
  toggleRequirement();
</script>
@endpush
@endsection
