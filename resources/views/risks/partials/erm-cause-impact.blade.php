{{-- السبب (اختيار واحد) + الأثر (أكتر من اختيار) — بيُستخدم في الإضافة والتعديل --}}
@php
  $currentRisk      = $risk ?? null;
  $selectedCause    = old('cause_detail_id', $currentRisk?->cause_detail_id);
  $selectedImpacts  = array_map('intval', old('impact_items', $currentRisk ? $currentRisk->impactItems->pluck('id')->all() : []));
@endphp

<div class="row g-3">
  <div class="col-12">
    <label class="form-label">السبب <span class="required-mark">*</span></label>
    <select name="cause_detail_id" class="form-select @error('cause_detail_id') is-invalid @enderror" required>
      <option value="">-- اختر السبب --</option>
      @foreach($causeDetails->groupBy(fn ($d) => $d->causeSubcategory?->subcategory_name) as $group => $items)
        <optgroup label="{{ $group }}">
          @foreach($items as $cause)
            <option value="{{ $cause->id }}" @selected((string) $selectedCause === (string) $cause->id)>
              {{ $cause->detail_code }} — {{ $cause->detail_name }}
            </option>
          @endforeach
        </optgroup>
      @endforeach
    </select>
    @error('cause_detail_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-12 @error('impact_items') wizard-group-invalid @enderror" data-require-checked-group="impact_items[]">
    <label class="form-label">الأثر <span class="required-mark">*</span> <small class="text-muted">(اختر أثراً واحداً على الأقل)</small></label>
    <div class="border rounded p-3" style="max-height:340px;overflow-y:auto;">
      @foreach($impactItems->groupBy(fn ($i) => $i->impactCategory?->category_name) as $group => $items)
        <div class="fw-bold text-primary mt-2 mb-1">{{ $group }}</div>
        <div class="row">
          @foreach($items as $impact)
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="impact_items[]" value="{{ $impact->id }}"
                       id="impact{{ $impact->id }}" @checked(in_array((int) $impact->id, $selectedImpacts, true))>
                <label class="form-check-label" for="impact{{ $impact->id }}">
                  {{ $impact->item_name }}
                  <span class="badge {{ str_starts_with((string) $impact->nature_name, 'مالي') ? 'bg-warning text-dark' : 'bg-secondary' }}">{{ $impact->nature_name }}</span>
                  <small class="text-muted">{{ $impact->item_code }}</small>
                </label>
              </div>
            </div>
          @endforeach
        </div>
      @endforeach
    </div>
    @if($errors->has('impact_items'))
      <div class="text-danger small mt-1 wizard-group-feedback" style="display:block">{{ $errors->first('impact_items') }}</div>
    @else
      <div class="text-danger small mt-1 wizard-group-feedback" style="display:none">يجب اختيار أثر واحد على الأقل.</div>
    @endif
  </div>
</div>