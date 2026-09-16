<?php $__env->startSection('title', 'سجل المخاطر المحتملة'); ?>
<?php $__env->startSection('page-title', 'سجل المخاطر المحتملة'); ?>
<?php $__env->startSection('breadcrumbs'); ?>
  <li class="breadcrumb-item active">سجل المخاطر المحتملة</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-body">

    <div class="d-flex justify-content-between align-items-center mb-2">
      <form method="GET" class="d-flex" style="max-width: 350px;">
        <input type="text" name="q" value="<?php echo e($search); ?>" class="form-control me-2" placeholder="بحث في وصف الخطر...">
        <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
      </form>

      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-risks')): ?>
        <a href="<?php echo e(route('risks.create')); ?>" class="btn btn-primary">
          <i class="bi bi-plus-lg"></i> تسجيل خطر محتمل جديد
        </a>
      <?php endif; ?>
    </div>

    <?php if($risks->isEmpty()): ?>
      <p class="text-center text-muted py-2">لا توجد مخاطر محتملة مسجلة بعد.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped datatable">
          <thead>
            <tr>
              <th>وصف الخطر</th>
              <th>تصنيف بازل</th>
              <th>القطاعات المسؤولة</th>
              <th>الحالة الحالية</th>
              <th>عدد الأحداث</th>
              <th>الحالة</th>
              <th>إجراءات</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $risks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $risk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td><?php echo e(\Illuminate\Support\Str::limit($risk->risk_description, 50)); ?></td>
                <td><?php echo e($risk->classification_label); ?></td>
                <td><span class="badge bg-secondary"><?php echo e($risk->sector_details_count); ?> قطاع</span></td>
                <td><?php echo e($risk->latestResolutionStatus?->resolutionStatus?->status_name ?? '—'); ?></td>
                <td><?php echo e($risk->incidents_count); ?></td>
                <td>
                  <?php if($risk->validity): ?>
                    <span class="badge bg-success">نشط</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">غير نشط</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="<?php echo e(route('risks.show', $risk)); ?>" class="btn btn-sm btn-outline-primary">عرض</a>
                  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-risks')): ?>
                    <a href="<?php echo e(route('risks.edit', $risk)); ?>" class="btn btn-sm btn-outline-secondary">تعديل</a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/risks/index.blade.php ENDPATH**/ ?>