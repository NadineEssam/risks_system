<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title>انتهت الجلسة</title>
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.rtl.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/rtl-overrides.css') }}" rel="stylesheet">
</head>
<body>
  <section class="section min-vh-100 d-flex flex-column align-items-center justify-content-center text-center px-3">
    <h1 class="display-4">419</h1>
    <h2>انتهت صلاحية الصفحة</h2>
    <p class="text-muted mt-2">انتهت الجلسة بسبب عدم النشاط. أعد تحميل الصفحة وحاول مرة أخرى.</p>
    <a href="{{ url()->previous() }}" class="btn btn-primary mt-3">الرجوع للصفحة السابقة</a>
  </section>
</body>
</html>