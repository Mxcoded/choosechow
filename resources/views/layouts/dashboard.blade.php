<!-- resources/views/layouts/dashboard.blade.php --><!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Dashboard - ChooseChow')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
     <link rel="stylesheet" href="{{ asset('storage/fontawesome-pro/css/all.min.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        :root {
        --primary-color: #DC143C;
        --secondary-color: #F75270;
        --accent-color: #F7CAC9;
        --background-color: #FDEBD0;
        --success-color: #28a745;
        --danger-color: #dc3545;
        --dark-color: #343a40;
        }

        body {
            background: linear-gradient(135deg, var(--background-color) 0%, var(--accent-color) 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
            color: white;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }

        .topbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-left: auto;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 0.75rem;
        }

        .dashboard-container {
            padding: 2rem;
        }

        .dashboard-header {
            margin-bottom: 2rem;
        }

        .dashboard-title {
            color: var(--dark-color);
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .dashboard-subtitle {
            color: #6c757d;
            font-size: 1rem;
        }

        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.5rem;
            color: white;
        }

        .stat-content h3 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.25rem;
            color: var(--dark-color);
        }

        .stat-label {
            color: #6c757d;
            margin-bottom: 0.25rem;
        }

        .stat-change {
            font-size: 0.875rem;
        }

        .stat-change.positive {
            color: var(--success-color);
        }

        /* Dashboard Cards */
        .dashboard-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .card-header {
            background: #f8f9fa;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .card-title {
            font-weight: 600;
            margin: 0;
            color: var(--dark-color);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Order Items */
        .order-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #f1f3f4;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-image img {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 1rem;
        }

        .order-details {
            flex: 1;
        }

        .order-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--dark-color);
        }

        .order-chef {
            color: #6c757d;
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
        }

        .order-date {
            color: #6c757d;
            font-size: 0.8rem;
        }

        .order-status {
            text-align: center;
            margin: 0 1rem;
        }

        .order-price {
            font-weight: 600;
            color: var(--dark-color);
            margin-top: 0.25rem;
        }

        .order-actions {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        /* Recommended Meals */
        .recommended-meal {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #f1f3f4;
        }

        .recommended-meal:last-child {
            border-bottom: none;
        }

        .recommended-meal img {
            width: 80px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 1rem;
        }

        .meal-info {
            flex: 1;
        }

        .meal-info h6 {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--dark-color);
        }

        .meal-info p {
            color: #6c757d;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .price {
            font-weight: 600;
            color: var(--primary-color);
        }

        /* Performance Items */
        .performance-item {
            margin-bottom: 1rem;
        }

        .performance-item:last-child {
            margin-bottom: 0;
        }

        /* Admin specific styles */
        .status-item {
            padding: 0.5rem 0;
            border-bottom: 1px solid #f1f3f4;
        }

        .status-item:last-child {
            border-bottom: none;
        }

        .platform-stat {
            margin-bottom: 1rem;
        }

        .platform-stat:last-child {
            margin-bottom: 0;
        }

        .activity-timeline {
            position: relative;
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .activity-item:last-child {
            margin-bottom: 0;
        }

        .activity-item::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 40px;
            bottom: -20px;
            width: 2px;
            background: #e9ecef;
        }

        .activity-item:last-child::before {
            display: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 1rem;
            position: relative;
            z-index: 1;
        }

        .activity-content h6 {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--dark-color);
        }

        .activity-content p {
            color: #6c757d;
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            transform: translateY(-1px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .dashboard-container {
                padding: 1rem;
            }

            .dashboard-header .row {
                text-align: center;
            }

            .dashboard-header .col-md-6:last-child {
                margin-top: 1rem;
            }

            .order-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .order-actions {
                flex-direction: row;
                width: 100%;
            }
        }

        @yield('styles')
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
       <div class="sidebar-header">
            <a href="{{ route('dashboard') }}" class="sidebar-brand">
               <img src="{{ asset('storage/img/choosechowlogo.png') }}" alt="ChooseChow Logo" 
                         class="h-24 w-24 rounded-full shadow-md transition-transform duration-300 hover:scale-105">
                    <span class="text-3xl font-extrabold text-white-600 tracking-tight">ChooseChow</span>
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                
                @if(Auth::user()->user_type === 'admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                            <i class="fas fa-users"></i>
                            Manage Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.chefs') ? 'active' : '' }}" href="{{ route('admin.chefs') }}">
                            <i class="fas fa-user-tie"></i>
                            Manage Chefs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.orders') ? 'active' : '' }}" href="{{ route('admin.orders') }}">
                            <i class="fas fa-clipboard-list"></i>
                            All Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}" href="{{ route('admin.reports') }}">
                            <i class="fas fa-chart-bar"></i>
                            Reports & Analytics
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}">
                            <i class="fas fa-cogs"></i>
                            System Settings
                        </a>
                    </li>
                @elseif(Auth::user()->user_type === 'chef')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('chef.menus') ? 'active' : '' }}" href="{{ route('chef.menus') }}">
                            <i class="fas fa-utensils"></i>
                            My Menus
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('chef.orders') ? 'active' : '' }}" href="{{ route('chef.orders') }}">
                            <i class="fas fa-shopping-bag"></i>
                            Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('chef.profile') ? 'active' : '' }}" href="{{ route('chef.profile') }}">
                            <i class="fas fa-user-circle"></i>
                            Chef Profile
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('chefs.index') ? 'active' : '' }}" href="{{ route('chefs.index') }}">
                            <i class="fas fa-search"></i>
                            Find Chefs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customer.orders') ? 'active' : '' }}" href="{{ route('customer.orders') }}">
                            <i class="fas fa-shopping-bag"></i>
                            My Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customer.favorites') ? 'active' : '' }}" href="{{ route('customer.favorites') }}">
                            <i class="fas fa-heart"></i>
                            Favorites
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customer.profile') ? 'active' : '' }}" href="{{ route('customer.profile') }}">
                            <i class="fas fa-user-circle"></i>
                            Profile
                        </a>
                    </li>
                @endif
                
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('welcome') }}">
                        <i class="fas fa-home"></i>
                        Back to Site
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="topbar">
            <button class="btn btn-link d-md-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="user-info">
                <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="user-avatar">
                <div>
                    <div class="fw-bold">{{ Auth::user()->full_name }}</div>
                    <small class="text-muted text-capitalize">{{ Auth::user()->user_type }}</small>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        @yield('content')
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebarToggle');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(e.target) && 
                !toggle?.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });

        @yield('scripts')
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'9835f6e8245cbeeb',t:'MTc1ODU4Nzc1MS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
