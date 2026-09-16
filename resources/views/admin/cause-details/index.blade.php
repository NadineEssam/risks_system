@extends('layouts.app')

@section('title', 'تصنيف الأسباب - السبب الجذري')
@section('page-title', 'تصنيف الأسباب - السبب الجذري (CAUSE_DETAIL — L4)')
@section('breadcrumbs')
  <li class="breadcrumb-item">البيانات المرجعية</li>
  <li class="breadcrumb-item active">تصنيف الأسباب - السبب الجذري</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">
    <h5 class="card-title">إضافة سبب جذري جديد</h5>
    <form method="POST" action="{{ route('admin.cause-details.store') }}" class="row g-2 mb-4">
      @csrf
      <div class="col-md-3">
        <select name="cause_subcategory_id" class="form-select" required>
          <option value="">-- التصنيف الفرعي --</option>
          @foreach($subcategories as $subcategory)
            <option value="{{ $subcategory->id }}">{{ $subcategory->subcategory_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2"><input type="text" name="detail_code" class="form-control" placeholder="الكود (اختياري)"></div>
      <div class="col-md-5"><input type="text" name="detail_name" class="form-control" placeholder="السبب الجذري" required></div>
      <div class="col-md-2"><button class="btn btn-primary w-100">إضافة</button></div>
    </form>

    <div class="table-responsive">
      <table class="table table-striped datatable">
        <thead><tr><th>فئة السبب</th><th>التصنيف الفرعي</th><th>الكود</th><th>السبب الجذري</th><th>الحالة</th><th></th></tr></thead>
        <tbody>
          @foreach($details as $detail)
            <tr>
              <td>{{ $detail->causeSubcategory?->causeCategory?->category_name }}</td>
              <td>{{ $detail->causeSubcategory?->subcategory_name }}</td>
              <td>{{ $detail->detail_code }}</td>
              <td>{{ $detail->detail_name }}</td>
              <td>
                @if($detail->validity)<span class="badge bg-success">مفعل</span>@else<span class="badge bg-secondary">غير مفعل</span>@endif
              </td>
              <td>
                <form method="POST" action="{{ route('admin.cause-details.toggle', $detail) }}">
                  @csrf
                  <button class="btn btn-sm btn-outline-{{ $detail->validity ? 'danger' : 'success' }}">
                    {{ $detail->validity ? 'إلغاء التفعيل' : 'تفعيل' }}
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
