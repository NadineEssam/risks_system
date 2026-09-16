{{--
  مصفوفة صلاحيات مُجمَّعة حسب مسار العمل — تُستخدم في شاشتي إضافة/تعديل
  الدور. نفس فكرة الجدول المستخدم في "نظام خدمة العملاء"
  (dashboard.include.permissions_table) لكن بأسلوب Bootstrap الموحّد لهذا
  النظام. يتوقع متغيراً اختيارياً $role عند التعديل.
--}}
@php
  $selectedPermissions = isset($role) ? $role->permissions->pluck('name')->toArray() : old('permissions', []);
  $groups = \Spatie\Permission\Models\Permission::where('guard_name', 'web')
      ->orderBy('id')
      ->get()
      ->groupBy(fn ($permission) => $permission->group ?? 'general');
@endphp

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
      @forelse($groups as $group => $permissionsList)
        @php($groupKey = md5($group))
        <tr>
          <td class="bg-light">
            <div class="form-check mb-0">
              <input type="checkbox" class="form-check-input select-all-group" id="group_{{ $groupKey }}" data-group="{{ $groupKey }}">
              <label class="form-check-label fw-bold" for="group_{{ $groupKey }}">
                {{ $permissionsList->first()->group_ar ?? $group }}
              </label>
            </div>
          </td>
          <td>
            <div class="d-flex flex-wrap" style="gap: .75rem 1.5rem;">
              @foreach($permissionsList as $permission)
                <div class="form-check mb-0">
                  <input type="checkbox"
                         class="form-check-input permission-item"
                         id="perm_{{ $permission->id }}"
                         name="permissions[]"
                         value="{{ $permission->name }}"
                         data-group="{{ $groupKey }}"
                         @checked(in_array($permission->name, $selectedPermissions))>
                  <label class="form-check-label" for="perm_{{ $permission->id }}">
                    {{ $permission->ar_name ?? $permission->name }}
                  </label>
                </div>
              @endforeach
            </div>
          </td>
        </tr>
      @empty
        <tr><td colspan="2" class="text-center text-muted py-3">لا توجد صلاحيات مُعرَّفة بعد.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

@once
  @push('scripts')
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
  @endpush
@endonce
