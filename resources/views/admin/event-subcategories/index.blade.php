@extends('layouts.app')

@section('title', 'تصنيف بازل الفرعي')
@section('page-title', 'تصنيف بازل الفرعي (EVENT_SUBCATEGORY — L3)')
@section('breadcrumbs')
  <li class="breadcrumb-item">البيانات المرجعية</li>
  <li class="breadcrumb-item active">تصنيف بازل الفرعي</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">
    <h5 class="card-title">إضافة تصنيف فرعي جديد</h5>
    <form method="POST" action="{{ route('admin.event-subcategories.store') }}" class="row g-2 mb-4">
      @csrf
      <div class="col-md-3">
        <select name="events_id" class="form-select" required>
          <option value="">-- تصنيف بازل التفصيلي --</option>
          @foreach($events as $event)
            <option value="{{ $event->id }}">{{ $event->event_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2"><input type="text" name="subcategory_code" class="form-control" placeholder="الكود (اختياري)"></div>
      <div class="col-md-5"><input type="text" name="subcategory_name" class="form-control" placeholder="اسم التصنيف الفرعي" required></div>
      <div class="col-md-2"><button class="btn btn-primary w-100">إضافة</button></div>
    </form>

    <div class="table-responsive">
      <table class="table table-striped datatable">
        <thead><tr><th>التصنيف العام</th><th>التصنيف التفصيلي</th><th>الكود</th><th>التصنيف الفرعي</th><th>الحالة</th><th></th></tr></thead>
        <tbody>
          @foreach($subcategories as $subcategory)
            <tr>
              <td>{{ $subcategory->event?->eventType?->type_name }}</td>
              <td>{{ $subcategory->event?->event_name }}</td>
              <td>{{ $subcategory->subcategory_code }}</td>
              <td>{{ $subcategory->subcategory_name }}</td>
              <td>
                @if($subcategory->validity)<span class="badge bg-success">مفعل</span>@else<span class="badge bg-secondary">غير مفعل</span>@endif
              </td>
              <td>
                <form method="POST" action="{{ route('admin.event-subcategories.toggle', $subcategory) }}">
                  @csrf
                  <button class="btn btn-sm btn-outline-{{ $subcategory->validity ? 'danger' : 'success' }}">
                    {{ $subcategory->validity ? 'إلغاء التفعيل' : 'تفعيل' }}
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
