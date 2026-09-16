<header id="header" class="header fixed-top d-flex align-items-center">

  <div class="d-flex align-items-center justify-content-between">
    <a href="<?php echo e(route('dashboard')); ?>" class="logo d-flex align-items-center">
      <span class="d-none d-lg-block">نظام إدارة المخاطر</span>
    </a>
    <i class="bi bi-list toggle-sidebar-btn"></i>
  </div><!-- End Logo -->

  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">

      <li class="nav-item dropdown pe-3">
        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
          <i class="bi bi-person-circle fs-4"></i>
          <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo e(auth()->user()->name); ?></span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
          <li class="dropdown-header">
            <h6><?php echo e(auth()->user()->name); ?></h6>
            <span><?php echo e(auth()->user()->job_title ?? 'مستخدم النظام'); ?></span>
            <?php if(auth()->user()->sector): ?>
              <span class="d-block small text-muted"><?php echo e(auth()->user()->sector->sector_ar); ?></span>
            <?php endif; ?>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
              <?php echo csrf_field(); ?>
              <button type="submit" class="dropdown-item d-flex align-items-center">
                <i class="bi bi-box-arrow-right"></i>
                <span>تسجيل الخروج</span>
              </button>
            </form>
          </li>
        </ul>
      </li>

    </ul>
  </nav>

</header><!-- End Header -->
<?php /**PATH C:\xampp\htdocs\riskmanagementsystem\risk-management\resources\views/layouts/partials/header.blade.php ENDPATH**/ ?>