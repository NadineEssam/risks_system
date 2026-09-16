<?php $__env->startSection('title', 'متابعة المؤشرات'); ?>
<?php $__env->startSection('page-title', 'متابعة المؤشرات'); ?>
<?php $__env->startSection('breadcrumbs'); ?>
  <li class="breadcrumb-item active">متابعة المؤشرات</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-body">

    <div class="d-flex justify-content-end mb-2">
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-indicator-followups')): ?>
        <a href="<?php echo e(route('indicator-followups.create')); ?>" class="btn btn-primary">
          <i class="bi bi-plus-lg"></i> تسجيل متابعة جديدة
        </a>
      <?php endif; ?>
    </div>

    <?php if($followups->isEmpty()): ?>
      <p class="text-center text-muted py-2">لا توجد متابعات مسجلة بعد.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped datatable">
          <thead>
            <tr>
              <th>المؤشر</th>
              <th>تاريخ القياس</th>
              <th>القيمة الفعلية</th>
              <th>مستوى الحد</th>
              <th>أسباب التغيّر</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $followups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $followup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td><?php echo e(\Illuminate\Support\Str::limit($followup->indicator?->indicator_name, 40)); ?></td>
                <td><?php echo e($followup->measurement_date?->format('Y-m-d')); ?></td>
                <td><?php echo e($followup->actual_value); ?></td>
                <td>
                  <?php $isAcceptable = $followup->thresholdLevel?->isAcceptable(); ?>
                  <span class="badge <?php echo e($isAcceptable ? 'bg-success' : 'bg-warning text-dark'); ?>">
                    <?php echo e($followup->thresholdLevel?->level_name); ?>

                  </span>
                </td>
                <td><?php echo e(\Illuminate\Support\Str::limit($followup->change_reason, 40) ?: '—'); ?></td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/indicator_followups/index.blade.php ENDPATH**/ ?>