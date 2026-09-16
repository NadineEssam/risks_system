@extends('layouts.app')

@section('title', isset($role) ? 'تعديل دور' : 'إضافة دور جديد')
@section('page-title', isset($role) ? 'تعديل الدور: '.$role->name : 'إضافة دور جديد')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">المستخدمون والصلاحيات</a></li>
  <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">الأدوار</a></li>
  <li class="breadcrumb-item active">{{ isset($role) ? 'تعديل' : 'إضافة' }}</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">

    <form method="POST" action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}">
      @csrf
      @if(isset($role)) @method('PUT') @endif

      <div class="row mb-4">
        <label for="name" class="col-sm-2 col-form-label">اسم الدور <span class="required-mark">*</span></label>
        <div class="col-sm-6">
          <input type="text"
                 class="form-control @error('name') is-invalid @enderror"
                 id="name"
                 name="name"
                 required
                 {{ isset($role) && $role->name === 'super-admin' ? 'readonly' : '' }}
                 value="{{ old('name', $role->name ?? '') }}">
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="row mb-4">
        <label class="col-sm-2 col-form-label">الصلاحيات</label>
        <div class="col-sm-10">
          @include('admin.roles._permissions_table')
        </div>
      </div>

      <div class="row">
        <div class="col-sm-10 offset-sm-2">
          <button type="submit" class="btn btn-success">
            <i class="bi bi-check-lg"></i> حفظ الدور
          </button>
          <a href="{{ route('admin.roles.index') }}" class="btn btn-link text-muted">إلغاء</a>
        </div>
      </div>
    </form>

  </div>
</div>
@endsection
