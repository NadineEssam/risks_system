@extends('layouts.app')

@section('title', 'المستخدمون والصلاحيات')
@section('page-title', 'المستخدمون والصلاحيات')
@section('breadcrumbs')
  <li class="breadcrumb-item active">المستخدمون والصلاحيات</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body pt-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="card-title m-0 p-0">قائمة المستخدمين</h5>
      <div class="d-flex gap-2">
        @if(PerUser('admin.roles.index'))
          <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-shield-quarter"></i> إدارة الأدوار والصلاحيات
          </a>
        @endif
        @if(PerUser('admin.users.create'))
          <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bx bx-plus"></i> إضافة مستخدم جديد
          </a>
        @endif
      </div>
    </div>

    <div class="table-responsive">
      {{ $dataTable->table(['class' => 'table text-center align-middle datatable-custom', 'style' => 'width:100%']) }}
    </div>

  </div>
</div>
@endsection

@push('scripts')
  {{ $dataTable->scripts() }}
@endpush