@extends('layouts.app')

@section('title', 'تسجيل متابعة حدث')
@section('page-title', 'تسجيل متابعة حدث')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('incident-followups.index') }}">متابعة الأحداث</a></li>
  <li class="breadcrumb-item active">تسجيل جديد</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">

    @if($incidents->isEmpty())
      <div class="alert alert-warning mb-0">
        لا توجد أحداث محوَّلة لقطاعك تستدعي متابعة حالياً (حل جزئي / غير مقبول / جارى المتابعة).
      </div>
    @else
      <form method="POST" action="{{ route('incident-followups.store') }}" data-wizard>
        @csrf

        <div class="wizard-step" data-step-title="الحدث">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">الحدث <span class="required-mark">*</span></label>
              <select name="incident_id" class="form-select @error('incident_id') is-invalid @enderror" required>
                <option value="">-- اختر الحدث --</option>
                @foreach($incidents as $incident)
                  <option value="{{ $incident->id }}" @selected(old('incident_id') == $incident->id)>
                    {{ \Illuminate\Support\Str::limit($incident->potentialRiskRegister?->risk_description, 60) }}
                    — {{ $incident->discovery_date?->format('Y-m-d') }}
                  </option>
                @endforeach
              </select>
              @error('incident_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>

        <div class="wizard-step" data-step-title="بيانات المتابعة">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">تاريخ المتابعة <span class="required-mark">*</span></label>
              <input type="date" name="followup_date" value="{{ old('followup_date', now()->format('Y-m-d')) }}" class="form-control" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">تصنيف المتابعة <span class="required-mark">*</span></label>
              <select name="followup_entry_type_id" class="form-select @error('followup_entry_type_id') is-invalid @enderror" required>
                <option value="">-- اختر --</option>
                @foreach($entryTypes as $type)
                  <option value="{{ $type->id }}" @selected(old('followup_entry_type_id') == $type->id)>{{ $type->type_name }}</option>
                @endforeach
              </select>
              @error('followup_entry_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
              <label class="form-label">حالة المتابعة <span class="required-mark">*</span></label>
              <select name="followup_status_id" class="form-select @error('followup_status_id') is-invalid @enderror" required>
                <option value="">-- اختر --</option>
                @foreach($statuses as $status)
                  @if($canDecide || $status->status_name === 'جارى المتابعة')
                    <option value="{{ $status->id }}" @selected(old('followup_status_id') == $status->id)>{{ $status->status_name }}</option>
                  @endif
                @endforeach
              </select>
              @error('followup_status_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
              @unless($canDecide)
                <div class="form-text">قرارات الإغلاق أو قبول الخطر تصدر عن القطاع المركزي للمخاطر فقط.</div>
              @endunless
            </div>
          </div>
        </div>

        <div class="wizard-step" data-step-title="نص المتابعة">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">نص المتابعة <span class="required-mark">*</span></label>
              <textarea name="entry_text" rows="4" class="form-control @error('entry_text') is-invalid @enderror" required>{{ old('entry_text') }}</textarea>
              @error('entry_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>

        <div class="wizard-controls d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
          <div>
            <button type="button" class="btn btn-outline-secondary wizard-prev d-none"><i class="bi bi-arrow-right"></i> السابق</button>
            <a href="{{ route('incident-followups.index') }}" class="btn btn-link text-muted">إلغاء</a>
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
@endsection
