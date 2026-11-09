{{-- تصنيف الضوابط الرقابية (أكتر من اختيار) — بيُستخدم في الإضافة والتعديل --}}
@php
  $currentRisk       = $risk ?? null;
  $selectedControls  = array_map('intval', old('control_categories', $currentRisk ? $currentRisk->controlCategories->pluck('id')->all() : []));
@endphp

<div class="col-12">
  <label class="form-label">تصنيف الضوابط الرقابية <small class="text-muted">(يمكن اختيار أكثر من ضابط)</small></label>
  <div class="border rounded p-3 @error('control_categories') border-danger @enderror" style="max-height:340px;overflow-y:auto;">
    @foreach($controlCategories->groupBy(fn ($c) => $c->controlDomain?->domain_name) as $group => $items)
      <div class="fw-bold text-primary mt-2 mb-1">{{ $group }}</div>
      <div class="row">
        @foreach($items as $control)
          <div class="col-md-6">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="control_categories[]" value="{{ $control->id }}"
                     id="control{{ $control->id }}" @checked(in_array((int) $control->id, $selectedControls, true))>
              <label class="form-check-label" for="control{{ $control->id }}">
                {{ $control->category_name }} <small class="text-muted">{{ $control->category_code }}</small>
              </label>
            </div>
          </div>
        @endforeach
      </div>
    @endforeach
  </div>
  @error('control_categories')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</div>