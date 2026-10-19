@extends('layouts.app')

@section('title', 'التقارير')
@section('page-title', 'التقارير')
@section('breadcrumbs')
  <li class="breadcrumb-item active">التقارير</li>
@endsection

@section('content')
<div class="row reports-page">
  @forelse($reports as $report)
    <div class="col-xl-4 col-md-6 mb-3">
      <a href="{{ route('reports.show', $report->key()) }}" class="text-decoration-none">
        <div class="card report-card h-100 mb-0">
          <div class="card-body pt-3">
            <div class="d-flex align-items-center gap-3">
              <div class="report-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="{{ $report->icon() }}"></i>
              </div>
              <div>
                <h6 class="report-title mb-1">{{ $report->label() }}</h6>
                <small class="report-desc">{{ $report->description() }}</small>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>
  @empty
    <div class="col-12">
      <div class="alert alert-info"><i class="bx bx-info-circle"></i> لا توجد تقارير متاحة لصلاحياتك.</div>
    </div>
  @endforelse
</div>
@endsection