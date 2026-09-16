@extends('layouts.app')

@section('title', 'تصنيف الأسباب - الفئة')
@section('page-title', 'تصنيف الأسباب - الفئة (CAUSE_CATEGORY — L2)')
@section('breadcrumbs')
  <li class="breadcrumb-item">البيانات المرجعية</li>
  <li class="breadcrumb-item active">تصنيف الأسباب - الفئة</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">
    <h5 class="card-title">إضافة فئة سبب جديدة</h5>
    <form method="POST" action="{{ route('admin.cause-categories.store') }}" class="row g-2 mb-4">
      @csrf
      <div class="col-md-3"><input type="text" name="category_code" class="form-control" placeholder="الكود (اختياري)"></div>
      <div class="col-md-7"><input type="text" name="category_name" class="form-control" placeholder="اسم فئة السبب" required></div>
      <div class="col-md-2"><button class="btn btn-primary w-100">إضافة</button></div>
    </form>

    <div class="table-responsive">
      <table class="table table-striped datatable">
        <thead><tr><th>الكود</th><th>فئة السبب</th><th>الحالة</th><th></th></tr></thead>
        <tbody>
          @foreach($categories as $category)
            <tr>
              <td>{{ $category->category_code }}</td>
              <td>{{ $category->category_name }}</td>
              <td>
                @if($category->validity)<span class="badge bg-success">مفعل</span>@else<span class="badge bg-secondary">غير مفعل</span>@endif
              </td>
              <td>
                <form method="POST" action="{{ route('admin.cause-categories.toggle', $category) }}">
                  @csrf
                  <button class="btn btn-sm btn-outline-{{ $category->validity ? 'danger' : 'success' }}">
                    {{ $category->validity ? 'إلغاء التفعيل' : 'تفعيل' }}
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
@endsection
