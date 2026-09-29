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
  <h2>{{ $report->label() }}</h2>
  <div class="meta">
    جهاز تنمية المشروعات المتوسطة والصغيرة ومتناهية الصغر — نظام إدارة المخاطر التشغيلية
    — تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }} — عدد السجلات: {{ $results->count() }}
    @if(! empty($filters['date_from']) || ! empty($filters['date_to']))
      — الفترة: {{ $filters['date_from'] ?? '...' }} إلى {{ $filters['date_to'] ?? '...' }}
    @endif
  </div>

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
</body>
</html>