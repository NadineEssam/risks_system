<?php $__env->startSection('title', 'متابعة الأحداث'); ?>
<?php $__env->startSection('page-title', 'متابعة الأحداث'); ?>
<?php $__env->startSection('breadcrumbs'); ?>
  <li class="breadcrumb-item active">متابعة الأحداث</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-body">

    <div class="d-flex justify-content-end mb-2">
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-incident-followups')): ?>
        <a href="<?php echo e(route('incident-followups.create')); ?>" class="btn btn-primary">
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
              <th>الخطر المحتمل</th>
              <th>القطاع</th>
              <th>تاريخ المتابعة</th>
              <th>نوع الإدخال</th>
              <th>حالة المتابعة</th>
              <th>نص المتابعة</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $followups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $followup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td><?php echo e(\Illuminate\Support\Str::limit($followup->incidentSectorResponsibility?->incident?->potentialRiskRegister?->risk_description, 35)); ?></td>
                <td><?php echo e($followup->incidentSectorResponsibility?->sector?->sector_ar); ?></td>
                <td><?php echo e($followup->followup_date?->format('Y-m-d')); ?></td>
                <td><span class="badge bg-info"><?php echo e($followup->followupEntryType?->type_name); ?></span></td>
                <td><span class="badge bg-secondary"><?php echo e($followup->followupStatus?->status_name); ?></span></td>
                <td><?php echo e(\Illuminate\Support\Str::limit($followup->entry_text, 60)); ?></td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/incident_followups/index.blade.php ENDPATH**/ ?>