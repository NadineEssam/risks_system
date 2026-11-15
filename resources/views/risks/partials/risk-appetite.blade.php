{{-- شهية المخاطر: حد لكل مستوى على درجة الخطر (التكرار × الأثر، 1-25) + الإجراء المطلوب --}}
@php
  $currentRisk = $risk ?? null;
  $saved = $currentRisk ? $currentRisk->appetiteDetails->keyBy('threshold_level_id') : collect();
  $defaults = [1 => 1, 2 => 8, 3 => 15]; // نفس التقسيم الحالي للنظام
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
  تُقارن هذه الحدود بـ <strong>درجة خطر الحدث</strong> (التكرار × الأثر، من 1 إلى 25):<br>
  <strong>متزايد:</strong> المقبول &lt; المتوسط &lt; المرتفع — من حد المرتفع فأكثر = مرتفع.<br>
  <strong>متناقص:</strong> المقبول &gt; المتوسط &gt; المرتفع — من حد المرتفع فأقل = مرتفع.
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
        <input type="number" min="1" max="25" step="1" required
               name="appetite[{{ $i }}][threshold_value]"
               class="form-control mb-2 @error("appetite.$i.threshold_value") is-invalid @enderror"
               placeholder="القيمة الحدية (1-25)"
               value="{{ old("appetite.$i.threshold_value", $row?->threshold_value ?? ($defaults[(int) $level->sort_order] ?? '')) }}">
        @error("appetite.$i.threshold_value")<div class="invalid-feedback">{{ $message }}</div>@enderror
        <textarea name="appetite[{{ $i }}][required_action]" rows="2" class="form-control"
                  placeholder="الإجراء المطلوب عند الوصول لهذا المستوى">{{ old("appetite.$i.required_action", $row?->required_action) }}</textarea>
      </div>
    </div>
  @endforeach
</div>