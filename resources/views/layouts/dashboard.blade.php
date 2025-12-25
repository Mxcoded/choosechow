<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Dashboard - ChooseChow')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* === FOOD PSYCHOLOGY THEME (Red/Warm) === */
        :root {
            --primary-color: #DC143C;  /* Crimson: Stimulates appetite */
            --primary-hover: #b90e30;
            --secondary-color: #F75270; /* Soft Red/Pink */
            --accent-color: #FFF5E6;   /* Warm Beige Background */
            --text-dark: #2D3436;
            --text-muted: #636e72;
            --sidebar-width: 260px;
        }

        body {
            background-color: var(--accent-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
            overflow-x: hidden; /* Prevent horizontal scroll on mobile */
        }

        /* === SIDEBAR === */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color) 0%, #a00b28 100%);
            color: white;
            z-index: 1040;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.05);
        }

        .sidebar-brand {
            font-size: 1.4rem;
            font-weight: 800;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-nav {
            flex-grow: 1;
            padding: 1rem 0;
            overflow-y: auto;
        }

        .nav-link {
            color: rgba(255,255,255,0.85);
            padding: 0.85rem 1.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: all 0.2s;
            text-decoration: none;
            border-left: 4px solid transparent;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left-color: #fff;
        }

        .nav-link i {
            width: 25px;
            font-size: 1.1rem;
            margin-right: 10px;
            text-align: center;
        }

        /* Logout Section at Bottom */
        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.1);
        }

        /* === MAIN CONTENT === */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
        }

        /* Top Bar */
        .topbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between; /* Space between Toggle and User Info */
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        /* User Profile in Topbar */
        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-color);
            padding: 2px;
        }

        /* === MOBILE RESPONSIVENESS === */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%); /* Hide Sidebar by default */
            }
            .sidebar.show {
                transform: translateX(0); /* Slide in */
            }
            .main-content {
                margin-left: 0; /* Full width content */
            }
            
            /* Overlay when sidebar is open */
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 1035;
            }
            .sidebar-overlay.show {
                display: block;
            }
        }

        /* Buttons & Cards Theme Override */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }
        .text-primary { color: var(--primary-color) !important; }
        .bg-primary { background-color: var(--primary-color) !important; }

        @yield('styles')
    </style>
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('dashboard') }}" class="sidebar-brand">
                <i class="fas fa-utensils"></i> ChooseChow
            </a>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>

                @if(Auth::user()->hasRole('admin'))
                    <li class="nav-item"><a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"><i class="fas fa-users"></i> Users</a></li>
                    <li class="nav-item"><a href="{{ route('admin.orders') }}" class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}"><i class="fas fa-receipt"></i> Orders</a></li>
                
                @elseif(Auth::user()->hasRole('chef'))
                    <li class="nav-item">
                        <a href="{{ route('chef.menus.index') }}" class="nav-link {{ request()->routeIs('chef.menus*') ? 'active' : '' }}">
                            <i class="fas fa-book-open"></i> My Menu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('chef.orders.index') }}" class="nav-link {{ request()->routeIs('chef.orders*') ? 'active' : '' }}">
                            <i class="fas fa-bell"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('chef.profile') }}" class="nav-link {{ request()->routeIs('chef.profile*') ? 'active' : '' }}">
                            <i class="fas fa-user-chef"></i> Profile
                        </a>
                    </li>

                @else
                    <li class="nav-item"><a href="{{ route('chefs.index') }}" class="nav-link"><i class="fas fa-search"></i> Find Food</a></li>
                    <li class="nav-item"><a href="{{ route('customer.orders') }}" class="nav-link"><i class="fas fa-shopping-bag"></i> My Orders</a></li>
                @endif
            </ul>
        </nav>

        <div class="sidebar-footer">
            <a href="{{ route('logout') }}" class="nav-link text-white"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars fa-lg"></i>
            </button>

            <h5 class="m-0 d-none d-lg-block fw-bold text-muted">@yield('page_title', '')</h5>

            <div class="dropdown">
                <div class="user-dropdown" data-bs-toggle="dropdown">
                    <div class="text-end d-none d-sm-block">
                        <div class="fw-bold small">{{ Auth::user()->first_name }}</div>
                        <div class="text-muted small" style="font-size: 0.75rem;">{{ ucfirst(Auth::user()->getRoleNames()->first()) }}</div>
                    </div>
                    <img src="{{ Auth::user()->avatar ? asset('storage/'.Auth::user()->avatar) : 'https://ui-avatars.com/api/?background=DC143C&color=fff&name='.urlencode(Auth::user()->full_name) }}" 
                         alt="User" class="user-avatar">
                </div>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><a class="dropdown-item" href="{{ route('welcome') }}">Home Page</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Sign Out
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="p-3 p-md-4">
            @yield('content')
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile Sidebar Logic
        const toggleBtn = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        if(toggleBtn) {
            toggleBtn.addEventListener('click', toggleSidebar);
            overlay.addEventListener('click', toggleSidebar);
        }
    </script>
    @yield('scripts')
</body>
</html>