<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'نظام إدارة المخاطر')</title>

  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files (نسخة RTL من Bootstrap) -->
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.rtl.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/datatable.css') }}" rel="stylesheet">

  <!-- Template Main CSS File + تجاوزات RTL -->
  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/rtl-overrides.css') }}" rel="stylesheet">

  @stack('styles')
</head>

<body>

  @include('layouts.partials.header')
  @include('layouts.partials.sidebar')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>@yield('page-title', 'لوحة التحكم')</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
          @yield('breadcrumbs')
        </ol>
      </nav>
    </div><!-- End Page Title -->

    

    <section class="section">
      @yield('content')
    </section>

  </main><!-- End #main -->

  @include('layouts.partials.footer')

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/datatable/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
  <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/quill/quill.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
  <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>

  <!-- Template Main JS File -->
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script src="{{ asset('vendor/sweetalert/sweetalert.all.js') }}"></script>
  <script src="{{ asset('assets/js/delete-confirm.js') }}"></script>

  {{-- رسائل النظام بـ SweetAlert (نفس نظام الشكاوى) --}}
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const flash = @json([
        'success'   => session('success'),
        'warning'   => session('warning'),
        'error'     => session('error'),
        'hasErrors' => $errors->any(),
      ]);

      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
      });

      if (flash.success) Toast.fire({ icon: 'success', title: flash.success });
      if (flash.warning) Toast.fire({ icon: 'warning', title: flash.warning });

      if (flash.error) {
        Swal.fire({ icon: 'error', title: 'تنبيه', text: flash.error, confirmButtonText: 'حسناً' });
      } else if (flash.hasErrors) {
        Toast.fire({ icon: 'error', title: 'يوجد أخطاء في البيانات — راجع الحقول المظللة باللون الأحمر.', timer: 5000 });
      }
    });
  </script>
  {{-- أخطاء التحقق من الخادم — wizard.js بيعلّم بيها الحقول ويفتح الخطوة اللي فيها الخطأ --}}
  <script>window.serverErrors = @json($errors->getBag('default')->getMessages());</script>
  <script src="{{ asset('assets/js/wizard.js') }}"></script>

  @stack('scripts')

</body>

</html>
