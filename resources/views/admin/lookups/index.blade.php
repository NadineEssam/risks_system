@extends('layouts.app')

@section('title', $definition['title'])
@section('page-title', $definition['title'])
@section('breadcrumbs')
  <li class="breadcrumb-item">البيانات المرجعية</li>
  <li class="breadcrumb-item active">{{ $definition['title'] }}</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">

    <h5 class="card-title">إضافة عنصر جديد</h5>
    <form method="POST" action="{{ route('admin.lookups.store', $type) }}" class="row g-2 mb-4">
      @csrf
      <div class="col-md-{{ ($definition['has_sort_order'] ?? false) ? 6 : 9 }}">
        <input type="text" name="{{ $definition['field'] }}" class="form-control" placeholder="{{ $definition['label'] }}" required>
      </div>
      @if($definition['has_sort_order'] ?? false)
        <div class="col-md-3">
          <input type="number" name="sort_order" class="form-control" placeholder="ترتيب العرض">
        </div>
      @endif
      <div class="col-md-3">
        <button class="btn btn-primary w-100">إضافة</button>
      </div>
    </form>

    @if($items->isEmpty())
      <p class="text-center text-muted py-3">لا توجد عناصر مسجلة بعد.</p>
    @else
      <div class="table-responsive">
        <table class="table table-striped align-middle datatable">
          <thead>
            <tr>
              <th>{{ $definition['label'] }}</th>
              @if($definition['has_sort_order'] ?? false)<th>الترتيب</th>@endif
              <th>الحالة</th>
              <th style="width: 160px;">إجراءات</th>
            </tr>
          </thead>
          <tbody>
            @foreach($items as $item)
              <tr>
                <td>{{ $item->{$definition['field']} }}</td>
                @if($definition['has_sort_order'] ?? false)<td>{{ $item->sort_order }}</td>@endif
                <td>
                  @if($item->validity)
                    <span class="badge bg-success">مفعل</span>
                  @else
                    <span class="badge bg-secondary">غير مفعل</span>
                  @endif
                </td>
                <td>
                  <form method="POST" action="{{ route('admin.lookups.toggle', [$type, $item->id]) }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-{{ $item->validity ? 'danger' : 'success' }}">
                      {{ $item->validity ? 'إلغاء التفعيل' : 'تفعيل' }}
                    </button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

    <p class="text-muted small mb-0">
      لتعديل اسم عنصر موجود: أضف عنصراً بديلاً بالاسم الصحيح ثم قم بإلغاء تفعيل العنصر القديم للحفاظ على سجل تاريخي كامل بالبيانات المرتبطة به.
    </p>

  </div>
</div>
@endsection
