<?php $__env->startSection('title', 'إضافة مؤشر جديد'); ?>
<?php $__env->startSection('page-title', 'إضافة مؤشر قياس مخاطر جديد'); ?>
<?php $__env->startSection('breadcrumbs'); ?>
  <li class="breadcrumb-item"><a href="<?php echo e(route('indicators.index')); ?>">مؤشرات قياس المخاطر</a></li>
  <li class="breadcrumb-item active">إضافة جديد</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-body">
    <form method="POST" action="<?php echo e(route('indicators.store')); ?>" data-wizard>
      <?php echo csrf_field(); ?>

      <div class="wizard-step" data-step-title="الخطر المحتمل المرتبط">
        <div class="row g-3">
          <div class="col-12">
            <select name="potential_risk_register_id" class="form-select <?php $__errorArgs = ['potential_risk_register_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
              <option value="">-- اختر الخطر المحتمل --</option>
              <?php $__currentLoopData = $risks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $risk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($risk->id); ?>" <?php if(old('potential_risk_register_id', $selectedRiskId) == $risk->id): echo 'selected'; endif; ?>>
                  <?php echo e($risk->classification_label); ?> — <?php echo e(\Illuminate\Support\Str::limit($risk->risk_description, 60)); ?>

                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['potential_risk_register_id'];
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

      <div class="wizard-step" data-step-title="خصائص المؤشر">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">اسم / وصف المؤشر <span class="required-mark">*</span></label>
            <textarea name="indicator_name" rows="2" class="form-control <?php $__errorArgs = ['indicator_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required><?php echo e(old('indicator_name')); ?></textarea>
            <?php $__errorArgs = ['indicator_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
          <div class="col-md-3">
            <label class="form-label">طبيعة المؤشر <span class="required-mark">*</span></label>
            <select name="indicator_nature_id" class="form-select" required>
              <?php $__currentLoopData = $natures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($n->id); ?>"><?php echo e($n->nature_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">وحدة القياس <span class="required-mark">*</span></label>
            <select name="measurement_unit_id" class="form-select" required>
              <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($u->id); ?>"><?php echo e($u->unit_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">دورية الإبلاغ <span class="required-mark">*</span></label>
            <select name="reporting_frequency_id" class="form-select" required>
              <?php $__currentLoopData = $frequencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($f->id); ?>"><?php echo e($f->frequency_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">وحدة النشاط <span class="required-mark">*</span></label>
            <select name="activity_unit_id" class="form-select" required>
              <?php $__currentLoopData = $activityUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($a->id); ?>"><?php echo e($a->unit_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">الإجراءات ذات الصلة</label>
            <textarea name="related_actions" rows="2" class="form-control"><?php echo e(old('related_actions')); ?></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">مصادر البيانات</label>
            <textarea name="data_sources" rows="2" class="form-control"><?php echo e(old('data_sources')); ?></textarea>
          </div>
        </div>
      </div>

      <div class="wizard-step" data-step-title="مستويات حدود المؤشر">
        <div class="row g-3">
          <?php $__currentLoopData = $thresholdLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <input type="hidden" name="thresholds[<?php echo e($i); ?>][threshold_level_id]" value="<?php echo e($level->id); ?>">
            <div class="col-md-4">
              <div class="border rounded p-3 h-100">
                <label class="form-label fw-bold"><?php echo e($level->level_name); ?></label>
                <input type="number" step="0.0001" name="thresholds[<?php echo e($i); ?>][threshold_value]" class="form-control mb-2" placeholder="القيمة الحدية" required>
                <textarea name="thresholds[<?php echo e($i); ?>][required_action]" rows="2" class="form-control" placeholder="الإجراء عند تجاوز هذا المستوى (إلزامي للمستوى المتوسط والمرتفع)"></textarea>
              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($r->id); ?>"><?php echo e($r->role_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
          <a href="<?php echo e(route('indicators.index')); ?>" class="btn btn-link text-muted">إلغاء</a>
        </div>
        <div>
          <button type="button" class="btn btn-primary wizard-next">التالي <i class="bi bi-arrow-left"></i></button>
          <button type="submit" class="btn btn-success wizard-submit d-none">حفظ المؤشر</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/indicators/create.blade.php ENDPATH**/ ?>