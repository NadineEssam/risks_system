<?php $__env->startSection('title', 'تصنيف بازل الدقيق'); ?>
<?php $__env->startSection('page-title', 'تصنيف بازل الدقيق (EVENT_DETAIL — L4)'); ?>
<?php $__env->startSection('breadcrumbs'); ?>
  <li class="breadcrumb-item">البيانات المرجعية</li>
  <li class="breadcrumb-item active">تصنيف بازل الدقيق</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-body">
    <h5 class="card-title">إضافة تصنيف دقيق جديد</h5>
    <form method="POST" action="<?php echo e(route('admin.event-details.store')); ?>" class="row g-2 mb-4">
      <?php echo csrf_field(); ?>
      <div class="col-md-3">
        <select name="event_subcategory_id" class="form-select" required>
          <option value="">-- تصنيف بازل الفرعي --</option>
          <?php $__currentLoopData = $subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($subcategory->id); ?>"><?php echo e($subcategory->subcategory_name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="col-md-2"><input type="text" name="detail_code" class="form-control" placeholder="الكود (اختياري)"></div>
      <div class="col-md-3"><input type="text" name="detail_name" class="form-control" placeholder="اسم التصنيف الدقيق" required></div>
      <div class="col-md-3"><input type="text" name="bank_example" class="form-control" placeholder="مثال مصرفي (اختياري)"></div>
      <div class="col-md-1"><button class="btn btn-primary w-100">إضافة</button></div>
    </form>

    <div class="table-responsive">
      <table class="table table-striped datatable">
        <thead><tr><th>التصنيف الفرعي</th><th>الكود</th><th>التصنيف الدقيق</th><th>مثال مصرفي</th><th>الحالة</th><th></th></tr></thead>
        <tbody>
          <?php $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td><?php echo e($detail->eventSubcategory?->subcategory_name); ?></td>
              <td><?php echo e($detail->detail_code); ?></td>
              <td><?php echo e($detail->detail_name); ?></td>
              <td><?php echo e(\Illuminate\Support\Str::limit($detail->bank_example, 60)); ?></td>
              <td>
                <?php if($detail->validity): ?><span class="badge bg-success">مفعل</span><?php else: ?><span class="badge bg-secondary">غير مفعل</span><?php endif; ?>
              </td>
              <td>
                <form method="POST" action="<?php echo e(route('admin.event-details.toggle', $detail)); ?>">
                  <?php echo csrf_field(); ?>
                  <button class="btn btn-sm btn-outline-<?php echo e($detail->validity ? 'danger' : 'success'); ?>">
                    <?php echo e($detail->validity ? 'إلغاء التفعيل' : 'تفعيل'); ?>

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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/admin/event-details/index.blade.php ENDPATH**/ ?>