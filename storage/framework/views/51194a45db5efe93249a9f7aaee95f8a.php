
<?php
  $selectedPermissions = isset($role) ? $role->permissions->pluck('name')->toArray() : old('permissions', []);
  $groups = \Spatie\Permission\Models\Permission::where('guard_name', 'web')
      ->orderBy('id')
      ->get()
      ->groupBy(fn ($permission) => $permission->group ?? 'general');
?>

<div class="table-responsive border rounded">
  <table class="table table-hover mb-0 align-middle">
    <thead class="table-light">
      <tr>
        <th style="width: 260px;">مجموعة الصلاحيات</th>
        <th>
          <div class="form-check mb-0">
            <input type="checkbox" class="form-check-input" id="permissions_select_all">
            <label class="form-check-label fw-bold text-primary" for="permissions_select_all">تحديد كل الصلاحيات</label>
          </div>
        </th>
      </tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $permissionsList): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php ($groupKey = md5($group)); ?>
        <tr>
          <td class="bg-light">
            <div class="form-check mb-0">
              <input type="checkbox" class="form-check-input select-all-group" id="group_<?php echo e($groupKey); ?>" data-group="<?php echo e($groupKey); ?>">
              <label class="form-check-label fw-bold" for="group_<?php echo e($groupKey); ?>">
                <?php echo e($permissionsList->first()->group_ar ?? $group); ?>

              </label>
            </div>
          </td>
          <td>
            <div class="d-flex flex-wrap" style="gap: .75rem 1.5rem;">
              <?php $__currentLoopData = $permissionsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="form-check mb-0">
                  <input type="checkbox"
                         class="form-check-input permission-item"
                         id="perm_<?php echo e($permission->id); ?>"
                         name="permissions[]"
                         value="<?php echo e($permission->name); ?>"
                         data-group="<?php echo e($groupKey); ?>"
                         <?php if(in_array($permission->name, $selectedPermissions)): echo 'checked'; endif; ?>>
                  <label class="form-check-label" for="perm_<?php echo e($permission->id); ?>">
                    <?php echo e($permission->ar_name ?? $permission->name); ?>

                  </label>
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          </td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="2" class="text-center text-muted py-3">لا توجد صلاحيات مُعرَّفة بعد.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php if (! $__env->hasRenderedOnce('96364460-715f-403c-86f2-933872ed654e')): $__env->markAsRenderedOnce('96364460-715f-403c-86f2-933872ed654e'); ?>
  <?php $__env->startPush('scripts'); ?>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('permissions_select_all');
        const groupCheckboxes = document.querySelectorAll('.select-all-group');
        const permissionItems = document.querySelectorAll('.permission-item');

        function updateGroupState(groupKey) {
          const items = document.querySelectorAll(`.permission-item[data-group="${groupKey}"]`);
          const checked = document.querySelectorAll(`.permission-item[data-group="${groupKey}"]:checked`);
          const groupCheckbox = document.getElementById(`group_${groupKey}`);
          if (!groupCheckbox) return;
          groupCheckbox.checked = items.length > 0 && items.length === checked.length;
          groupCheckbox.indeterminate = checked.length > 0 && checked.length < items.length;
        }

        function updateGlobalState() {
          if (!selectAll) return;
          const total = permissionItems.length;
          const checked = document.querySelectorAll('.permission-item:checked').length;
          selectAll.checked = total > 0 && total === checked;
          selectAll.indeterminate = checked > 0 && checked < total;
        }

        groupCheckboxes.forEach((cb) => updateGroupState(cb.dataset.group));
        updateGlobalState();

        if (selectAll) {
          selectAll.addEventListener('change', function () {
            permissionItems.forEach((item) => { item.checked = this.checked; });
            groupCheckboxes.forEach((cb) => { cb.checked = this.checked; cb.indeterminate = false; });
          });
        }

        groupCheckboxes.forEach((cb) => {
          cb.addEventListener('change', function () {
            document.querySelectorAll(`.permission-item[data-group="${this.dataset.group}"]`)
              .forEach((item) => { item.checked = this.checked; });
            updateGlobalState();
          });
        });

        permissionItems.forEach((item) => {
          item.addEventListener('change', function () {
            updateGroupState(this.dataset.group);
            updateGlobalState();
          });
        });
      });
    </script>
  <?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/admin/roles/_permissions_table.blade.php ENDPATH**/ ?>