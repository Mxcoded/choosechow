<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ChooseChow')</title>

    {{-- LOAD ASSETS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- FONTS & ICONS --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        a { text-decoration: none !important; }
        body { font-family: sans-serif; }
    </style>

    {{-- Initialize theme early to prevent flashing --}}
    <script>
        try {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        } catch (e) {}
    </script>
</head>
<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex flex-col min-h-screen transition-colors duration-200">

    {{-- NAVIGATION BAR --}}
    <nav x-data="{ open: false }" class="bg-white sticky top-0 z-50 border-b border-gray-100 backdrop-blur-md bg-opacity-90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                
                {{-- LEFT: LOGO --}}
                <div class="flex items-center">
                    <a href="/" class="flex-shrink-0 flex items-center">
                        <i class="fas fa-utensils text-red-600 text-2xl mr-2"></i>
                        <span class="font-extrabold text-2xl tracking-tighter text-gray-900">
                            Choose<span class="text-red-600">Chow</span>
                        </span>
                    </a>
                </div>

                {{-- CENTER: DESKTOP MENU --}}
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/" class="text-base font-medium {{ request()->is('/') ? 'text-red-600' : 'text-gray-600' }} hover:text-red-600 transition-colors">Home</a>
                    <a href="{{ route('chef.index') }}" class="text-base font-medium {{ request()->routeIs('chef.*') ? 'text-red-600' : 'text-gray-600' }} hover:text-red-600 transition-colors">Find Chow</a>
                    <a href="{{ route('how-it-works') }}" class="text-base font-medium {{ request()->routeIs('how-it-works') ? 'text-red-600' : 'text-gray-600' }} hover:text-red-600 transition-colors">How it Works</a>
                    <a href="{{ route('about') }}" class="text-base font-medium {{ request()->routeIs('about') ? 'text-red-600' : 'text-gray-600' }} hover:text-red-600 transition-colors">About</a>
                    <a href="{{ route('contact') }}" class="text-base font-medium {{ request()->routeIs('contact') ? 'text-red-600' : 'text-gray-600' }} hover:text-red-600 transition-colors">Contact</a>
                </div>

                {{-- RIGHT: ACTIONS --}}
                <div class="hidden md:flex items-center space-x-4">
                    {{-- Theme Toggle --}}
                    <button id="theme-toggle" aria-label="Toggle theme" class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        <i class="fas fa-moon text-lg dark:hidden"></i>
                        <i class="fas fa-sun text-lg hidden dark:inline"></i>
                    </button>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-base font-bold text-gray-900 hover:text-red-600">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-base font-bold text-gray-900 hover:text-red-600">Login</a>
                        <a href="{{ route('register') }}" class="bg-red-600 text-white px-5 py-2.5 rounded-full font-bold hover:bg-red-700 transition shadow-lg shadow-red-600/30">Get Started</a>
                    @endauth
                </div>

                {{-- MOBILE MENU BUTTON --}}
                <div class="-mr-2 flex items-center md:hidden gap-4">
                    <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <button id="mobile-theme-toggle" aria-label="Toggle theme" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                        <i class="fas fa-moon text-lg dark:hidden"></i>
                        <i class="fas fa-sun text-lg hidden dark:inline"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- MOBILE MENU DROPDOWN --}}
        <div x-show="open" class="md:hidden bg-white border-t shadow-xl transition-all" style="display: none;">
            <div class="pt-2 pb-3 space-y-1 px-4">
                <a href="/" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Home</a>
                <a href="{{ route('chef.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Find Chow</a>
                <a href="{{ route('how-it-works') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">How it Works</a>
                <a href="{{ route('about') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">About</a>
                <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Contact</a>
                
                <div class="mt-4 border-t border-gray-100 pt-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="block w-full text-center px-4 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-gray-900 hover:bg-gray-800">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="block w-full text-center px-4 py-3 mb-2 border border-gray-300 rounded-md text-base font-medium text-gray-700 bg-white hover:bg-gray-50">Log in</a>
                        <a href="{{ route('register') }}" class="block w-full text-center px-4 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-red-600 hover:bg-red-700">Get Started</a>
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
    <footer class="bg-white border-t border-gray-100 mt-auto">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                
                {{-- Brand --}}
                <div class="col-span-1">
                    <span class="font-extrabold text-2xl tracking-tighter text-gray-900">
                        Choose<span class="text-red-600">Chow</span>
                    </span>
                    <p class="mt-4 text-sm text-gray-500">
                        Authentic homemade meals delivered to your doorstep.
                    </p>
                </div>

                {{-- Company --}}
                <div class="col-span-1">
                    <h4 class="font-bold text-gray-900 mb-4">Company</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('about') }}" class="text-sm text-gray-500 hover:text-red-600">About Us</a></li>
                        <li><a href="{{ route('how-it-works') }}" class="text-sm text-gray-500 hover:text-red-600">How it Works</a></li>
                        <li><a href="{{ route('chef.index') }}" class="text-sm text-gray-500 hover:text-red-600">Find Chefs</a></li>
                    </ul>
                </div>

                {{-- Legal --}}
                <div class="col-span-1">
                    <h4 class="font-bold text-gray-900 mb-4">Support & Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('contact') }}" class="text-sm text-gray-500 hover:text-red-600">Contact Us</a></li>
                        <li><a href="{{ route('privacy') }}" class="text-sm text-gray-500 hover:text-red-600">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}" class="text-sm text-gray-500 hover:text-red-600">Terms of Service</a></li>
                    </ul>
                </div>

                {{-- Newsletter --}}
                <div class="col-span-1">
                    <h4 class="font-bold text-gray-900 mb-4">Newsletter</h4>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="flex flex-col space-y-2">
                        @csrf
                        
                        @if(session('success'))
                            <div class="bg-green-50 text-green-700 px-3 py-2 rounded-lg text-xs font-bold border border-green-200 mb-2">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="bg-red-50 text-red-700 px-3 py-2 rounded-lg text-xs font-bold border border-red-200 mb-2">
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        <input type="email" name="email" placeholder="Enter your email" required 
                               class="px-4 py-2 rounded-lg bg-gray-50 border border-gray-200 text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-12 border-t border-gray-100 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-400">&copy; {{ date('Y') }} ChooseChow. Abuja, Nigeria.</p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-gray-500"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-gray-400 hover:text-gray-500"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="text-gray-400 hover:text-gray-500"><i class="fab fa-twitter fa-lg"></i></a>
                </div>
            </div>
        </div>
    </footer>

    </body>
</html>