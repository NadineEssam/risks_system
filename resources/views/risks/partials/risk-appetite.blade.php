{{-- شهية المخاطر: حد لكل مستوى على درجة الخطر (التكرار × الأثر، 1-25) + الإجراء المطلوب --}}
@php
  $currentRisk = $risk ?? null;
  $saved = $currentRisk ? $currentRisk->appetiteDetails->keyBy('threshold_level_id') : collect();
  $defaults = []; // نسب مئوية — بيدخلها المستخدم
@endphp

<div class="row g-3 mb-3">
  <div class="col-md-4">
    <label class="form-label">طبيعة شهية المخاطر <span class="required-mark">*</span></label>
    <select name="appetite_nature_id" class="form-select @error('appetite_nature_id') is-invalid @enderror" required>
      <option value="">-- اختر --</option>
      @foreach($indicatorNatures as $nature)
        <option value="{{ $nature->id }}" @selected((string) old('appetite_nature_id', $currentRisk?->appetite_nature_id) === (string) $nature->id)>
          {{ $nature->nature_name }}
        </option>
      @endforeach
    </select>
    @error('appetite_nature_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="alert alert-info small mb-3">
  <i class="bx bx-info-circle"></i>
  حدود شهية المخاطر <strong>نسبة مئوية من 0% إلى 100%</strong>:<br>
  <strong>متزايد:</strong> المقبول &lt; المتوسط &lt; المرتفع.<br>
  <strong>متناقص:</strong> المقبول &gt; المتوسط &gt; المرتفع.
</div>

@error('appetite')
  <div class="alert alert-danger is-invalid"><i class="bx bx-error"></i> {{ $message }}</div>
@enderror

<div class="row g-3">
  @foreach($thresholdLevels as $i => $level)
    @php $row = $saved[$level->id] ?? null; @endphp
    <input type="hidden" name="appetite[{{ $i }}][threshold_level_id]" value="{{ $level->id }}">
    <div class="col-md-4">
      <div class="border rounded p-3 h-100">
        <label class="form-label fw-bold">{!! $level->badge() !!}</label>
        <div class="input-group mb-2">
          <input type="number" min="0" max="100" step="0.01" required
                 name="appetite[{{ $i }}][threshold_value]"
                 class="form-control @error("appetite.$i.threshold_value") is-invalid @enderror"
                 placeholder="النسبة (0 - 100)"
                 value="{{ old("appetite.$i.threshold_value", $row ? rtrim(rtrim(number_format((float) $row->threshold_value, 2, '.', ''), '0'), '.') : ($defaults[(int) $level->sort_order] ?? '')) }}">
          <span class="input-group-text">%</span>
        </div>
        @error("appetite.$i.threshold_value")<div class="invalid-feedback">{{ $message }}</div>@enderror
        <textarea name="appetite[{{ $i }}][required_action]" rows="2" class="form-control"
                  placeholder="الإجراء المطلوب عند الوصول لهذا المستوى">{{ old("appetite.$i.required_action", $row?->required_action) }}</textarea>
      </div>
    </div>
  @endforeach
</div>