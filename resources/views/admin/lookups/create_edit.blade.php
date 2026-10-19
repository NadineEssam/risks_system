@extends('layouts.app')

@php $isEdit = isset($item) && $item; @endphp

@section('title', ($isEdit ? 'تعديل ' : 'إضافة ').($definition['singular'] ?? ''))
@section('page-title', ($isEdit ? 'تعديل ' : 'إضافة ').($definition['singular'] ?? '').' — '.$definition['title'])
@section('breadcrumbs')
  <li class="breadcrumb-item">البيانات المرجعية</li>
  <li class="breadcrumb-item"><a href="{{ route('admin.'.$type.'.index') }}">{{ $definition['title'] }}</a></li>
  <li class="breadcrumb-item active">{{ $isEdit ? 'تعديل' : 'إضافة' }}</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body pt-3">
    <h5 class="card-title">
      <i class="bx {{ $isEdit ? 'bx-edit-alt' : 'bx-plus' }} text-primary"></i>
      {{ $isEdit ? 'تعديل' : 'إضافة' }} {{ $definition['singular'] ?? '' }}
    </h5>

    @if(! empty($definition['note']))
      <div class="alert alert-warning small py-2"><i class="bx bx-error"></i> {{ $definition['note'] }}</div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger">
        <strong><i class="bx bx-error"></i> يرجى تصحيح الأخطاء التالية:</strong>
        <ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif

    <form method="POST" action="{{ $isEdit ? route('admin.'.$type.'.update', $item->getKey()) : route('admin.'.$type.'.store') }}">
      @csrf
      @if($isEdit) @method('PUT') @endif

      <div class="row g-3">
        @foreach($definition['fields'] as $field)
          @php
            $name  = $field['name'];
            $value = old($name, $item?->$name);
          @endphp

          <div class="{{ $field['type'] === 'textarea' ? 'col-12' : 'col-md-6' }}">
            <label class="form-label">
              {{ $field['label'] }} @if($field['required'])<span class="text-danger">*</span>@endif
            </label>

            @switch($field['type'])
              @case('select')
                <select name="{{ $name }}" class="form-select @error($name) is-invalid @enderror">
                  <option value="">-- اختر --</option>
                  @foreach($options[$name] as $option)
                    <option value="{{ $option->getKey() }}" @selected((string) $value === (string) $option->getKey())>
                      {{ $option->{$field['display']} }}@if(! $option->validity) (غير فعّال)@endif
                    </option>
                  @endforeach
                </select>
                @break

              @case('textarea')
                <textarea name="{{ $name }}" rows="4" class="form-control @error($name) is-invalid @enderror">{{ $value }}</textarea>
                @break

              @case('number')
                <input type="number" name="{{ $name }}" value="{{ $value }}" class="form-control @error($name) is-invalid @enderror">
                @break

              @default
                <input type="text" name="{{ $name }}" value="{{ $value }}" class="form-control @error($name) is-invalid @enderror">
            @endswitch

            @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        @endforeach

        <div class="col-md-6">
          <label class="form-label">الحالة</label>
          <select name="validity" class="form-select">
            <option value="1" @selected(old('validity', $item?->validity ?? 1) == 1)>فعّال</option>
            <option value="0" @selected(old('validity', $item?->validity ?? 1) == 0)>غير فعّال</option>
          </select>
        </div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button class="btn btn-primary"><i class="bx bx-save"></i> {{ $isEdit ? 'حفظ التعديل' : 'حفظ' }}</button>
        <a href="{{ route('admin.'.$type.'.index') }}" class="btn btn-outline-secondary"><i class="bx bx-arrow-back"></i> رجوع</a>
      </div>
    </form>
  </div>
</div>
@endsection