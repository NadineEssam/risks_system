<?php $__env->startSection('title', 'لوحة التحكم'); ?>
<?php $__env->startSection('page-title', 'لوحة التحكم'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
  <div class="col-xxl-3 col-md-6">
    <div class="card info-card">
      <div class="card-body">
        <h5 class="card-title">سجل المخاطر المحتملة</h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10">
            <i class="bi bi-shield-exclamation text-primary"></i>
          </div>
          <div class="ps-3">
            <h6><?php echo e($stats['potential_risks']); ?></h6>
            <span class="text-muted small">خطر مسجل ونشط</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xxl-3 col-md-6">
    <div class="card info-card">
      <div class="card-body">
        <h5 class="card-title">أحداث قيد المتابعة</h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10">
            <i class="bi bi-exclamation-triangle text-warning"></i>
          </div>
          <div class="ps-3">
            <h6><?php echo e($stats['open_incidents']); ?></h6>
            <span class="text-muted small">حل جزئي / غير مقبول</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xxl-3 col-md-6">
    <div class="card info-card">
      <div class="card-body">
        <h5 class="card-title">مؤشرات مفعّلة (KRI)</h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10">
            <i class="bi bi-speedometer2 text-info"></i>
          </div>
          <div class="ps-3">
            <h6><?php echo e($stats['active_indicators']); ?></h6>
            <span class="text-muted small">مؤشر قيد المتابعة</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xxl-3 col-md-6">
    <div class="card info-card">
      <div class="card-body">
        <h5 class="card-title">أحداث عالية الخطورة</h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-danger bg-opacity-10">
            <i class="bi bi-fire text-danger"></i>
          </div>
          <div class="ps-3">
            <h6><?php echo e($stats['high_risk_incidents']); ?></h6>
            <span class="text-muted small">درجة خطر &ge; 15</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">أحدث الأحداث المسجلة</h5>
        <div class="table-responsive">
          <table class="table table-borderless">
            <thead>
              <tr>
                <th>الخطر المحتمل</th>
                <th>الإدارة</th>
                <th>درجة الخطر</th>
                <th>الحالة</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $recentIncidents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $incident): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e(\Illuminate\Support\Str::limit($incident->potentialRiskRegister?->risk_description, 40)); ?></td>
                  <td><?php echo e($incident->department?->depname_ar); ?></td>
                  <td class="risk-degree-cell"><?php echo \App\Support\RiskDegreeHelper::badge($incident->risk_degree); ?></td>
                  <td><?php echo e($incident->resolutionStatus?->status_name ?? '—'); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="text-center text-muted py-3">لا توجد أحداث مسجلة بعد.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">مؤشرات تجاوزت المستوى المقبول</h5>
        <div class="activity">
          <?php $__empty_1 = true; $__currentLoopData = $breachedIndicators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $followup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="activity-item d-flex">
              <i class="bi bi-circle-fill activity-badge text-danger align-self-start"></i>
              <div class="activity-content">
                <strong><?php echo e(\Illuminate\Support\Str::limit($followup->indicator?->indicator_name, 35)); ?></strong>
                — <?php echo e($followup->thresholdLevel?->level_name); ?>

                <div class="small text-muted"><?php echo e($followup->measurement_date?->format('Y-m-d')); ?></div>
              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-muted text-center py-3 mb-0">لا توجد تجاوزات مسجلة حالياً.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/dashboard/index.blade.php ENDPATH**/ ?>