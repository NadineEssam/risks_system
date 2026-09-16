<?php $__env->startSection('title', 'تسجيل حدث جديد'); ?>
<?php $__env->startSection('page-title', 'تسجيل حدث جديد'); ?>
<?php $__env->startSection('breadcrumbs'); ?>
  <li class="breadcrumb-item"><a href="<?php echo e(route('incidents.index')); ?>">الأحداث التشغيلية</a></li>
  <li class="breadcrumb-item active">تسجيل جديد</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-body">

    <div class="alert alert-info">
      القطاع الإداري المتابع للحدث: <strong><?php echo e($department->depname_ar); ?></strong> (يُحدَّد تلقائياً وفقاً لقطاعك).
    </div>

    <?php if($risks->isEmpty()): ?>
      <div class="alert alert-warning">
        لا توجد مخاطر محتملة مرتبطة بقطاعك الإداري حتى الآن. يجب ربط قطاعك بخطر محتمل من سجل المخاطر المحتملة أولاً.
      </div>
    <?php else: ?>
      <form method="POST" action="<?php echo e(route('incidents.store')); ?>" data-wizard>
        <?php echo csrf_field(); ?>

        <div class="wizard-step" data-step-title="الخطر المرتبط">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">الخطر المحتمل المرتبط <span class="required-mark">*</span></label>
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
                  <option value="<?php echo e($risk->id); ?>" <?php if(old('potential_risk_register_id') == $risk->id): echo 'selected'; endif; ?>>
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

        <div class="wizard-step" data-step-title="بيانات الحدث">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">تاريخ اكتشاف المشكلة <span class="required-mark">*</span></label>
              <input type="date" name="discovery_date" value="<?php echo e(old('discovery_date', now()->format('Y-m-d'))); ?>" class="form-control <?php $__errorArgs = ['discovery_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
              <?php $__errorArgs = ['discovery_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="col-md-6">
              <label class="form-label">تاريخ بداية الحدث</label>
              <input type="date" name="start_date" value="<?php echo e(old('start_date')); ?>" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label">درجة الأثر (1 إلى 5) <span class="required-mark">*</span></label>
              <select name="impact_score" class="form-select <?php $__errorArgs = ['impact_score'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                <option value="">-- اختر درجة الأثر --</option>
                <?php for($i = 1; $i <= 5; $i++): ?>
                  <option value="<?php echo e($i); ?>" <?php if(old('impact_score') == $i): echo 'selected'; endif; ?>><?php echo e($i); ?></option>
                <?php endfor; ?>
              </select>
              <?php $__errorArgs = ['impact_score'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              <div class="form-text">درجة الخطر النهائية = عدد مرات التكرار (يُحتسب تلقائياً بحد أقصى 5) × درجة الأثر.</div>
            </div>
          </div>
        </div>

        <div class="wizard-step" data-step-title="تفاصيل إضافية">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">وصف الحدث</label>
              <textarea name="description" rows="2" class="form-control"><?php echo e(old('description')); ?></textarea>
            </div>

            <div class="col-md-6">
              <label class="form-label">الإجراء الحالي</label>
              <textarea name="current_procedure" rows="2" class="form-control"><?php echo e(old('current_procedure')); ?></textarea>
            </div>

            <div class="col-md-6">
              <label class="form-label">الإجراء المقترح</label>
              <textarea name="proposed_procedure" rows="2" class="form-control"><?php echo e(old('proposed_procedure')); ?></textarea>
            </div>

            <div class="col-12">
              <label class="form-label">الأثر الفعلي للمشكلة</label>
              <textarea name="actual_impact_problem" rows="2" class="form-control"><?php echo e(old('actual_impact_problem')); ?></textarea>
            </div>
          </div>
        </div>

        <div class="wizard-step" data-step-title="القطاعات المسؤولة">
          <div class="row g-3">
            <div class="col-12 <?php $__errorArgs = ['responsible_sectors'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> wizard-group-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" data-require-checked-group="responsible_sectors[]">
              <label class="form-label">القطاعات المسؤولة عن الحل <span class="required-mark">*</span></label>
              <div class="row">
                <?php $__currentLoopData = $sectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sector): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div class="col-md-4">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="responsible_sectors[]" value="<?php echo e($sector->sec_id); ?>"
                             id="sector<?php echo e($sector->sec_id); ?>" <?php if(in_array($sector->sec_id, old('responsible_sectors', []))): echo 'checked'; endif; ?>>
                      <label class="form-check-label" for="sector<?php echo e($sector->sec_id); ?>"><?php echo e($sector->sector_ar); ?></label>
                    </div>
                  </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </div>
              <?php if($errors->has('responsible_sectors')): ?>
                <div class="text-danger small mt-1 wizard-group-feedback" style="display:block"><?php echo e($errors->first('responsible_sectors')); ?></div>
              <?php else: ?>
                <div class="text-danger small mt-1 wizard-group-feedback" style="display:none">يجب تحديد قطاع واحد على الأقل مسؤول عن الحل.</div>
              <?php endif; ?>
              <div class="form-text">سيُضاف قطاعك الإداري وقطاع المخاطر المركزي تلقائياً ضمن القطاعات المسؤولة عن المتابعة.</div>
            </div>
          </div>
        </div>

        <div class="wizard-controls d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
          <div>
            <button type="button" class="btn btn-outline-secondary wizard-prev d-none"><i class="bi bi-arrow-right"></i> السابق</button>
            <a href="<?php echo e(route('incidents.index')); ?>" class="btn btn-link text-muted">إلغاء</a>
          </div>
          <div>
            <button type="button" class="btn btn-primary wizard-next">التالي <i class="bi bi-arrow-left"></i></button>
            <button type="submit" class="btn btn-success wizard-submit d-none">حفظ الحدث</button>
          </div>
        </div>
      </form>
    <?php endif; ?>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/incidents/create.blade.php ENDPATH**/ ?>