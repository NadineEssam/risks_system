@extends('layouts.app')

@section('title', 'تسجيل خطر محتمل جديد')
@section('page-title', 'تسجيل خطر محتمل جديد')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('risks.index') }}">سجل المخاطر المحتملة</a></li>
  <li class="breadcrumb-item active">تسجيل جديد</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('risks.store') }}" data-wizard>
      @csrf

      <div class="wizard-step" data-step-title="تصنيف بازل">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">تصنيف بازل العام (L1) <span class="required-mark">*</span></label>
            <select name="event_type_id" id="event_type_id" class="form-select @error('event_type_id') is-invalid @enderror" required>
              <option value="">-- اختر التصنيف العام --</option>
              @foreach($eventTypes as $type)
                <option value="{{ $type->id }}" @selected(old('event_type_id') == $type->id)>{{ $type->type_name }}</option>
              @endforeach
            </select>
            @error('event_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">تصنيف بازل التفصيلي (L2) <span class="required-mark">*</span></label>
            <select name="events_id" id="events_id" class="form-select @error('events_id') is-invalid @enderror" required>
              <option value="">-- اختر أولاً التصنيف العام --</option>
            </select>
            @error('events_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">تصنيف بازل الفرعي (L3) <span class="required-mark">*</span></label>
            <select name="event_subcategory_id" id="event_subcategory_id" class="form-select @error('event_subcategory_id') is-invalid @enderror" required>
              <option value="">-- اختر أولاً التصنيف التفصيلي --</option>
            </select>
            @error('event_subcategory_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">تصنيف بازل الدقيق (L4) <span class="required-mark">*</span></label>
            <select name="event_detail_id" id="event_detail_id" class="form-select @error('event_detail_id') is-invalid @enderror" required>
              <option value="">-- اختر أولاً التصنيف الفرعي --</option>
            </select>
            @error('event_detail_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>

      <div class="wizard-step" data-step-title="بيانات الخطر">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">وصف الخطر المحتمل <span class="required-mark">*</span></label>
            <textarea name="risk_description" rows="3" class="form-control @error('risk_description') is-invalid @enderror" required>{{ old('risk_description') }}</textarea>
            @error('risk_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-12">
            <label class="form-label">الضوابط المقترحة / إجراءات المواجهة</label>
            <textarea name="proposed_control" rows="3" class="form-control">{{ old('proposed_control') }}</textarea>
          </div>
        </div>
      </div>

      <div class="wizard-step" data-step-title="الحالة الأولية">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">الحالة الأولية للخطر المحتمل <span class="required-mark">*</span></label>
            <select name="resolution_status_id" class="form-select @error('resolution_status_id') is-invalid @enderror" required>
              <option value="">-- اختر الحالة --</option>
              @foreach($resolutionStatuses as $status)
                <option value="{{ $status->id }}" @selected(old('resolution_status_id') == $status->id)>{{ $status->status_name }}</option>
              @endforeach
            </select>
            @error('resolution_status_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="alert alert-info mt-3 small">
          بعد حفظ بيانات الخطر الأساسية، ستتمكن من ربط القطاعات الإدارية المسؤولة وتسجيل الإجراءات المطلوبة من كل قطاع من صفحة عرض الخطر.
        </div>
      </div>

      <div class="wizard-controls d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
        <div>
          <button type="button" class="btn btn-outline-secondary wizard-prev d-none"><i class="bi bi-arrow-right"></i> السابق</button>
          <a href="{{ route('risks.index') }}" class="btn btn-link text-muted">إلغاء</a>
        </div>
        <div>
          <button type="button" class="btn btn-primary wizard-next">التالي <i class="bi bi-arrow-left"></i></button>
          <button type="submit" class="btn btn-success wizard-submit d-none">حفظ ومتابعة</button>
        </div>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  // سلسلة تصنيف بازل الأربعة مستويات: النوع العام (L1) → التفصيلي (L2)
  // → الفرعي (L3) → الدقيق (L4، وهو الحقل الفعلي المحفوظ على الخطر).
  const allEvents = @json($events->map(fn($e) => ['id' => $e->id, 'event_type_id' => $e->event_type_id, 'event_name' => $e->event_name]));
  const allSubcategories = @json($eventSubcategories->map(fn($s) => ['id' => $s->id, 'events_id' => $s->events_id, 'subcategory_name' => $s->subcategory_name]));
  const allDetails = @json($eventDetails->map(fn($d) => ['id' => $d->id, 'event_subcategory_id' => $d->event_subcategory_id, 'detail_name' => $d->detail_name]));

  const eventTypeSelect = document.getElementById('event_type_id');
  const eventsSelect = document.getElementById('events_id');
  const subcategorySelect = document.getElementById('event_subcategory_id');
  const detailSelect = document.getElementById('event_detail_id');

  const oldEventId = @json(old('events_id'));
  const oldSubcategoryId = @json(old('event_subcategory_id'));
  const oldDetailId = @json(old('event_detail_id'));

  function fillSelect(select, items, valueKey, labelKey, placeholder, selectedId) {
    select.innerHTML = `<option value="">${placeholder}</option>`;
    items.forEach(item => {
      const opt = document.createElement('option');
      opt.value = item[valueKey];
      opt.textContent = item[labelKey];
      if (selectedId != null && String(item[valueKey]) === String(selectedId)) opt.selected = true;
      select.appendChild(opt);
    });
  }

  function populateEvents(selectedId = null) {
    const typeId = eventTypeSelect.value;
    const filtered = allEvents.filter(e => String(e.event_type_id) === String(typeId));
    fillSelect(eventsSelect, filtered, 'id', 'event_name', '-- اختر التصنيف التفصيلي --', selectedId);
    populateSubcategories();
  }

  function populateSubcategories(selectedId = null) {
    const eventId = eventsSelect.value;
    const filtered = allSubcategories.filter(s => String(s.events_id) === String(eventId));
    fillSelect(subcategorySelect, filtered, 'id', 'subcategory_name', '-- اختر التصنيف الفرعي --', selectedId);
    populateDetails();
  }

  function populateDetails(selectedId = null) {
    const subcategoryId = subcategorySelect.value;
    const filtered = allDetails.filter(d => String(d.event_subcategory_id) === String(subcategoryId));
    fillSelect(detailSelect, filtered, 'id', 'detail_name', '-- اختر التصنيف الدقيق --', selectedId);
  }

  eventTypeSelect.addEventListener('change', () => populateEvents());
  eventsSelect.addEventListener('change', () => populateSubcategories());
  subcategorySelect.addEventListener('change', () => populateDetails());

  if (eventTypeSelect.value) {
    populateEvents(oldEventId);
    if (oldEventId) populateSubcategories(oldSubcategoryId);
    if (oldSubcategoryId) populateDetails(oldDetailId);
  }
</script>
@endpush
@endsection
