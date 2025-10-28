<nav class="navbar navbar-expand navbar-dark shadow-lg sticky-top"
    style="background: linear-gradient(135deg, #1a511e 0%, #2d7a32 100%); backdrop-filter: blur(10px); z-index:9999;">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        {{-- Brand --}}
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <img src="{{ asset('/images/vitarich-logo.png') }}" alt="Vitarich Logo" class="me-2" style="height: 40px;" />
            <span class="fs-5 fw-bold text-white d-none d-sm-inline">ASF Monitoring</span>
        </a>

        {{-- Right Section (Notifications + Profile) --}}
        <ul class="navbar-nav ms-auto d-flex align-items-center flex-row gap-2 gap-lg-3">

            {{-- Notification Bell --}}

            <li class="nav-item dropdown me-3">
                <a class="nav-link position-relative notification-bell" href="#" id="notifDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell fa-lg text-white notification-icon"></i>
                    @php
                        $unread = \App\Models\Notification::where('receiver_id', Auth::id())
                            ->whereNull('read_at')
                            ->count();
                    @endphp
                    @if ($unread > 0)
                        <span class="position-absolute notification-badge" id="notificationBadge">
                            {{ $unread }}
                        </span>
                    @endif
                </a>

                <div class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notifDropdown">
                    <div class="notification-header">
                        <h6 class="mb-0">
                            <i class="fas fa-bell me-2"></i>Notifications
                        </h6>
                        @if ($unread > 0)
                            <button class="btn btn-sm btn-outline-primary mark-all-read" id="markAllRead">
                                <i class="fas fa-check-double me-1"></i>Mark all read
                            </button>
                        @endif
                    </div>

                    <div class="notification-list" id="notificationList">
                        @php
                            $notifications = \App\Models\Notification::where('receiver_id', Auth::id())
                                ->latest()
                                ->take(5)
                                ->get();
                        @endphp

                        @forelse($notifications as $notif)
                            <div class="notification-item {{ $notif->read_at ? 'read' : 'unread' }}"
                                data-id="{{ $notif->id }}" data-url="{{ $notif->url ?? '#' }}"
                                style="cursor: pointer;">
                                @if (!$notif->read_at)
                                    <div class="notification-bullet"></div>
                                @endif
                                <div class="notification-content">
                                    <div class="notification-text">
                                        {{ Str::limit(is_array($notif->data) ? json_encode($notif->data) : $notif->data, 80) }}
                                    </div>
                                    <div class="notification-time">
                                        <i class="fas fa-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="notification-empty">
                                <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">No notifications yet</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="notification-footer">
                        <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-list me-2"></i>View All Notifications
                        </a>
                    </div>
                </div>
            </li>

            {{-- Profile Dropdown --}}
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle profile-toggle d-flex align-items-center" href="#"
                    id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&size=36&background=ffffff&color=1a511e&font-size=0.6"
                        class="profile-avatar">
                    <span class="ms-2 d-none d-md-inline">{{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end profile-dropdown" aria-labelledby="profileDropdown">
                    <li>
                        <span class="dropdown-item">{{ Auth::user()->name }}</span>
                    </li>
                    <hr class="dropdown-divider">

                    <li><a class="dropdown-item" href="{{ route ('profile.edit')}}"><i class="fas fa-user me-2"></i>Profile</a></li>

                    <li>

                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger" type="submit">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </li>

        </ul>
    </div>
</nav>


<!-- Enhanced CSS Styles -->
<style>
    /* Notification Bell Animation */
    .notification-bell {
        transition: all 0.3s ease;
        border-radius: 50%;
        padding: 8px;
    }

    .notification-bell:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: scale(1.1);
    }

    .notification-icon {
        transition: all 0.3s ease;
    }

    .notification-bell:hover .notification-icon {
        animation: bellRing 0.5s ease-in-out;
    }

    @keyframes bellRing {

        0%,
        100% {
            transform: rotate(0deg);
        }

        25% {
            transform: rotate(15deg);
        }

        75% {
            transform: rotate(-15deg);
        }
    }

    /* Enhanced Notification Badge */
    .notification-badge {
        top: -5px;
        start: 75%;
        background: linear-gradient(45deg, #ff4757, #ff6b7a);
        color: white;
        border-radius: 12px;
        padding: 2px 6px;
        font-size: 0.7rem;
        font-weight: bold;
        min-width: 18px;
        text-align: center;
        border: 2px solid #1a511e;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }
    }

    /* Notification Dropdown */
    .notification-dropdown {
        width: 380px;
        max-height: 500px;
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        padding: 0;
        overflow: hidden;
    }

    .notification-header {
        background: linear-gradient(135deg, #1a511e, #2d7a32);
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .mark-all-read {
        color: white;
        border-color: rgba(255, 255, 255, 0.3);
        font-size: 0.8rem;
    }

    .mark-all-read:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.5);
        color: white;
    }

    .notification-list {
        max-height: 320px;
        overflow-y: auto;
        padding: 0;
    }

    .notification-item {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: flex-start;
        transition: all 0.3s ease;
        position: relative;
    }

    .notification-item:hover {
        background: #f8f9fa;
        transform: translateX(5px);
    }

    .notification-item.unread {
        background: linear-gradient(90deg, #f8f9ff, #ffffff);
    }

    /* Red bullet mark for unread notifications */
    .notification-bullet {
        width: 10px;
        height: 10px;
        background: #ff4757;
        border-radius: 50%;
        margin-right: 12px;
        margin-top: 6px;
        flex-shrink: 0;
        box-shadow: 0 0 6px rgba(255, 71, 87, 0.5);
    }

    .notification-content {
        flex: 1;
    }

    .notification-text {
        font-size: 0.9rem;
        line-height: 1.4;
        color: #333;
        margin-bottom: 5px;
    }

    .notification-time {
        font-size: 0.75rem;
        color: #666;
        display: flex;
        align-items: center;
    }

    .notification-empty {
        text-align: center;
        padding: 40px 20px;
    }

    .notification-footer {
        background: #f8f9fa;
        padding: 15px 20px;
        border-top: 1px solid #eee;
    }

    /* Profile Dropdown Enhancements */
    .profile-toggle {
        transition: all 0.3s ease;
        border-radius: 25px;
        padding: 5px 10px;
    }

    .profile-toggle:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .profile-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }

    .profile-toggle:hover .profile-avatar {
        border-color: rgba(255, 255, 255, 0.5);
        transform: scale(1.05);
    }

    .profile-dropdown {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        min-width: 250px;
    }

    .profile-dropdown .dropdown-item {
        padding: 10px 20px;
        transition: all 0.3s ease;
    }

    .profile-dropdown .dropdown-item:hover {
        background: #f8f9fa;
        transform: translateX(5px);
    }

    /* Loading States */
    .loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .btn:disabled {
        opacity: 0.6;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .notification-dropdown {
            width: 320px;
        }
    }

    /* Custom Scrollbar */
    .notification-list::-webkit-scrollbar {
        width: 4px;
    }

    .notification-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .notification-list::-webkit-scrollbar-thumb {
        background: #1a511e;
        border-radius: 2px;
    }

    .notification-list::-webkit-scrollbar-thumb:hover {
        background: #2d7a32;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!csrfToken) {
        console.error('CSRF token not found. Ensure <meta name="csrf-token" content="{{ csrf_token() }}"> is in your HTML head.');
        return;
    }

    // Handle individual notification clicks
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const notifId = this.dataset.id;
            const url = this.dataset.url || '#';
            const isUnread = this.classList.contains('unread');

            console.log('Clicked notification:', { id: notifId, url, isUnread });

            if (isUnread) {
                // Show loading state
                this.classList.add('loading');

                // Mark as read via AJAX
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

                    if (data.success) {
                        // Update UI
                        this.classList.remove('unread');
                        this.classList.add('read');

                        // Remove red bullet
                        const bullet = this.querySelector('.notification-bullet');
                        if (bullet) bullet.remove();

                        // Update badge count only if notification was not already read
                        if (!data.was_already_read) {
                            const badge = document.getElementById('notificationBadge');
                            if (badge) {
                                const currentCount = parseInt(badge.textContent) || 0;
                                const newCount = Math.max(0, currentCount - 1);
                                badge.textContent = newCount;
                                if (newCount === 0) badge.style.display = 'none';
                            }

                            // Hide "Mark all read" button if no unread notifications
                            const unreadItems = document.querySelectorAll('.notification-item.unread');
                            const markAllBtn = document.getElementById('markAllRead');
                            if (unreadItems.length === 0 && markAllBtn) {
                                markAllBtn.style.display = 'none';
                            }
                        }

                        // Redirect after UI updates
                        if (url && url !== '#') {
                            setTimeout(() => window.location.href = url, 300);
                        }
                    } else {
                        console.error('Failed to mark as read:', data.message);
                        // Redirect even if marking failed
                        if (url && url !== '#') {
                            setTimeout(() => window.location.href = url, 300);
                        }
                    }

                    // Reset loading state
                    this.classList.remove('loading');
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
                    this.classList.remove('loading');
                    // Redirect even on error
                    if (url && url !== '#') {
                        setTimeout(() => window.location.href = url, 300);
                    }
                });
            } else {
                // Already read, just redirect
                if (url && url !== '#') {
                    window.location.href = url;
                }
            }
        });
    });

    // Mark all notifications as read
    const markAllBtn = document.getElementById('markAllRead');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';

            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({}),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update all unread notifications
                    document.querySelectorAll('.notification-item.unread').forEach(item => {
                        item.classList.remove('unread');
                        item.classList.add('read');
                        const bullet = item.querySelector('.notification-bullet');
                        if (bullet) bullet.remove();
                    });

                    // Hide badge and button
                    const badge = document.getElementById('notificationBadge');
                    if (badge) {
                        badge.style.display = 'none';
                    }
                    this.style.display = 'none';
                }
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-check-double me-1"></i>Mark all read';
            })
            .catch(error => {
                console.error('Error marking all notifications as read:', error);
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-check-double me-1"></i>Mark all read';
            });
        });
    }

    // Auto-refresh badge every 30 seconds
    setInterval(() => {
        fetch('/notifications/count', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notificationBadge');
            if (badge) {
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            }

            // Show/hide mark all read button
            if (markAllBtn) {
                markAllBtn.style.display = data.unread_count > 0 ? 'inline-block' : 'none';
            }
        })
        .catch(error => {
            console.error('Error fetching notification count:', error);
        });
    }, 30000);
});
</script>
