<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASF Monitoring System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #1a511e;
            /* Dark green */
            --secondary-color: #80c342;
            /* Light green */
            --bg-color: #f0f4f7;
            --font-family: 'Poppins', sans-serif;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--bg-color);
        }

        /* Navbar Styling */
        .navbar-custom {
            background-color: var(--primary-color);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: #fff;
            transition: color 0.3s ease;
        }

        .navbar-custom .nav-link:hover {
            color: var(--secondary-color);
        }

        /* Sidebar (if used) */
        .sidebar {
            background-color: var(--primary-color);
            min-height: 100vh;
            color: #fff;
            padding-top: 1rem;
        }

        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: var(--secondary-color);
            color: var(--primary-color);
        }

        /* Hero Section */
        .hero {
            padding: 6rem 0;
            background-color: #fff;
            text-align: center;
            height: 100vh;
        }

        .hero h1 {
            color: var(--primary-color);
            font-weight: 700;
        }

        .hero p {
            color: #555;
            font-size: 1.25rem;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <img src="{{ asset('/images/vitarich-logo.png') }}" alt="Vitarich" height="40" class="me-2" />
                <span class="fs-5 fw-bold">ASF Monitoring</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                    @auth
                        @if (auth()->user()->roles->first()?->name === 'admin')
                            <li class="nav-item me-3">
                                <a class="nav-link" href="{{ route('admin.users.index') }}">
                                    <i class="fa fa-users me-1"></i> User Management
                                </a>
                            </li>
                        @endif
                    @endauth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            {{ auth()->check() ? auth()->user()->name : 'Guest' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            @auth
                                <li><a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a>
                                </li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            @endauth
                            @guest
                                <li><a class="dropdown-item" href="{{ route('login') }}">Login</a></li>
                            @endguest
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid hero">
        <div class="container">
            <h1 class="display-4 mb-3">Welcome to <span style="color: var(--secondary-color);">ASF Monitoring
                    System</span></h1>
            <p class="lead">A smart solution for detecting and managing African Swine Fever symptoms.</p>
            <hr class="my-4">

            @auth
                @php
                    $role = auth()->user()->roles->first()?->name;
                @endphp
                @if ($role === 'admin')
                    <div class="mt-4">
                        <p class="h5 mb-2 text-muted">You are logged in as **Administrator**</p>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">Go to Admin Dashboard</a>
                    </div>
                @elseif($role === 'vet')
                    <div class="mt-4">
                        <p class="h5 mb-2 text-muted">You are logged in as **Veterinaries**</p>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">Go to Your Dashboard</a>
                    </div>
                @elseif($role === 'farmer')
                    <div class="mt-4">
                        <p class="h5 mb-2 text-muted">You are logged in as **Farmer**</p>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">Go to Your Dashboard</a>
                    </div>
                @else
                    {{-- Handle other roles if any --}}
                    <div class="mt-4">
                        <p class="h5 mb-2 text-muted">Your role is not defined for this page.</p>
                    </div>
                @endif
            @else
                <div class="mt-4">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg me-2">Log in</a>
                </div>
            @endauth
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
