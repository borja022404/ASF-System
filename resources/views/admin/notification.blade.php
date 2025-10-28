@extends('layouts.Admin.app')

@section('content')
<div class="main-content">
    <h3 class="mb-4">Notifications & Alerts</h3>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-bell me-2"></i>Recent Notifications
                    </h6>
                </div>
                <div class="card-body p-0">
                    @forelse($notifications as $notification)
                        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center mb-1 p-3 border-bottom {{ is_null($notification->read_at) ? 'bg-light' : '' }}">
                            <!-- Icon -->
                            <div class="me-3 mb-2 mb-md-0">
                                @if($notification->type === 'new_case_note')
                                    <i class="fas fa-notes-medical text-info fs-4"></i>
                                @elseif($notification->type === 'report_status_updated')
                                    <i class="fas fa-exclamation-circle text-success fs-4"></i>
                                @else
                                    <i class="fas fa-bell text-secondary fs-4"></i>
                                @endif
                            </div>

                            <!-- Text -->
                            <div class="flex-grow-1 w-75">
                                <strong class="{{ is_null($notification->read_at) ? 'fw-bold' : '' }}">
                                    {{ ucwords(str_replace('_', ' ', $notification->type)) }}
                                </strong>
                                <p class="mb-1 text-truncate notification-text">
                                    {{ $notification->data }}
                                </p>
                                <small class="text-muted d-block">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>

                            <!-- Action -->
                            <div class="mt-2 mt-md-0 ms-md-3">
                                <a href="{{ $notification->url }}" class="btn btn-sm btn-outline-primary w-100 w-md-auto">
                                    View
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-bell-slash text-muted" style="font-size: 2.5rem;"></i>
                            <p class="text-muted mt-2 mb-0">No notifications yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hover effect */
    .card-body > div:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s;
        border-radius: 6px;
    }

    /* Responsive text */
    .notification-text {
        max-width: 100%;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    /* On smaller screens, allow wrapping instead of truncating */
    @media (max-width: 576px) {
        .notification-text {
            white-space: normal;
        }
    }
</style>
@endsection
