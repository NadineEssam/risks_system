@extends('layouts.app')

@section('title', 'عرض مستخدم')
@section('page-title', 'عرض مستخدم')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">المستخدمون والصلاحيات</a></li>
  <li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('content')
<div class="row">

  {{-- بيانات المستخدم --}}
  <div class="col-lg-6">
    <div class="card"><div class="card-body pt-3">
      <div class="d-flex justify-content-between align-items-center">
        <h5 class="card-title"><i class="bx bx-user text-primary"></i> بيانات المستخدم</h5>
        @if(PerUser('admin.users.edit'))
          <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
            <i class="bx bx-edit-alt"></i> تعديل
          </a>
        @endif
      </div>

      <table class="table table-borderless mb-0">
        <tr><th class="text-muted" style="width:40%">الاسم</th><td>{{ $user->name }}</td></tr>
        <tr><th class="text-muted">اسم مستخدم الدومين</th><td><code>{{ $user->domain_username }}</code></td></tr>
        <tr><th class="text-muted">البريد الإلكتروني</th><td>{{ $user->email ?? '—' }}</td></tr>
        <tr><th class="text-muted">المسمى الوظيفي</th><td>{{ $user->job_title ?? '—' }}</td></tr>
        <tr><th class="text-muted">القطاع</th><td>{{ $user->sector?->sector_ar ?? '—' }}</td></tr>
        <tr><th class="text-muted">الإدارة</th><td>{{ $user->department?->depname_ar ?? '—' }}</td></tr>
        <tr>
          <th class="text-muted">الحالة</th>
          <td>
            @if($user->is_active)<span class="badge bg-success">فعّال</span>@else<span class="badge bg-danger">غير فعّال</span>@endif
          </td>
        </tr>
        @if($user->last_login_at ?? null)
          <tr><th class="text-muted">آخر دخول</th><td>{{ \Illuminate\Support\Carbon::parse($user->last_login_at)->format('Y-m-d H:i') }}</td></tr>
        @endif
      </table>
    </div></div>
  </div>

  {{-- الأدوار والصلاحيات --}}
  <div class="col-lg-6">
    <div class="card"><div class="card-body pt-3">
      <h5 class="card-title"><i class="bx bx-shield-quarter text-primary"></i> الأدوار والصلاحيات</h5>

      @forelse($user->roles as $role)
        <div class="border rounded p-3 mb-2">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge bg-info fs-6">{{ $role->name }}</span>
            <small class="text-muted">{{ $role->name === 'super-admin' ? 'كل الصلاحيات' : $role->permissions->count().' صلاحية' }}</small>
          </div>
          @if($role->name !== 'super-admin')
            @foreach($role->permissions->groupBy('group_ar') as $group => $perms)
              <div class="small mb-1">
                <strong>{{ $group ?: '—' }}:</strong>
                {{ $perms->pluck('ar_name')->filter()->implode('، ') }}
              </div>
            @endforeach
          @endif
        </div>
      @empty
        <p class="text-muted mb-0">لا توجد أدوار مسندة لهذا المستخدم.</p>
      @endforelse
    </div></div>
  </div>

</div>

<a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
  <i class="bx bx-arrow-back"></i> رجوع للقائمة
</a>
@endsection