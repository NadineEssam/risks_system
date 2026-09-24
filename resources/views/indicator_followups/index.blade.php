@extends('layouts.app')

@section('title', 'متابعة المؤشرات')
@section('page-title', 'متابعة المؤشرات')
@section('breadcrumbs')
  <li class="breadcrumb-item active">متابعة المؤشرات</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">

    <div class="d-flex justify-content-end mb-2">
      @can('indicator-followups.create')
        <a href="{{ route('indicator-followups.create') }}" class="btn btn-primary">
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
              <th>المؤشر</th>
              <th>تاريخ القياس</th>
              <th>القيمة الفعلية</th>
              <th>مستوى الحد</th>
              <th>أسباب التغيّر</th>
            </tr>
          </thead>
          <tbody>
            @foreach($followups as $followup)
              <tr>
                <td>{{ \Illuminate\Support\Str::limit($followup->indicator?->indicator_name, 40) }}</td>
                <td>{{ $followup->measurement_date?->format('Y-m-d') }}</td>
                <td>{{ $followup->actual_value }}</td>
                <td>
                  @php $isAcceptable = $followup->thresholdLevel?->isAcceptable(); @endphp
                  <span class="badge {{ $isAcceptable ? 'bg-success' : 'bg-warning text-dark' }}">
                    {{ $followup->thresholdLevel?->level_name }}
                  </span>
                </td>
                <td>{{ \Illuminate\Support\Str::limit($followup->change_reason, 40) ?: '—' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
@endsection
