@extends('layouts.app')

@section('title', 'سجل المخاطر المحتملة')
@section('page-title', 'سجل المخاطر المحتملة')
@section('breadcrumbs')
  <li class="breadcrumb-item active">سجل المخاطر المحتملة</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">

    <div class="d-flex justify-content-between align-items-center mb-2">
      <form method="GET" class="d-flex" style="max-width: 350px;">
        <input type="text" name="q" value="{{ $search }}" class="form-control me-2" placeholder="بحث في وصف الخطر...">
        <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
      </form>

      @can('create-risks')
        <a href="{{ route('risks.create') }}" class="btn btn-primary">
          <i class="bi bi-plus-lg"></i> تسجيل خطر محتمل جديد
        </a>
      @endcan
    </div>

    @if($risks->isEmpty())
      <p class="text-center text-muted py-2">لا توجد مخاطر محتملة مسجلة بعد.</p>
    @else
      <div class="table-responsive">
        <table class="table table-striped datatable">
          <thead>
            <tr>
              <th>وصف الخطر</th>
              <th>تصنيف بازل</th>
              <th>القطاعات المسؤولة</th>
              <th>الحالة الحالية</th>
              <th>عدد الأحداث</th>
              <th>الحالة</th>
              <th>إجراءات</th>
            </tr>
          </thead>
          <tbody>
            @foreach($risks as $risk)
              <tr>
                <td>{{ \Illuminate\Support\Str::limit($risk->risk_description, 50) }}</td>
                <td>{{ $risk->classification_label }}</td>
                <td><span class="badge bg-secondary">{{ $risk->sector_details_count }} قطاع</span></td>
                <td>{{ $risk->latestResolutionStatus?->resolutionStatus?->status_name ?? '—' }}</td>
                <td>{{ $risk->incidents_count }}</td>
                <td>
                  @if($risk->validity)
                    <span class="badge bg-success">نشط</span>
                  @else
                    <span class="badge bg-secondary">غير نشط</span>
                  @endif
                </td>
                <td>
                  <a href="{{ route('risks.show', $risk) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                  @can('edit-risks')
                    <a href="{{ route('risks.edit', $risk) }}" class="btn btn-sm btn-outline-secondary">تعديل</a>
                  @endcan
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
