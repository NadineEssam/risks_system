<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title>غير مصرح بالوصول</title>
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.rtl.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/rtl-overrides.css') }}" rel="stylesheet">
</head>
<body>
  <section class="section min-vh-100 d-flex flex-column align-items-center justify-content-center">
    <h1 class="display-4">403</h1>
    <h2>ليس لديك صلاحية للوصول لهذه الصفحة</h2>
    <p class="text-muted">{{ $exception->getMessage() ?: 'برجاء التواصل مع مسئول النظام إذا كنت تعتقد أن هذا خطأ.' }}</p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3">العودة إلى لوحة التحكم</a>
  </section>
</body>
</html>
