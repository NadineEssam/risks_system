<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('dashboard') ? '' : 'collapsed' }}" href="{{ route('dashboard') }}">
        <i class="bi bi-grid"></i>
        <span>لوحة التحكم</span>
      </a>
    </li>

    @canany(['view-risks'])
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('risks.*') ? '' : 'collapsed' }}" href="{{ route('risks.index') }}">
        <i class="bi bi-shield-exclamation"></i>
        <span>سجل المخاطر المحتملة</span>
      </a>
    </li>
    @endcanany

    @canany(['view-incidents'])
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('incidents.*') ? '' : 'collapsed' }}" href="{{ route('incidents.index') }}">
        <i class="bi bi-exclamation-triangle"></i>
        <span>الأحداث التشغيلية</span>
      </a>
    </li>
    @endcanany

    @canany(['view-incident-followups'])
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('incident-followups.*') ? '' : 'collapsed' }}" href="{{ route('incident-followups.index') }}">
        <i class="bi bi-clock-history"></i>
        <span>متابعة الأحداث</span>
      </a>
    </li>
    @endcanany

    @canany(['view-indicators'])
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('indicators.*') ? '' : 'collapsed' }}" href="{{ route('indicators.index') }}">
        <i class="bi bi-speedometer2"></i>
        <span>مؤشرات قياس المخاطر KRI</span>
      </a>
    </li>
    @endcanany

    @canany(['view-indicator-followups'])
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('indicator-followups.*') ? '' : 'collapsed' }}" href="{{ route('indicator-followups.index') }}">
        <i class="bi bi-graph-up-arrow"></i>
        <span>متابعة المؤشرات</span>
      </a>
    </li>
    @endcanany

    @canany(['view-reports'])
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('reports.*') ? '' : 'collapsed' }}" href="{{ route('reports.index') }}">
        <i class="bi bi-bar-chart-line"></i>
        <span>التقارير</span>
      </a>
    </li>
    @endcanany

    @role('super-admin')
    <li class="nav-heading">الإعدادات والإدارة</li>

    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#admin-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-gear"></i><span>البيانات المرجعية</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="admin-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
       
        <li><a href="{{ route('admin.event-types.index') }}"><i class="bi bi-circle"></i><span>تصنيف بازل العام</span></a></li>
        <li><a href="{{ route('admin.events.index') }}"><i class="bi bi-circle"></i><span>تصنيف بازل التفصيلي</span></a></li>
        <li><a href="{{ route('admin.event-subcategories.index') }}"><i class="bi bi-circle"></i><span>تصنيف بازل الفرعي</span></a></li>
        <li><a href="{{ route('admin.event-details.index') }}"><i class="bi bi-circle"></i><span>تصنيف بازل الدقيق</span></a></li>
        <li><a href="{{ route('admin.resolution-statuses.index') }}"><i class="bi bi-circle"></i><span>حالات حل الخطر</span></a></li>
        <li><a href="{{ route('admin.followup-statuses.index') }}"><i class="bi bi-circle"></i><span>حالات المتابعة</span></a></li>
        <li><a href="{{ route('admin.followup-entry-types.index') }}"><i class="bi bi-circle"></i><span>أنواع إدخال المتابعة</span></a></li>
        <li><a href="{{ route('admin.indicator-natures.index') }}"><i class="bi bi-circle"></i><span>طبيعة المؤشر</span></a></li>
        <li><a href="{{ route('admin.measurement-units.index') }}"><i class="bi bi-circle"></i><span>وحدات القياس</span></a></li>
        <li><a href="{{ route('admin.reporting-frequencies.index') }}"><i class="bi bi-circle"></i><span>دورية الإبلاغ</span></a></li>
        <li><a href="{{ route('admin.activity-units.index') }}"><i class="bi bi-circle"></i><span>وحدات النشاط</span></a></li>
        <li><a href="{{ route('admin.threshold-levels.index') }}"><i class="bi bi-circle"></i><span>مستويات الحدود</span></a></li>
        <li><a href="{{ route('admin.responsible-roles.index') }}"><i class="bi bi-circle"></i><span>أدوار المسئولين</span></a></li>
      </ul>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#users-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-people"></i><span>المستخدمون والصلاحيات</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="users-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li><a href="{{ route('admin.users.index') }}"><i class="bi bi-circle"></i><span>المستخدمون</span></a></li>
        <li><a href="{{ route('admin.roles.index') }}"><i class="bi bi-circle"></i><span>الأدوار والصلاحيات</span></a></li>
      </ul>
    </li>
    @endrole

  </ul>

</aside><!-- End Sidebar-->
