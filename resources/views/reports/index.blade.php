@extends('layouts.app')

@section('title', 'التقارير')
@section('page-title', 'التقارير')
@section('breadcrumbs')
  <li class="breadcrumb-item active">التقارير</li>
@endsection

@section('content')
<div class="row">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">توزيع الأحداث حسب درجة الخطر</h5>
        <div id="degreeChart"></div>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">توزيع الأحداث حسب حالة الحل</h5>
        <div id="statusChart"></div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">أكثر المخاطر المحتملة تكراراً في الأحداث</h5>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead><tr><th>الخطر المحتمل</th><th>عدد القطاعات</th><th>عدد الأحداث</th></tr></thead>
            <tbody>
              @forelse($topRisks as $risk)
                <tr>
                  <td>{{ \Illuminate\Support\Str::limit($risk->risk_description, 45) }}</td>
                  <td>{{ $risk->sector_details_count }}</td>
                  <td><span class="badge bg-primary">{{ $risk->incidents_count }}</span></td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-center text-muted py-3">لا توجد بيانات كافية بعد.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">آخر تجاوزات مؤشرات قياس المخاطر (KRI)</h5>
        <ul class="list-group list-group-flush">
          @forelse($breachedIndicators as $followup)
            <li class="list-group-item px-0">
              <div class="d-flex justify-content-between">
                <strong>{{ \Illuminate\Support\Str::limit($followup->indicator?->indicator_name, 30) }}</strong>
                <span class="badge bg-warning text-dark">{{ $followup->thresholdLevel?->level_name }}</span>
              </div>
              <div class="small text-muted">{{ $followup->measurement_date?->format('Y-m-d') }} — القيمة: {{ $followup->actual_value }}</div>
            </li>
          @empty
            <li class="list-group-item px-0 text-muted small text-center py-3">لا توجد تجاوزات مسجلة.</li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // درجة الخطر: تدرج حالة ثابت (أخضر=مقبول، برتقالي=متوسط، أحمر=مرتفع)
    new ApexCharts(document.querySelector('#degreeChart'), {
      chart: { type: 'bar', height: 300, toolbar: { show: false } },
      series: [{ name: 'عدد الأحداث', data: @json(array_values($degreeBuckets)) }],
      xaxis: { categories: @json(array_keys($degreeBuckets)) },
      colors: ['#2eca6a', '#f6c23e', '#e74a3b'],
      plotOptions: { bar: { distributed: true, borderRadius: 4, columnWidth: '45%' } },
      legend: { show: false },
      dataLabels: { enabled: true },
    }).render();

    // حالة الحل: تصنيفات ثابتة الترتيب لكل حالة
    const statusLabels = @json($statusBreakdown->keys());
    const statusValues = @json($statusBreakdown->values());
    new ApexCharts(document.querySelector('#statusChart'), {
      chart: { type: 'donut', height: 300 },
      series: statusValues.length ? statusValues : [1],
      labels: statusLabels.length ? statusLabels : ['لا توجد بيانات'],
      colors: ['#4154f1', '#ff771d', '#e74a3b', '#7239ea'],
      legend: { position: 'bottom' },
      dataLabels: { enabled: true },
    }).render();
  });
</script>
@endpush
@endsection
