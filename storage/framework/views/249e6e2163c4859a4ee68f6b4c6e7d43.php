<?php $__env->startSection('title', isset($role) ? 'تعديل دور' : 'إضافة دور جديد'); ?>
<?php $__env->startSection('page-title', isset($role) ? 'تعديل الدور: '.$role->name : 'إضافة دور جديد'); ?>
<?php $__env->startSection('breadcrumbs'); ?>
  <li class="breadcrumb-item"><a href="<?php echo e(route('admin.users.index')); ?>">المستخدمون والصلاحيات</a></li>
  <li class="breadcrumb-item"><a href="<?php echo e(route('admin.roles.index')); ?>">الأدوار</a></li>
  <li class="breadcrumb-item active"><?php echo e(isset($role) ? 'تعديل' : 'إضافة'); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-body">

    <form method="POST" action="<?php echo e(isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store')); ?>">
      <?php echo csrf_field(); ?>
      <?php if(isset($role)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

      <div class="row mb-4">
        <label for="name" class="col-sm-2 col-form-label">اسم الدور <span class="required-mark">*</span></label>
        <div class="col-sm-6">
          <input type="text"
                 class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                 id="name"
                 name="name"
                 required
                 <?php echo e(isset($role) && $role->name === 'super-admin' ? 'readonly' : ''); ?>

                 value="<?php echo e(old('name', $role->name ?? '')); ?>">
          <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>

      <div class="row mb-4">
        <label class="col-sm-2 col-form-label">الصلاحيات</label>
        <div class="col-sm-10">
          <?php echo $__env->make('admin.roles._permissions_table', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-10 offset-sm-2">
          <button type="submit" class="btn btn-success">
            <i class="bi bi-check-lg"></i> حفظ الدور
          </button>
          <a href="<?php echo e(route('admin.roles.index')); ?>" class="btn btn-link text-muted">إلغاء</a>
        </div>
      </div>
    </form>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/admin/roles/create_edit.blade.php ENDPATH**/ ?>