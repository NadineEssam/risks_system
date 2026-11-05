{{-- شهية المخاطر: حد لكل مستوى على درجة الخطر (التكرار × الأثر، 1-25) + الإجراء المطلوب --}}
@php
  $currentRisk = $risk ?? null;
  $saved = $currentRisk ? $currentRisk->appetiteDetails->keyBy('threshold_level_id') : collect();
  $defaults = [1 => 1, 2 => 8, 3 => 15]; // نفس التقسيم الحالي للنظام
@endphp

<div class="alert alert-info small mb-3">
  <i class="bx bx-info-circle"></i>
  تُقارن هذه الحدود بـ <strong>درجة خطر الحدث</strong> (التكرار × الأثر، من 1 إلى 25):
  أقل من حد المتوسط = <strong>مقبول</strong> — من حد المتوسط وأقل من حد المرتفع = <strong>متوسط</strong> — من حد المرتفع فأكثر = <strong>مرتفع</strong>.
  يجب أن تكون الحدود تصاعدية (المقبول &lt; المتوسط &lt; المرتفع).
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