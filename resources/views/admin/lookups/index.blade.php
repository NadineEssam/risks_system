@extends('layouts.app')

@section('title', $definition['title'])
@section('page-title', $definition['title'])
@section('breadcrumbs')
  <li class="breadcrumb-item">البيانات المرجعية</li>
  <li class="breadcrumb-item active">{{ $definition['title'] }}</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body pt-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="card-title m-0 p-0">قائمة {{ $definition['title'] }}</h5>
      @if(PerUser('admin.'.$type.'.create'))
        <a href="{{ route('admin.'.$type.'.create') }}" class="btn btn-primary">
          <i class="bx bx-plus"></i> إضافة {{ $definition['singular'] ?? '' }}
        </a>
      @endif
    </div>

    @if(! empty($definition['note']))
      <div class="alert alert-warning small py-2">
        <i class="bx bx-error"></i> {{ $definition['note'] }}
      </div>
    @endif

    <div class="table-responsive">
      {{ $dataTable->table(['class' => 'table text-center align-middle datatable-custom', 'style' => 'width:100%']) }}
    </div>

  </div>
</div>
@endsection

@push('scripts')
  {{ $dataTable->scripts() }}
@endpush