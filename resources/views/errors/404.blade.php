<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title>الصفحة غير موجودة</title>
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.rtl.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/rtl-overrides.css') }}" rel="stylesheet">
</head>
<body>
  <section class="section min-vh-100 d-flex flex-column align-items-center justify-content-center">
    <h1 class="display-4">404</h1>
    <h2>الصفحة المطلوبة غير موجودة</h2>
    <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3">العودة إلى لوحة التحكم</a>
  </section>
</body>
</html>
