@extends('layouts.app')

@section('title', 'مؤشرات قياس المخاطر')
@section('page-title', 'مؤشرات قياس المخاطر (KRI)')
@section('breadcrumbs')
  <li class="breadcrumb-item active">مؤشرات قياس المخاطر</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body pt-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="card-title m-0 p-0">قائمة مؤشرات قياس المخاطر (KRI)</h5>
      @can('indicators.create')
        <a href="{{ route('indicators.create') }}" class="btn btn-primary">
          <i class="bx bx-plus"></i> إضافة مؤشر جديد
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
