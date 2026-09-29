@extends('layouts.app')

@section('title', 'عرض '.($definition['singular'] ?? ''))
@section('page-title', 'عرض '.($definition['singular'] ?? '').' — '.$definition['title'])
@section('breadcrumbs')
  <li class="breadcrumb-item">البيانات المرجعية</li>
  <li class="breadcrumb-item"><a href="{{ route('admin.'.$type.'.index') }}">{{ $definition['title'] }}</a></li>
  <li class="breadcrumb-item active">#{{ $item->getKey() }}</li>
@endsection

@section('content')
<div class="row">

  <div class="col-lg-7">
    <div class="card"><div class="card-body pt-3">
      <div class="d-flex justify-content-between align-items-center">
        <h5 class="card-title"><i class="bx bx-show text-primary"></i> البيانات</h5>
        @if(PerUser('admin.'.$type.'.edit'))
          <a href="{{ route('admin.'.$type.'.edit', $item->getKey()) }}" class="btn btn-sm btn-outline-primary">
            <i class="bx bx-edit-alt"></i> تعديل
          </a>
        @endif
      </div>

      <table class="table table-borderless mb-0">
        @foreach($definition['fields'] as $field)
          <tr>
            <th class="text-muted" style="width:35%">{{ $field['label'] }}</th>
            <td style="white-space:pre-wrap;word-break:break-word;">@if($field['type'] === 'select'){{ $item->{$field['relation']}?->{$field['display']} ?? '—' }}@else{{ $item->{$field['name']} ?? '—' }}@endif</td>
          </tr>
        @endforeach
        <tr>
          <th class="text-muted">الحالة</th>
          <td>@if($item->validity)<span class="badge bg-success">فعّال</span>@else<span class="badge bg-danger">غير فعّال</span>@endif</td>
        </tr>
        <tr><th class="text-muted">أنشئ بواسطة</th><td>{{ $item->created_by ?? '—' }}</td></tr>
        @if($item->updated_by)
          <tr><th class="text-muted">آخر تعديل بواسطة</th><td>{{ $item->updated_by }}</td></tr>
        @endif
      </table>
    </div></div>
  </div>

  {{-- الاستخدام: بيحدد إذا كان الحذف مسموح --}}
  <div class="col-lg-5">
    <div class="card"><div class="card-body pt-3">
      <h5 class="card-title"><i class="bx bx-link text-primary"></i> الاستخدام في النظام</h5>
      @php $totalUsage = collect($usage)->sum('count'); @endphp

      <ul class="list-group list-group-flush mb-3">
        @forelse($usage as $u)
          <li class="list-group-item px-0 d-flex justify-content-between">
            {{ $u['label'] }}
            <span class="badge {{ $u['count'] ? 'bg-primary' : 'bg-secondary' }}">{{ $u['count'] }}</span>
          </li>
        @empty
          <li class="list-group-item px-0 text-muted">غير مرتبط بجداول أخرى.</li>
        @endforelse
      </ul>

      @if($totalUsage)
        <div class="small text-danger"><i class="bx bx-lock-alt"></i> لا يمكن حذفه لأنه مستخدم — يمكنك إلغاء تفعيله.</div>
      @else
        <div class="small text-success"><i class="bx bx-check-circle"></i> غير مستخدم — يمكن حذفه.</div>
      @endif
    </div></div>
  </div>

</div>

<a href="{{ route('admin.'.$type.'.index') }}" class="btn btn-outline-secondary">
  <i class="bx bx-arrow-back"></i> رجوع للقائمة
</a>
@endsection