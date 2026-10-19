<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title>حدث خطأ غير متوقع</title>
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.rtl.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/rtl-overrides.css') }}" rel="stylesheet">
</head>
<body>
  <section class="section min-vh-100 d-flex flex-column align-items-center justify-content-center text-center px-3">
    <h1 class="display-4">500</h1>
    <h2>حدث خطأ غير متوقع</h2>
    <p class="text-muted mt-2">تم تسجيل الخطأ وسيتم مراجعته. حاول مرة أخرى، وإذا تكررت المشكلة تواصل مع إدارة النظام.</p>
    <a href="{{ url('/') }}" class="btn btn-primary mt-3">العودة إلى لوحة التحكم</a>
  </section>
</body>
</html>