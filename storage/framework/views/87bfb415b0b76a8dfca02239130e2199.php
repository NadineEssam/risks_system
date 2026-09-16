<?php $__env->startSection('title', 'الأحداث التشغيلية'); ?>
<?php $__env->startSection('page-title', 'الأحداث التشغيلية'); ?>
<?php $__env->startSection('breadcrumbs'); ?>
  <li class="breadcrumb-item active">الأحداث التشغيلية</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-body">

    <div class="d-flex justify-content-end mb-2">
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-incidents')): ?>
        <a href="<?php echo e(route('incidents.create')); ?>" class="btn btn-primary">
          <i class="bi bi-plus-lg"></i> تسجيل حدث جديد
        </a>
      <?php endif; ?>
    </div>

    <?php if($incidents->isEmpty()): ?>
      <p class="text-center text-muted py-2">لا توجد أحداث مسجلة بعد.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped datatable">
          <thead>
            <tr>
              <th>الخطر المحتمل المرتبط</th>
              <th>الإدارة</th>
              <th>تاريخ الاكتشاف</th>
              <th>التكرار</th>
              <th>الأثر</th>
              <th>درجة الخطر</th>
              <th>الحالة</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $incidents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $incident): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td><?php echo e(\Illuminate\Support\Str::limit($incident->potentialRiskRegister?->risk_description, 40)); ?></td>
                <td><?php echo e($incident->department?->depname_ar); ?></td>
                <td><?php echo e($incident->discovery_date?->format('Y-m-d')); ?></td>
                <td><?php echo e($incident->frequency_score); ?></td>
                <td><?php echo e($incident->impact_score); ?></td>
                <td class="risk-degree-cell"><?php echo \App\Support\RiskDegreeHelper::badge($incident->risk_degree); ?></td>
                <td><?php echo e($incident->resolutionStatus?->status_name ?? '—'); ?></td>
                <td><a href="<?php echo e(route('incidents.show', $incident)); ?>" class="btn btn-sm btn-outline-primary">عرض</a></td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/incidents/index.blade.php ENDPATH**/ ?>