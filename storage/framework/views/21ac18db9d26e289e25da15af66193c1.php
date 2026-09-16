<?php $__env->startSection('title', 'تفاصيل الحدث'); ?>
<?php $__env->startSection('page-title', 'تفاصيل الحدث'); ?>
<?php $__env->startSection('breadcrumbs'); ?>
  <li class="breadcrumb-item"><a href="<?php echo e(route('incidents.index')); ?>">الأحداث التشغيلية</a></li>
  <li class="breadcrumb-item active">تفاصيل</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"><?php echo e($incident->potentialRiskRegister?->classification_label); ?></h5>
        <p><?php echo e($incident->potentialRiskRegister?->risk_description); ?></p>

        <div class="row">
          <div class="col-md-4"><strong>الإدارة:</strong> <?php echo e($incident->department?->depname_ar); ?></div>
          <div class="col-md-4"><strong>تاريخ الاكتشاف:</strong> <?php echo e($incident->discovery_date?->format('Y-m-d')); ?></div>
          <div class="col-md-4">
            <strong>حالة الحدث:</strong> <?php echo e($incident->resolutionStatus?->status_name ?? '—'); ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-incidents')): ?>
              <form method="POST" action="<?php echo e(route('incidents.status.update', $incident)); ?>" class="d-flex gap-2 mt-1">
                <?php echo csrf_field(); ?>
                <select name="resolution_status_id" class="form-select form-select-sm" style="max-width: 200px;">
                  <option value="">-- تحديد الحالة --</option>
                  <?php $__currentLoopData = $resolutionStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($status->id); ?>" <?php if($incident->resolution_status_id === $status->id): echo 'selected'; endif; ?>><?php echo e($status->status_name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button class="btn btn-sm btn-outline-primary">حفظ</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-md-4"><strong>عدد مرات التكرار:</strong> <?php echo e($incident->frequency_score); ?></div>
          <div class="col-md-4"><strong>درجة الأثر:</strong> <?php echo e($incident->impact_score); ?></div>
          <div class="col-md-4"><strong>درجة الخطر:</strong> <?php echo \App\Support\RiskDegreeHelper::badge($incident->risk_degree); ?></div>
        </div>

        <?php if($incident->description): ?>
          <p class="mt-3"><strong>وصف الحدث:</strong><br><?php echo e($incident->description); ?></p>
        <?php endif; ?>
        <?php if($incident->current_procedure): ?>
          <p><strong>الإجراء الحالي:</strong><br><?php echo e($incident->current_procedure); ?></p>
        <?php endif; ?>
        <?php if($incident->proposed_procedure): ?>
          <p><strong>الإجراء المقترح:</strong><br><?php echo e($incident->proposed_procedure); ?></p>
        <?php endif; ?>
        <?php if($incident->actual_impact_problem): ?>
          <p><strong>الأثر الفعلي للمشكلة:</strong><br><?php echo e($incident->actual_impact_problem); ?></p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">القطاعات المسؤولة عن المتابعة</h5>
        <ul class="list-group list-group-flush">
          <?php $__currentLoopData = $incident->sectorResponsibilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
              <?php echo e($resp->sector?->sector_ar); ?>

              <span class="badge bg-light text-dark"><?php echo e($resp->followups->count()); ?> متابعة</span>
            </li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-incident-followups')): ?>
          <a href="<?php echo e(route('incident-followups.index', ['incident' => $incident->id])); ?>" class="btn btn-sm btn-outline-primary mt-2">
            عرض المتابعات
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/incidents/show.blade.php ENDPATH**/ ?>