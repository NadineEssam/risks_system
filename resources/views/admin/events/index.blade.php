@extends('layouts.app')

@section('title', 'تصنيف بازل التفصيلي')
@section('page-title', 'تصنيف بازل التفصيلي (EVENTS)')
@section('breadcrumbs')
  <li class="breadcrumb-item">البيانات المرجعية</li>
  <li class="breadcrumb-item active">تصنيف بازل التفصيلي</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">
    <h5 class="card-title">إضافة تصنيف تفصيلي جديد</h5>
    <form method="POST" action="{{ route('admin.events.store') }}" class="row g-2 mb-4">
      @csrf
      <div class="col-md-4">
        <select name="event_type_id" class="form-select" required>
          <option value="">-- تصنيف بازل العام --</option>
          @foreach($eventTypes as $type)
            <option value="{{ $type->id }}">{{ $type->type_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6"><input type="text" name="event_name" class="form-control" placeholder="اسم التصنيف التفصيلي" required></div>
      <div class="col-md-2"><button class="btn btn-primary w-100">إضافة</button></div>
    </form>

    <div class="table-responsive">
      <table class="table table-striped datatable">
        <thead><tr><th>التصنيف العام</th><th>التصنيف التفصيلي</th><th>الحالة</th><th></th></tr></thead>
        <tbody>
          @foreach($events as $event)
            <tr>
              <td>{{ $event->eventType?->type_name }}</td>
              <td>{{ $event->event_name }}</td>
              <td>
                @if($event->validity)<span class="badge bg-success">مفعل</span>@else<span class="badge bg-secondary">غير مفعل</span>@endif
              </td>
              <td>
                <form method="POST" action="{{ route('admin.events.toggle', $event) }}">
                  @csrf
                  <button class="btn btn-sm btn-outline-{{ $event->validity ? 'danger' : 'success' }}">
                    {{ $event->validity ? 'إلغاء التفعيل' : 'تفعيل' }}
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
