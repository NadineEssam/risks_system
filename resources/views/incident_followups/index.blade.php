@extends('layouts.app')

@section('title', 'متابعة الأحداث')
@section('page-title', 'متابعة الأحداث')
@section('breadcrumbs')
  <li class="breadcrumb-item active">متابعة الأحداث</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">

    <div class="d-flex justify-content-end mb-2">
      @can('incident-followups.create')
        <a href="{{ route('incident-followups.create') }}" class="btn btn-primary">
          <i class="bi bi-plus-lg"></i> تسجيل متابعة جديدة
        </a>
      @endcan
    </div>

    @if($followups->isEmpty())
      <p class="text-center text-muted py-2">لا توجد متابعات مسجلة بعد.</p>
    @else
      <div class="table-responsive">
        <table class="table table-striped datatable">
          <thead>
            <tr>
              <th>الخطر المحتمل</th>
              <th>القطاع</th>
              <th>تاريخ المتابعة</th>
              <th>نوع الإدخال</th>
              <th>حالة المتابعة</th>
              <th>نص المتابعة</th>
            </tr>
          </thead>
          <tbody>
            @foreach($followups as $followup)
              <tr>
                <td>{{ \Illuminate\Support\Str::limit($followup->incidentSectorResponsibility?->incident?->potentialRiskRegister?->risk_description, 35) }}</td>
                <td>{{ $followup->incidentSectorResponsibility?->sector?->sector_ar }}</td>
                <td>{{ $followup->followup_date?->format('Y-m-d') }}</td>
                <td><span class="badge bg-info">{{ $followup->followupEntryType?->type_name }}</span></td>
                <td><span class="badge bg-secondary">{{ $followup->followupStatus?->status_name }}</span></td>
                <td>{{ \Illuminate\Support\Str::limit($followup->entry_text, 60) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
@endsection
