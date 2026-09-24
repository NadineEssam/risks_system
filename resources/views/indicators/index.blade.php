@extends('layouts.app')

@section('title', 'مؤشرات قياس المخاطر')
@section('page-title', 'مؤشرات قياس المخاطر (KRI)')
@section('breadcrumbs')
  <li class="breadcrumb-item active">مؤشرات قياس المخاطر</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">

    <div class="d-flex justify-content-end mb-2">
      @can('indicators.create')
        <a href="{{ route('indicators.create') }}" class="btn btn-primary">
          <i class="bi bi-plus-lg"></i> إضافة مؤشر جديد
        </a>
      @endcan
    </div>

    @if($indicators->isEmpty())
      <p class="text-center text-muted py-2">لا توجد مؤشرات مسجلة بعد.</p>
    @else
      <div class="table-responsive">
        <table class="table table-striped datatable">
          <thead>
            <tr>
              <th>اسم المؤشر</th>
              <th>الخطر المرتبط</th>
              <th>طبيعة المؤشر</th>
              <th>دورية الإبلاغ</th>
              <th>الحالة</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($indicators as $indicator)
              <tr>
                <td>{{ \Illuminate\Support\Str::limit($indicator->indicator_name, 40) }}</td>
                <td>{{ \Illuminate\Support\Str::limit($indicator->potentialRiskRegister?->risk_description, 30) }}</td>
                <td>{{ $indicator->nature?->nature_name }}</td>
                <td>{{ $indicator->reportingFrequency?->frequency_name }}</td>
                <td>
                  @if($indicator->validity)
                    <span class="badge bg-success">مفعل</span>
                  @else
                    <span class="badge bg-secondary">غير مفعل</span>
                  @endif
                </td>
                <td><a href="{{ route('indicators.show', $indicator) }}" class="btn btn-sm btn-outline-primary">عرض</a></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
@endsection
