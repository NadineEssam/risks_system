@extends('layouts.app')

@section('title', 'إضافة مؤشر جديد')
@section('page-title', 'إضافة مؤشر قياس مخاطر جديد')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('indicators.index') }}">مؤشرات قياس المخاطر</a></li>
  <li class="breadcrumb-item active">إضافة جديد</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('indicators.store') }}" data-wizard>
      @csrf

      <div class="wizard-step" data-step-title="الخطر المحتمل المرتبط">
        <div class="row g-3">
          <div class="col-12">
            <select name="potential_risk_register_id" class="form-select @error('potential_risk_register_id') is-invalid @enderror" required>
              <option value="">-- اختر الخطر المحتمل --</option>
              @foreach($risks as $risk)
                <option value="{{ $risk->id }}" @selected(old('potential_risk_register_id', $selectedRiskId) == $risk->id)>
                  {{ $risk->classification_label }} — {{ \Illuminate\Support\Str::limit($risk->risk_description, 60) }}
                </option>
              @endforeach
            </select>
            @error('potential_risk_register_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>

      <div class="wizard-step" data-step-title="خصائص المؤشر">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">اسم / وصف المؤشر <span class="required-mark">*</span></label>
            <textarea name="indicator_name" rows="2" class="form-control @error('indicator_name') is-invalid @enderror" required>{{ old('indicator_name') }}</textarea>
            @error('indicator_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label class="form-label">طبيعة المؤشر <span class="required-mark">*</span></label>
            <select name="indicator_nature_id" class="form-select" required>
              @foreach($natures as $n)<option value="{{ $n->id }}">{{ $n->nature_name }}</option>@endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">وحدة القياس <span class="required-mark">*</span></label>
            <select name="measurement_unit_id" class="form-select" required>
              @foreach($units as $u)<option value="{{ $u->id }}">{{ $u->unit_name }}</option>@endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">دورية الإبلاغ <span class="required-mark">*</span></label>
            <select name="reporting_frequency_id" class="form-select" required>
              @foreach($frequencies as $f)<option value="{{ $f->id }}">{{ $f->frequency_name }}</option>@endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">وحدة النشاط <span class="required-mark">*</span></label>
            <select name="activity_unit_id" class="form-select" required>
              @foreach($activityUnits as $a)<option value="{{ $a->id }}">{{ $a->unit_name }}</option>@endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">الإجراءات ذات الصلة</label>
            <textarea name="related_actions" rows="2" class="form-control">{{ old('related_actions') }}</textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">مصادر البيانات</label>
            <textarea name="data_sources" rows="2" class="form-control">{{ old('data_sources') }}</textarea>
          </div>
        </div>
      </div>

      <div class="wizard-step" data-step-title="مستويات حدود المؤشر">
        <div class="row g-3">
          @foreach($thresholdLevels as $i => $level)
            <input type="hidden" name="thresholds[{{ $i }}][threshold_level_id]" value="{{ $level->id }}">
            <div class="col-md-4">
              <div class="border rounded p-3 h-100">
                <label class="form-label fw-bold">{{ $level->level_name }}</label>
                <input type="number" step="0.0001" name="thresholds[{{ $i }}][threshold_value]" class="form-control mb-2" placeholder="القيمة الحدية" required>
                <textarea name="thresholds[{{ $i }}][required_action]" rows="2" class="form-control" placeholder="الإجراء عند تجاوز هذا المستوى (إلزامي للمستوى المتوسط والمرتفع)"></textarea>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <div class="wizard-step" data-step-title="مسئولو المؤشر">
        <div id="responsibles-wrapper">
          <div class="row g-3 mb-2 responsible-row">
            <div class="col-md-3"><input type="text" name="responsibles[0][full_name]" class="form-control" placeholder="الاسم الكامل" required></div>
            <div class="col-md-3"><input type="text" name="responsibles[0][job_title]" class="form-control" placeholder="المسمى الوظيفي"></div>
            <div class="col-md-3"><input type="email" name="responsibles[0][email]" class="form-control" placeholder="البريد الإلكتروني"></div>
            <div class="col-md-3">
              <select name="responsibles[0][responsible_role_id]" class="form-select" required>
                @foreach($roles as $r)<option value="{{ $r->id }}">{{ $r->role_name }}</option>@endforeach
              </select>
            </div>
          </div>
        </div>
        <button type="button" id="add-responsible" class="btn btn-sm btn-outline-primary">
          <i class="bi bi-plus"></i> إضافة مسئول آخر
        </button>
      </div>

      <div class="wizard-controls d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
        <div>
          <button type="button" class="btn btn-outline-secondary wizard-prev d-none"><i class="bi bi-arrow-right"></i> السابق</button>
          <a href="{{ route('indicators.index') }}" class="btn btn-link text-muted">إلغاء</a>
        </div>
        <div>
          <button type="button" class="btn btn-primary wizard-next">التالي <i class="bi bi-arrow-left"></i></button>
          <button type="submit" class="btn btn-success wizard-submit d-none">حفظ المؤشر</button>
        </div>
      </div>
    </form>
  </div>
</div>

@push('scripts')
  {{ $dataTable->scripts() }}
<script>
  let responsibleIndex = 1;
  document.getElementById('add-responsible').addEventListener('click', function () {
    const wrapper = document.getElementById('responsibles-wrapper');
    const row = wrapper.querySelector('.responsible-row').cloneNode(true);
    row.querySelectorAll('input, select').forEach(el => {
      el.name = el.name.replace(/\[\d+\]/, `[${responsibleIndex}]`);
      if (el.tagName === 'INPUT') el.value = '';
    });
    wrapper.appendChild(row);
    responsibleIndex++;
  });
</script>
@endpush
@endsection
