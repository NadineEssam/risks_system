<?php $__env->startSection('title', 'المستخدمون والصلاحيات'); ?>
<?php $__env->startSection('page-title', 'المستخدمون والصلاحيات'); ?>
<?php $__env->startSection('breadcrumbs'); ?>
  <li class="breadcrumb-item active">المستخدمون والصلاحيات</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h5 class="card-title p-0 mb-0">إضافة مستخدم جديد</h5>
      <a href="<?php echo e(route('admin.roles.index')); ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-shield-lock"></i> إدارة الأدوار والصلاحيات
      </a>
    </div>
    <p class="small text-muted">
      تسجيل الدخول يتم عبر اسم مستخدم الدومين (Active Directory) وكلمة مرور
      الدومين — تأكد من إدخال اسم المستخدم الفعلي للموظف على الشبكة.
    </p>
    <form method="POST" action="<?php echo e(route('admin.users.store')); ?>" class="row g-2 mb-4">
      <?php echo csrf_field(); ?>
      <div class="col-md-3"><input type="text" name="name" class="form-control" placeholder="الاسم الكامل" required></div>
      <div class="col-md-3"><input type="text" name="userID" class="form-control" placeholder="اسم مستخدم الدومين" required></div>
      <div class="col-md-3"><input type="email" name="email" class="form-control" placeholder="البريد الإلكتروني" required></div>
      <div class="col-md-3"><input type="text" name="job_title" class="form-control" placeholder="المسمى الوظيفي"></div>

      <div class="col-md-3">
        <select name="sector_id" class="form-select sector-select">
          <option value="">-- القطاع --</option>
          <?php $__currentLoopData = $sectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sector): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($sector->sec_id); ?>" data-sector-code="<?php echo e($sector->sector_code); ?>"><?php echo e($sector->sector_ar); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="col-md-3">
        <select name="department_id" class="form-select department-select">
          <option value="">-- اختر القطاع أولاً --</option>
        </select>
      </div>
      <div class="col-md-6">
        <div class="d-flex flex-wrap gap-3">
          <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="roles[]" value="<?php echo e($role->name); ?>" id="role<?php echo e($role->id); ?>">
              <label class="form-check-label small" for="role<?php echo e($role->id); ?>"><?php echo e($role->name); ?></label>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
      <div class="col-12"><button class="btn btn-primary">إضافة المستخدم</button></div>
    </form>

    <div class="table-responsive">
      <table class="table table-striped datatable">
        <thead><tr><th>الاسم</th><th>اسم مستخدم الدومين</th><th>البريد الإلكتروني</th><th>القطاع / الإدارة</th><th>الأدوار <small class="text-muted fw-normal">(اضغط Ctrl مع النقر لاختيار أكثر من دور)</small></th><th>الحالة</th><th></th></tr></thead>
        <tbody>
          <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td><?php echo e($user->name); ?></td>
              <td><code><?php echo e($user->domain_username); ?></code></td>
              <td><?php echo e($user->email); ?></td>
              <td>
                <form method="POST" action="<?php echo e(route('admin.users.sectorDepartment.update', $user)); ?>" class="d-flex flex-column gap-1">
                  <?php echo csrf_field(); ?>
                  <select name="sector_id" class="form-select form-select-sm sector-select" style="min-width: 170px;" title="<?php echo e($user->sector?->sector_ar); ?>">
                    <option value="">-- القطاع --</option>
                    <?php $__currentLoopData = $sectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sector): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($sector->sec_id); ?>" data-sector-code="<?php echo e($sector->sector_code); ?>" <?php if($user->sector_id == $sector->sec_id): echo 'selected'; endif; ?>><?php echo e($sector->sector_ar); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                  <select name="department_id" class="form-select form-select-sm department-select" style="min-width: 170px;" data-current="<?php echo e($user->department_id); ?>" title="<?php echo e($user->department?->depname_ar); ?>">
                    <option value="">-- الإدارة --</option>
                  </select>
                  <button class="btn btn-sm btn-outline-primary align-self-start" title="حفظ القطاع/الإدارة">
                    <i class="bi bi-check-lg"></i> حفظ
                  </button>
                </form>
              </td>
              <td>
                <form method="POST" action="<?php echo e(route('admin.users.roles.update', $user)); ?>" class="d-flex align-items-start gap-2">
                  <?php echo csrf_field(); ?>
                  <select name="roles[]" multiple size="<?php echo e(min(3, max(2, $roles->count()))); ?>" class="form-select form-select-sm" style="min-width: 160px;">
                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($role->name); ?>" <?php if($user->roles->contains('id', $role->id)): echo 'selected'; endif; ?>><?php echo e($role->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                  <button class="btn btn-sm btn-outline-primary" title="حفظ الأدوار">
                    <i class="bi bi-check-lg"></i>
                  </button>
                </form>
              </td>
              <td>
                <?php if($user->is_active): ?><span class="badge bg-success">مفعل</span><?php else: ?><span class="badge bg-secondary">غير مفعل</span><?php endif; ?>
              </td>
              <td>
                <form method="POST" action="<?php echo e(route('admin.users.toggle', $user)); ?>">
                  <?php echo csrf_field(); ?>
                  <button class="btn btn-sm btn-outline-<?php echo e($user->is_active ? 'danger' : 'success'); ?>">
                    <?php echo e($user->is_active ? 'إلغاء التفعيل' : 'تفعيل'); ?>

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

<?php
  // مصفوفة مبسطة للإدارات (id/sector_code/name) لتمريرها للجافاسكريبت —
  // بُنيت هنا في خطوة منفصلة قبل الـscript (بدل استدعاء map() متعدد الأسطر
  // مباشرة داخل توجيه الطباعة بالسكريبت) لتفادي مشاكل تحليل التوجيهات
  // متعددة الأسطر في Blade.
  $departmentsForJs = $departments->map(fn ($d) => [
      'id' => $d->dep_id,
      'sector_code' => $d->sector_code,
      'name' => $d->depname_ar,
  ]);
?>

<?php $__env->startPush('scripts'); ?>
<script>
  // ربط قوائم "الإدارة" بـ"القطاع" المختار في كل فورم بالصفحة (فورم إضافة
  // مستخدم جديد + فورم تعديل كل مستخدم بالجدول) — الإدارة مرتبطة بالقطاع عبر
  // sector_code (وليس sec_id مباشرة)، لذا نحمل كل الإدارات مرة واحدة هنا
  // ونفلترها بجافاسكريبت حسب sector_code الخاص بالقطاع المختار في كل فورم.
  const allDepartments = <?php echo json_encode($departmentsForJs, 15, 512) ?>;

  function fillDepartmentSelect(select, sectorCode, selectedId = null) {
    select.innerHTML = '<option value="">-- الإدارة --</option>';
    if (! sectorCode) return;
    allDepartments
      .filter(d => d.sector_code === sectorCode)
      .forEach(d => {
        const opt = document.createElement('option');
        opt.value = d.id;
        opt.textContent = d.name;
        if (selectedId != null && String(d.id) === String(selectedId)) opt.selected = true;
        select.appendChild(opt);
      });
  }

  document.querySelectorAll('.sector-select').forEach(sectorSelect => {
    const form = sectorSelect.closest('form');
    const departmentSelect = form?.querySelector('.department-select');
    if (! departmentSelect) return;

    const initialDepartmentId = departmentSelect.dataset.current || null;

    function syncDepartments(selectedId = null) {
      const selectedOption = sectorSelect.selectedOptions[0];
      const sectorCode = selectedOption ? selectedOption.dataset.sectorCode : null;
      fillDepartmentSelect(departmentSelect, sectorCode, selectedId);
    }

    sectorSelect.addEventListener('change', () => syncDepartments());

    if (sectorSelect.value) {
      // قطاع محدد بالفعل (صف مستخدم موجود) — املأ إداراته مع تأشير الإدارة الحالية.
      syncDepartments(initialDepartmentId);
    } else if (initialDepartmentId) {
      // حالة قديمة: إدارة محددة على المستخدم بدون قطاع محدد — استنتج القطاع
      // تلقائياً من الإدارة نفسها بدل ترك القائمتين متعارضتين.
      const dept = allDepartments.find(d => String(d.id) === String(initialDepartmentId));
      if (dept) {
        const matchingSectorOption = Array.from(sectorSelect.options)
          .find(o => o.dataset.sectorCode === dept.sector_code);
        if (matchingSectorOption) sectorSelect.value = matchingSectorOption.value;
        fillDepartmentSelect(departmentSelect, dept.sector_code, initialDepartmentId);
      }
    }
  });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/admin/users/index.blade.php ENDPATH**/ ?>