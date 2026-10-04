@extends('layouts.app')

@section('title', $report->label())
@section('page-title', $report->label())
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">التقارير</a></li>
  <li class="breadcrumb-item active">{{ $report->label() }}</li>
@endsection

@php
  // روابط التصدير بنفس الفلاتر
  $exportUrl = fn ($format) => route('reports.export', array_merge(['key' => $report->key()], $filters, ['format' => $format]));
@endphp

@section('content')
{{-- الفلاتر --}}
<div class="card">
  <div class="card-body pt-3">
    <h5 class="card-title"><i class="bx bx-filter-alt text-primary"></i> الفلاتر</h5>
    <p class="small text-muted">{{ $report->description() }}</p>

    <form method="GET" action="{{ route('reports.show', $report->key()) }}" class="row g-3 align-items-end">
      <input type="hidden" name="run" value="1">

      @foreach($report->filters() as $filter)
        <div class="col-md-3">
          <label class="form-label">{{ $filter['label'] }}</label>
          @if($filter['type'] === 'select')
            <select name="{{ $filter['name'] }}" class="form-select @error($filter['name']) is-invalid @enderror">
              <option value="">الكل</option>
              @foreach($filter['options'] ?? [] as $value => $text)
                <option value="{{ $value }}" @selected((string) ($filters[$filter['name']] ?? '') === (string) $value)>{{ $text }}</option>
              @endforeach
            </select>
          @else
            <input type="date" name="{{ $filter['name'] }}" value="{{ $filters[$filter['name']] ?? '' }}"
                   class="form-control @error($filter['name']) is-invalid @enderror">
          @endif
          @error($filter['name'])<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      @endforeach

      <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-primary"><i class="bx bx-search"></i> عرض التقرير</button>
        <a href="{{ route('reports.show', $report->key()) }}" class="btn btn-outline-secondary" title="مسح الفلاتر"><i class="bx bx-reset"></i></a>
      </div>
    </form>
  </div>
</div>

{{-- النتائج --}}
@if($results !== null)
<div class="card">
  <div class="card-body pt-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
      <h5 class="card-title m-0 p-0">
        النتائج <span class="badge bg-primary">{{ $results->count() }}</span>
      </h5>
      <div class="d-flex gap-2">
        <a href="{{ $exportUrl('xlsx') }}" class="btn btn-sm btn-success"><i class="bx bxs-file-export"></i> Excel</a>
        <a href="{{ $exportUrl('pdf') }}" class="btn btn-sm btn-danger"><i class="bx bxs-file-pdf"></i> PDF</a>
      </div>
    </div>

    <div class="table-responsive">
      <table id="report-table" class="table text-center align-middle datatable-custom" style="width:100%">
        <thead>
          <tr>@foreach($report->headings() as $heading)<th>{{ $heading }}</th>@endforeach</tr>
        </thead>
        <tbody>
          @foreach($results as $row)
            <tr>@foreach($report->map($row) as $cell)<td style="white-space:pre-wrap;">{{ $cell }}</td>@endforeach</tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif
@endsection

@push('scripts')
@if($results !== null)
<script>
  // بحث وترقيم بالعربي على الصفحة (البيانات نفسها من السيرفر)
  $('#report-table').DataTable({
    order: [],
    pageLength: 25,
    lengthMenu: [10, 25, 50, 100],
    language: @json(\App\DataTables\BaseDataTable::ARABIC),
  });
</script>
@endif
@endpush