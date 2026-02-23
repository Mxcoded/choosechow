<header class="bg-white dark:bg-dark-section shadow-sm sticky top-0 z-50 transition-all duration-300 font-sans border-b border-gray-100 dark:border-dark-border" id="main-header">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('welcome') }}" class="flex items-center gap-2 group">
                    <img src="{{ asset('storage/img/choosechowlogo.png') }}" alt="ChooseChow Logo" 
                         class="h-12 w-12 rounded-full shadow-md group-hover:scale-105 transition-transform duration-300">
                    <span class="text-2xl font-bold tracking-tight transition-colors">
                        <span class="text-gray-900 dark:text-content-primary">Choose</span><span class="text-red-600 dark:text-accent">Chow</span>
                    </span>
                </a>
            </div>

            <nav class="hidden md:flex space-x-8">
                <a href="{{ route('welcome') }}" class="font-medium transition-colors {{ request()->routeIs('welcome') ? 'text-red-600 dark:text-accent' : 'text-gray-600 dark:text-content-primary hover:text-red-600 dark:hover:text-accent' }}">Home</a>
                <a href="{{ route('chef.index') }}" class="font-medium transition-colors {{ request()->routeIs('chefs.*') ? 'text-red-600 dark:text-accent' : 'text-gray-600 dark:text-content-primary hover:text-red-600 dark:hover:text-accent' }}">Find Chefs</a>
                <a href="{{ route('subscriptions.plans') }}" class="font-medium transition-colors {{ request()->routeIs('subscriptions.*') ? 'text-red-600 dark:text-accent' : 'text-gray-600 dark:text-content-primary hover:text-red-600 dark:hover:text-accent' }}">Meal Plans</a>
                <a href="{{ route('how-it-works') }}" class="font-medium transition-colors {{ request()->routeIs('how-it-works') ? 'text-red-600 dark:text-accent' : 'text-gray-600 dark:text-content-primary hover:text-red-600 dark:hover:text-accent' }}">How it Works</a>
                <a href="{{ route('about') }}" class="font-medium transition-colors {{ request()->routeIs('about') ? 'text-red-600 dark:text-accent' : 'text-gray-600 dark:text-content-primary hover:text-red-600 dark:hover:text-accent' }}">About</a>
            </nav>

            <div class="hidden md:flex items-center space-x-4">
                @guest
                    <a href="{{ route('login') }}" class="text-gray-600 dark:text-content-primary hover:text-accent font-medium transition-colors">Log in</a>
                    <a href="{{ route('register') }}" class="bg-accent hover:bg-accent-hover text-white px-6 py-2.5 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                        Sign Up
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 bg-accent/10 dark:bg-accent/20 text-accent hover:bg-accent/20 dark:hover:bg-accent/30 px-5 py-2.5 rounded-xl font-semibold transition-all">
                        <span>Dashboard</span>
                        <i class="fas fa-arrow-right text-sm"></i>
                    </a>
                @endguest
            </div>

            <div class="flex items-center md:hidden">
                <button type="button" onclick="toggleMobileMenu()" class="text-gray-600 dark:text-content-primary hover:text-accent focus:outline-none p-2" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="md:hidden hidden bg-white dark:bg-dark-card border-t border-gray-100 dark:border-dark-border absolute w-full left-0 shadow-lg" id="mobile-menu">
        <div class="px-4 pt-2 pb-6 space-y-2">
            <a href="{{ route('welcome') }}" class="block px-4 py-3 rounded-xl text-base font-medium text-gray-700 dark:text-content-primary hover:text-accent hover:bg-red-50 dark:hover:bg-dark-section transition-colors">Home</a>
            <a href="{{ route('chef.index') }}" class="block px-4 py-3 rounded-xl text-base font-medium text-gray-700 dark:text-content-primary hover:text-accent hover:bg-red-50 dark:hover:bg-dark-section transition-colors">Find Chefs</a>
            <a href="{{ route('subscriptions.plans') }}" class="block px-4 py-3 rounded-xl text-base font-medium text-gray-700 dark:text-content-primary hover:text-accent hover:bg-red-50 dark:hover:bg-dark-section transition-colors">Meal Plans</a>
            <a href="{{ route('how-it-works') }}" class="block px-4 py-3 rounded-xl text-base font-medium text-gray-700 dark:text-content-primary hover:text-accent hover:bg-red-50 dark:hover:bg-dark-section transition-colors">How it Works</a>
            <a href="{{ route('about') }}" class="block px-4 py-3 rounded-xl text-base font-medium text-gray-700 dark:text-content-primary hover:text-accent hover:bg-red-50 dark:hover:bg-dark-section transition-colors">About</a>
            
            <div class="border-t border-gray-100 dark:border-dark-border my-2 pt-2">
                @guest
                    <a href="{{ route('login') }}" class="block w-full text-center px-4 py-3 text-gray-600 dark:text-content-primary font-medium hover:text-accent transition-colors">Log in</a>
                    <a href="{{ route('register') }}" class="block w-full text-center px-4 py-3 mt-2 rounded-xl shadow-md text-white bg-accent hover:bg-accent-hover font-semibold transition-colors">Sign Up</a>
                @else
                    <a href="{{ route('dashboard') }}" class="block w-full text-center px-4 py-3 mt-2 rounded-xl shadow-md text-white bg-accent hover:bg-accent-hover font-semibold transition-colors">Go to Dashboard</a>
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