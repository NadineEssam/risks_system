@extends('layouts.app')

@section('title', 'لوحة التحكم')
@section('page-title', 'لوحة التحكم')

@section('content')
<style>
  /* ===== تصميم رسمي كلاسيكي (محصور جوه .risk-dashboard) ===== */
  .risk-dashboard {
    --navy: #012970; --steel: #1e3a5f; --blue: #4a6fa5; --teal: #2a9d8f;
    --gold: #b8860b; --burgundy: #8b2c2c; --gray: #6c757d; --border: #dee4ec; --bg: #f6f8fb;
  }
  .risk-dashboard, .risk-dashboard * { font-family: 'Cairo', 'Tahoma', sans-serif; }
  .risk-dashboard i.bi { font-family: 'bootstrap-icons' !important; }

  .risk-dashboard .card {
    border: 1px solid var(--border) !important; border-radius: 8px !important;
    box-shadow: 0 1px 3px rgba(1, 41, 112, .06); background: #fff;
  }
  .risk-dashboard .card-title { font-size: 14px; font-weight: 600; color: var(--gray); padding: 0; }

  .risk-dashboard .form-label { font-size: 13px; color: var(--steel); }
  .risk-dashboard .form-control-lg { border-radius: 6px; border: 1px solid var(--border); font-size: 15px; }
  .risk-dashboard .btn-lg { border-radius: 6px; }

  /* KPI: خط علوي ملون + أيقونة بخلفية فاتحة */
  .risk-dashboard .kpi-row { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 1rem; }
  @media (max-width: 1200px) { .risk-dashboard .kpi-row { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
  @media (max-width: 768px)  { .risk-dashboard .kpi-row { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
  @media (max-width: 480px)  { .risk-dashboard .kpi-row { grid-template-columns: 1fr; } }
  .risk-dashboard .kpi-card { border-top: 4px solid var(--kpi-color) !important; }
  .risk-dashboard .kpi-card .card-body { padding: 18px 20px; }

  .risk-dashboard .card-icon {
    width: 48px; height: 48px; min-width: 48px; border-radius: 8px; display: flex;
    align-items: center; justify-content: center; font-size: 22px;
    color: var(--kpi-color); background: var(--kpi-bg);
  }
  .risk-dashboard .bg-primary-gradient { --kpi-color: #012970; --kpi-bg: #e8edf6; }
  .risk-dashboard .bg-info-gradient    { --kpi-color: #4a6fa5; --kpi-bg: #edf2f9; }
  .risk-dashboard .bg-warning-gradient { --kpi-color: #b8860b; --kpi-bg: #f9f3e3; }
  .risk-dashboard .bg-success-gradient { --kpi-color: #2a9d8f; --kpi-bg: #e6f4f2; }
  .risk-dashboard .bg-danger-gradient  { --kpi-color: #8b2c2c; --kpi-bg: #f6e9e9; }

  .risk-dashboard .counter { font-size: 28px; font-weight: 700; color: var(--navy); margin: 0; }
  .risk-dashboard .counter-label { color: #8a94a6; font-size: 12px; }

  /* رؤوس الأقسام */
  .risk-dashboard .custom-header {
    padding: 14px 20px; font-size: 15px; font-weight: 700; color: var(--navy);
    background: #fafbfd; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
  }
  .risk-dashboard .custom-header i { color: var(--blue); font-size: 17px; }
  .risk-dashboard .chart-box { padding: 12px; }
  .risk-dashboard h4.fw-bold { font-size: 16px; color: var(--navy); }
  .risk-dashboard .round-icon {
    width: 42px; height: 42px; border-radius: 8px; display: flex; align-items: center;
    justify-content: center; font-size: 19px;
  }
</style>

<div class="risk-dashboard">

  {{-- ===== فلتر التاريخ (زي الشكاوى) ===== --}}
  <div class="card mb-4">
    <div class="card-body pt-4">
      <form method="GET" action="{{ route('dashboard') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label fw-bold">من تاريخ</label>
          <input type="date" name="from" value="{{ $from }}" max="{{ now()->toDateString() }}" class="form-control form-control-lg">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-bold">إلى تاريخ</label>
          <input type="date" name="to" value="{{ $to }}" max="{{ now()->toDateString() }}" class="form-control form-control-lg">
        </div>
        <div class="col-md-4 d-flex gap-2">
          <button class="btn btn-primary btn-lg w-100"><i class="bi bi-funnel"></i> فلترة</button>
          <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg w-100">إعادة ضبط</a>
        </div>
      </form>
    </div>
  </div>

  {{-- ===== KPI (5 كروت) ===== --}}
  @php
    $kpiCards = [
      ['إجمالي الأحداث', $kpis['total'],      'كل الأحداث المسجلة',          'bg-primary-gradient', 'bi-collection'],
      ['جديدة',          $kpis['new'],        'بدون أي متابعة',              'bg-info-gradient',    'bi-plus-circle'],
      ['جارى المتابعة',  $kpis['processing'], 'تحتاج متابعة',                'bg-warning-gradient', 'bi-hourglass-split'],
      ['تم الحل',        $kpis['solved'],     'أحداث تم حلها',               'bg-success-gradient', 'bi-check-circle'],
      ['مغلقة',          $kpis['closed'],     'إغلاق / قبول الخطر',          'bg-danger-gradient',  'bi-lock'],
    ];
  @endphp
  <div class="kpi-row mb-4">
    @foreach($kpiCards as [$title, $value, $label, $gradient, $icon])
      <div class="card kpi-card h-100 mb-0 {{ $gradient }}">
        <div class="card-body d-flex align-items-center justify-content-between">
          <div>
            <div class="card-title mb-2">{{ $title }}</div>
            <h2 class="counter">{{ $value }}</h2>
            <div class="counter-label">{{ $label }}</div>
          </div>
          <div class="card-icon {{ $gradient }}"><i class="bi {{ $icon }}"></i></div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- ===== الحالة + التصنيف (يسار) / الدرجة + المستويات (يمين) ===== --}}
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card mb-4">
        <div class="custom-header"><span>📈 الأحداث حسب آخر متابعة</span><i class="bi bi-graph-up"></i></div>
        <div class="card-body chart-box"><div id="statusChart"></div></div>
      </div>
      <div class="card mb-4">
        <div class="custom-header"><span>📊 الأحداث حسب تصنيف بازل العام</span><i class="bi bi-bar-chart-fill"></i></div>
        <div class="card-body chart-box"><div id="typeChart"></div></div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card mb-4">
        <div class="custom-header"><span>🎯 الأحداث حسب درجة الخطر</span><i class="bi bi-pie-chart-fill"></i></div>
        <div class="card-body chart-box"><div id="degreeChart"></div></div>
      </div>
      <div class="card mb-4">
        <div class="custom-header"><span>📌 قياسات المؤشرات حسب المستوى</span><i class="bi bi-ui-checks-grid"></i></div>
        <div class="card-body chart-box"><div id="levelChart"></div></div>
      </div>
    </div>
  </div>

  {{-- ===== حالة الحل ===== --}}
  <div class="card mb-4">
    <div class="custom-header"><span>📡 الأحداث حسب حالة الحل</span><i class="bi bi-broadcast"></i></div>
    <div class="card-body chart-box"><div id="resolutionChart"></div></div>
  </div>

  {{-- ===== الاتجاه الشهري / القطاعات / الإدارات ===== --}}
  @foreach([
        ['trendChart',      'الاتجاه الشهري للأحداث',     'عدد الأحداث المكتشفة في كل شهر',     '#e8edf6', '#012970', 'bi-calendar3'],
    ['sectorChart',     'الأحداث حسب القطاعات',       'توزيع الأحداث على القطاعات',          '#e8edf6', '#012970', 'bi-diagram-3'],
    ['departmentChart', 'الأحداث حسب الإدارات',        'أعلى 15 إدارة في عدد الأحداث',        '#e8edf6', '#012970', 'bi-building'],
  ] as [$id, $title, $subtitle, $bg, $color, $icon])
    <div class="card mb-4">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h4 class="mb-1 fw-bold">{{ $title }}</h4>
            <p class="text-muted mb-0 small">{{ $subtitle }}</p>
          </div>
          <div class="round-icon" style="background:{{ $bg }};color:{{ $color }};"><i class="bi {{ $icon }}"></i></div>
        </div>
        <div id="{{ $id }}"></div>
      </div>
    </div>
  @endforeach

</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // ===== عدّاد متحرك (زي الشكاوى) =====
    document.querySelectorAll('.risk-dashboard .counter').forEach(el => {
      const end = parseInt(el.innerText) || 0;
      let current = 0;
      const step = Math.max(1, Math.ceil(end / 40));
      const timer = setInterval(() => {
        current += step;
        if (current >= end) { el.innerText = end; clearInterval(timer); } else { el.innerText = current; }
      }, 20);
    });

    // ===== البيانات =====
    const data = {
      status:     @json($byStatus),
      type:       @json($byEventType),
      degree:     @json($byDegree),
      level:      @json($byLevel),
      resolution: @json($byResolution),
      month:      @json($byMonth),
      sector:     @json($bySector),
      department: @json($byDepartment),
    };
    const font = 'Cairo, sans-serif';
    const keys = o => Object.keys(o);
    const vals = o => Object.values(o);

    // دونات بنفس إعدادات الشكاوى (لو مفيش بيانات يظهر "لا توجد بيانات")
    function donut(selector, obj, colors) {
      const empty = vals(obj).reduce((a, b) => a + b, 0) === 0;
      new ApexCharts(document.querySelector(selector), {
        series: empty ? [1] : vals(obj),
        labels: empty ? ['لا توجد بيانات'] : keys(obj),
        colors: empty ? ['#e5e7eb'] : colors,
        chart: { type: 'donut', height: 340, fontFamily: font },
        legend: { position: 'bottom' },
        dataLabels: { enabled: ! empty },
        tooltip: { enabled: ! empty },
        plotOptions: { pie: { donut: { size: '72%', labels: { show: true, total: {
          show: true, label: 'الإجمالي',
          formatter: () => empty ? 0 : vals(obj).reduce((a, b) => a + b, 0),
        } } } } },
      }).render();
    }

    // شريط أفقي بنفس إعدادات "حسب القطاعات" في الشكاوى
    function hbar(selector, obj, color, height) {
      new ApexCharts(document.querySelector(selector), {
        series: [{ name: 'عدد الأحداث', data: vals(obj) }],
        chart: { type: 'bar', height: Math.max(height, keys(obj).length * 45), toolbar: { show: false }, fontFamily: font },
        colors: [color],
        grid: { borderColor: '#e5e7eb', strokeDashArray: 4, xaxis: { lines: { show: true } }, yaxis: { lines: { show: false } } },
        plotOptions: { bar: { horizontal: true, borderRadius: 14, borderRadiusApplication: 'end', barHeight: '58%', distributed: true } },
        dataLabels: { enabled: true, style: { fontSize: '13px', fontWeight: '700', colors: ['#111827'] } },
        xaxis: { categories: keys(obj), labels: { style: { colors: '#6b7280', fontSize: '12px' } } },
        yaxis: { labels: { maxWidth: 320, style: { fontSize: '13px', fontWeight: 600, colors: '#0f172a' } } },
        legend: { show: false },
        noData: { text: 'لا توجد بيانات' },
      }).render();
    }

    // 📈 الحالة (area)
    new ApexCharts(document.querySelector('#statusChart'), {
      series: [{ name: 'عدد الأحداث', data: vals(data.status) }],
      chart: { type: 'area', height: 350, toolbar: { show: false }, fontFamily: font },
      colors: ['#00c896'],
      stroke: { curve: 'smooth', width: 4 },
      fill: { type: 'gradient', gradient: { opacityFrom: .5, opacityTo: .05 } },
      dataLabels: { enabled: false },
      xaxis: { categories: keys(data.status) },
    }).render();

    // 📊 تصنيف بازل (bar)
    new ApexCharts(document.querySelector('#typeChart'), {
      series: [{ name: 'عدد الأحداث', data: vals(data.type) }],
      chart: { type: 'bar', height: 360, toolbar: { show: false }, fontFamily: font },
      colors: ['#5b8cff'],
      plotOptions: { bar: { borderRadius: 10, columnWidth: '45%' } },
      dataLabels: { enabled: false },
      grid: { borderColor: '#f1f1f1' },
      xaxis: { categories: keys(data.type) },
      noData: { text: 'لا توجد بيانات' },
    }).render();

    // 🎯 درجة الخطر — 📌 مستويات المؤشرات — 📡 حالة الحل
        // ألوان رسمية: أخضر مؤسسي / ذهبي / عنابي للمستويات — كحلي ودرجاته للباقي
    donut('#degreeChart', data.degree, ['#2a9d8f', '#b8860b', '#8b2c2c']);
    donut('#levelChart', data.level, ['#2a9d8f', '#b8860b', '#8b2c2c', '#6c757d']);
    donut('#resolutionChart', data.resolution, ['#012970', '#4a6fa5', '#2a9d8f', '#b8860b', '#8b2c2c', '#6c757d']);

    // 📅 الاتجاه الشهري (area بنفس "نوع النشاط")
    new ApexCharts(document.querySelector('#trendChart'), {
      series: [{ name: 'عدد الأحداث', data: vals(data.month) }],
      chart: { type: 'area', height: 350, toolbar: { show: false }, fontFamily: font },
      colors: ['#6366f1'],
      stroke: { curve: 'smooth', width: 4 },
      fill: { type: 'gradient', gradient: { opacityFrom: .5, opacityTo: .05 } },
      dataLabels: { enabled: false },
      xaxis: { categories: keys(data.month) },
      noData: { text: 'لا توجد بيانات' },
    }).render();

    // 🏛️ القطاعات — 🏢 الإدارات
    hbar('#sectorChart', data.sector, '#012970', 420);
    hbar('#departmentChart', data.department, '#4a6fa5', 480);
  });
</script>
@endpush