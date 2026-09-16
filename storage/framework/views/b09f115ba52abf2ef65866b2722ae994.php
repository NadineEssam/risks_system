<?php $__env->startSection('title', $definition['title']); ?>
<?php $__env->startSection('page-title', $definition['title']); ?>
<?php $__env->startSection('breadcrumbs'); ?>
  <li class="breadcrumb-item">البيانات المرجعية</li>
  <li class="breadcrumb-item active"><?php echo e($definition['title']); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-body">

    <h5 class="card-title">إضافة عنصر جديد</h5>
    <form method="POST" action="<?php echo e(route('admin.lookups.store', $type)); ?>" class="row g-2 mb-4">
      <?php echo csrf_field(); ?>
      <div class="col-md-<?php echo e(($definition['has_sort_order'] ?? false) ? 6 : 9); ?>">
        <input type="text" name="<?php echo e($definition['field']); ?>" class="form-control" placeholder="<?php echo e($definition['label']); ?>" required>
      </div>
      <?php if($definition['has_sort_order'] ?? false): ?>
        <div class="col-md-3">
          <input type="number" name="sort_order" class="form-control" placeholder="ترتيب العرض">
        </div>
      <?php endif; ?>
      <div class="col-md-3">
        <button class="btn btn-primary w-100">إضافة</button>
      </div>
    </form>

    <?php if($items->isEmpty()): ?>
      <p class="text-center text-muted py-3">لا توجد عناصر مسجلة بعد.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped align-middle datatable">
          <thead>
            <tr>
              <th><?php echo e($definition['label']); ?></th>
              <?php if($definition['has_sort_order'] ?? false): ?><th>الترتيب</th><?php endif; ?>
              <th>الحالة</th>
              <th style="width: 160px;">إجراءات</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td><?php echo e($item->{$definition['field']}); ?></td>
                <?php if($definition['has_sort_order'] ?? false): ?><td><?php echo e($item->sort_order); ?></td><?php endif; ?>
                <td>
                  <?php if($item->validity): ?>
                    <span class="badge bg-success">مفعل</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">غير مفعل</span>
                  <?php endif; ?>
                </td>
                <td>
                  <form method="POST" action="<?php echo e(route('admin.lookups.toggle', [$type, $item->id])); ?>">
                    <?php echo csrf_field(); ?>
                    <button class="btn btn-sm btn-outline-<?php echo e($item->validity ? 'danger' : 'success'); ?>">
                      <?php echo e($item->validity ? 'إلغاء التفعيل' : 'تفعيل'); ?>

                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

    <p class="text-muted small mb-0">
      لتعديل اسم عنصر موجود: أضف عنصراً بديلاً بالاسم الصحيح ثم قم بإلغاء تفعيل العنصر القديم للحفاظ على سجل تاريخي كامل بالبيانات المرتبطة به.
    </p>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/admin/lookups/index.blade.php ENDPATH**/ ?>