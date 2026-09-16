<?php $__env->startSection('title', 'تفاصيل المؤشر'); ?>
<?php $__env->startSection('page-title', 'تفاصيل المؤشر'); ?>
<?php $__env->startSection('breadcrumbs'); ?>
  <li class="breadcrumb-item"><a href="<?php echo e(route('indicators.index')); ?>">مؤشرات قياس المخاطر</a></li>
  <li class="breadcrumb-item active">تفاصيل</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="card-title mb-0"><?php echo e($indicator->indicator_name); ?></h5>
          <?php if($indicator->validity): ?>
            <span class="badge bg-success">مفعل</span>
          <?php else: ?>
            <span class="badge bg-secondary">غير مفعل</span>
          <?php endif; ?>
        </div>

        <p class="text-muted">مرتبط بالخطر: <?php echo e($indicator->potentialRiskRegister?->risk_description); ?></p>

        <div class="row">
          <div class="col-md-3"><strong>الطبيعة:</strong> <?php echo e($indicator->nature?->nature_name); ?></div>
          <div class="col-md-3"><strong>وحدة القياس:</strong> <?php echo e($indicator->measurementUnit?->unit_name); ?></div>
          <div class="col-md-3"><strong>دورية الإبلاغ:</strong> <?php echo e($indicator->reportingFrequency?->frequency_name); ?></div>
          <div class="col-md-3"><strong>وحدة النشاط:</strong> <?php echo e($indicator->activityUnit?->unit_name); ?></div>
        </div>

        <?php if($indicator->related_actions): ?>
          <p class="mt-3"><strong>الإجراءات ذات الصلة:</strong><br><?php echo e($indicator->related_actions); ?></p>
        <?php endif; ?>
        <?php if($indicator->data_sources): ?>
          <p><strong>مصادر البيانات:</strong><br><?php echo e($indicator->data_sources); ?></p>
        <?php endif; ?>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">مستويات الحدود</h5>
        <table class="table table-sm">
          <thead><tr><th>المستوى</th><th>القيمة الحدية</th><th>الإجراء عند التجاوز</th></tr></thead>
          <tbody>
            <?php $__currentLoopData = $indicator->thresholdDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $threshold): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td><?php echo e($threshold->thresholdLevel?->level_name); ?></td>
                <td><?php echo e($threshold->threshold_value); ?></td>
                <td><?php echo e($threshold->required_action ?? '—'); ?></td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">سجل المتابعة (<?php echo e($indicator->followups->count()); ?>)</h5>
        <table class="table table-sm">
          <thead><tr><th>تاريخ القياس</th><th>القيمة الفعلية</th><th>المستوى</th></tr></thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $indicator->followups->sortByDesc('measurement_date'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $followup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr>
                <td><?php echo e($followup->measurement_date?->format('Y-m-d')); ?></td>
                <td><?php echo e($followup->actual_value); ?></td>
                <td><?php echo e($followup->thresholdLevel?->level_name); ?></td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr><td colspan="3" class="text-muted text-center py-2">لا توجد متابعات مسجلة بعد.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-indicator-followups')): ?>
          <a href="<?php echo e(route('indicator-followups.create')); ?>" class="btn btn-sm btn-outline-primary">تسجيل متابعة جديدة</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">مسئولو المؤشر</h5>
        <ul class="list-group list-group-flush">
          <?php $__currentLoopData = $indicator->responsibles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $responsible): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="list-group-item px-0">
              <strong><?php echo e($responsible->full_name); ?></strong><br>
              <span class="small text-muted"><?php echo e($responsible->role?->role_name); ?> — <?php echo e($responsible->job_title); ?></span>
            </li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/indicators/show.blade.php ENDPATH**/ ?>