<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?php echo $__env->yieldContent('title', 'نظام إدارة المخاطر'); ?></title>

  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files (نسخة RTL من Bootstrap) -->
  <link href="<?php echo e(asset('assets/vendor/bootstrap/css/bootstrap.rtl.min.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset('assets/vendor/bootstrap-icons/bootstrap-icons.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset('assets/vendor/boxicons/css/boxicons.min.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset('assets/vendor/quill/quill.snow.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset('assets/vendor/quill/quill.bubble.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset('assets/vendor/remixicon/remixicon.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset('assets/vendor/simple-datatables/style.css')); ?>" rel="stylesheet">

  <!-- Template Main CSS File + تجاوزات RTL -->
  <link href="<?php echo e(asset('assets/css/style.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset('assets/css/rtl-overrides.css')); ?>" rel="stylesheet">

  <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>

  <?php echo $__env->make('layouts.partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('layouts.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1><?php echo $__env->yieldContent('page-title', 'لوحة التحكم'); ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">الرئيسية</a></li>
          <?php echo $__env->yieldContent('breadcrumbs'); ?>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <?php if(session('success')): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>يرجى تصحيح الأخطاء التالية:</strong>
        <ul class="mb-0">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <section class="section">
      <?php echo $__env->yieldContent('content'); ?>
    </section>

  </main><!-- End #main -->

  <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="<?php echo e(asset('assets/vendor/apexcharts/apexcharts.min.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/vendor/chart.js/chart.umd.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/vendor/echarts/echarts.min.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/vendor/quill/quill.min.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/vendor/simple-datatables/simple-datatables.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/vendor/tinymce/tinymce.min.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/vendor/php-email-form/validate.js')); ?>"></script>

  <!-- Template Main JS File -->
  <script src="<?php echo e(asset('assets/js/main.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/js/wizard.js')); ?>"></script>

  <?php echo $__env->yieldPushContent('scripts'); ?>

</body>

</html>
<?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/layouts/app.blade.php ENDPATH**/ ?>