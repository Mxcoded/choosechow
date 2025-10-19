<!-- Header -->
<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <!-- Logo and Brand -->
            <div class="flex items-center space-x-4">
                <a href="{{ route('home') }}" class="flex items-center space-x-3" aria-label="ChooseChow Home">
                    <img src="{{ asset('storage/img/choosechowlogo.png') }}" alt="ChooseChow Logo" 
                         class="h-24 w-24 rounded-full shadow-md transition-transform duration-300 hover:scale-105">
                    <span class="text-3xl font-extrabold text-red-600 tracking-tight">ChooseChow</span>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="hidden md:flex space-x-8">
                <a href="{{ route('home') }}" class="@if(request()->routeIs('home')) text-red-600 font-semibold @else text-gray-700 hover:text-red-600 @endif transition-colors">Home</a>
                <a href="{{ route('chefs.index') }}" class="@if(request()->routeIs('chefs.*')) text-red-600 font-semibold @else text-gray-700 hover:text-red-600 @endif transition-colors">Find Chefs</a>
                <a href="{{ route('subscriptions.plans') }}" class="@if(request()->routeIs('subscriptions.*')) text-red-600 font-semibold @else text-gray-700 hover:text-red-600 @endif transition-colors">Subscriptions</a>
                <a href="{{ route('how-it-works') }}" class="@if(request()->routeIs('how-it-works')) text-red-600 font-semibold @else text-gray-700 hover:text-red-600 @endif transition-colors">How It Works</a>
                <a href="{{ route('about') }}" class="@if(request()->routeIs('about')) text-red-600 font-semibold @else text-gray-700 hover:text-red-600 @endif transition-colors">About</a>
                <a href="{{ route('contact') }}" class="@if(request()->routeIs('contact')) text-red-600 font-semibold @else text-gray-700 hover:text-red-600 @endif transition-colors">Contact</a>  
            </nav>

            <!-- Auth Buttons -->
            <div class="flex items-center space-x-4">
            @guest
    
                <a href="{{ route('login') }}" class="text-gray-700 hover:text-red-600 transition-colors font-medium">Login</a>
                <a href="{{ route('register') }}" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition-colors font-semibold">Sign Up</a>
            
            @endguest
            @auth
                <a href="{{ route('dashboard') }}" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition-colors font-semibold">Dashboard</a>
            @endauth
            </div>
        </div>
    </div>
</header>