@extends('layouts.app')

@section('title', 'تسجيل حدث جديد')
@section('page-title', 'تسجيل حدث جديد')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('incidents.index') }}">الأحداث التشغيلية</a></li>
  <li class="breadcrumb-item active">تسجيل جديد</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">

    <div class="alert alert-info">
      القطاع الإداري المتابع للحدث: <strong>{{ $department->depname_ar }}</strong> (يُحدَّد تلقائياً وفقاً لقطاعك).
    </div>

    @if($risks->isEmpty())
      <div class="alert alert-warning">
        لا توجد مخاطر محتملة مرتبطة بقطاعك الإداري حتى الآن. يجب ربط قطاعك بخطر محتمل من سجل المخاطر المحتملة أولاً.
      </div>
    @else
      <form method="POST" action="{{ route('incidents.store') }}" data-wizard>
        @csrf

        <div class="wizard-step" data-step-title="الخطر المرتبط">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">الخطر المحتمل المرتبط <span class="required-mark">*</span></label>
              <select name="potential_risk_register_id" class="form-select @error('potential_risk_register_id') is-invalid @enderror" required>
                <option value="">-- اختر الخطر المحتمل --</option>
                @foreach($risks as $risk)
                  <option value="{{ $risk->id }}" @selected(old('potential_risk_register_id') == $risk->id)>
                    {{ $risk->classification_label }} — {{ \Illuminate\Support\Str::limit($risk->risk_description, 60) }}
                  </option>
                @endforeach
              </select>
              @error('potential_risk_register_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>

        <div class="wizard-step" data-step-title="بيانات الحدث">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">تاريخ اكتشاف المشكلة <span class="required-mark">*</span></label>
              <input type="date" name="discovery_date" value="{{ old('discovery_date', now()->format('Y-m-d')) }}" class="form-control @error('discovery_date') is-invalid @enderror" required>
              @error('discovery_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">تاريخ بداية الحدث</label>
              <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label">درجة الأثر (1 إلى 5) <span class="required-mark">*</span></label>
              <select name="impact_score" class="form-select @error('impact_score') is-invalid @enderror" required>
                <option value="">-- اختر درجة الأثر --</option>
                @for($i = 1; $i <= 5; $i++)
                  <option value="{{ $i }}" @selected(old('impact_score') == $i)>{{ $i }}</option>
                @endfor
              </select>
              @error('impact_score')<div class="invalid-feedback">{{ $message }}</div>@enderror
              <div class="form-text">درجة الخطر النهائية = عدد مرات التكرار (يُحتسب تلقائياً بحد أقصى 5) × درجة الأثر.</div>
            </div>
          </div>
        </div>

        <div class="wizard-step" data-step-title="تفاصيل إضافية">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">وصف الحدث</label>
              <textarea name="description" rows="2" class="form-control">{{ old('description') }}</textarea>
            </div>

            <div class="col-md-6">
              <label class="form-label">الإجراء الحالي</label>
              <textarea name="current_procedure" rows="2" class="form-control">{{ old('current_procedure') }}</textarea>
            </div>

            <div class="col-md-6">
              <label class="form-label">الإجراء المقترح</label>
              <textarea name="proposed_procedure" rows="2" class="form-control">{{ old('proposed_procedure') }}</textarea>
            </div>

            <div class="col-12">
              <label class="form-label">الأثر الفعلي للمشكلة</label>
              <textarea name="actual_impact_problem" rows="2" class="form-control">{{ old('actual_impact_problem') }}</textarea>
            </div>
          </div>
        </div>

        <div class="wizard-step" data-step-title="القطاعات المسؤولة">
          <div class="row g-3">
            <div class="col-12 @error('responsible_sectors') wizard-group-invalid @enderror" data-require-checked-group="responsible_sectors[]">
              <label class="form-label">القطاعات المسؤولة عن الحل <span class="required-mark">*</span></label>
              <div class="row">
                @foreach($sectors as $sector)
                  <div class="col-md-4">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="responsible_sectors[]" value="{{ $sector->sec_id }}"
                             id="sector{{ $sector->sec_id }}" @checked(in_array($sector->sec_id, old('responsible_sectors', [])))>
                      <label class="form-check-label" for="sector{{ $sector->sec_id }}">{{ $sector->sector_ar }}</label>
                    </div>
                  </div>
                @endforeach
              </div>
              @if($errors->has('responsible_sectors'))
                <div class="text-danger small mt-1 wizard-group-feedback" style="display:block">{{ $errors->first('responsible_sectors') }}</div>
              @else
                <div class="text-danger small mt-1 wizard-group-feedback" style="display:none">يجب تحديد قطاع واحد على الأقل مسؤول عن الحل.</div>
              @endif
              <div class="form-text">سيُضاف قطاعك الإداري وقطاع المخاطر المركزي تلقائياً ضمن القطاعات المسؤولة عن المتابعة.</div>
            </div>
          </div>
        </div>

        <div class="wizard-controls d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
          <div>
            <button type="button" class="btn btn-outline-secondary wizard-prev d-none"><i class="bi bi-arrow-right"></i> السابق</button>
            <a href="{{ route('incidents.index') }}" class="btn btn-link text-muted">إلغاء</a>
          </div>
          <div>
            <button type="button" class="btn btn-primary wizard-next">التالي <i class="bi bi-arrow-left"></i></button>
            <button type="submit" class="btn btn-success wizard-submit d-none">حفظ الحدث</button>
          </div>
        </div>
      </form>
    @endif

  </div>
</div>
@endsection
