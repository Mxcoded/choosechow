<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/img/choosechowlogo.png') }}">

    <title>@yield('title', 'ChooseChow')</title>

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
                    }
                }
            }
        }
    </script>

    {{-- BOOTSTRAP CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- ALPINE.JS --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- FONTS & ICONS --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        a { text-decoration: none !important; }
        body { font-family: 'Figtree', sans-serif; }
        
        /* Food Psychology Custom Styles */
        .text-appetizing {
            background: linear-gradient(135deg, #dc2626, #f97316);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .bg-appetizing-gradient {
            background: linear-gradient(135deg, #dc2626 0%, #f97316 100%);
        }
        .star-glow {
            text-shadow: 0 0 8px rgba(250, 204, 21, 0.5);
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-chow-cream-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex flex-col min-h-screen transition-colors duration-200">

    {{-- NAVIGATION BAR --}}
    <nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 sticky top-0 z-50 border-b border-chow-cream-200 dark:border-gray-700 backdrop-blur-md bg-opacity-95 dark:bg-opacity-95 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                
                {{-- LEFT: LOGO --}}
                <div class="h-16 flex items-center">
                    <a href="/" class="flex-shrink-0 flex items-center group">
                       <img src="{{ asset('storage/img/choosechowlogo.png') }}" alt="ChooseChow Logo" 
                     class="h-12 w-12 rounded-full shadow-sm group-hover:scale-105 transition-transform duration-300">
                        <span class="font-extrabold text-2xl tracking-tighter text-gray-900 dark:text-white">
                            Choose<span class="text-chow-red-600 dark:text-chow-orange-500">Chow</span>
                        </span>
                    </a>
                </div>

                {{-- CENTER: DESKTOP MENU --}}
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/" class="text-base font-medium {{ request()->is('/') ? 'text-chow-red-600 dark:text-chow-orange-400' : 'text-chow-brown-600 dark:text-gray-300' }} hover:text-chow-orange-500 transition-colors">Home</a>
                    <a href="{{ route('chef.index') }}" class="text-base font-medium {{ request()->routeIs('chef.*') ? 'text-chow-red-600 dark:text-chow-orange-400' : 'text-chow-brown-600 dark:text-gray-300' }} hover:text-chow-orange-500 transition-colors">Find Chow</a>
                    <a href="{{ route('how-it-works') }}" class="text-base font-medium {{ request()->routeIs('how-it-works') ? 'text-chow-red-600 dark:text-chow-orange-400' : 'text-chow-brown-600 dark:text-gray-300' }} hover:text-chow-orange-500 transition-colors">How it Works</a>
                    <a href="{{ route('about') }}" class="text-base font-medium {{ request()->routeIs('about') ? 'text-chow-red-600 dark:text-chow-orange-400' : 'text-chow-brown-600 dark:text-gray-300' }} hover:text-chow-orange-500 transition-colors">About</a>
                    <a href="{{ route('contact') }}" class="text-base font-medium {{ request()->routeIs('contact') ? 'text-chow-red-600 dark:text-chow-orange-400' : 'text-chow-brown-600 dark:text-gray-300' }} hover:text-chow-orange-500 transition-colors">Contact</a>
                </div>

                {{-- RIGHT: ACTIONS --}}
                <div class="hidden md:flex items-center space-x-4">
                    {{-- THEME TOGGLE --}}
                    <button @click="darkMode = !darkMode" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="Toggle theme">
                        <i x-show="!darkMode" class="fas fa-moon text-chow-brown-600 text-lg"></i>
                        <i x-show="darkMode" class="fas fa-sun text-chow-gold-400 text-lg"></i>
                    </button>
                    
                    {{-- CART BUTTON --}}
                    @php
                        $cartCount = count(session('cart', []));
                        $cartTotal = 0;
                        foreach(session('cart', []) as $item) {
                            $cartTotal += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
                        }
                    @endphp
                    <a href="{{ route('cart.index') }}" class="relative p-2 rounded-full hover:bg-chow-orange-50 dark:hover:bg-gray-700 transition-colors group" title="View Cart">
                        <i class="fas fa-shopping-bag text-chow-brown-600 dark:text-gray-300 text-lg group-hover:text-chow-orange-500"></i>
                        @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 bg-chow-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                    
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-base font-bold text-chow-brown-800 dark:text-white hover:text-chow-orange-500 transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-base font-bold text-chow-brown-800 dark:text-white hover:text-chow-orange-500 transition-colors">Login</a>
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-chow-red-600 to-chow-orange-500 text-white px-5 py-2.5 rounded-full font-bold hover:from-chow-red-700 hover:to-chow-orange-600 transition-all shadow-lg shadow-chow-red-600/30 hover:shadow-chow-orange-500/40">Get Started</a>
                    @endauth
                </div>

                {{-- MOBILE MENU BUTTON --}}
                <div class="-mr-2 flex items-center md:hidden gap-3">
                    {{-- Mobile Cart --}}
                    <a href="{{ route('cart.index') }}" class="relative p-2 rounded-full hover:bg-chow-orange-50 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-shopping-bag text-chow-brown-600 dark:text-gray-300 text-lg"></i>
                        @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 bg-chow-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                    {{-- Mobile Theme Toggle --}}
                    <button @click="darkMode = !darkMode" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i x-show="!darkMode" class="fas fa-moon text-chow-brown-600 text-lg"></i>
                        <i x-show="darkMode" class="fas fa-sun text-chow-gold-400 text-lg"></i>
                    </button>
                    <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-chow-brown-600 dark:text-gray-300 hover:text-chow-orange-500 hover:bg-chow-cream-100 dark:hover:bg-gray-700 focus:outline-none transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- MOBILE MENU DROPDOWN --}}
        <div x-show="open" class="md:hidden bg-white dark:bg-gray-800 border-t border-chow-cream-200 dark:border-gray-700 shadow-xl transition-all" style="display: none;">
            <div class="pt-2 pb-3 space-y-1 px-4">
                <a href="/" class="block px-3 py-2 rounded-md text-base font-medium text-chow-brown-600 dark:text-gray-300 hover:text-chow-orange-500 hover:bg-chow-cream-50 dark:hover:bg-gray-700">Home</a>
                <a href="{{ route('chef.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-chow-brown-600 dark:text-gray-300 hover:text-chow-orange-500 hover:bg-chow-cream-50 dark:hover:bg-gray-700">Find Chow</a>
                <a href="{{ route('how-it-works') }}" class="block px-3 py-2 rounded-md text-base font-medium text-chow-brown-600 dark:text-gray-300 hover:text-chow-orange-500 hover:bg-chow-cream-50 dark:hover:bg-gray-700">How it Works</a>
                <a href="{{ route('about') }}" class="block px-3 py-2 rounded-md text-base font-medium text-chow-brown-600 dark:text-gray-300 hover:text-chow-orange-500 hover:bg-chow-cream-50 dark:hover:bg-gray-700">About</a>
                <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-md text-base font-medium text-chow-brown-600 dark:text-gray-300 hover:text-chow-orange-500 hover:bg-chow-cream-50 dark:hover:bg-gray-700">Contact</a>
                
                <div class="mt-4 border-t border-chow-cream-200 dark:border-gray-700 pt-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="block w-full text-center px-4 py-3 border border-transparent rounded-full shadow-sm text-base font-medium text-white bg-chow-brown-800 hover:bg-chow-brown-900">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="block w-full text-center px-4 py-3 mb-2 border border-chow-cream-300 dark:border-gray-600 rounded-full text-base font-medium text-chow-brown-600 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-chow-cream-50 dark:hover:bg-gray-600">Log in</a>
                        <a href="{{ route('register') }}" class="block w-full text-center px-4 py-3 border border-transparent rounded-full shadow-sm text-base font-medium text-white bg-gradient-to-r from-chow-red-600 to-chow-orange-500 hover:from-chow-red-700 hover:to-chow-orange-600">Get Started</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- PAGE CONTENT --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-chow-cream-100 dark:bg-gray-800 border-t border-chow-cream-200 dark:border-gray-700 mt-auto transition-colors duration-200">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                
                {{-- Brand --}}
                <div class="col-span-1">
                    <span class="font-extrabold text-2xl tracking-tighter text-chow-brown-800 dark:text-white">
                        Choose<span class="text-chow-red-600 dark:text-chow-orange-500">Chow</span>
                    </span>
                    <p class="mt-4 text-sm text-chow-brown-600 dark:text-gray-400">
                        Authentic homemade meals delivered to your doorstep.
                    </p>
                </div>

                {{-- Company --}}
                <div class="col-span-1">
                    <h4 class="font-bold text-chow-brown-800 dark:text-white mb-4">Company</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('about') }}" class="text-sm text-chow-brown-600 dark:text-gray-400 hover:text-chow-orange-500 transition-colors">About Us</a></li>
                        <li><a href="{{ route('how-it-works') }}" class="text-sm text-chow-brown-600 dark:text-gray-400 hover:text-chow-orange-500 transition-colors">How it Works</a></li>
                        <li><a href="{{ route('chef.index') }}" class="text-sm text-chow-brown-600 dark:text-gray-400 hover:text-chow-orange-500 transition-colors">Find Chefs</a></li>
                    </ul>
                </div>

                {{-- Legal --}}
                <div class="col-span-1">
                    <h4 class="font-bold text-chow-brown-800 dark:text-white mb-4">Support & Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('contact') }}" class="text-sm text-chow-brown-600 dark:text-gray-400 hover:text-chow-orange-500 transition-colors">Contact Us</a></li>
                        <li><a href="{{ route('privacy') }}" class="text-sm text-chow-brown-600 dark:text-gray-400 hover:text-chow-orange-500 transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}" class="text-sm text-chow-brown-600 dark:text-gray-400 hover:text-chow-orange-500 transition-colors">Terms of Service</a></li>
                    </ul>
                </div>

                {{-- Newsletter --}}
                <div class="col-span-1">
                    <h4 class="font-bold text-chow-brown-800 dark:text-white mb-4">Newsletter</h4>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="flex flex-col space-y-2">
                        @csrf
                        
                        @if(session('success'))
                            <div class="bg-chow-fresh-50 dark:bg-chow-fresh-900/30 text-chow-fresh-700 dark:text-chow-fresh-300 px-3 py-2 rounded-lg text-xs font-bold border border-chow-fresh-200 dark:border-chow-fresh-800 mb-2">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="bg-chow-red-50 dark:bg-chow-red-900/30 text-chow-red-700 dark:text-chow-red-300 px-3 py-2 rounded-lg text-xs font-bold border border-chow-red-200 dark:border-chow-red-800 mb-2">
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        <input type="email" name="email" placeholder="Enter your email" required 
                               class="px-4 py-2 rounded-lg bg-white dark:bg-gray-700 border border-chow-cream-300 dark:border-gray-600 text-chow-brown-800 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-chow-orange-500 focus:border-chow-orange-500">
                        <button type="submit" class="px-4 py-2 bg-gradient-to-r from-chow-red-600 to-chow-orange-500 text-white font-bold rounded-lg hover:from-chow-red-700 hover:to-chow-orange-600 transition-all">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-12 border-t border-chow-cream-300 dark:border-gray-700 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-chow-brown-500 dark:text-gray-500">&copy; {{ date('Y') }} ChooseChow. Abuja, Nigeria.</p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="text-chow-brown-400 dark:text-gray-500 hover:text-chow-orange-500 transition-colors"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-chow-brown-400 dark:text-gray-500 hover:text-chow-orange-500 transition-colors"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="text-chow-brown-400 dark:text-gray-500 hover:text-chow-orange-500 transition-colors"><i class="fab fa-twitter fa-lg"></i></a>
                </div>
            </div>
        </div>
    </footer>

    {{-- FLOATING CART BUTTON (shows when cart has items) --}}
    @if($cartCount > 0)
    <div class="fixed bottom-6 right-6 z-50 md:hidden">
        <a href="{{ route('cart.index') }}" 
           class="flex items-center gap-3 bg-gradient-to-r from-chow-red-600 to-chow-orange-500 text-white px-5 py-3 rounded-full shadow-2xl shadow-chow-red-600/40 hover:shadow-chow-orange-500/50 transition-all transform hover:scale-105">
            <i class="fas fa-shopping-bag text-lg"></i>
            <span class="font-bold">View Cart ({{ $cartCount }})</span>
            <span class="bg-white/20 px-2 py-0.5 rounded-full text-sm font-bold">₦{{ number_format($cartTotal) }}</span>
        </a>
    </div>
    @endif

    {{-- Desktop Floating Cart (bottom right) --}}
    @if($cartCount > 0)
    <div class="hidden md:block fixed bottom-6 right-6 z-50">
        <a href="{{ route('cart.index') }}" 
           class="group flex items-center gap-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 pl-4 pr-6 py-3 rounded-full shadow-2xl hover:shadow-chow-orange-200 dark:hover:shadow-none transition-all transform hover:scale-105">
            <div class="relative">
                <div class="w-12 h-12 bg-gradient-to-r from-chow-red-600 to-chow-orange-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-shopping-bag text-white text-lg"></i>
                </div>
                <span class="absolute -top-1 -right-1 bg-chow-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                    {{ $cartCount }}
                </span>
            </div>
            <div>
                <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Your Cart</div>
                <div class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-chow-orange-500 transition-colors">₦{{ number_format($cartTotal) }}</div>
            </div>
            <i class="fas fa-arrow-right text-gray-400 group-hover:text-chow-orange-500 group-hover:translate-x-1 transition-all"></i>
        </a>
    </div>
    @endif

    {{-- Initialize dark mode from localStorage before Alpine loads --}}
    <script>
        if (localStorage.getItem('darkMode') === 'true' || 
            (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</body>
</html>
