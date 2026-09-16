<?php $__env->startSection('title', 'تفاصيل الخطر المحتمل'); ?>
<?php $__env->startSection('page-title', 'تفاصيل الخطر المحتمل'); ?>
<?php $__env->startSection('breadcrumbs'); ?>
  <li class="breadcrumb-item"><a href="<?php echo e(route('risks.index')); ?>">سجل المخاطر المحتملة</a></li>
  <li class="breadcrumb-item active">تفاصيل</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="row">
  <div class="col-lg-8">

    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="card-title mb-0"><?php echo e($risk->classification_label); ?></h5>
          <div>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-risks')): ?>
              <a href="<?php echo e(route('risks.edit', $risk)); ?>" class="btn btn-sm btn-outline-secondary">تعديل</a>
              <form action="<?php echo e(route('risks.toggle', $risk)); ?>" method="POST" class="d-inline">
                <?php echo csrf_field(); ?>
                <button class="btn btn-sm btn-outline-<?php echo e($risk->validity ? 'danger' : 'success'); ?>">
                  <?php echo e($risk->validity ? 'إلغاء التفعيل' : 'تفعيل'); ?>

                </button>
              </form>
            <?php endif; ?>
          </div>
        </div>

        <p class="mt-2"><strong>وصف الخطر:</strong><br><?php echo e($risk->risk_description); ?></p>
        <?php if($risk->proposed_control): ?>
          <p><strong>الضوابط المقترحة / إجراءات المواجهة:</strong><br><?php echo e($risk->proposed_control); ?></p>
        <?php endif; ?>

        <div class="small text-muted">
          أُنشئ بواسطة <?php echo e($risk->created_by); ?> بتاريخ <?php echo e($risk->creation_date?->format('Y-m-d H:i')); ?>

        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">القطاعات الإدارية المسؤولة والإجراءات المطلوبة</h5>

        <?php $__empty_1 = true; $__currentLoopData = $risk->sectorDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sectorDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center">
              <strong><i class="bi bi-diagram-3 me-1"></i> <?php echo e($sectorDetail->sector?->sector_ar); ?></strong>
              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-risks')): ?>
                <form action="<?php echo e(route('risks.sectors.destroy', [$risk, $sectorDetail])); ?>" method="POST"
                      onsubmit="return confirm('هل أنت متأكد من إلغاء ربط هذا القطاع؟')">
                  <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                  <button class="btn btn-sm btn-link text-danger p-0">إلغاء الربط</button>
                </form>
              <?php endif; ?>
            </div>

            <ul class="list-group list-group-flush mt-2">
              <?php $__empty_2 = true; $__currentLoopData = $sectorDetail->requiredActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                  <div>
                    <?php echo e($action->required_action); ?>

                    <?php if($action->expiration_date): ?>
                      <span class="badge bg-light text-dark">تنتهي: <?php echo e($action->expiration_date->format('Y-m-d')); ?></span>
                    <?php endif; ?>
                  </div>
                  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-risks')): ?>
                    <form action="<?php echo e(route('required-actions.destroy', $action)); ?>" method="POST">
                      <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                      <button class="btn btn-sm btn-link text-danger p-0">حذف</button>
                    </form>
                  <?php endif; ?>
                </li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                <li class="list-group-item px-0 text-muted small">لا توجد إجراءات مطلوبة مسجلة لهذا القطاع بعد.</li>
              <?php endif; ?>
            </ul>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['create-risks', 'edit-risks'])): ?>
              <form action="<?php echo e(route('risk-sectors.actions.store', $sectorDetail)); ?>" method="POST" class="row g-2 mt-2">
                <?php echo csrf_field(); ?>
                <div class="col-md-7">
                  <input type="text" name="required_action" class="form-control form-control-sm" placeholder="نص الإجراء المطلوب" required>
                </div>
                <div class="col-md-3">
                  <input type="date" name="expiration_date" class="form-control form-control-sm" placeholder="تاريخ الانتهاء">
                </div>
                <div class="col-md-2">
                  <button class="btn btn-sm btn-outline-primary w-100">إضافة</button>
                </div>
              </form>
            <?php endif; ?>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <p class="text-muted">لم يتم ربط أي قطاعات إدارية بعد.</p>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['create-risks', 'edit-risks'])): ?>
          <form action="<?php echo e(route('risks.sectors.store', $risk)); ?>" method="POST" class="d-flex gap-2 mt-3">
            <?php echo csrf_field(); ?>
            <select name="sectors_sec_id" class="form-select" required>
              <option value="">-- اختر قطاعاً إدارياً لربطه --</option>
              <?php $__currentLoopData = $allSectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sector): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($sector->sec_id); ?>"><?php echo e($sector->sector_ar); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button class="btn btn-primary text-nowrap">ربط القطاع</button>
          </form>
        <?php endif; ?>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">الأحداث المرتبطة بهذا الخطر (<?php echo e($risk->incidents->count()); ?>)</h5>
        <div class="table-responsive">
          <table class="table table-sm table-borderless">
            <thead><tr><th>الإدارة</th><th>تاريخ الاكتشاف</th><th>درجة الخطر</th><th>الحالة</th></tr></thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $risk->incidents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $incident): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($incident->department?->depname_ar); ?></td>
                  <td><?php echo e($incident->discovery_date?->format('Y-m-d')); ?></td>
                  <td><?php echo \App\Support\RiskDegreeHelper::badge($incident->risk_degree); ?></td>
                  <td><?php echo e($incident->resolutionStatus?->status_name ?? '—'); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="text-muted text-center py-2">لا توجد أحداث مسجلة لهذا الخطر بعد.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>

  <div class="col-lg-4">

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">حالة حل الخطر</h5>
        <ul class="list-group list-group-flush mb-3">
          <?php $__empty_1 = true; $__currentLoopData = $risk->resolutionStatusDetails->sortByDesc('creation_date'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <li class="list-group-item px-0 d-flex justify-content-between">
              <span><?php echo e($statusDetail->resolutionStatus?->status_name); ?></span>
              <span class="text-muted small"><?php echo e($statusDetail->creation_date?->format('Y-m-d')); ?></span>
            </li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <li class="list-group-item px-0 text-muted small">لا يوجد سجل حالات.</li>
          <?php endif; ?>
        </ul>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('approve-risks')): ?>
          <form action="<?php echo e(route('risks.status.update', $risk)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <label class="form-label small">اعتماد حالة جديدة</label>
            <div class="d-flex gap-2">
              <select name="resolution_status_id" class="form-select form-select-sm" required>
                <?php $__currentLoopData = $resolutionStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($status->id); ?>"><?php echo e($status->status_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <button class="btn btn-sm btn-primary text-nowrap">اعتماد</button>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">مؤشرات قياس المخاطر المرتبطة (<?php echo e($risk->indicators->count()); ?>)</h5>
        <ul class="list-group list-group-flush">
          <?php $__empty_1 = true; $__currentLoopData = $risk->indicators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $indicator): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <li class="list-group-item px-0"><?php echo e(\Illuminate\Support\Str::limit($indicator->indicator_name, 60)); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <li class="list-group-item px-0 text-muted small">لا توجد مؤشرات مرتبطة بعد.</li>
          <?php endif; ?>
        </ul>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-indicators')): ?>
          <a href="<?php echo e(route('indicators.create', ['risk' => $risk->id])); ?>" class="btn btn-sm btn-outline-primary mt-2">
            <i class="bi bi-plus-lg"></i> إضافة مؤشر لهذا الخطر
          </a>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/risks/show.blade.php ENDPATH**/ ?>