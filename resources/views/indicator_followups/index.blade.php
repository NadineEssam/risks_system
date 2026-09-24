@extends('layouts.app')

@section('title', 'متابعة المؤشرات')
@section('page-title', 'متابعة المؤشرات')
@section('breadcrumbs')
  <li class="breadcrumb-item active">متابعة المؤشرات</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body pt-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="card-title m-0 p-0">قائمة متابعات المؤشرات</h5>
      @can('indicator-followups.create')
        <a href="{{ route('indicator-followups.create') }}" class="btn btn-primary">
          <i class="bx bx-plus"></i> تسجيل متابعة جديدة
        </a>
      @endcan
    </div>

    <div class="table-responsive">
      {{ $dataTable->table(['class' => 'table text-center align-middle datatable-custom', 'style' => 'width:100%']) }}
    </div>

  </div>
</div>
@endsection

@push('scripts')
  {{ $dataTable->scripts() }}
@endpush
