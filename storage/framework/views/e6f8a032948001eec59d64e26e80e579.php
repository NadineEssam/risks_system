<?php $__env->startSection('title', 'تسجيل خطر محتمل جديد'); ?>
<?php $__env->startSection('page-title', 'تسجيل خطر محتمل جديد'); ?>
<?php $__env->startSection('breadcrumbs'); ?>
  <li class="breadcrumb-item"><a href="<?php echo e(route('risks.index')); ?>">سجل المخاطر المحتملة</a></li>
  <li class="breadcrumb-item active">تسجيل جديد</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-body">
    <form method="POST" action="<?php echo e(route('risks.store')); ?>" data-wizard>
      <?php echo csrf_field(); ?>

      <div class="wizard-step" data-step-title="تصنيف بازل">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">تصنيف بازل العام (L1) <span class="required-mark">*</span></label>
            <select name="event_type_id" id="event_type_id" class="form-select <?php $__errorArgs = ['event_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
              <option value="">-- اختر التصنيف العام --</option>
              <?php $__currentLoopData = $eventTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($type->id); ?>" <?php if(old('event_type_id') == $type->id): echo 'selected'; endif; ?>><?php echo e($type->type_name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['event_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>

          <div class="col-md-6">
            <label class="form-label">تصنيف بازل التفصيلي (L2) <span class="required-mark">*</span></label>
            <select name="events_id" id="events_id" class="form-select <?php $__errorArgs = ['events_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
              <option value="">-- اختر أولاً التصنيف العام --</option>
            </select>
            <?php $__errorArgs = ['events_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>

          <div class="col-md-6">
            <label class="form-label">تصنيف بازل الفرعي (L3) <span class="required-mark">*</span></label>
            <select name="event_subcategory_id" id="event_subcategory_id" class="form-select <?php $__errorArgs = ['event_subcategory_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
              <option value="">-- اختر أولاً التصنيف التفصيلي --</option>
            </select>
            <?php $__errorArgs = ['event_subcategory_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>

          <div class="col-md-6">
            <label class="form-label">تصنيف بازل الدقيق (L4) <span class="required-mark">*</span></label>
            <select name="event_detail_id" id="event_detail_id" class="form-select <?php $__errorArgs = ['event_detail_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
              <option value="">-- اختر أولاً التصنيف الفرعي --</option>
            </select>
            <?php $__errorArgs = ['event_detail_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
        </div>
      </div>

      <div class="wizard-step" data-step-title="بيانات الخطر">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">وصف الخطر المحتمل <span class="required-mark">*</span></label>
            <textarea name="risk_description" rows="3" class="form-control <?php $__errorArgs = ['risk_description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required><?php echo e(old('risk_description')); ?></textarea>
            <?php $__errorArgs = ['risk_description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>

          <div class="col-12">
            <label class="form-label">الضوابط المقترحة / إجراءات المواجهة</label>
            <textarea name="proposed_control" rows="3" class="form-control"><?php echo e(old('proposed_control')); ?></textarea>
          </div>
        </div>
      </div>

      <div class="wizard-step" data-step-title="الحالة الأولية">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">الحالة الأولية للخطر المحتمل <span class="required-mark">*</span></label>
            <select name="resolution_status_id" class="form-select <?php $__errorArgs = ['resolution_status_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
              <option value="">-- اختر الحالة --</option>
              <?php $__currentLoopData = $resolutionStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($status->id); ?>" <?php if(old('resolution_status_id') == $status->id): echo 'selected'; endif; ?>><?php echo e($status->status_name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['resolution_status_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
        </div>

        <div class="alert alert-info mt-3 small">
          بعد حفظ بيانات الخطر الأساسية، ستتمكن من ربط القطاعات الإدارية المسؤولة وتسجيل الإجراءات المطلوبة من كل قطاع من صفحة عرض الخطر.
        </div>
      </div>

      <div class="wizard-controls d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
        <div>
          <button type="button" class="btn btn-outline-secondary wizard-prev d-none"><i class="bi bi-arrow-right"></i> السابق</button>
          <a href="<?php echo e(route('risks.index')); ?>" class="btn btn-link text-muted">إلغاء</a>
        </div>
        <div>
          <button type="button" class="btn btn-primary wizard-next">التالي <i class="bi bi-arrow-left"></i></button>
          <button type="submit" class="btn btn-success wizard-submit d-none">حفظ ومتابعة</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
  // سلسلة تصنيف بازل الأربعة مستويات: النوع العام (L1) → التفصيلي (L2)
  // → الفرعي (L3) → الدقيق (L4، وهو الحقل الفعلي المحفوظ على الخطر).
  const allEvents = <?php echo json_encode($events->map(fn($e) => ['id' => $e->id, 'event_type_id' => $e->event_type_id, 'event_name' => $e->event_name])) ?>;
  const allSubcategories = <?php echo json_encode($eventSubcategories->map(fn($s) => ['id' => $s->id, 'events_id' => $s->events_id, 'subcategory_name' => $s->subcategory_name])) ?>;
  const allDetails = <?php echo json_encode($eventDetails->map(fn($d) => ['id' => $d->id, 'event_subcategory_id' => $d->event_subcategory_id, 'detail_name' => $d->detail_name])) ?>;

  const eventTypeSelect = document.getElementById('event_type_id');
  const eventsSelect = document.getElementById('events_id');
  const subcategorySelect = document.getElementById('event_subcategory_id');
  const detailSelect = document.getElementById('event_detail_id');

  const oldEventId = <?php echo json_encode(old('events_id'), 15, 512) ?>;
  const oldSubcategoryId = <?php echo json_encode(old('event_subcategory_id'), 15, 512) ?>;
  const oldDetailId = <?php echo json_encode(old('event_detail_id'), 15, 512) ?>;

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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/risks/create.blade.php ENDPATH**/ ?>