{{-- عرض تصنيفات ERM على صفحة الخطر --}}
<div class="border-top pt-3 mt-3">
  <p class="mb-1"><strong>الأسباب ونسبة مساهمة كل سبب:</strong></p>
  <div class="mb-3">
    @forelse($risk->causeDetails->sortByDesc('pivot.percentage') as $cause)
      @php $pct = (float) $cause->pivot->percentage; @endphp
      <div class="mb-2">
        <div class="d-flex justify-content-between small">
          <span>
            {{ $cause->causeSubcategory?->subcategory_name }} — {{ $cause->detail_name }}
            <span class="text-muted">({{ $cause->detail_code }})</span>
          </span>
          <strong>{{ rtrim(rtrim(number_format($pct, 2, '.', ''), '0'), '.') }}%</strong>
        </div>
        <div class="progress" style="height:6px;">
          <div class="progress-bar" role="progressbar" style="width: {{ $pct }}%"></div>
        </div>
      </div>
    @empty
      <span class="text-muted">—</span>
    @endforelse
  </div>

  <p class="mb-1"><strong>الأثر:</strong></p>
  <div class="d-flex flex-wrap gap-2 mb-3">
    @forelse($risk->impactItems as $impact)
      <span class="badge bg-light text-dark border" style="font-size:13px;white-space:normal;">
        {{ $impact->impactCategory?->category_name }} — {{ $impact->item_name }}
        <span class="text-muted">({{ $impact->nature_name }} · {{ $impact->item_code }})</span>
      </span>
    @empty
      <span class="text-muted">—</span>
    @endforelse
  </div>

  <p class="mb-1"><strong>تصنيف الضوابط الرقابية:</strong></p>
  <div class="d-flex flex-wrap gap-2">
    @forelse($risk->controlCategories as $control)
      <span class="badge bg-light text-dark border" style="font-size:13px;white-space:normal;">
        {{ $control->controlDomain?->domain_name }} — {{ $control->category_name }}
        <span class="text-muted">({{ $control->category_code }})</span>
      </span>
    @empty
      <span class="text-muted">—</span>
    @endforelse
  </div>
</div>