@extends('layouts.app')

@section('title', 'الإشعارات')
@section('page-title', 'الإشعارات')
@section('breadcrumbs')
  <li class="breadcrumb-item active">الإشعارات</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body pt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="card-title m-0 p-0"><i class="bi bi-bell text-primary"></i> كل الإشعارات</h5>
      @if(auth()->user()->unreadNotifications()->exists())
        <form method="POST" action="{{ route('notifications.read-all') }}" class="m-0">
          @csrf
          <button class="btn btn-sm btn-outline-primary"><i class="bi bi-check2-all"></i> تحديد الكل كمقروء</button>
        </form>
      @endif
    </div>

    <ul class="list-group list-group-flush">
      @forelse($notifications as $notification)
        <li class="list-group-item px-0 {{ $notification->read_at ? '' : 'bg-light' }}">
          <a href="{{ route('notifications.read', $notification->id) }}" class="d-flex gap-3 text-decoration-none text-dark">
            <i class="bi {{ $notification->data['icon'] ?? 'bi-bell' }} fs-4 text-{{ $notification->data['color'] ?? 'primary' }}"></i>
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between">
                <strong>{{ $notification->data['title'] ?? '' }}</strong>
                <small class="text-muted">{{ $notification->created_at?->format('Y-m-d H:i') }}</small>
              </div>
              <div class="small">{{ $notification->data['body'] ?? '' }}</div>
              @unless($notification->read_at)<span class="badge bg-primary mt-1">جديد</span>@endunless
            </div>
          </a>
        </li>
      @empty
        <li class="list-group-item px-0 text-center text-muted py-4">لا توجد إشعارات.</li>
      @endforelse
    </ul>

    <div class="mt-3">{{ $notifications->links() }}</div>
  </div>
</div>
@endsection