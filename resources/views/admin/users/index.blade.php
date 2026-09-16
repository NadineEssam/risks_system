@extends('layouts.app')

@section('title', 'المستخدمون والصلاحيات')
@section('page-title', 'المستخدمون والصلاحيات')
@section('breadcrumbs')
  <li class="breadcrumb-item active">المستخدمون والصلاحيات</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h5 class="card-title p-0 mb-0">إضافة مستخدم جديد</h5>
      <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-shield-lock"></i> إدارة الأدوار والصلاحيات
      </a>
    </div>
    <p class="small text-muted">
      تسجيل الدخول يتم عبر اسم مستخدم الدومين (Active Directory) وكلمة مرور
      الدومين — تأكد من إدخال اسم المستخدم الفعلي للموظف على الشبكة.
    </p>
    <form method="POST" action="{{ route('admin.users.store') }}" class="row g-2 mb-4">
      @csrf
      <div class="col-md-3"><input type="text" name="name" class="form-control" placeholder="الاسم الكامل" required></div>
      <div class="col-md-3"><input type="text" name="userID" class="form-control" placeholder="اسم مستخدم الدومين" required></div>
      <div class="col-md-3"><input type="email" name="email" class="form-control" placeholder="البريد الإلكتروني" required></div>
      <div class="col-md-3"><input type="text" name="job_title" class="form-control" placeholder="المسمى الوظيفي"></div>

      <div class="col-md-3">
        <select name="sector_id" class="form-select sector-select">
          <option value="">-- القطاع --</option>
          @foreach($sectors as $sector)
            <option value="{{ $sector->sec_id }}" data-sector-code="{{ $sector->sector_code }}">{{ $sector->sector_ar }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <select name="department_id" class="form-select department-select">
          <option value="">-- اختر القطاع أولاً --</option>
        </select>
      </div>
      <div class="col-md-6">
        <div class="d-flex flex-wrap gap-3">
          @foreach($roles as $role)
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role{{ $role->id }}">
              <label class="form-check-label small" for="role{{ $role->id }}">{{ $role->name }}</label>
            </div>
          @endforeach
        </div>
      </div>
      <div class="col-12"><button class="btn btn-primary">إضافة المستخدم</button></div>
    </form>

    <div class="table-responsive">
      <table class="table table-striped datatable">
        <thead><tr><th>الاسم</th><th>اسم مستخدم الدومين</th><th>البريد الإلكتروني</th><th>القطاع / الإدارة</th><th>الأدوار <small class="text-muted fw-normal">(اضغط Ctrl مع النقر لاختيار أكثر من دور)</small></th><th>الحالة</th><th></th></tr></thead>
        <tbody>
          @foreach($users as $user)
            <tr>
              <td>{{ $user->name }}</td>
              <td><code>{{ $user->domain_username }}</code></td>
              <td>{{ $user->email }}</td>
              <td>
                <form method="POST" action="{{ route('admin.users.sectorDepartment.update', $user) }}" class="d-flex flex-column gap-1">
                  @csrf
                  <select name="sector_id" class="form-select form-select-sm sector-select" style="min-width: 170px;" title="{{ $user->sector?->sector_ar }}">
                    <option value="">-- القطاع --</option>
                    @foreach($sectors as $sector)
                      <option value="{{ $sector->sec_id }}" data-sector-code="{{ $sector->sector_code }}" @selected($user->sector_id == $sector->sec_id)>{{ $sector->sector_ar }}</option>
                    @endforeach
                  </select>
                  <select name="department_id" class="form-select form-select-sm department-select" style="min-width: 170px;" data-current="{{ $user->department_id }}" title="{{ $user->department?->depname_ar }}">
                    <option value="">-- الإدارة --</option>
                  </select>
                  <button class="btn btn-sm btn-outline-primary align-self-start" title="حفظ القطاع/الإدارة">
                    <i class="bi bi-check-lg"></i> حفظ
                  </button>
                </form>
              </td>
              <td>
                <form method="POST" action="{{ route('admin.users.roles.update', $user) }}" class="d-flex align-items-start gap-2">
                  @csrf
                  <select name="roles[]" multiple size="{{ min(3, max(2, $roles->count())) }}" class="form-select form-select-sm" style="min-width: 160px;">
                    @foreach($roles as $role)
                      <option value="{{ $role->name }}" @selected($user->roles->contains('id', $role->id))>{{ $role->name }}</option>
                    @endforeach
                  </select>
                  <button class="btn btn-sm btn-outline-primary" title="حفظ الأدوار">
                    <i class="bi bi-check-lg"></i>
                  </button>
                </form>
              </td>
              <td>
                @if($user->is_active)<span class="badge bg-success">مفعل</span>@else<span class="badge bg-secondary">غير مفعل</span>@endif
              </td>
              <td>
                <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                  @csrf
                  <button class="btn btn-sm btn-outline-{{ $user->is_active ? 'danger' : 'success' }}">
                    {{ $user->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

@php
  // مصفوفة مبسطة للإدارات (id/sector_code/name) لتمريرها للجافاسكريبت —
  // بُنيت هنا في خطوة منفصلة قبل الـscript (بدل استدعاء map() متعدد الأسطر
  // مباشرة داخل توجيه الطباعة بالسكريبت) لتفادي مشاكل تحليل التوجيهات
  // متعددة الأسطر في Blade.
  $departmentsForJs = $departments->map(fn ($d) => [
      'id' => $d->dep_id,
      'sector_code' => $d->sector_code,
      'name' => $d->depname_ar,
  ]);
@endphp

@push('scripts')
<script>
  // ربط قوائم "الإدارة" بـ"القطاع" المختار في كل فورم بالصفحة (فورم إضافة
  // مستخدم جديد + فورم تعديل كل مستخدم بالجدول) — الإدارة مرتبطة بالقطاع عبر
  // sector_code (وليس sec_id مباشرة)، لذا نحمل كل الإدارات مرة واحدة هنا
  // ونفلترها بجافاسكريبت حسب sector_code الخاص بالقطاع المختار في كل فورم.
  const allDepartments = @json($departmentsForJs);

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
@endpush
@endsection