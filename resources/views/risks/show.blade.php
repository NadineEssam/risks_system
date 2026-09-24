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
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="card-title mb-0">{{ $risk->classification_label }}</h5>
          <div>
            @can('risks.edit')
              <a href="{{ route('risks.edit', $risk) }}" class="btn btn-sm btn-outline-secondary">تعديل</a>
              <form action="{{ route('risks.toggle', $risk) }}" method="POST" class="d-inline">
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
                      onsubmit="return confirm('هل أنت متأكد من إلغاء ربط هذا القطاع؟')">
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
                    <form action="{{ route('required-actions.destroy', $action) }}" method="POST">
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
            <thead><tr><th>الإدارة</th><th>تاريخ الاكتشاف</th><th>درجة الخطر</th><th>الحالة</th></tr></thead>
            <tbody>
              @forelse($risk->incidents as $incident)
                <tr>
                  <td>{{ $incident->department?->depname_ar }}</td>
                  <td>{{ $incident->discovery_date?->format('Y-m-d') }}</td>
                  <td>@riskDegreeBadge($incident->risk_degree)</td>
                  <td>{{ $incident->resolutionStatus?->status_name ?? '—' }}</td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-muted text-center py-2">لا توجد أحداث مسجلة لهذا الخطر بعد.</td></tr>
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
          @forelse($risk->resolutionStatusDetails->sortByDesc('creation_date') as $statusDetail)
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
                @foreach($resolutionStatuses as $status)
                  <option value="{{ $status->id }}">{{ $status->status_name }}</option>
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
            <li class="list-group-item px-0">{{ \Illuminate\Support\Str::limit($indicator->indicator_name, 60) }}</li>
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
