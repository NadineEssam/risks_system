{{-- الأسباب (أكتر من سبب + نسبة لكل سبب، المجموع 100%) + الأثر (أكتر من اختيار) --}}
@php
  $currentRisk     = $risk ?? null;
  $selectedImpacts = array_map('intval', old('impact_items', $currentRisk ? $currentRisk->impactItems->pluck('id')->all() : []));
  $causeGroups     = $causeDetails->groupBy(fn ($d) => $d->causeSubcategory?->subcategory_name);
  $fmtPct          = fn ($v) => $v === null || $v === '' ? '' : rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.');

  $causeRows = old('causes', $currentRisk
      ? $currentRisk->causeDetails->map(fn ($c) => ['cause_detail_id' => $c->id, 'percentage' => $fmtPct($c->pivot->percentage)])->values()->all()
      : []);
  if (empty($causeRows)) { $causeRows = [['cause_detail_id' => null, 'percentage' => 100]]; }
@endphp

<div class="row g-3">
  {{-- ===== الأسباب ===== --}}
  <div class="col-12">
    <label class="form-label">
      الأسباب <span class="required-mark">*</span>
      <small class="text-muted">(سبب واحد أو أكثر — مجموع النسب يجب أن يساوي 100%)</small>
    </label>

    <div id="causes-wrapper">
      @foreach($causeRows as $i => $row)
        <div class="row g-2 mb-2 cause-row">
          <div class="col-md-8">
            <select name="causes[{{ $i }}][cause_detail_id]" class="form-select @error("causes.$i.cause_detail_id") is-invalid @enderror" required>
              <option value="">-- اختر السبب --</option>
              @foreach($causeGroups as $group => $items)
                <optgroup label="{{ $group }}">
                  @foreach($items as $cause)
                    <option value="{{ $cause->id }}" @selected((string) ($row['cause_detail_id'] ?? '') === (string) $cause->id)>
                      {{ $cause->detail_code }} — {{ $cause->detail_name }}
                    </option>
                  @endforeach
                </optgroup>
              @endforeach
            </select>
            @error("causes.$i.cause_detail_id")<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <div class="input-group">
              <input type="number" name="causes[{{ $i }}][percentage]" class="form-control cause-percentage @error("causes.$i.percentage") is-invalid @enderror"
                     min="0.01" max="100" step="0.01" required placeholder="النسبة" value="{{ $row['percentage'] ?? '' }}">
              <span class="input-group-text">%</span>
            </div>
            @error("causes.$i.percentage")<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-1 d-flex align-items-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-cause {{ $loop->first ? 'invisible' : '' }}" title="حذف السبب">
              <i class="bx bx-trash"></i>
            </button>
          </div>
        </div>
      @endforeach
    </div>

    <div class="d-flex justify-content-between align-items-center mt-1">
      <button type="button" id="add-cause" class="btn btn-sm btn-outline-primary"><i class="bx bx-plus"></i> إضافة سبب آخر</button>
      <span>مجموع النسب: <strong id="causes-total">0</strong>%</span>
    </div>
    @error('causes')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- ===== الأثر ===== --}}
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

@push('scripts')
<script>
  // الأسباب: إضافة/حذف صف + مجموع النسب لحظياً (أخضر = 100%)
  (function () {
    const wrapper = document.getElementById('causes-wrapper');
    if (! wrapper) return;
    const totalEl = document.getElementById('causes-total');
    let index = wrapper.querySelectorAll('.cause-row').length;

    function refreshTotal() {
      let total = 0;
      wrapper.querySelectorAll('.cause-percentage').forEach(i => total += parseFloat(i.value) || 0);
      total = Math.round(total * 100) / 100;
      totalEl.textContent = total;
      totalEl.className = total === 100 ? 'text-success' : 'text-danger';
    }

    document.getElementById('add-cause').addEventListener('click', () => {
      const row = wrapper.querySelector('.cause-row').cloneNode(true);
      row.querySelectorAll('select, input').forEach(el => {
        el.name = el.name.replace(/\[\d+\]/, `[${index}]`);
        el.value = '';
        el.classList.remove('is-invalid');
      });
      row.querySelectorAll('.invalid-feedback, .text-danger.small, .server-error').forEach(el => el.remove());
      row.querySelector('.remove-cause').classList.remove('invisible');
      wrapper.appendChild(row);
      index++;
      refreshTotal();
    });

    wrapper.addEventListener('click', (e) => {
      const btn = e.target.closest('.remove-cause');
      if (btn && wrapper.querySelectorAll('.cause-row').length > 1) {
        btn.closest('.cause-row').remove();
        refreshTotal();
      }
    });

    wrapper.addEventListener('input', refreshTotal);
    refreshTotal();
  })();
</script>
@endpush