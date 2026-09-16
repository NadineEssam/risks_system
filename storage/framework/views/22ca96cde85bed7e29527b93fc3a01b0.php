<?php $__env->startSection('title', 'مؤشرات قياس المخاطر'); ?>
<?php $__env->startSection('page-title', 'مؤشرات قياس المخاطر (KRI)'); ?>
<?php $__env->startSection('breadcrumbs'); ?>
  <li class="breadcrumb-item active">مؤشرات قياس المخاطر</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-body">

    <div class="d-flex justify-content-end mb-2">
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-indicators')): ?>
        <a href="<?php echo e(route('indicators.create')); ?>" class="btn btn-primary">
          <i class="bi bi-plus-lg"></i> إضافة مؤشر جديد
        </a>
      <?php endif; ?>
    </div>

    <?php if($indicators->isEmpty()): ?>
      <p class="text-center text-muted py-2">لا توجد مؤشرات مسجلة بعد.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped datatable">
          <thead>
            <tr>
              <th>اسم المؤشر</th>
              <th>الخطر المرتبط</th>
              <th>طبيعة المؤشر</th>
              <th>دورية الإبلاغ</th>
              <th>الحالة</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $indicators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $indicator): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td><?php echo e(\Illuminate\Support\Str::limit($indicator->indicator_name, 40)); ?></td>
                <td><?php echo e(\Illuminate\Support\Str::limit($indicator->potentialRiskRegister?->risk_description, 30)); ?></td>
                <td><?php echo e($indicator->nature?->nature_name); ?></td>
                <td><?php echo e($indicator->reportingFrequency?->frequency_name); ?></td>
                <td>
                  <?php if($indicator->validity): ?>
                    <span class="badge bg-success">مفعل</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">غير مفعل</span>
                  <?php endif; ?>
                </td>
                <td><a href="<?php echo e(route('indicators.show', $indicator)); ?>" class="btn btn-sm btn-outline-primary">عرض</a></td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/indicators/index.blade.php ENDPATH**/ ?>