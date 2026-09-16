@extends('layouts.app')

@section('title', 'القطاعات')
@section('page-title', 'القطاعات الإدارية')
@section('breadcrumbs')
  <li class="breadcrumb-item">البيانات المرجعية</li>
  <li class="breadcrumb-item active">القطاعات</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">
    <h5 class="card-title">إضافة قطاع جديد</h5>
    <form method="POST" action="{{ route('admin.sectors.store') }}" class="row g-2 mb-4">
      @csrf
      <div class="col-md-3"><input type="text" name="sector_code" class="form-control" placeholder="كود القطاع" required></div>
      <div class="col-md-4"><input type="text" name="sector_ar" class="form-control" placeholder="اسم القطاع (عربي)" required></div>
      <div class="col-md-3"><input type="text" name="sector_en" class="form-control" placeholder="اسم القطاع (إنجليزي)"></div>
      <div class="col-md-2"><button class="btn btn-primary w-100">إضافة</button></div>
    </form>

    <div class="table-responsive">
      <table class="table table-striped datatable">
        <thead><tr><th>الكود</th><th>القطاع (عربي)</th><th>القطاع (إنجليزي)</th><th>الحالة</th><th></th></tr></thead>
        <tbody>
          @forelse($sectors as $sector)
            <tr>
              <td>{{ $sector->sector_code }}</td>
              <td>{{ $sector->sector_ar }}</td>
              <td>{{ $sector->sector_en }}</td>
              <td>
                @if($sector->validity)<span class="badge bg-success">مفعل</span>@else<span class="badge bg-secondary">غير مفعل</span>@endif
              </td>
              <td>
                <form method="POST" action="{{ route('admin.sectors.toggle', $sector) }}">
                  @csrf
                  <button class="btn btn-sm btn-outline-{{ $sector->validity ? 'danger' : 'success' }}">
                    {{ $sector->validity ? 'إلغاء التفعيل' : 'تفعيل' }}
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted py-2">لا توجد قطاعات مسجلة بعد.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
