@extends('layouts.app')

@section('title', 'تفاصيل الخطر المحتمل')
@section('page-title', 'تفاصيل الخطر المحتمل')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('risks.index') }}">سجل المخاطر المحتملة</a></li>
  <li class="breadcrumb-item active">تفاصيل</li>
@endsection

@section('content')

<div class="row">
  <div class="col-lg-8">

    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
          <h5 class="card-title mb-0 flex-grow-1" style="line-height:1.7;">{{ $risk->classification_label }}</h5>
          <div class="d-flex gap-2 flex-shrink-0 text-nowrap">
            @can('risks.edit')
              <a href="{{ route('risks.edit', $risk) }}" class="btn btn-sm btn-outline-secondary">تعديل</a>
                <form action="{{ route('risks.toggle', $risk) }}" method="POST" class="d-inline m-0">
                @csrf
                <button class="btn btn-sm btn-outline-{{ $risk->validity ? 'danger' : 'success' }}">
                  {{ $risk->validity ? 'إلغاء التفعيل' : 'تفعيل' }}
                </button>
              </form>
            @endcan
          </div>
        </div>

        <p class="mt-2"><strong>وصف الخطر:</strong><br>{{ $risk->risk_description }}</p>
        @if($risk->proposed_control)
          <p><strong>الضوابط المقترحة / إجراءات المواجهة:</strong><br>{{ $risk->proposed_control }}</p>
        @endif

        <div class="small text-muted">
          أُنشئ بواسطة {{ $risk->created_by }} بتاريخ {{ $risk->creation_date?->format('Y-m-d H:i') }}
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">القطاعات الإدارية المسؤولة والإجراءات المطلوبة</h5>

        @forelse($risk->sectorDetails as $sectorDetail)
          <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center">
              <strong><i class="bi bi-diagram-3 me-1"></i> {{ $sectorDetail->sector?->sector_ar }}</strong>
              @can('risks.edit')
                <form action="{{ route('risks.sectors.destroy', [$risk, $sectorDetail]) }}" method="POST"
                      data-confirm-title="إلغاء ربط القطاع"
                      data-confirm="سيتم إلغاء ربط «{{ $sectorDetail->sector?->sector_ar }}» وحذف كل الإجراءات المطلوبة المرتبطة به."
                      data-confirm-button="نعم، إلغاء الربط">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-link text-danger p-0">إلغاء الربط</button>
                </form>
              @endcan
            </div>

            <ul class="list-group list-group-flush mt-2">
              @forelse($sectorDetail->requiredActions as $action)
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                  <div>
                    {{ $action->required_action }}
                    @if($action->expiration_date)
                      <span class="badge bg-light text-dark">تنتهي: {{ $action->expiration_date->format('Y-m-d') }}</span>
                    @endif
                  </div>
                  @can('risks.edit')
                    <form action="{{ route('required-actions.destroy', $action) }}" method="POST"
                          data-confirm-title="حذف الإجراء"
                          data-confirm="سيتم حذف الإجراء: «{{ \Illuminate\Support\Str::limit($action->required_action, 60) }}»"
                          data-confirm-button="نعم، احذف">
                      @csrf @method('DELETE')
                      <button class="btn btn-sm btn-link text-danger p-0">حذف</button>
                    </form>
                  @endcan
                </li>
              @empty
                <li class="list-group-item px-0 text-muted small">لا توجد إجراءات مطلوبة مسجلة لهذا القطاع بعد.</li>
              @endforelse
            </ul>

            @canany(['risks.create', 'risks.edit'])
              <form action="{{ route('risk-sectors.actions.store', $sectorDetail) }}" method="POST" class="row g-2 mt-2">
                @csrf
                <div class="col-md-7">
                  <input type="text" name="required_action" class="form-control form-control-sm" placeholder="نص الإجراء المطلوب" required>
                </div>
                <div class="col-md-3">
                  <input type="date" name="expiration_date" class="form-control form-control-sm" placeholder="تاريخ الانتهاء">
                </div>
                <div class="col-md-2">
                  <button class="btn btn-sm btn-outline-primary w-100">إضافة</button>
                </div>
              </form>
            @endcanany
          </div>
        @empty
          <p class="text-muted">لم يتم ربط أي قطاعات إدارية بعد.</p>
        @endforelse

        @canany(['risks.create', 'risks.edit'])
          <form action="{{ route('risks.sectors.store', $risk) }}" method="POST" class="d-flex gap-2 mt-3">
            @csrf
            <select name="sectors_sec_id" class="form-select" required>
              <option value="">-- اختر قطاعاً إدارياً لربطه --</option>
              @foreach($allSectors as $sector)
                <option value="{{ $sector->sec_id }}">{{ $sector->sector_ar }}</option>
              @endforeach
            </select>
            <button class="btn btn-primary text-nowrap">ربط القطاع</button>
          </form>
        @endcanany
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">الأحداث المرتبطة بهذا الخطر ({{ $risk->incidents->count() }})</h5>
        <div class="table-responsive">
          <table class="table table-sm table-borderless">
            <thead>
              <tr>
                <th>وصف الحدث</th>
                <th>الإدارة</th>
                <th class="text-nowrap">تاريخ الاكتشاف</th>
                <th class="text-nowrap">درجة الخطر</th>
                <th>الحالة</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @forelse($risk->incidents->sortByDesc('id') as $incident)
                <tr>
                  {{-- وصف الحدث: رابط للحدث + النص الكامل عند الوقوف عليه --}}
                  <td style="min-width:220px;">
                    <a href="{{ route('incidents.show', $incident) }}" class="text-decoration-none"
                       title="{{ $incident->description }}">
                      {{ \Illuminate\Support\Str::limit($incident->description, 60) ?: 'حدث #'.$incident->id }}
                    </a>
                  </td>
                  <td>{{ $incident->department?->depname_ar ?? '—' }}</td>
                  <td class="text-nowrap">{{ $incident->discovery_date?->format('Y-m-d') ?? '—' }}</td>
                  <td class="text-nowrap">@riskDegreeBadge($incident->risk_degree)</td>
                  <td>{{ $incident->resolutionStatus?->status_name ?? '—' }}</td>
                  <td>
                    @if(PerUser('incidents.show'))
                      <a href="{{ route('incidents.show', $incident) }}" class="btn btn-sm btn-outline-info action-btn"
                         data-bs-toggle="tooltip" title="عرض الحدث">
                        <i class="bx bx-show"></i>
                      </a>
                    @endif
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-muted text-center py-2">لا توجد أحداث مسجلة لهذا الخطر بعد.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>

  <div class="col-lg-4">

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">حالة حل الخطر</h5>
        <ul class="list-group list-group-flush mb-3">
          @php $currentStatusId = $risk->resolutionStatusDetails->sortByDesc('id')->first()?->resolution_status_id; @endphp
          @forelse($risk->resolutionStatusDetails->sortByDesc('id') as $statusDetail)
            <li class="list-group-item px-0 d-flex justify-content-between">
              <span>{{ $statusDetail->resolutionStatus?->status_name }}</span>
              <span class="text-muted small">{{ $statusDetail->creation_date?->format('Y-m-d') }}</span>
            </li>
          @empty
            <li class="list-group-item px-0 text-muted small">لا يوجد سجل حالات.</li>
          @endforelse
        </ul>

        @can('risks.status.update')
          <form action="{{ route('risks.status.update', $risk) }}" method="POST">
            @csrf
            <label class="form-label small">اعتماد حالة جديدة</label>
            <div class="d-flex gap-2">
              <select name="resolution_status_id" class="form-select form-select-sm" required>
                <option value="">-- اختر الحالة الجديدة --</option>
                @foreach($resolutionStatuses as $status)
                  @php $isCurrent = (string) $status->id === (string) $currentStatusId; @endphp
                  <option value="{{ $status->id }}" @disabled($isCurrent)>
                    {{ $status->status_name }}{{ $isCurrent ? ' (الحالة الحالية)' : '' }}
                  </option>
                @endforeach
              </select>
              <button class="btn btn-sm btn-primary text-nowrap">اعتماد</button>
            </div>
          </form>
        @endcan
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">مؤشرات قياس المخاطر المرتبطة ({{ $risk->indicators->count() }})</h5>
        <ul class="list-group list-group-flush">
          @forelse($risk->indicators as $indicator)
            @php $last = $indicator->lastFollowup(); @endphp
            <li class="list-group-item px-0">
              {{-- اسم المؤشر: سطر كامل عشان يبقى مقروء --}}
              <a href="{{ route('indicators.show', $indicator) }}" class="d-block text-decoration-none fw-semibold mb-2"
                 style="line-height:1.7;" title="{{ $indicator->indicator_name }}">
                <i class="bx bx-bar-chart-alt-2 text-primary"></i>
                {{ \Illuminate\Support\Str::limit($indicator->indicator_name, 120) }}
              </a>
              <div class="d-flex justify-content-between align-items-center gap-2">
                {{-- آخر مستوى بعرض ثابت --}}
                <span style="min-width:150px;display:inline-block;">
                  {!! $last?->thresholdLevel?->badge() ?? \App\Models\ThresholdLevel::emptyBadge() !!}
                </span>
                <div class="d-flex gap-1">
                  @if(PerUser('indicators.show'))
                    <a href="{{ route('indicators.show', $indicator) }}" class="btn btn-sm btn-outline-info action-btn"
                       data-bs-toggle="tooltip" title="عرض المؤشر"><i class="bx bx-show"></i></a>
                  @endif
                  @if(PerUser('indicator-followups.index'))
                    <a href="{{ route('indicator-followups.index', $indicator) }}" class="btn btn-sm btn-outline-primary action-btn"
                       data-bs-toggle="tooltip" title="سجل قياسات المؤشر"><i class="bx bx-line-chart"></i></a>
                  @endif
                </div>
              </div>
            </li>
          @empty
            <li class="list-group-item px-0 text-muted small">لا توجد مؤشرات مرتبطة بعد.</li>
          @endforelse
        </ul>
        @can('indicators.create')
          <a href="{{ route('indicators.create', ['risk' => $risk->id]) }}" class="btn btn-sm btn-outline-primary mt-2">
            <i class="bi bi-plus-lg"></i> إضافة مؤشر لهذا الخطر
          </a>
        @endcan
      </div>
    </div>

  </div>
</div>
@endsection
