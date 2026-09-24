@extends('layouts.app')

@section('title', 'متابعة الأحداث')
@section('page-title', 'متابعة الأحداث')
@section('breadcrumbs')
  <li class="breadcrumb-item active">متابعة الأحداث</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body pt-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="card-title m-0 p-0">قائمة متابعات الأحداث</h5>
      @can('incident-followups.create')
        <a href="{{ route('incident-followups.create') }}" class="btn btn-primary">
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