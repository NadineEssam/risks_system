@extends('layouts.app')

@section('title', 'الإدارات')
@section('page-title', 'الإدارات')
@section('breadcrumbs')
  <li class="breadcrumb-item">البيانات المرجعية</li>
  <li class="breadcrumb-item active">الإدارات</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">
    <h5 class="card-title">إضافة إدارة جديدة</h5>
    <form method="POST" action="{{ route('admin.departments.store') }}" class="row g-2 mb-4">
      @csrf
      <div class="col-md-3">
        <select name="sector_code" class="form-select" required>
          <option value="">-- القطاع --</option>
          @foreach($sectors as $sector)
            <option value="{{ $sector->sector_code }}">{{ $sector->sector_ar }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2"><input type="text" name="dep_code" class="form-control" placeholder="كود الإدارة" required></div>
      <div class="col-md-3"><input type="text" name="depname_ar" class="form-control" placeholder="اسم الإدارة (عربي)" required></div>
      <div class="col-md-3"><input type="text" name="depname_en" class="form-control" placeholder="اسم الإدارة (إنجليزي)"></div>
      <div class="col-md-1"><button class="btn btn-primary w-100">إضافة</button></div>
    </form>

    <div class="table-responsive">
      <table class="table table-striped datatable">
        <thead><tr><th>القطاع</th><th>الكود</th><th>الإدارة (عربي)</th><th>الإدارة (إنجليزي)</th><th>الحالة</th><th></th></tr></thead>
        <tbody>
          @forelse($departments as $department)
            <tr>
              <td>{{ $department->sector?->sector_ar }}</td>
              <td>{{ $department->dep_code }}</td>
              <td>{{ $department->depname_ar }}</td>
              <td>{{ $department->depname_en }}</td>
              <td>
                @if($department->validity)<span class="badge bg-success">مفعل</span>@else<span class="badge bg-secondary">غير مفعل</span>@endif
              </td>
              <td>
                <form method="POST" action="{{ route('admin.departments.toggle', $department) }}">
                  @csrf
                  <button class="btn btn-sm btn-outline-{{ $department->validity ? 'danger' : 'success' }}">
                    {{ $department->validity ? 'إلغاء التفعيل' : 'تفعيل' }}
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-2">لا توجد إدارات مسجلة بعد.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
