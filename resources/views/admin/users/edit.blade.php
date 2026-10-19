@extends('layouts.app')

@section('title', 'تعديل مستخدم')
@section('page-title', 'تعديل مستخدم')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">المستخدمون والصلاحيات</a></li>
  <li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('content')
<div class="row">

  {{-- بيانات المستخدم + التفعيل --}}
  <div class="col-lg-4">
    <div class="card"><div class="card-body pt-3">
      <h5 class="card-title">بيانات المستخدم</h5>
      <p class="mb-1"><strong>الاسم:</strong> {{ $user->name }}</p>
      <p class="mb-1"><strong>اسم مستخدم الدومين:</strong> <code>{{ $user->domain_username }}</code></p>
      <p class="mb-1"><strong>البريد الإلكتروني:</strong> {{ $user->email }}</p>
      <p class="mb-3"><strong>الحالة:</strong>
        @if($user->is_active)<span class="badge bg-success">فعّال</span>@else<span class="badge bg-danger">غير فعّال</span>@endif
      </p>
      @can('admin.users.toggle')
        <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
          @csrf
          <button class="btn btn-sm btn-outline-{{ $user->is_active ? 'danger' : 'success' }}">
            <i class="bx {{ $user->is_active ? 'bx-block' : 'bx-check-circle' }}"></i>
            {{ $user->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}
          </button>
        </form>
      @endcan
    </div></div>
  </div>

  {{-- القطاع / الإدارة --}}
  @can('admin.users.sectorDepartment.update')
  <div class="col-lg-4">
    <div class="card"><div class="card-body pt-3">
      <h5 class="card-title">القطاع / الإدارة</h5>
      <form method="POST" action="{{ route('admin.users.sectorDepartment.update', $user) }}">
        @csrf
        <label class="form-label">القطاع</label>
        <select name="sector_id" id="sector_id" class="form-select mb-3">
          <option value="">-- القطاع --</option>
          @foreach($sectors as $sector)
            <option value="{{ $sector->sec_id }}" data-sector-code="{{ $sector->sector_code }}" @selected($user->sector_id == $sector->sec_id)>{{ $sector->sector_ar }}</option>
          @endforeach
        </select>
        <label class="form-label">الإدارة</label>
        <select name="department_id" id="department_id" class="form-select mb-3" data-current="{{ $user->department_id }}">
          <option value="">-- الإدارة --</option>
        </select>
        <button class="btn btn-primary"><i class="bx bx-save"></i> حفظ</button>
      </form>
    </div></div>
  </div>
  @endcan

  {{-- الأدوار --}}
  @can('admin.users.roles.update')
  <div class="col-lg-4">
    <div class="card"><div class="card-body pt-3">
      <h5 class="card-title">الأدوار</h5>
      <form method="POST" action="{{ route('admin.users.roles.update', $user) }}">
        @csrf
        @foreach($roles as $role)
          <div class="form-check mb-1">
            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role{{ $role->id }}"
                   @checked($user->roles->contains('id', $role->id))>
            <label class="form-check-label" for="role{{ $role->id }}">{{ $role->name }}</label>
          </div>
        @endforeach
        <button class="btn btn-primary mt-2"><i class="bx bx-save"></i> حفظ الأدوار</button>
      </form>
    </div></div>
  </div>
  @endcan

</div>

<a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="bx bx-arrow-back"></i> رجوع للقائمة</a>
@endsection

@php
  $departmentsForJs = $departments->map(fn ($d) => ['id' => $d->dep_id, 'sector_code' => $d->sector_code, 'name' => $d->depname_ar]);
@endphp

@push('scripts')
<script>
  // الإدارات مرتبطة بالقطاع عن طريق sector_code (من new_po)
  const allDepartments = @json($departmentsForJs);
  const sectorSelect = document.getElementById('sector_id');
  const departmentSelect = document.getElementById('department_id');

  function fillDepartments(selectedId = null) {
    if (! sectorSelect || ! departmentSelect) return;
    const code = sectorSelect.selectedOptions[0]?.dataset.sectorCode;
    departmentSelect.innerHTML = '<option value="">-- الإدارة --</option>';
    allDepartments.filter(d => d.sector_code === code).forEach(d => {
      const opt = new Option(d.name, d.id, false, String(d.id) === String(selectedId));
      departmentSelect.appendChild(opt);
    });
  }

  if (sectorSelect) {
    sectorSelect.addEventListener('change', () => fillDepartments());
    fillDepartments(departmentSelect.dataset.current);
  }
</script>
@endpush