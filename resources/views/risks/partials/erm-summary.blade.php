{{-- عرض تصنيفات ERM على صفحة الخطر --}}
<div class="border-top pt-3 mt-3">
  <p class="mb-2">
    <strong>السبب:</strong>
    @if($risk->causeDetail)
      {{ $risk->causeDetail->causeSubcategory?->subcategory_name }} —
      {{ $risk->causeDetail->detail_name }}
      <span class="badge bg-light text-dark">{{ $risk->causeDetail->detail_code }}</span>
    @else
      <span class="text-muted">—</span>
    @endif
  </p>

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