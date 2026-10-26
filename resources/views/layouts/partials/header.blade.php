<header id="header" class="header fixed-top d-flex align-items-center">

  <div class="d-flex align-items-center justify-content-between">
    <a href="{{ route('dashboard') }}" class="logo d-flex align-items-center">
      <span class="d-none d-lg-block">نظام إدارة المخاطر</span>
    </a>
    <i class="bi bi-list toggle-sidebar-btn"></i>
  </div><!-- End Logo -->

  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">

      {{-- 🔔 الإشعارات --}}
      @php
        $unreadCount = auth()->user()->unreadNotifications()->count();
        $latestUnread = auth()->user()->unreadNotifications()->latest()->take(6)->get();
      @endphp
      <li class="nav-item dropdown">
        <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown" title="الإشعارات">
          <i class="bi bi-bell"></i>
          @if($unreadCount)
            <span class="badge bg-danger badge-number">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
          @endif
        </a>

        {{-- dropdown-menu-start: في RTL بيفتح ناحية الصفحة بدل ما يخرج برة الشاشة --}}
        <ul class="dropdown-menu dropdown-menu-start dropdown-menu-arrow notifications"
            style="width:380px;max-width:92vw;max-height:70vh;overflow-y:auto;">
          <li class="dropdown-header d-flex justify-content-between align-items-center">
            <span>{{ $unreadCount ? 'لديك '.$unreadCount.' إشعار غير مقروء' : 'لا توجد إشعارات جديدة' }}</span>
            <a href="{{ route('notifications.index') }}"><span class="badge rounded-pill bg-primary p-2">عرض الكل</span></a>
          </li>
          <li><hr class="dropdown-divider"></li>

          @foreach($latestUnread as $notification)
            <li class="notification-item">
              <a href="{{ route('notifications.read', $notification->id) }}" class="d-flex align-items-start gap-2 text-decoration-none text-dark w-100">
                <i class="bi {{ $notification->data['icon'] ?? 'bi-bell' }} text-{{ $notification->data['color'] ?? 'primary' }}"></i>
                <div>
                  <h4>{{ $notification->data['title'] ?? '' }}</h4>
                  <p>{{ \Illuminate\Support\Str::limit($notification->data['body'] ?? '', 80) }}</p>
                  <p>{{ $notification->created_at?->diffForHumans() }}</p>
                </div>
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
          @endforeach
        </ul>
      </li>

      <li class="nav-item dropdown pe-3">
        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
          <i class="bi bi-person-circle fs-4"></i>
          <span class="d-none d-md-block dropdown-toggle ps-2">{{ auth()->user()->name }}</span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
          <li class="dropdown-header">
            <h6>{{ auth()->user()->name }}</h6>
            <span>{{ auth()->user()->job_title ?? 'مستخدم النظام' }}</span>
            @if(auth()->user()->sector)
              <span class="d-block small text-muted">{{ auth()->user()->sector->sector_ar }}</span>
            @endif
          </li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
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
