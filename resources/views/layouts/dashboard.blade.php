<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Dashboard - ChooseChow')</title>
    
    {{-- LOAD ASSETS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: sans-serif; background-color: #f9fafb; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    {{-- SIDEBAR: Only visible on Desktop --}}
    <aside class="fixed top-0 left-0 z-50 h-screen w-64 bg-red-700 text-white flex flex-col shadow-xl hidden lg:flex">
        <div class="h-16 flex items-center px-6 border-b border-white/10 bg-black/10">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                    <img src="{{ asset('storage/img/choosechowlogo.png') }}" alt="ChooseChow Logo" 
                         class="h-12 w-12 rounded-full shadow-sm group-hover:scale-105 transition-transform duration-300">
                    <span class="text-2xl font-bold text-red-600 tracking-tight group-hover:text-red-700 transition-colors">ChooseChow</span>
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
                {{-- NEW CONTACT LINK --}}
                <li>
                    <a href="{{ route('contact') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('contact') ? 'active-nav' : '' }}">
                        <i class="fas fa-envelope w-6"></i> Contact Us
                    </a>
                </li>
                
                @auth
                    {{-- 2. ADMIN LINKS --}}
                    @if(Auth::user()->hasRole('admin'))
                        <li class="px-6 py-2 text-xs font-bold text-red-200 uppercase mt-4">Admin Control</li>
                        <li><a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('dashboard') ? 'active-nav' : '' }}"><i class="fas fa-chart-line w-6"></i> Overview</a></li>
                        <li><a href="{{ route('admin.withdrawals.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('admin.withdrawals*') ? 'active-nav' : '' }}"><i class="fas fa-money-bill-wave w-6"></i> Payouts</a></li>
                        <li><a href="{{ route('admin.users') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('admin.users*') ? 'active-nav' : '' }}"><i class="fas fa-users w-6"></i> Customers</a></li>
                        <li><a href="{{ route('admin.chef') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('admin.chef*') ? 'active-nav' : '' }}"><i class="fas fa-utensils w-6"></i> Kitchens</a></li>
                        <li><a href="{{ route('admin.orders') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('admin.orders*') ? 'active-nav' : '' }}"><i class="fas fa-receipt w-6"></i> All Orders</a></li>
                        <li><a href="{{ route('admin.reports') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('admin.reports*') ? 'active-nav' : '' }}"><i class="fas fa-chart-pie w-6"></i> Reports</a></li>
                        <li><a href="{{ route('admin.settings') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('admin.settings*') ? 'active-nav' : '' }}"><i class="fas fa-cogs w-6"></i> Settings</a></li>

                    {{-- 3. CHEF LINKS --}}
                    @elseif(Auth::user()->hasRole('chef'))
                        <li class="px-6 py-2 text-xs font-bold text-red-200 uppercase mt-4">Kitchen</li>
                        <li><a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('dashboard') ? 'active-nav' : '' }}"><i class="fas fa-tachometer-alt w-6"></i> Dashboard</a></li>
                        <li><a href="{{ route('chef.menus.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('chef.menus*') ? 'active-nav' : '' }}"><i class="fas fa-book-open w-6"></i> My Menu</a></li>
                        <li><a href="{{ route('chef.orders.index') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('chef.orders*') ? 'active-nav' : '' }}"><i class="fas fa-box-open w-6"></i> Orders</a></li>
                        <li><a href="{{ route('chef.wallet') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('chef.wallet*') ? 'active-nav' : '' }}"><i class="fas fa-wallet w-6"></i> Wallet</a></li>
                        <li><a href="{{ route('chef.profile.edit') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('chef.profile*') ? 'active-nav' : '' }}"><i class="fas fa-store w-6"></i> Store Profile</a></li>
                        <li><a href="{{ route('chef.profile') }}" target="_blank" class="flex items-center px-6 py-3 hover:bg-white/10"><i class="fas fa-external-link-alt w-6"></i> View Live Store</a></li>

                    {{-- 4. CUSTOMER LINKS --}}
                    @else
                        <li class="px-6 py-2 text-xs font-bold text-red-200 uppercase mt-4">My Account</li>
                        <li><a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('dashboard*') ? 'active-nav' : '' }}"><i class="fas fa-shopping-bag w-6"></i> Dashboard</a></li>
                        <li><a href="{{ route('customer.orders') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('customer.orders*') ? 'active-nav' : '' }}"><i class="fas fa-shopping-bag w-6"></i> My Orders</a></li>
                        <li><a href="{{ route('customer.profile') }}" class="flex items-center px-6 py-3 hover:bg-white/10 {{ request()->routeIs('customer.profile*') ? 'active-nav' : '' }}"><i class="fas fa-user-cog w-6"></i> My Profile</a></li>
                    @endif
                @endauth
            </ul>
        </nav>

        {{-- BOTTOM ACTION --}}
        <div class="p-4 bg-black/20">
            @auth
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center text-white/90 hover:text-white w-full">
                        <i class="fas fa-sign-out-alt w-6"></i> Sign Out
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="flex items-center justify-center bg-white text-red-700 font-bold py-2 rounded shadow-sm hover:bg-gray-100">
                    Login / Sign Up
                </a>
            @endauth
        </div>
    </aside>

    {{-- MAIN CONTENT AREA --}}
    <div class="lg:ml-64 min-h-screen flex flex-col">
        
        {{-- HEADER --}}
        <header class="bg-white shadow-sm h-16 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-40">
            
            {{-- Mobile Menu Trigger & Page Title --}}
            <div class="flex items-center">
                <button class="lg:hidden text-gray-600 focus:outline-none mr-4">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h1 class="text-lg lg:text-xl font-bold text-gray-700 truncate">@yield('page_title')</h1>
            </div>

            {{-- DASHBOARD SEARCH BAR --}}
            <form action="{{ route('chef.index') }}" method="GET" class="hidden md:flex items-center flex-1 max-w-lg mx-auto px-6">
                <div class="relative w-full">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </span>
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Search..." 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-gray-50 placeholder-gray-500 focus:outline-none focus:bg-white focus:ring-1 focus:ring-red-500 focus:border-red-500 sm:text-sm transition duration-150 ease-in-out"
                    >
                </div>
            </form>
            
            {{-- Right Side: Cart & User --}}
            <div class="flex items-center gap-4">
                
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
                    <div class="p-2 rounded-full hover:bg-red-50 transition-colors">
                        <i class="fas fa-shopping-cart text-gray-600 text-xl group-hover:text-red-600"></i>
                    </div>
                    @if($totalQuantity > 0)
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full border-2 border-white">
                            {{ $totalQuantity }}
                        </span>
                    @endif
                </a>

                {{-- USER PROFILE / LOGIN --}}
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center border-l pl-4 border-gray-200 focus:outline-none">
                            <div class="text-right mr-3 hidden sm:block">
                                <div class="text-sm font-bold text-gray-700">{{ Auth::user()->first_name }}</div>
                                <div class="text-xs text-gray-400 capitalize">{{ Auth::user()->getRoleNames()->first() ?? 'User' }}</div>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600 font-bold border border-red-200">
                                {{ substr(Auth::user()->first_name, 0, 1) }}
                            </div>
                            <i class="fas fa-chevron-down ml-2 text-xs text-gray-400"></i>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div x-show="open" @click.away="open = false" 
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg py-2 z-50 border border-gray-100"
                             style="display: none;">
                            
                            @if(Auth::user()->hasRole('chef'))
                                <a href="{{ route('chef.personal.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-red-600">
                                    <i class="fas fa-user mr-2 w-5 text-center"></i> My Profile
                                </a>
                            @else
                                <a href="{{ route('customer.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-red-600">
                                    <i class="fas fa-user mr-2 w-5 text-center"></i> My Profile
                                </a>
                            @endif

                            <div class="border-t border-gray-100 my-1"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-bold">
                                    <i class="fas fa-sign-out-alt mr-2 w-5 text-center"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-bold text-gray-600 hover:text-red-600">Login</a>
                @endauth
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="p-4 lg:p-8 flex-1">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>