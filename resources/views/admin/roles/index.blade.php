@extends('layouts.app')

@section('title', 'الأدوار والصلاحيات')
@section('page-title', 'الأدوار والصلاحيات')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">المستخدمون والصلاحيات</a></li>
  <li class="breadcrumb-item active">الأدوار</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">

    <div class="d-flex justify-content-end mb-2">
      <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> إضافة دور جديد
      </a>
    </div>

    @if($roles->isEmpty())
      <p class="text-center text-muted py-2">لا توجد أدوار مسجلة بعد.</p>
    @else
      <div class="table-responsive">
        <table class="table table-striped align-middle datatable">
          <thead>
            <tr>
              <th>اسم الدور</th>
              <th>عدد الصلاحيات</th>
              <th style="width: 180px;">إجراءات</th>
            </tr>
          </thead>
          <tbody>
            @foreach($roles as $role)
              <tr>
                <td>{{ $role->name }}</td>
                <td><span class="badge bg-info text-dark">{{ $role->permissions_count }}</span></td>
                <td>
                  <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-secondary">تعديل</a>
                  @if($role->name !== 'super-admin')
                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger">حذف</button>
                    </form>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
@endsection
