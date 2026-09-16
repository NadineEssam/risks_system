<?php $__env->startSection('title', 'الأدوار والصلاحيات'); ?>
<?php $__env->startSection('page-title', 'الأدوار والصلاحيات'); ?>
<?php $__env->startSection('breadcrumbs'); ?>
  <li class="breadcrumb-item"><a href="<?php echo e(route('admin.users.index')); ?>">المستخدمون والصلاحيات</a></li>
  <li class="breadcrumb-item active">الأدوار</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-body">

    <div class="d-flex justify-content-end mb-2">
      <a href="<?php echo e(route('admin.roles.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> إضافة دور جديد
      </a>
    </div>

    <?php if($roles->isEmpty()): ?>
      <p class="text-center text-muted py-2">لا توجد أدوار مسجلة بعد.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped align-middle datatable">
          <thead>
            <tr>
              <th>اسم الدور</th>
              <th>عدد الصلاحيات</th>
              <th style="width: 180px;">إجراءات</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td><?php echo e($role->name); ?></td>
                <td><span class="badge bg-info text-dark"><?php echo e($role->permissions_count); ?></span></td>
                <td>
                  <a href="<?php echo e(route('admin.roles.edit', $role)); ?>" class="btn btn-sm btn-outline-secondary">تعديل</a>
                  <?php if($role->name !== 'super-admin'): ?>
                    <form action="<?php echo e(route('admin.roles.destroy', $role)); ?>" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟');">
                      <?php echo csrf_field(); ?>
                      <?php echo method_field('DELETE'); ?>
                      <button class="btn btn-sm btn-outline-danger">حذف</button>
                    </form>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/admin/roles/index.blade.php ENDPATH**/ ?>