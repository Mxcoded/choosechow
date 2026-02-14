<header class="bg-white shadow-sm sticky top-0 z-50 transition-all duration-300 font-sans" id="main-header">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('welcome') }}" class="flex items-center gap-2 group">
                    <img src="{{ asset('storage/img/choosechowlogo.png') }}" alt="ChooseChow Logo" 
                         class="h-12 w-12 rounded-full shadow-sm group-hover:scale-105 transition-transform duration-300">
                    <span class="text-2xl font-bold text-red-600 tracking-tight group-hover:text-red-700 transition-colors">ChooseChow</span>
                </a>
            </div>

            <nav class="hidden md:flex space-x-8">
                <a href="{{ route('welcome') }}" class="text-gray-600 hover:text-red-600 font-medium transition-colors {{ request()->routeIs('welcome') ? 'text-red-600' : '' }}">Home</a>
                <a href="{{ route('chef.index') }}" class="text-gray-600 hover:text-red-600 font-medium transition-colors {{ request()->routeIs('chefs.*') ? 'text-red-600' : '' }}">Find Chefs</a>
                <a href="{{ route('subscriptions.plans') }}" class="text-gray-600 hover:text-red-600 font-medium transition-colors {{ request()->routeIs('subscriptions.*') ? 'text-red-600' : '' }}">Meal Plans</a>
                <a href="{{ route('how-it-works') }}" class="text-gray-600 hover:text-red-600 font-medium transition-colors {{ request()->routeIs('how-it-works') ? 'text-red-600' : '' }}">How it Works</a>
                <a href="{{ route('about') }}" class="text-gray-600 hover:text-red-600 font-medium transition-colors {{ request()->routeIs('about') ? 'text-red-600' : '' }}">About</a>
            </nav>

            <div class="hidden md:flex items-center space-x-4">
                @guest
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-red-600 font-medium transition-colors">Log in</a>
                    <a href="{{ route('register') }}" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-full font-semibold shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                        Sign Up
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 bg-red-50 text-red-600 hover:bg-red-100 px-5 py-2.5 rounded-full font-semibold transition-all">
                        <span>Dashboard</span>
                        <i class="fas fa-arrow-right text-sm"></i>
                    </a>
                @endguest
            </div>

            <div class="flex items-center md:hidden">
                <button type="button" onclick="toggleMobileMenu()" class="text-gray-600 hover:text-red-600 focus:outline-none p-2" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="md:hidden hidden bg-white border-t border-gray-100 absolute w-full left-0 shadow-lg" id="mobile-menu">
        <div class="px-4 pt-2 pb-6 space-y-2">
            <a href="{{ route('welcome') }}" class="block px-3 py-3 rounded-md text-base font-medium hover:text-red-600 hover:bg-red-50">Home</a>
            <a href="{{ route('chef.index') }}" class="block px-3 py-3 rounded-md text-base font-medium hover:text-red-600 hover:bg-red-50">Find Chefs</a>
            <a href="{{ route('subscriptions.plans') }}" class="block px-3 py-3 rounded-md text-base font-medium hover:text-red-600 hover:bg-red-50">Meal Plans</a>
            <a href="{{ route('how-it-works') }}" class="block px-3 py-3 rounded-md text-base font-medium hover:text-red-600 hover:bg-red-50">How it Works</a>
             <a href="{{ route('about') }}" class="block px-3 py-3 rounded-md text-base font-medium hover:text-red-600 hover:bg-red-50">About</a>
            
            <div class="border-t border-gray-100 my-2 pt-2">
                @guest
                    <a href="{{ route('login') }}" class="block w-full text-center px-4 py-3 text-gray-600 font-medium hover:text-red-600">Log in</a>
                    <a href="{{ route('register') }}" class="block w-full text-center px-4 py-3 mt-2 border border-transparent rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 font-bold">Sign Up</a>
                @else
                    <a href="{{ route('dashboard') }}" class="block w-full text-center px-4 py-3 mt-2 border border-transparent rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 font-bold">Go to Dashboard</a>
                @endguest
            </div>
        </div>
    </div>
</header>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    }
</script>