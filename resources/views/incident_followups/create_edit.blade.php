@extends('layouts.app')

@php $isEdit = isset($followup) && $followup; @endphp

@section('title', $isEdit ? 'تعديل متابعة' : 'إضافة متابعة')
@section('page-title', $isEdit ? 'تعديل متابعة' : 'إضافة متابعة')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('incidents.index') }}">الأحداث التشغيلية</a></li>
  <li class="breadcrumb-item"><a href="{{ route('incident-followups.index', $incident) }}">متابعات الحدث #{{ $incident->id }}</a></li>
  <li class="breadcrumb-item active">{{ $isEdit ? 'تعديل' : 'إضافة' }}</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body pt-3">
    <h5 class="card-title">
      <i class="bx {{ $isEdit ? 'bx-edit-alt' : 'bx-plus' }} text-primary"></i>
      {{ $isEdit ? 'تعديل متابعة' : 'إضافة متابعة' }} — الحدث #{{ $incident->id }}
    </h5>

    <div class="p-3 bg-light border rounded mb-4 small">
      <strong>الخطر المحتمل:</strong> {{ $incident->potentialRiskRegister?->risk_description ?? '—' }}
      <br><strong>الإدارة:</strong> {{ $incident->department?->depname_ar ?? '—' }}
    </div>

    {{-- ملخص الأخطاء --}}
    @if($errors->any())
      <div class="alert alert-danger">
        <strong><i class="bx bx-error"></i> يرجى تصحيح الأخطاء التالية:</strong>
        <ul class="mb-0 mt-2">
          @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
      </div>
    @endif

    <form method="POST"
          action="{{ $isEdit ? route('incident-followups.update', $followup) : route('incident-followups.store', $incident) }}">
      @csrf
      @if($isEdit) @method('PUT') @endif

      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">تاريخ المتابعة <span class="text-danger">*</span></label>
          <input type="date" name="followup_date" max="{{ now()->toDateString() }}"
                 class="form-control @error('followup_date') is-invalid @enderror"
                 value="{{ old('followup_date', $followup?->followup_date?->format('Y-m-d') ?? now()->toDateString()) }}">
          @error('followup_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-4">
          <label class="form-label">نوع الإدخال <span class="text-danger">*</span></label>
          <select name="followup_entry_type_id" class="form-select @error('followup_entry_type_id') is-invalid @enderror">
            <option value="">-- اختر --</option>
            @foreach($entryTypes as $type)
              <option value="{{ $type->id }}" @selected(old('followup_entry_type_id', $followup?->followup_entry_type_id) == $type->id)>{{ $type->type_name }}</option>
            @endforeach
          </select>
          @error('followup_entry_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-4">
          <label class="form-label">حالة المتابعة <span class="text-danger">*</span></label>
          <select name="followup_status_id" id="followup_status_id" class="form-select @error('followup_status_id') is-invalid @enderror">
            <option value="">-- اختر --</option>
            @foreach($statuses as $status)
              <option value="{{ $status->id }}"
                      data-closing="{{ in_array($status->status_name, \App\Support\IncidentAccess::CLOSING_STATUSES, true) ? 1 : 0 }}"
                      @selected(old('followup_status_id', $followup?->followup_status_id) == $status->id)>{{ $status->status_name }}</option>
            @endforeach
          </select>
          @error('followup_status_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div id="closingHint" class="small text-danger mt-1 d-none">
            <i class="bx bx-lock-alt"></i> اختيار هذه الحالة سيغلق الحدث.
          </div>
        </div>

        <div class="col-12">
          <label class="form-label">نص المتابعة <span class="text-danger">*</span></label>
          <textarea name="entry_text" rows="5" class="form-control @error('entry_text') is-invalid @enderror">{{ old('entry_text', $followup?->entry_text) }}</textarea>
          @error('entry_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button class="btn btn-primary"><i class="bx bx-save"></i> {{ $isEdit ? 'حفظ التعديل' : 'حفظ المتابعة' }}</button>
        <a href="{{ route('incident-followups.index', $incident) }}" class="btn btn-outline-secondary"><i class="bx bx-arrow-back"></i> رجوع</a>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // تنبيه لو الحالة المختارة بتقفل الحدث (إغلاق / قبول الخطر)
  const statusSelect = document.getElementById('followup_status_id');
  const closingHint  = document.getElementById('closingHint');
  function toggleClosingHint() {
    closingHint.classList.toggle('d-none', statusSelect.selectedOptions[0]?.dataset.closing !== '1');
  }
  statusSelect.addEventListener('change', toggleClosingHint);
  toggleClosingHint();
</script>
@endpush