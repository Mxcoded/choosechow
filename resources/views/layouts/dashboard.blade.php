<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/img/choosechowlogo.png') }}">
    
    <title>@yield('title', 'Dashboard - ChooseChow')</title>
    
    {{-- TAILWIND CSS via CDN with Custom Config --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'chow-red': {
                            50: '#fef2f2', 100: '#fee2e2', 200: '#fecaca', 300: '#fca5a5',
                            400: '#f87171', 500: '#ef4444', 600: '#dc2626', 700: '#b91c1c',
                            800: '#991b1b', 900: '#7f1d1d',
                        },
                        'chow-orange': {
                            50: '#fff7ed', 100: '#ffedd5', 200: '#fed7aa', 300: '#fdba74',
                            400: '#fb923c', 500: '#f97316', 600: '#ea580c', 700: '#c2410c',
                            800: '#9a3412', 900: '#7c2d12',
                        },
                        'chow-gold': {
                            50: '#fefce8', 100: '#fef9c3', 200: '#fef08a', 300: '#fde047',
                            400: '#facc15', 500: '#eab308', 600: '#ca8a04', 700: '#a16207',
                            800: '#854d0e', 900: '#713f12',
                        },
                        'chow-cream': {
                            50: '#fffbf5', 100: '#fef7ed', 200: '#fdf2e3', 300: '#fce7d3',
                            400: '#f9d9bd', 500: '#f5c9a3',
                        },
                        'chow-brown': {
                            50: '#fdf8f3', 100: '#f5ebe0', 200: '#e6d5c3', 300: '#d4b896',
                            400: '#c49a6c', 500: '#a67c52', 600: '#92400e', 700: '#7c3410',
                            800: '#78350f', 900: '#5c2a0e',
                        },
                        'chow-fresh': {
                            50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0', 300: '#86efac',
                            400: '#4ade80', 500: '#22c55e', 600: '#16a34a', 700: '#15803d',
                            800: '#166534', 900: '#14532d',
                        },
                        // Dark Navy Blue Theme - Premium Food App Design
                        'dark': {
                            'base': '#0E1A2B',      // Main background
                            'section': '#132238',   // Section background
                            'card': '#1B2E4B',      // Card background
                            'border': '#243B5A',    // Border color
                        },
                        'accent': {
                            DEFAULT: '#E76F51',     // Terracotta accent
                            'hover': '#D65A3F',     // Accent hover
                            'light': '#F4A261',     // Highlight color
                        },
                        'content': {
                            'primary': '#F1F5F9',   // Primary text
                            'secondary': '#B6C2D9', // Secondary text
                        },
                    }
                }
            }
        }
    </script>
    
    {{-- BOOTSTRAP CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- FONTS & ICONS --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- JQUERY & ALPINE --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; }
        .active-nav { border-left: 4px solid #E76F51; background-color: rgba(231, 111, 81, 0.15); font-weight: 600; }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); }
        .dark .card-hover:hover { box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4); }
    </style>
</head>
<body class="bg-gray-100 dark:bg-dark-base text-gray-800 dark:text-content-primary transition-colors duration-300" x-data="{ sidebarOpen: false }">

    {{-- MOBILE SIDEBAR OVERLAY --}}
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false" 
         class="fixed inset-0 z-40 bg-black/50 lg:hidden" 
         style="display: none;"></div>

    {{-- MOBILE SIDEBAR --}}
    <aside x-show="sidebarOpen"
           x-transition:enter="transition ease-in-out duration-300 transform"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in-out duration-300 transform"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           class="fixed top-0 left-0 z-50 h-screen w-64 bg-chow-red-700 dark:bg-dark-section text-white flex flex-col shadow-xl lg:hidden transition-colors duration-300"
           style="display: none;">
        <div class="h-16 flex items-center justify-between px-6 border-b border-white/10 dark:border-dark-border bg-black/10 dark:bg-black/20">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                <img src="{{ asset('storage/img/choosechowlogo.png') }}" alt="ChooseChow Logo" 
                     class="h-10 w-10 rounded-full shadow-sm">
                <span class="text-xl font-bold text-white">ChooseChow</span>
            </a>
            <button @click="sidebarOpen = false" class="text-white/80 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto py-4">
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('chef.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('chef.index') ? 'active-nav' : '' }}">
                        <i class="fas fa-search w-6"></i> Find Chow
                    </a>
                </li>
                
                
                @auth
                    @if(Auth::user()->hasRole('admin'))
                        <li class="px-6 py-2 text-xs font-bold text-chow-orange-200 uppercase mt-4">Admin Control</li>
                        <li><a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('dashboard') ? 'active-nav' : '' }}"><i class="fas fa-chart-line w-6"></i> Overview</a></li>
                        <li><a href="{{ route('admin.withdrawals.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('admin.withdrawals*') ? 'active-nav' : '' }}"><i class="fas fa-money-bill-wave w-6"></i> Payouts</a></li>
                        <li><a href="{{ route('admin.users') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('admin.users*') ? 'active-nav' : '' }}"><i class="fas fa-users w-6"></i> Customers</a></li>
                        <li><a href="{{ route('admin.chef') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('admin.chef*') ? 'active-nav' : '' }}"><i class="fas fa-utensils w-6"></i> Kitchens</a></li>
                        <li><a href="{{ route('admin.orders') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('admin.orders*') ? 'active-nav' : '' }}"><i class="fas fa-receipt w-6"></i> All Orders</a></li>
                        <li><a href="{{ route('admin.reports') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('admin.reports*') ? 'active-nav' : '' }}"><i class="fas fa-chart-pie w-6"></i> Reports</a></li>
                        <li><a href="{{ route('admin.settings') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('admin.settings*') ? 'active-nav' : '' }}"><i class="fas fa-cogs w-6"></i> Settings</a></li>
                    @elseif(Auth::user()->hasRole('chef'))
                        <li class="px-6 py-2 text-xs font-bold text-chow-orange-200 uppercase mt-4">Kitchen</li>
                        <li><a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('dashboard') ? 'active-nav' : '' }}"><i class="fas fa-tachometer-alt w-6"></i> Dashboard</a></li>
                        <li><a href="{{ route('chef.menus.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('chef.menus*') ? 'active-nav' : '' }}"><i class="fas fa-book-open w-6"></i> My Menu</a></li>
                        <li><a href="{{ route('chef.orders.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('chef.orders*') ? 'active-nav' : '' }}"><i class="fas fa-box-open w-6"></i> Orders</a></li>
                        <li><a href="{{ route('chef.wallet') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('chef.wallet*') ? 'active-nav' : '' }}"><i class="fas fa-wallet w-6"></i> Wallet</a></li>
                        <li><a href="{{ route('chef.profile.edit') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('chef.profile*') ? 'active-nav' : '' }}"><i class="fas fa-store w-6"></i> Store Profile</a></li>
                        <li><a href="{{ route('chef.profile') }}" target="_blank" class="flex items-center px-6 py-3 hover:bg-white/10"><i class="fas fa-external-link-alt w-6"></i> View Live Store</a></li>
                    @else
                        <li class="px-6 py-2 text-xs font-bold text-chow-orange-200 uppercase mt-4">My Account</li>
                        <li><a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('dashboard*') ? 'active-nav' : '' }}"><i class="fas fa-shopping-bag w-6"></i> Dashboard</a></li>
                        <li><a href="{{ route('customer.orders') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('customer.orders*') ? 'active-nav' : '' }}"><i class="fas fa-shopping-bag w-6"></i> My Orders</a></li>
                        <li><a href="{{ route('customer.profile') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('customer.profile*') ? 'active-nav' : '' }}"><i class="fas fa-user-cog w-6"></i> My Profile</a></li>
                    @endif
                @endauth
            </ul>
        </nav>

        <div class="p-4 bg-black/20">
            @auth
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center text-white/90 hover:text-white w-full">
                        <i class="fas fa-sign-out-alt w-6"></i> Sign Out
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="flex items-center justify-center bg-white text-chow-red-700 font-bold py-2 rounded shadow-sm hover:bg-gray-100 transition-colors">
                    Login / Sign Up
                </a>
            @endauth
        </div>
    </aside>

    {{-- DESKTOP SIDEBAR: Only visible on Desktop --}}
    <aside class="fixed top-0 left-0 z-50 h-screen w-64 bg-chow-red-700 dark:bg-dark-section text-white flex flex-col shadow-xl hidden lg:flex transition-colors duration-300">
        <div class="h-16 flex items-center px-6 border-b border-white/10 dark:border-dark-border bg-black/10 dark:bg-black/20">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                <img src="{{ asset('storage/img/choosechowlogo.png') }}" alt="ChooseChow Logo" 
                     class="h-12 w-12 rounded-full shadow-sm group-hover:scale-105 transition-transform duration-300">
                <span class="text-2xl font-bold text-white tracking-tight group-hover:text-chow-cream-100 transition-colors">ChooseChow</span>
            </a>
        </div>

        <nav class="flex-1 overflow-y-auto py-4">
            <ul class="space-y-1">
                {{-- 1. PUBLIC LINKS --}}
                <li>
                    <a href="{{ route('chef.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('chef.index') ? 'active-nav' : '' }}">
                        <i class="fas fa-search w-6"></i> Find Chow
                    </a>
                </li>
                
                @auth
                    {{-- 2. ADMIN LINKS --}}
                    @if(Auth::user()->hasRole('admin'))
                        <li class="px-6 py-2 text-xs font-bold text-chow-orange-200 dark:text-chow-orange-300 uppercase mt-4">Admin Control</li>
                        <li><a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('dashboard') ? 'active-nav' : '' }}"><i class="fas fa-chart-line w-6"></i> Overview</a></li>
                        <li><a href="{{ route('admin.withdrawals.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('admin.withdrawals*') ? 'active-nav' : '' }}"><i class="fas fa-money-bill-wave w-6"></i> Payouts</a></li>
                        <li><a href="{{ route('admin.users') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('admin.users*') ? 'active-nav' : '' }}"><i class="fas fa-users w-6"></i> Customers</a></li>
                        <li><a href="{{ route('admin.chef') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('admin.chef*') ? 'active-nav' : '' }}"><i class="fas fa-utensils w-6"></i> Kitchens</a></li>
                        <li><a href="{{ route('admin.orders') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('admin.orders*') ? 'active-nav' : '' }}"><i class="fas fa-receipt w-6"></i> All Orders</a></li>
                        <li><a href="{{ route('admin.reports') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('admin.reports*') ? 'active-nav' : '' }}"><i class="fas fa-chart-pie w-6"></i> Reports</a></li>
                        <li><a href="{{ route('admin.settings') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('admin.settings*') ? 'active-nav' : '' }}"><i class="fas fa-cogs w-6"></i> Settings</a></li>

                    {{-- 3. CHEF LINKS --}}
                    @elseif(Auth::user()->hasRole('chef'))
                        <li class="px-6 py-2 text-xs font-bold text-chow-orange-200 dark:text-chow-orange-300 uppercase mt-4">Kitchen</li>
                        <li><a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('dashboard') ? 'active-nav' : '' }}"><i class="fas fa-tachometer-alt w-6"></i> Dashboard</a></li>
                        <li><a href="{{ route('chef.menus.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('chef.menus*') ? 'active-nav' : '' }}"><i class="fas fa-book-open w-6"></i> My Menu</a></li>
                        <li><a href="{{ route('chef.orders.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('chef.orders*') ? 'active-nav' : '' }}"><i class="fas fa-box-open w-6"></i> Orders</a></li>
                        <li><a href="{{ route('chef.wallet') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('chef.wallet*') ? 'active-nav' : '' }}"><i class="fas fa-wallet w-6"></i> Wallet</a></li>
                        <li><a href="{{ route('chef.profile.edit') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('chef.profile*') ? 'active-nav' : '' }}"><i class="fas fa-store w-6"></i> Store Profile</a></li>
                        <li><a href="{{ route('chef.profile') }}" target="_blank" class="flex items-center px-6 py-3 hover:bg-white/10"><i class="fas fa-external-link-alt w-6"></i> View Live Store</a></li>

                    {{-- 4. CUSTOMER LINKS --}}
                    @else
                        <li class="px-6 py-2 text-xs font-bold text-chow-orange-200 dark:text-chow-orange-300 uppercase mt-4">My Account</li>
                        <li><a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('dashboard*') ? 'active-nav' : '' }}"><i class="fas fa-shopping-bag w-6"></i> Dashboard</a></li>
                        <li><a href="{{ route('customer.orders') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('customer.orders*') ? 'active-nav' : '' }}"><i class="fas fa-shopping-bag w-6"></i> My Orders</a></li>
                        <li><a href="{{ route('customer.profile') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('customer.profile*') ? 'active-nav' : '' }}"><i class="fas fa-user-cog w-6"></i> My Profile</a></li>
                    @endif
                      {{-- NEW CONTACT LINK --}}
                <li>
                    <a href="{{ route('contact') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('contact') ? 'active-nav' : '' }}">
                        <i class="fas fa-question-circle w-6"></i> Help 
                    </a>
                </li>
                @endauth
            </ul>
        </nav>

        {{-- BOTTOM ACTION --}}
        <div class="p-4 bg-black/20 dark:bg-black/30">
            @auth
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center text-white/90 hover:text-white w-full">
                        <i class="fas fa-sign-out-alt w-6"></i> Sign Out
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="flex items-center justify-center bg-white dark:bg-chow-orange-500 text-chow-red-700 dark:text-white font-bold py-2 rounded shadow-sm hover:bg-gray-100 dark:hover:bg-chow-orange-600 transition-colors">
                    Login / Sign Up
                </a>
            @endauth
        </div>
    </aside>

    {{-- MAIN CONTENT AREA --}}
    <div class="lg:ml-64 min-h-screen flex flex-col">
        
        {{-- HEADER --}}
        <header class="bg-white dark:bg-dark-section shadow-sm h-16 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-40 border-b border-gray-200 dark:border-dark-border">
            
            {{-- Mobile Menu Trigger & Page Title --}}
            <div class="flex items-center">
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-600 dark:text-content-primary focus:outline-none mr-4">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h1 class="text-lg lg:text-xl font-semibold text-gray-800 dark:text-content-primary truncate">@yield('page_title')</h1>
            </div>

            {{-- DASHBOARD SEARCH BAR --}}
            <form action="{{ route('chef.index') }}" method="GET" class="hidden md:flex items-center flex-1 max-w-lg mx-auto px-6">
                <div class="relative w-full">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 dark:text-content-secondary"></i>
                    </span>
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Search..." 
                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-dark-border rounded-xl leading-5 bg-gray-50 dark:bg-dark-card text-gray-800 dark:text-content-primary placeholder-gray-500 dark:placeholder-content-secondary focus:outline-none focus:bg-white dark:focus:bg-dark-card focus:ring-2 focus:ring-accent focus:border-accent sm:text-sm transition duration-200 ease-in-out"
                    >
                </div>
            </form>
            
            {{-- Right Side: Theme Toggle, Cart & User --}}
            <div class="flex items-center gap-4">
                
                {{-- THEME TOGGLE --}}
                <button @click="darkMode = !darkMode" class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-dark-card transition-colors" title="Toggle theme">
                    <i x-show="!darkMode" class="fas fa-moon text-gray-600 text-lg"></i>
                    <i x-show="darkMode" class="fas fa-sun text-accent-light text-lg"></i>
                </button>
                
                {{-- CART ICON --}}
                @php
                    $cart = session('cart', []);
                    $totalQuantity = 0;
                    if(is_array($cart)) {
                        foreach($cart as $id => $details) {
                            $totalQuantity += $details['quantity'] ?? 1;
                        }
                    }
                @endphp

                <a href="{{ route('cart.index') }}" class="relative group mr-2">
                    <div class="p-2 rounded-xl hover:bg-chow-orange-50 dark:hover:bg-dark-card transition-colors">
                        <i class="fas fa-shopping-cart text-gray-600 dark:text-content-primary text-xl group-hover:text-accent"></i>
                    </div>
                    @if($totalQuantity > 0)
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-accent rounded-full border-2 border-white dark:border-dark-section">
                            {{ $totalQuantity }}
                        </span>
                    @endif
                </a>

                {{-- USER PROFILE / LOGIN --}}
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center border-l pl-4 border-gray-200 dark:border-dark-border focus:outline-none">
                            <div class="text-right mr-3 hidden sm:block">
                                <div class="text-sm font-semibold text-gray-800 dark:text-content-primary">{{ Auth::user()->first_name }}</div>
                                <div class="text-xs text-gray-500 dark:text-content-secondary capitalize">{{ Auth::user()->getRoleNames()->first() ?? 'User' }}</div>
                            </div>
                            <div class="w-9 h-9 rounded-xl bg-accent/20 dark:bg-accent flex items-center justify-center text-accent dark:text-white font-semibold">
                                {{ substr(Auth::user()->first_name, 0, 1) }}
                            </div>
                            <i class="fas fa-chevron-down ml-2 text-xs text-gray-400 dark:text-content-secondary"></i>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div x-show="open" @click.away="open = false" 
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-dark-card rounded-xl shadow-lg py-2 z-50 border border-gray-100 dark:border-dark-border"
                             style="display: none;">
                            
                            @if(Auth::user()->hasRole('chef'))
                                <a href="{{ route('chef.personal.edit') }}" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-content-primary hover:bg-gray-50 dark:hover:bg-dark-section hover:text-accent transition-colors">
                                    <i class="fas fa-user mr-2 w-5 text-center"></i> My Profile
                                </a>
                            @else
                                <a href="{{ route('customer.profile') }}" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-content-primary hover:bg-gray-50 dark:hover:bg-dark-section hover:text-accent transition-colors">
                                    <i class="fas fa-user mr-2 w-5 text-center"></i> My Profile
                                </a>
                            @endif

                            <div class="border-t border-gray-100 dark:border-dark-border my-1"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2.5 text-sm text-accent hover:bg-accent/10 font-semibold transition-colors">
                                    <i class="fas fa-sign-out-alt mr-2 w-5 text-center"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 dark:text-content-primary hover:text-accent transition-colors">Login</a>
                @endauth
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="p-4 lg:p-8 flex-1">
            @if(session('success'))
                <div class="bg-chow-fresh-100 dark:bg-chow-fresh-900/30 border-l-4 border-chow-fresh-500 text-chow-fresh-700 dark:text-chow-fresh-300 p-4 mb-6 rounded shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-chow-red-100 dark:bg-chow-red-900/30 border-l-4 border-chow-red-500 text-chow-red-700 dark:text-chow-red-300 p-4 mb-6 rounded shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    {{-- Initialize dark mode from localStorage before Alpine loads --}}
    <script>
        if (localStorage.getItem('darkMode') === 'true' || 
            (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</body>
</html>
