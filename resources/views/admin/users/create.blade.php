@extends('layouts.app')

@section('title', 'إضافة مستخدم')
@section('page-title', 'إضافة مستخدم جديد')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">المستخدمون والصلاحيات</a></li>
  <li class="breadcrumb-item active">إضافة</li>
@endsection

@section('content')
<form method="POST" action="{{ route('admin.users.store') }}">
  @csrf

  @if($errors->any())
    <div class="alert alert-danger">
      <strong><i class="bx bx-error"></i> يرجى تصحيح الأخطاء التالية:</strong>
      <ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="row">

    {{-- بيانات المستخدم --}}
    <div class="col-lg-7">
      <div class="card"><div class="card-body pt-3">
        <h5 class="card-title"><i class="bx bx-user-plus text-primary"></i> بيانات المستخدم</h5>
        <p class="small text-muted">
          <i class="bx bx-info-circle"></i>
          تسجيل الدخول يتم عبر اسم مستخدم الدومين (Active Directory) وكلمة مرور الدومين — تأكد من إدخال اسم المستخدم الفعلي للموظف على الشبكة.
        </p>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">اسم مستخدم الدومين <span class="text-danger">*</span></label>
            <input type="text" name="userID" value="{{ old('userID') }}" dir="ltr" placeholder="nadine.essam" class="form-control @error('userID') is-invalid @enderror">
            @error('userID')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" dir="ltr" class="form-control @error('email') is-invalid @enderror">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">المسمى الوظيفي</label>
            <input type="text" name="job_title" value="{{ old('job_title') }}" class="form-control @error('job_title') is-invalid @enderror">
            @error('job_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">القطاع</label>
            <select name="sector_id" id="sector_id" class="form-select @error('sector_id') is-invalid @enderror">
              <option value="">-- القطاع --</option>
              @foreach($sectors as $sector)
                <option value="{{ $sector->sec_id }}" data-sector-code="{{ $sector->sector_code }}" @selected(old('sector_id') == $sector->sec_id)>{{ $sector->sector_ar }}</option>
              @endforeach
            </select>
            @error('sector_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">الإدارة</label>
            <select name="department_id" id="department_id" data-current="{{ old('department_id') }}" class="form-select @error('department_id') is-invalid @enderror">
              <option value="">-- اختر القطاع أولاً --</option>
            </select>
            @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div></div>
    </div>

    {{-- الأدوار --}}
    <div class="col-lg-5">
      <div class="card"><div class="card-body pt-3">
        <h5 class="card-title"><i class="bx bx-shield-quarter text-primary"></i> الأدوار <span class="text-danger">*</span></h5>
        <div data-require-checked-group class="@error('roles') wizard-group-invalid @enderror">
          @foreach($roles as $role)
            <div class="form-check border rounded p-2 ps-5 mb-2">
              <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role{{ $role->id }}"
                     @checked(in_array($role->name, old('roles', []), true))>
              <label class="form-check-label" for="role{{ $role->id }}">{{ $role->name }}</label>
            </div>
          @endforeach
          @error('roles')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
      </div></div>
    </div>

  </div>

  <div class="d-flex gap-2">
    <button class="btn btn-primary"><i class="bx bx-save"></i> حفظ المستخدم</button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="bx bx-arrow-back"></i> رجوع</a>
  </div>
</form>
@endsection

@php
  $departmentsForJs = $departments->map(fn ($d) => ['id' => $d->dep_id, 'sector_code' => $d->sector_code, 'name' => $d->depname_ar]);
@endphp

@push('scripts')
<script>
  // الإدارات مرتبطة بالقطاع عن طريق sector_code (من new_po)
  const allDepartments   = @json($departmentsForJs);
  const sectorSelect     = document.getElementById('sector_id');
  const departmentSelect = document.getElementById('department_id');

  function fillDepartments(selectedId = null) {
    const code = sectorSelect.selectedOptions[0]?.dataset.sectorCode;
    departmentSelect.innerHTML = '<option value="">' + (code ? '-- الإدارة --' : '-- اختر القطاع أولاً --') + '</option>';
    allDepartments.filter(d => d.sector_code === code).forEach(d => {
      departmentSelect.appendChild(new Option(d.name, d.id, false, String(d.id) === String(selectedId)));
    });
  }

  sectorSelect.addEventListener('change', () => fillDepartments());
  fillDepartments(departmentSelect.dataset.current);
</script>
@endpush