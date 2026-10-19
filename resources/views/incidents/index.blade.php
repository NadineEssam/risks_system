@extends('layouts.app')

@section('title', 'الأحداث التشغيلية')
@section('page-title', 'الأحداث التشغيلية')
@section('breadcrumbs')
  <li class="breadcrumb-item active">الأحداث التشغيلية</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body pt-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="card-title m-0 p-0">قائمة الأحداث التشغيلية</h5>
      @can('incidents.create')
        <a href="{{ route('incidents.create') }}" class="btn btn-primary">
          <i class="bx bx-plus"></i> تسجيل حدث جديد
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
