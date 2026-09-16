<?php $__env->startSection('title', 'تسجيل متابعة مؤشر'); ?>
<?php $__env->startSection('page-title', 'تسجيل متابعة مؤشر'); ?>
<?php $__env->startSection('breadcrumbs'); ?>
  <li class="breadcrumb-item"><a href="<?php echo e(route('indicator-followups.index')); ?>">متابعة المؤشرات</a></li>
  <li class="breadcrumb-item active">تسجيل جديد</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-body">

    <?php if($indicators->isEmpty()): ?>
      <div class="alert alert-warning mb-0">
        لا توجد مؤشرات مفعّلة مرتبطة بقطاعك الإداري حالياً.
      </div>
    <?php else: ?>
      <form method="POST" action="<?php echo e(route('indicator-followups.store')); ?>" data-wizard>
        <?php echo csrf_field(); ?>

        <div class="wizard-step" data-step-title="المؤشر">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">المؤشر <span class="required-mark">*</span></label>
              <select name="indicators_id" class="form-select <?php $__errorArgs = ['indicators_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                <option value="">-- اختر المؤشر --</option>
                <?php $__currentLoopData = $indicators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $indicator): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($indicator->id); ?>" <?php if(old('indicators_id') == $indicator->id): echo 'selected'; endif; ?>>
                    <?php echo e(\Illuminate\Support\Str::limit($indicator->indicator_name, 70)); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php $__errorArgs = ['indicators_id'];
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

        <div class="wizard-step" data-step-title="بيانات القياس">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">تاريخ القياس <span class="required-mark">*</span></label>
              <input type="date" name="measurement_date" value="<?php echo e(old('measurement_date', now()->format('Y-m-d'))); ?>" class="form-control <?php $__errorArgs = ['measurement_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
              <?php $__errorArgs = ['measurement_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="col-md-4">
              <label class="form-label">القيمة الفعلية <span class="required-mark">*</span></label>
              <input type="number" step="0.0001" name="actual_value" value="<?php echo e(old('actual_value')); ?>" class="form-control <?php $__errorArgs = ['actual_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
              <?php $__errorArgs = ['actual_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="col-md-4">
              <label class="form-label">مستوى حد المؤشر <span class="required-mark">*</span></label>
              <select name="threshold_level_id" id="threshold_level_id" class="form-select <?php $__errorArgs = ['threshold_level_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                <option value="">-- اختر --</option>
                <?php $__currentLoopData = $thresholdLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($level->id); ?>" data-acceptable="<?php echo e($level->isAcceptable() ? '1' : '0'); ?>" <?php if(old('threshold_level_id') == $level->id): echo 'selected'; endif; ?>>
                    <?php echo e($level->level_name); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php $__errorArgs = ['threshold_level_id'];
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

        <div class="wizard-step" data-step-title="أسباب التغيّر والإجراء">
          <div class="alert alert-info small" id="acceptable-hint">
            أسباب التغيّر والإجراء المتخذ إلزاميان فقط إذا كان مستوى الحد المختار غير "مقبول".
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">أسباب التغيّر <span class="required-mark conditional-required" style="display:none">*</span></label>
              <textarea name="change_reason" id="change_reason" rows="3" class="form-control <?php $__errorArgs = ['change_reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('change_reason')); ?></textarea>
              <?php $__errorArgs = ['change_reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="col-md-6">
              <label class="form-label">الإجراء المتخذ <span class="required-mark conditional-required" style="display:none">*</span></label>
              <textarea name="action_taken" id="action_taken" rows="3" class="form-control <?php $__errorArgs = ['action_taken'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('action_taken')); ?></textarea>
              <?php $__errorArgs = ['action_taken'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="col-12">
              <label class="form-label">ملاحظات</label>
              <textarea name="notes" rows="2" class="form-control"><?php echo e(old('notes')); ?></textarea>
            </div>
          </div>
        </div>

        <div class="wizard-controls d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
          <div>
            <button type="button" class="btn btn-outline-secondary wizard-prev d-none"><i class="bi bi-arrow-right"></i> السابق</button>
            <a href="<?php echo e(route('indicator-followups.index')); ?>" class="btn btn-link text-muted">إلغاء</a>
          </div>
          <div>
            <button type="button" class="btn btn-primary wizard-next">التالي <i class="bi bi-arrow-left"></i></button>
            <button type="submit" class="btn btn-success wizard-submit d-none">حفظ المتابعة</button>
          </div>
        </div>
      </form>
    <?php endif; ?>

  </div>
</div>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/indicator_followups/create.blade.php ENDPATH**/ ?>