@extends('layouts.app')

@section('title', 'تعديل الخطر المحتمل')
@section('page-title', 'تعديل الخطر المحتمل')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('risks.index') }}">سجل المخاطر المحتملة</a></li>
  <li class="breadcrumb-item active">تعديل</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('risks.update', $risk) }}">
      @csrf
      @method('PUT')

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">تصنيف بازل العام (L1) <span class="required-mark">*</span></label>
          <select name="event_type_id" id="event_type_id" class="form-select" required>
            @foreach($eventTypes as $type)
              <option value="{{ $type->id }}" @selected($risk->eventDetail?->eventSubcategory?->event?->event_type_id === $type->id)>{{ $type->type_name }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">تصنيف بازل التفصيلي (L2) <span class="required-mark">*</span></label>
          <select name="events_id" id="events_id" class="form-select" required></select>
        </div>

        <div class="col-md-6">
          <label class="form-label">تصنيف بازل الفرعي (L3) <span class="required-mark">*</span></label>
          <select name="event_subcategory_id" id="event_subcategory_id" class="form-select" required></select>
        </div>

        <div class="col-md-6">
          <label class="form-label">تصنيف بازل الدقيق (L4) <span class="required-mark">*</span></label>
          <select name="event_detail_id" id="event_detail_id" class="form-select" required></select>
        </div>

        <div class="col-12">
          <label class="form-label">وصف الخطر المحتمل <span class="required-mark">*</span></label>
          <textarea name="risk_description" rows="3" class="form-control" required>{{ old('risk_description', $risk->risk_description) }}</textarea>
        </div>

        <div class="col-12">
          <label class="form-label">الضوابط المقترحة / إجراءات المواجهة</label>
          <textarea name="proposed_control" rows="3" class="form-control">{{ old('proposed_control', $risk->proposed_control) }}</textarea>
        </div>
      </div>

      <div class="mt-3">
        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
        <a href="{{ route('risks.show', $risk) }}" class="btn btn-secondary">إلغاء</a>
      </div>
    </form>
  </div>
</div>

@push('scripts')
  {{ $dataTable->scripts() }}
<script>
  // سلسلة تصنيف بازل الأربعة مستويات، مع تحديد السلسلة الحالية للخطر عند التحميل.
  const allEvents = @json($events->map(fn($e) => ['id' => $e->id, 'event_type_id' => $e->event_type_id, 'event_name' => $e->event_name]));
  const allSubcategories = @json($eventSubcategories->map(fn($s) => ['id' => $s->id, 'events_id' => $s->events_id, 'subcategory_name' => $s->subcategory_name]));
  const allDetails = @json($eventDetails->map(fn($d) => ['id' => $d->id, 'event_subcategory_id' => $d->event_subcategory_id, 'detail_name' => $d->detail_name]));

  const eventTypeSelect = document.getElementById('event_type_id');
  const eventsSelect = document.getElementById('events_id');
  const subcategorySelect = document.getElementById('event_subcategory_id');
  const detailSelect = document.getElementById('event_detail_id');

  const currentEventId = @json(old('events_id', $risk->eventDetail?->eventSubcategory?->events_id));
  const currentSubcategoryId = @json(old('event_subcategory_id', $risk->eventDetail?->event_subcategory_id));
  const currentDetailId = @json(old('event_detail_id', $risk->event_detail_id));

  function fillSelect(select, items, valueKey, labelKey, selectedId) {
    select.innerHTML = '';
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
    fillSelect(eventsSelect, allEvents.filter(e => String(e.event_type_id) === String(typeId)), 'id', 'event_name', selectedId);
    populateSubcategories();
  }

  function populateSubcategories(selectedId = null) {
    const eventId = eventsSelect.value;
    fillSelect(subcategorySelect, allSubcategories.filter(s => String(s.events_id) === String(eventId)), 'id', 'subcategory_name', selectedId);
    populateDetails();
  }

  function populateDetails(selectedId = null) {
    const subcategoryId = subcategorySelect.value;
    fillSelect(detailSelect, allDetails.filter(d => String(d.event_subcategory_id) === String(subcategoryId)), 'id', 'detail_name', selectedId);
  }

  eventTypeSelect.addEventListener('change', () => populateEvents());
  eventsSelect.addEventListener('change', () => populateSubcategories());
  subcategorySelect.addEventListener('change', () => populateDetails());

  populateEvents(currentEventId);
  populateSubcategories(currentSubcategoryId);
  populateDetails(currentDetailId);
</script>
@endpush
@endsection
