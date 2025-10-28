@extends('layouts.Farmer.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Notifications</h4>
        @if ($notifications->isNotEmpty())
        <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-check-circle me-1"></i> Mark All as Read
            </button>
        </form>
        @endif
    </div>

    @if ($notifications->isNotEmpty())
    <ul class="list-group list-group-flush" id="notificationList">
        @foreach($notifications as $notif)
        <li class="list-group-item d-flex justify-content-between align-items-center notification-item
            {{ is_null($notif->read_at) ? 'unread bg-light border-start border-primary border-4 rounded-end shadow-sm' : 'read' }}"
            data-id="{{ $notif->id }}"
            data-url="{{ $notif->url ?? '#' }}">
            <div class="d-flex align-items-center">
                @if (is_null($notif->read_at))
                <span class="me-2 text-primary fs-5 notification-bullet"><i class="bi bi-bell-fill"></i></span>
                @else
                <span class="me-2 text-muted fs-5"><i class="bi bi-bell"></i></span>
                @endif
                <div>
                    <div class="{{ is_null($notif->read_at) ? 'fw-bold' : 'text-secondary' }} notification-text">
                        {{ is_array($notif->data) ? ($notif->data['message'] ?? 'New notification') : ($notif->data ?? 'New notification') }}
                    </div>
                    <small class="text-muted notification-time">{{ $notif->created_at->diffForHumans() }}</small>
                </div>
            </div>

            @if (is_null($notif->read_at))
            <button class="btn btn-sm btn-outline-secondary ms-auto mark-as-read" data-id="{{ $notif->id }}">
                <i class="bi bi-envelope-open"></i>
            </button>
            @endif
        </li>
        @endforeach
    </ul>
    @else
    <div class="alert alert-info text-center mt-4" role="alert">
        You're all caught up! No new notifications at the moment.
    </div>
    @endif

    <div class="mt-3">
        {{ $notifications->links() }}
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.error('CSRF token not found. Ensure <meta name="csrf-token" content="{{ csrf_token() }}"> is in your HTML head.');
        return;
    }

    // Handle individual notification "Mark as Read" clicks
    document.querySelectorAll('.mark-as-read').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const notifId = this.dataset.id;
            const listItem = this.closest('.notification-item');
            const url = listItem.dataset.url;

            console.log('Mark as read clicked:', { id: notifId, url });

            // Show loading state
            this.disabled = true;
            this.innerHTML = '<i class="bi bi-hourglass-split"></i>';
            listItem.classList.add('loading');

            // Send AJAX request to mark as read
            fetch(`/notifications/${notifId}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({}),
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response:', data);

                if (data.success && !data.was_already_read) {
                    // Update UI
                    listItem.classList.remove('unread', 'bg-light', 'border-start', 'border-primary', 'border-4', 'rounded-end', 'shadow-sm');
                    listItem.classList.add('read');

                    // Update bell icon
                    const bullet = listItem.querySelector('.notification-bullet');
                    if (bullet) {
                        bullet.classList.remove('text-primary');
                        bullet.classList.add('text-muted');
                        bullet.innerHTML = '<i class="bi bi-bell"></i>';
                    }

                    // Update text styling
                    const text = listItem.querySelector('.notification-text');
                    if (text) {
                        text.classList.remove('fw-bold');
                        text.classList.add('text-secondary');
                    }

                    // Remove the "Mark as Read" button
                    this.remove();

                    // Redirect if URL is provided
                    if (url && url !== '#') {
                        setTimeout(() => window.location.href = url, 300);
                    }
                } else if (data.success && data.was_already_read) {
                    console.log('Notification was already read');
                    if (url && url !== '#') {
                        setTimeout(() => window.location.href = url, 300);
                    }
                } else {
                    console.error('Failed to mark as read:', data.message);
                    // Show error state
                    this.innerHTML = '<i class="bi bi-exclamation-circle"></i>';
                }

                // Reset loading state
                listItem.classList.remove('loading');
                this.disabled = false;
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
                listItem.classList.remove('loading');
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-envelope-open"></i>';
                // Redirect even on error
                if (url && url !== '#') {
                    setTimeout(() => window.location.href = url, 300);
                }
            });
        });
    });

    // Handle notification item clicks (for redirecting without marking as read)
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function(e) {
            const url = this.dataset.url;
            if (url && url !== '#' && !e.target.closest('.mark-as-read')) {
                window.location.href = url;
            }
        });
    });
});
</script>
@endsection
@endsection
```