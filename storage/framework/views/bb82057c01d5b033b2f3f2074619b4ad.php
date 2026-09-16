<?php $__env->startSection('title', 'تصنيف بازل الفرعي'); ?>
<?php $__env->startSection('page-title', 'تصنيف بازل الفرعي (EVENT_SUBCATEGORY — L3)'); ?>
<?php $__env->startSection('breadcrumbs'); ?>
  <li class="breadcrumb-item">البيانات المرجعية</li>
  <li class="breadcrumb-item active">تصنيف بازل الفرعي</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-body">
    <h5 class="card-title">إضافة تصنيف فرعي جديد</h5>
    <form method="POST" action="<?php echo e(route('admin.event-subcategories.store')); ?>" class="row g-2 mb-4">
      <?php echo csrf_field(); ?>
      <div class="col-md-3">
        <select name="events_id" class="form-select" required>
          <option value="">-- تصنيف بازل التفصيلي --</option>
          <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($event->id); ?>"><?php echo e($event->event_name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="col-md-2"><input type="text" name="subcategory_code" class="form-control" placeholder="الكود (اختياري)"></div>
      <div class="col-md-5"><input type="text" name="subcategory_name" class="form-control" placeholder="اسم التصنيف الفرعي" required></div>
      <div class="col-md-2"><button class="btn btn-primary w-100">إضافة</button></div>
    </form>

    <div class="table-responsive">
      <table class="table table-striped datatable">
        <thead><tr><th>التصنيف العام</th><th>التصنيف التفصيلي</th><th>الكود</th><th>التصنيف الفرعي</th><th>الحالة</th><th></th></tr></thead>
        <tbody>
          <?php $__currentLoopData = $subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td><?php echo e($subcategory->event?->eventType?->type_name); ?></td>
              <td><?php echo e($subcategory->event?->event_name); ?></td>
              <td><?php echo e($subcategory->subcategory_code); ?></td>
              <td><?php echo e($subcategory->subcategory_name); ?></td>
              <td>
                <?php if($subcategory->validity): ?><span class="badge bg-success">مفعل</span><?php else: ?><span class="badge bg-secondary">غير مفعل</span><?php endif; ?>
              </td>
              <td>
                <form method="POST" action="<?php echo e(route('admin.event-subcategories.toggle', $subcategory)); ?>">
                  <?php echo csrf_field(); ?>
                  <button class="btn btn-sm btn-outline-<?php echo e($subcategory->validity ? 'danger' : 'success'); ?>">
                    <?php echo e($subcategory->validity ? 'إلغاء التفعيل' : 'تفعيل'); ?>

                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/admin/event-subcategories/index.blade.php ENDPATH**/ ?>