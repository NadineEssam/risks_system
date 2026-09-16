<?php $__env->startSection('title', 'تعديل الخطر المحتمل'); ?>
<?php $__env->startSection('page-title', 'تعديل الخطر المحتمل'); ?>
<?php $__env->startSection('breadcrumbs'); ?>
  <li class="breadcrumb-item"><a href="<?php echo e(route('risks.index')); ?>">سجل المخاطر المحتملة</a></li>
  <li class="breadcrumb-item active">تعديل</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-body">
    <form method="POST" action="<?php echo e(route('risks.update', $risk)); ?>">
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">تصنيف بازل العام (L1) <span class="required-mark">*</span></label>
          <select name="event_type_id" id="event_type_id" class="form-select" required>
            <?php $__currentLoopData = $eventTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($type->id); ?>" <?php if($risk->eventDetail?->eventSubcategory?->event?->event_type_id === $type->id): echo 'selected'; endif; ?>><?php echo e($type->type_name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
          <textarea name="risk_description" rows="3" class="form-control" required><?php echo e(old('risk_description', $risk->risk_description)); ?></textarea>
        </div>

        <div class="col-12">
          <label class="form-label">الضوابط المقترحة / إجراءات المواجهة</label>
          <textarea name="proposed_control" rows="3" class="form-control"><?php echo e(old('proposed_control', $risk->proposed_control)); ?></textarea>
        </div>
      </div>

      <div class="mt-3">
        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
        <a href="<?php echo e(route('risks.show', $risk)); ?>" class="btn btn-secondary">إلغاء</a>
      </div>
    </form>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
  // سلسلة تصنيف بازل الأربعة مستويات، مع تحديد السلسلة الحالية للخطر عند التحميل.
  const allEvents = <?php echo json_encode($events->map(fn($e) => ['id' => $e->id, 'event_type_id' => $e->event_type_id, 'event_name' => $e->event_name])) ?>;
  const allSubcategories = <?php echo json_encode($eventSubcategories->map(fn($s) => ['id' => $s->id, 'events_id' => $s->events_id, 'subcategory_name' => $s->subcategory_name])) ?>;
  const allDetails = <?php echo json_encode($eventDetails->map(fn($d) => ['id' => $d->id, 'event_subcategory_id' => $d->event_subcategory_id, 'detail_name' => $d->detail_name])) ?>;

  const eventTypeSelect = document.getElementById('event_type_id');
  const eventsSelect = document.getElementById('events_id');
  const subcategorySelect = document.getElementById('event_subcategory_id');
  const detailSelect = document.getElementById('event_detail_id');

  const currentEventId = <?php echo json_encode(old('events_id', $risk->eventDetail?->eventSubcategory?->events_id), 512) ?>;
  const currentSubcategoryId = <?php echo json_encode(old('event_subcategory_id', $risk->eventDetail?->event_subcategory_id), 512) ?>;
  const currentDetailId = <?php echo json_encode(old('event_detail_id', $risk->event_detail_id), 512) ?>;

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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/risks/edit.blade.php ENDPATH**/ ?>