<style>
    :root {
        --sidebar-width: 260px;
        --sidebar-collapsed-width: 75px;
        --primary-color: #198754;
    }

    .sidebar {
        min-height: 100vh;
        background: linear-gradient(135deg, var(--primary-color), #34495e);
        color: white;
        position: fixed;
        width: var(--sidebar-width);
        z-index: 1000;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        top: 0;
        left: 0;
        box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .sidebar-inner {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        scroll-behavior: smooth;
    }

    .sidebar-header {
        padding: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 90px;
        transition: all 0.4s ease;
    }


    .sidebar-header img {
        width: 100%;
        width: 80px;
    }


    .sidebar-header h4 {
        font-size: 1.1rem;
        margin-bottom: 2px;
    }

    .sidebar-header small {
        font-size: 0.75rem;
    }

  

    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.8);
        padding: 14px 20px;
        margin: 4px 10px;
        border-radius: 10px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        white-space: nowrap;
        position: relative;
        overflow: hidden;
    }

    .sidebar .nav-link i {
        font-size: 1.3rem;
        min-width: 35px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .sidebar .nav-link .link-text {
        margin-left: 10px;
        opacity: 1;
        transition: all 0.3s ease;
    }

    .sidebar .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.15);
        color: white;
        transform: translateX(5px);
    }

    .sidebar .nav-link.active {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
        font-weight: 600;
    }

    .sidebar .nav-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 70%;
        background: white;
        border-radius: 0 4px 4px 0;
    }

    .sidebar .badge {
        margin-left: auto;
        transition: all 0.3s ease;
    }

    .main-content {
        margin-left: var(--sidebar-width);
        padding: 20px;
        transition: margin-left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        min-height: 100vh;
    }

    /* Scroll Buttons */
    .scroll-btn {
        background: rgba(255, 255, 255, 0.15);
        color: white;
        border: none;
        width: 100%;
        padding: 6px 0;
        text-align: center;
        font-size: 1.2rem;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .scroll-btn:hover {
        background: rgba(255, 255, 255, 0.25);
    }

    /* Scrollbar Styling */
    .sidebar-inner::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-inner::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
    }

    .sidebar-inner::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
    }

    .sidebar-inner::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    /* Responsive Rules (same as before) */
    @media (max-width: 991px) {
        .sidebar {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-header {
            padding: 15px 10px;
            min-height: 10px;
        }


        .sidebar .nav-link {
            justify-content: center;
            padding: 16px 10px;
            margin: 4px 8px;
        }

        .sidebar .nav-link .link-text,
        .sidebar .badge {
            display: none;
        }

        .main-content {
            margin-left: var(--sidebar-collapsed-width);
        }
    }

    @media (min-width: 992px) {
        .sidebar {
            width: var(--sidebar-width);
        }



        .main-content {
            margin-left: var(--sidebar-width);
        }
    }
</style> <!-- Sidebar -->
<nav class="sidebar" id="sidebar"> <button class="scroll-btn" id="scrollUpBtn"><i class="bi bi-chevron-up"></i></button>
    <div class="sidebar-inner">
        <div class="sidebar-header">

            <img src="{{ asset('/images/vitarich-logo.png') }}" alt="Vitarich Logo" />

        </div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    href="{{ route('dashboard') }}" data-title="Overview">
                    <i class="bi bi-speedometer2"></i>
                    <span class="link-text">Overview</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}"
                    href="{{ route('admin.reports.index') }}" data-title="Reports">
                    <i class="bi bi-file-medical"></i>
                    <span class="link-text">Reports</span>
                    <span class="badge bg-danger" id="reports-badge"></span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}"
                    href="{{ route('admin.users.index') }}" data-title="Users">
                    <i class="bi bi-people"></i>
                    <span class="link-text">Users</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports.map') ? 'active' : '' }}"
                    href="{{ route('admin.reports.map') }}" data-title="Map View">
                    <i class="bi bi-geo-alt"></i>
                    <span class="link-text">Map View</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports.analysis') ? 'active' : '' }}"
                    href="{{ route('admin.reports.analysis') }}" data-title="Analytics">
                    <i class="bi bi-graph-up"></i>
                    <span class="link-text">Analytics</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('notifications.index') ? 'active' : '' }}"
                    href="{{ route('notifications.index') }}" data-title="Notifications">
                    <i class="bi bi-bell"></i>
                    <span class="link-text">Notifications</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}"
                    href="{{ route('profile.edit') }}" data-title="Profile">
                    <i class="bi bi-person"></i>
                    <span class="link-text">Profile</span>
                </a>
            </li>

            <li class="nav-item mt-3">
                <a class="nav-link text-danger" href="#" data-title="Logout"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="link-text">Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <button class="scroll-btn" id="scrollDownBtn"><i class="bi bi-chevron-down"></i></button>

</nav>
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;"> @csrf </form>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarInner = document.querySelector('.sidebar-inner');
        const scrollUpBtn = document.getElementById('scrollUpBtn');
        const scrollDownBtn = document.getElementById('scrollDownBtn');
        scrollUpBtn.addEventListener('click', () => {
            sidebarInner.scrollBy({
                top: -150,
                behavior: 'smooth'
            });
        });
        scrollDownBtn.addEventListener('click', () => {
            sidebarInner.scrollBy({
                top: 150,
                behavior: 'smooth'
            });
        });
        //  Show / hide scroll buttons based on scroll position 
        sidebarInner.addEventListener('scroll', () => {
            scrollUpBtn.style.display = sidebarInner.scrollTop > 50 ? 'block' : 'none';
            scrollDownBtn.style.display = sidebarInner.scrollTop + sidebarInner.clientHeight <
                sidebarInner.scrollHeight - 50 ? 'block' : 'none';
        }); // Initialize button visibility 
        sidebarInner.dispatchEvent(new Event('scroll'));
    });
</script>
