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

    {{-- ALPINE.JS --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- FONTS & ICONS --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        a { text-decoration: none !important; }
        body { font-family: 'Poppins', sans-serif; }
        
        /* Premium Dark Theme Styles */
        .text-appetizing {
            background: linear-gradient(135deg, #E76F51, #F4A261);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .bg-appetizing-gradient {
            background: linear-gradient(135deg, #E76F51 0%, #F4A261 100%);
        }
        .star-glow {
            text-shadow: 0 0 8px rgba(244, 162, 97, 0.5);
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        /* Premium card hover effect */
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }
        .dark .card-hover:hover {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }
    </style>
</head>
<body class="bg-chow-cream-50 dark:bg-dark-base text-gray-900 dark:text-content-primary flex flex-col min-h-screen transition-colors duration-300">

    {{-- NAVIGATION BAR --}}
    <nav x-data="{ open: false }" class="relative bg-gradient-to-br from-chow-cream-50 to-chow-cream-100 dark:from-dark-base dark:to-dark-section overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                
                {{-- LEFT: LOGO --}}
                <div class="h-16 flex items-center">
                    <a href="/" class="flex-shrink-0 flex items-center group">
                       <img src="{{ asset('storage/img/choosechowlogo.png') }}" alt="ChooseChow Logo" 
                     class="h-12 w-12 rounded-full shadow-md group-hover:scale-105 transition-transform duration-300">
                        &nbsp;<span class="font-bold text-2xl tracking-tight text-gray-900 dark:text-content-primary">
                            Choose<span class="text-chow-red-600 dark:text-accent">Chow</span>
                        </span>
                    </a>
                </div>

                {{-- CENTER: DESKTOP MENU --}}
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/" class="text-base font-medium {{ request()->is('/') ? 'text-chow-red-600 dark:text-accent' : 'text-chow-brown-600 dark:text-content-primary' }} hover:text-chow-orange-500 dark:hover:text-accent transition-colors">Home</a>
                    <a href="{{ route('chef.index') }}" class="text-base font-medium {{ request()->routeIs('chef.*') ? 'text-chow-red-600 dark:text-accent' : 'text-chow-brown-600 dark:text-content-primary' }} hover:text-chow-orange-500 dark:hover:text-accent transition-colors">Find Chow</a>
                    <a href="{{ route('how-it-works') }}" class="text-base font-medium {{ request()->routeIs('how-it-works') ? 'text-chow-red-600 dark:text-accent' : 'text-chow-brown-600 dark:text-content-primary' }} hover:text-chow-orange-500 dark:hover:text-accent transition-colors">How it Works</a>
                    <a href="{{ route('about') }}" class="text-base font-medium {{ request()->routeIs('about') ? 'text-chow-red-600 dark:text-accent' : 'text-chow-brown-600 dark:text-content-primary' }} hover:text-chow-orange-500 dark:hover:text-accent transition-colors">About</a>
                    <a href="{{ route('contact') }}" class="text-base font-medium {{ request()->routeIs('contact') ? 'text-chow-red-600 dark:text-accent' : 'text-chow-brown-600 dark:text-content-primary' }} hover:text-chow-orange-500 dark:hover:text-accent transition-colors">Contact</a>
                </div>

                {{-- RIGHT: ACTIONS --}}
                <div class="hidden md:flex items-center space-x-4">
                    {{-- THEME TOGGLE --}}
                    <button @click="darkMode = !darkMode" class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-dark-card transition-colors" title="Toggle theme">
                        <i x-show="!darkMode" class="fas fa-moon text-chow-brown-600 text-lg"></i>
                        <i x-show="darkMode" class="fas fa-sun text-accent-light text-lg"></i>
                    </button>
                    
                    {{-- CART BUTTON --}}
                    @php
                        $cartCount = count(session('cart', []));
                        $cartTotal = 0;
                        foreach(session('cart', []) as $item) {
                            $cartTotal += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
                        }
                    @endphp
                    <a href="{{ route('cart.index') }}" class="relative p-2 rounded-xl hover:bg-chow-orange-50 dark:hover:bg-dark-card transition-colors group" title="View Cart">
                        <i class="fas fa-shopping-bag text-chow-brown-600 dark:text-content-primary text-lg group-hover:text-chow-orange-500 dark:group-hover:text-accent"></i>
                        @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 bg-chow-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                    
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-base font-semibold text-chow-brown-800 dark:text-content-primary hover:text-chow-orange-500 dark:hover:text-accent transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-base font-semibold text-chow-brown-800 dark:text-content-primary hover:text-chow-orange-500 dark:hover:text-accent transition-colors">Login</a>
                        <a href="{{ route('register') }}" class="bg-accent hover:bg-accent-hover text-white px-6 py-2.5 rounded-xl font-semibold transition-all shadow-lg shadow-accent/30 hover:shadow-accent/50 hover:-translate-y-0.5">Get Started</a>
                    @endauth
                </div>

                {{-- MOBILE MENU BUTTON --}}
                <div class="-mr-2 flex items-center md:hidden gap-3">
                    {{-- Mobile Cart --}}
                    <a href="{{ route('cart.index') }}" class="relative p-2 rounded-xl hover:bg-chow-orange-50 dark:hover:bg-dark-card transition-colors">
                        <i class="fas fa-shopping-bag text-chow-brown-600 dark:text-content-primary text-lg"></i>
                        @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 bg-accent text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                    {{-- Mobile Theme Toggle --}}
                    <button @click="darkMode = !darkMode" class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-dark-card transition-colors">
                        <i x-show="!darkMode" class="fas fa-moon text-chow-brown-600 text-lg"></i>
                        <i x-show="darkMode" class="fas fa-sun text-accent-light text-lg"></i>
                    </button>
                    <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-xl text-chow-brown-600 dark:text-content-primary hover:text-chow-orange-500 dark:hover:text-accent hover:bg-chow-cream-100 dark:hover:bg-dark-card focus:outline-none transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- MOBILE MENU DROPDOWN --}}
        <div x-show="open" class="md:hidden bg-white dark:bg-dark-card border-t border-chow-cream-200 dark:border-dark-border shadow-xl transition-all" style="display: none;">
            <div class="pt-2 pb-3 space-y-1 px-4">
                <a href="/" class="block px-4 py-3 rounded-xl text-base font-medium text-chow-brown-600 dark:text-content-primary hover:text-accent hover:bg-chow-cream-50 dark:hover:bg-dark-section transition-colors">Home</a>
                <a href="{{ route('chef.index') }}" class="block px-4 py-3 rounded-xl text-base font-medium text-chow-brown-600 dark:text-content-primary hover:text-accent hover:bg-chow-cream-50 dark:hover:bg-dark-section transition-colors">Find Chow</a>
                <a href="{{ route('how-it-works') }}" class="block px-4 py-3 rounded-xl text-base font-medium text-chow-brown-600 dark:text-content-primary hover:text-accent hover:bg-chow-cream-50 dark:hover:bg-dark-section transition-colors">How it Works</a>
                <a href="{{ route('about') }}" class="block px-4 py-3 rounded-xl text-base font-medium text-chow-brown-600 dark:text-content-primary hover:text-accent hover:bg-chow-cream-50 dark:hover:bg-dark-section transition-colors">About</a>
                <a href="{{ route('contact') }}" class="block px-4 py-3 rounded-xl text-base font-medium text-chow-brown-600 dark:text-content-primary hover:text-accent hover:bg-chow-cream-50 dark:hover:bg-dark-section transition-colors">Contact</a>
                
                <div class="mt-4 border-t border-chow-cream-200 dark:border-dark-border pt-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="block w-full text-center px-4 py-3 rounded-xl shadow-md text-base font-semibold text-white bg-accent hover:bg-accent-hover transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="block w-full text-center px-4 py-3 mb-2 border border-chow-cream-300 dark:border-dark-border rounded-xl text-base font-medium text-chow-brown-600 dark:text-content-primary bg-white dark:bg-dark-section hover:bg-chow-cream-50 dark:hover:bg-dark-card transition-colors">Log in</a>
                        <a href="{{ route('register') }}" class="block w-full text-center px-4 py-3 rounded-xl shadow-md text-base font-semibold text-white bg-accent hover:bg-accent-hover transition-colors">Get Started</a>
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
    <footer class="bg-chow-cream-100 dark:bg-dark-section border-t border-chow-cream-200 dark:border-dark-border mt-auto transition-colors duration-300">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                
                {{-- Brand --}}
                <div class="col-span-1">
                    <span class="font-bold text-2xl tracking-tight text-chow-brown-800 dark:text-content-primary">
                        Choose<span class="text-chow-red-600 dark:text-accent">Chow</span>
                    </span>
                    <p class="mt-4 text-sm text-chow-brown-600 dark:text-content-secondary">
                        Authentic homemade meals delivered to your doorstep.
                    </p>
                </div>

                {{-- Company --}}
                <div class="col-span-1">
                    <h4 class="font-semibold text-chow-brown-800 dark:text-content-primary mb-4">Company</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ route('about') }}" class="text-sm text-chow-brown-600 dark:text-content-secondary hover:text-accent transition-colors">About Us</a></li>
                        <li><a href="{{ route('how-it-works') }}" class="text-sm text-chow-brown-600 dark:text-content-secondary hover:text-accent transition-colors">How it Works</a></li>
                        <li><a href="{{ route('chef.index') }}" class="text-sm text-chow-brown-600 dark:text-content-secondary hover:text-accent transition-colors">Find Chefs</a></li>
                    </ul>
                </div>

                {{-- Legal --}}
                <div class="col-span-1">
                    <h4 class="font-semibold text-chow-brown-800 dark:text-content-primary mb-4">Support & Legal</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ route('contact') }}" class="text-sm text-chow-brown-600 dark:text-content-secondary hover:text-accent transition-colors">Contact Us</a></li>
                        <li><a href="{{ route('privacy') }}" class="text-sm text-chow-brown-600 dark:text-content-secondary hover:text-accent transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}" class="text-sm text-chow-brown-600 dark:text-content-secondary hover:text-accent transition-colors">Terms of Service</a></li>
                    </ul>
                </div>

                {{-- Newsletter --}}
                <div class="col-span-1">
                    <h4 class="font-semibold text-chow-brown-800 dark:text-content-primary mb-4">Newsletter</h4>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="flex flex-col space-y-3">
                        @csrf
                        
                        @if(session('success'))
                            <div class="bg-chow-fresh-50 dark:bg-chow-fresh-900/30 text-chow-fresh-700 dark:text-chow-fresh-300 px-3 py-2 rounded-xl text-xs font-semibold border border-chow-fresh-200 dark:border-chow-fresh-800 mb-2">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="bg-chow-red-50 dark:bg-chow-red-900/30 text-chow-red-700 dark:text-chow-red-300 px-3 py-2 rounded-xl text-xs font-semibold border border-chow-red-200 dark:border-chow-red-800 mb-2">
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        <input type="email" name="email" placeholder="Enter your email" required 
                               class="px-4 py-3 rounded-xl bg-white dark:bg-dark-card border border-chow-cream-300 dark:border-dark-border text-chow-brown-800 dark:text-content-primary placeholder-gray-400 dark:placeholder-content-secondary focus:outline-none focus:ring-2 focus:ring-accent focus:border-accent transition-all">
                        <button type="submit" class="px-4 py-3 bg-accent hover:bg-accent-hover text-white font-semibold rounded-xl transition-all shadow-md hover:shadow-lg">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-12 border-t border-chow-cream-300 dark:border-dark-border pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-chow-brown-500 dark:text-content-secondary">&copy; {{ date('Y') }} ChooseChow. Abuja, Nigeria.</p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="text-chow-brown-400 dark:text-content-secondary hover:text-accent transition-colors"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-chow-brown-400 dark:text-content-secondary hover:text-accent transition-colors"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="text-chow-brown-400 dark:text-content-secondary hover:text-accent transition-colors"><i class="fab fa-twitter fa-lg"></i></a>
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
           class="group flex items-center gap-4 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border pl-4 pr-6 py-3 rounded-2xl shadow-2xl hover:shadow-accent/20 transition-all transform hover:scale-105">
            <div class="relative">
                <div class="w-12 h-12 bg-accent rounded-xl flex items-center justify-center shadow-lg shadow-accent/30">
                    <i class="fas fa-shopping-bag text-white text-lg"></i>
                </div>
                <span class="absolute -top-1 -right-1 bg-accent-light text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                    {{ $cartCount }}
                </span>
            </div>
            <div>
                <div class="text-xs text-gray-500 dark:text-content-secondary font-medium">Your Cart</div>
                <div class="text-lg font-bold text-gray-900 dark:text-content-primary group-hover:text-accent transition-colors">₦{{ number_format($cartTotal) }}</div>
            </div>
            <i class="fas fa-arrow-right text-gray-400 dark:text-content-secondary group-hover:text-accent group-hover:translate-x-1 transition-all"></i>
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
