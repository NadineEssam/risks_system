<?php $__env->startSection('title', 'تسجيل متابعة حدث'); ?>
<?php $__env->startSection('page-title', 'تسجيل متابعة حدث'); ?>
<?php $__env->startSection('breadcrumbs'); ?>
  <li class="breadcrumb-item"><a href="<?php echo e(route('incident-followups.index')); ?>">متابعة الأحداث</a></li>
  <li class="breadcrumb-item active">تسجيل جديد</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-body">

    <?php if($incidents->isEmpty()): ?>
      <div class="alert alert-warning mb-0">
        لا توجد أحداث محوَّلة لقطاعك تستدعي متابعة حالياً (حل جزئي / غير مقبول / جارى المتابعة).
      </div>
    <?php else: ?>
      <form method="POST" action="<?php echo e(route('incident-followups.store')); ?>" data-wizard>
        <?php echo csrf_field(); ?>

        <div class="wizard-step" data-step-title="الحدث">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">الحدث <span class="required-mark">*</span></label>
              <select name="incident_id" class="form-select <?php $__errorArgs = ['incident_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                <option value="">-- اختر الحدث --</option>
                <?php $__currentLoopData = $incidents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $incident): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($incident->id); ?>" <?php if(old('incident_id') == $incident->id): echo 'selected'; endif; ?>>
                    <?php echo e(\Illuminate\Support\Str::limit($incident->potentialRiskRegister?->risk_description, 60)); ?>

                    — <?php echo e($incident->discovery_date?->format('Y-m-d')); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php $__errorArgs = ['incident_id'];
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

        <div class="wizard-step" data-step-title="بيانات المتابعة">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">تاريخ المتابعة <span class="required-mark">*</span></label>
              <input type="date" name="followup_date" value="<?php echo e(old('followup_date', now()->format('Y-m-d'))); ?>" class="form-control" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">تصنيف المتابعة <span class="required-mark">*</span></label>
              <select name="followup_entry_type_id" class="form-select <?php $__errorArgs = ['followup_entry_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                <option value="">-- اختر --</option>
                <?php $__currentLoopData = $entryTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($type->id); ?>" <?php if(old('followup_entry_type_id') == $type->id): echo 'selected'; endif; ?>><?php echo e($type->type_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php $__errorArgs = ['followup_entry_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="col-md-4">
              <label class="form-label">حالة المتابعة <span class="required-mark">*</span></label>
              <select name="followup_status_id" class="form-select <?php $__errorArgs = ['followup_status_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                <option value="">-- اختر --</option>
                <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php if($canDecide || $status->status_name === 'جارى المتابعة'): ?>
                    <option value="<?php echo e($status->id); ?>" <?php if(old('followup_status_id') == $status->id): echo 'selected'; endif; ?>><?php echo e($status->status_name); ?></option>
                  <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php $__errorArgs = ['followup_status_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              <?php if (! ($canDecide)): ?>
                <div class="form-text">قرارات الإغلاق أو قبول الخطر تصدر عن القطاع المركزي للمخاطر فقط.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="wizard-step" data-step-title="نص المتابعة">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">نص المتابعة <span class="required-mark">*</span></label>
              <textarea name="entry_text" rows="4" class="form-control <?php $__errorArgs = ['entry_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required><?php echo e(old('entry_text')); ?></textarea>
              <?php $__errorArgs = ['entry_text'];
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

        <div class="wizard-controls d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
          <div>
            <button type="button" class="btn btn-outline-secondary wizard-prev d-none"><i class="bi bi-arrow-right"></i> السابق</button>
            <a href="<?php echo e(route('incident-followups.index')); ?>" class="btn btn-link text-muted">إلغاء</a>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/incident_followups/create.blade.php ENDPATH**/ ?>