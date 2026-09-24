@extends('layouts.app')

@section('title', 'سجل المخاطر المحتملة')
@section('page-title', 'سجل المخاطر المحتملة')
@section('breadcrumbs')
  <li class="breadcrumb-item active">سجل المخاطر المحتملة</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body pt-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="card-title m-0 p-0">قائمة المخاطر المحتملة</h5>
      @can('risks.create')
        <a href="{{ route('risks.create') }}" class="btn btn-primary">
          <i class="bx bx-plus"></i> تسجيل خطر محتمل جديد
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