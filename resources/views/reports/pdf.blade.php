<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: dejavusans, sans-serif; font-size: 10px; color: #1a1a1a; direction: rtl; }
    h2 { font-size: 15px; margin: 0 0 4px 0; color: #012970; }
    .meta { color: #666; font-size: 9px; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; }
    thead th { background: #1E3A5F; color: #fff; padding: 6px 5px; font-size: 9px; text-align: center; }
    tbody td { padding: 5px; border-bottom: 1px solid #e5e5e5; text-align: center; vertical-align: top; }
    tbody tr:nth-child(even) td { background: #f7f8fa; }
  </style>
</head>
<body>
  {{-- الهيدر: اللوجو وتحته اسم الجهاز والنظام --}}
  <div style="text-align:center; margin-bottom:10px;">
    <img src="{{ public_path('msmeda_logo_web.png') }}" style="height:60px;">
    <div style="font-size:11px; color:#012970; margin-top:4px;">
      جهاز تنمية المشروعات المتوسطة والصغيرة ومتناهية الصغر — نظام إدارة المخاطر التشغيلية
    </div>
  </div>

  <h2 style="text-align:center;">{{ $report->label() }}</h2>
  @if(! empty($filters['date_from']) || ! empty($filters['date_to']))
    <div class="meta" style="text-align:center;">
      الفترة: {{ $filters['date_from'] ?? '...' }} إلى {{ $filters['date_to'] ?? '...' }}
    </div>
  @endif

  <table>
    <thead>
      <tr>@foreach($report->headings() as $heading)<th>{{ $heading }}</th>@endforeach</tr>
    </thead>
    <tbody>
      @forelse($results as $row)
        <tr>@foreach($report->map($row) as $cell)<td>{{ $cell }}</td>@endforeach</tr>
      @empty
        <tr><td colspan="{{ count($report->headings()) }}">لا توجد بيانات</td></tr>
      @endforelse
    </tbody>
  </table>

  {{-- تحت التقرير: تاريخ الطباعة + عدد السجلات --}}
  <div class="meta" style="margin-top:10px;">
    تاريخ الطباعة: {{ now()->format('d-m-Y H:i') }} — عدد السجلات: {{ $results->count() }}
  </div>
</body>
</html>