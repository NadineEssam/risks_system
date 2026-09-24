<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('dashboard') ? '' : 'collapsed' }}" href="{{ route('dashboard') }}">
        <i class="bi bi-grid"></i>
        <span>لوحة التحكم</span>
      </a>
    </li>

    @canany(['risks.index'])
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('risks.*') ? '' : 'collapsed' }}" href="{{ route('risks.index') }}">
        <i class="bi bi-shield-exclamation"></i>
        <span>سجل المخاطر المحتملة</span>
      </a>
    </li>
    @endcanany

    @canany(['incidents.index'])
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('incidents.*') ? '' : 'collapsed' }}" href="{{ route('incidents.index') }}">
        <i class="bi bi-exclamation-triangle"></i>
        <span>الأحداث التشغيلية</span>
      </a>
    </li>
    @endcanany

    @canany(['incident-followups.index'])
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('incident-followups.*') ? '' : 'collapsed' }}" href="{{ route('incident-followups.index') }}">
        <i class="bi bi-clock-history"></i>
        <span>متابعة الأحداث</span>
      </a>
    </li>
    @endcanany

    @canany(['indicators.index'])
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('indicators.*') ? '' : 'collapsed' }}" href="{{ route('indicators.index') }}">
        <i class="bi bi-speedometer2"></i>
        <span>مؤشرات قياس المخاطر KRI</span>
      </a>
    </li>
    @endcanany

    @canany(['indicator-followups.index'])
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('indicator-followups.*') ? '' : 'collapsed' }}" href="{{ route('indicator-followups.index') }}">
        <i class="bi bi-graph-up-arrow"></i>
        <span>متابعة المؤشرات</span>
      </a>
    </li>
    @endcanany

    @canany(['reports.index'])
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('reports.*') ? '' : 'collapsed' }}" href="{{ route('reports.index') }}">
        <i class="bi bi-bar-chart-line"></i>
        <span>التقارير</span>
      </a>
    </li>
    @endcanany

        @php
      $lookupLinks = collect([
        'admin.event-types.index'           => 'تصنيف بازل العام',
        'admin.events.index'                => 'تصنيف بازل التفصيلي',
        'admin.event-subcategories.index'   => 'تصنيف بازل الفرعي',
        'admin.event-details.index'         => 'تصنيف بازل الدقيق',
        'admin.resolution-statuses.index'   => 'حالات حل الخطر',
        'admin.followup-statuses.index'     => 'حالات المتابعة',
        'admin.followup-entry-types.index'  => 'أنواع إدخال المتابعة',
        'admin.indicator-natures.index'     => 'طبيعة المؤشر',
        'admin.measurement-units.index'     => 'وحدات القياس',
        'admin.reporting-frequencies.index' => 'دورية الإبلاغ',
        'admin.activity-units.index'        => 'وحدات النشاط',
        'admin.threshold-levels.index'      => 'مستويات الحدود',
        'admin.responsible-roles.index'     => 'أدوار المسئولين',
      ])->filter(fn ($label, $route) => PerUser($route));

      $userLinks = collect([
        'admin.users.index' => 'المستخدمون',
        'admin.roles.index' => 'الأدوار والصلاحيات',
      ])->filter(fn ($label, $route) => PerUser($route));
    @endphp

    @if($lookupLinks->isNotEmpty() || $userLinks->isNotEmpty())
    <li class="nav-heading">الإعدادات والإدارة</li>
    @endif

    @if($lookupLinks->isNotEmpty())
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#admin-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-gear"></i><span>البيانات المرجعية</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="admin-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        @foreach($lookupLinks as $route => $label)
          <li><a href="{{ route($route) }}"><i class="bi bi-circle"></i><span>{{ $label }}</span></a></li>
        @endforeach
      </ul>
    </li>
    @endif

    @if($userLinks->isNotEmpty())
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#users-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-people"></i><span>المستخدمون والصلاحيات</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="users-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        @foreach($userLinks as $route => $label)
          <li><a href="{{ route($route) }}"><i class="bi bi-circle"></i><span>{{ $label }}</span></a></li>
        @endforeach
      </ul>
    </li>
    @endif

  </ul>

</aside><!-- End Sidebar-->
