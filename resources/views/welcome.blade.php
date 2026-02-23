@extends('layouts.app')

@section('title', 'Homemade Meals Delivered - ChooseChow')

@section('content')

{{-- 1. HERO SECTION --}}
<div class="relative bg-gradient-to-br from-chow-cream-50 to-chow-cream-100 dark:from-dark-base dark:to-dark-section overflow-hidden">
    <div class="max-w-7xl mx-auto">
        {{-- Decorative food pattern overlay --}}
        <div class="absolute inset-0 opacity-5 dark:opacity-[0.02] bg-[url('https://www.transparenttextures.com/patterns/food.png')]"></div>
        
        <div class="relative z-10 pb-8 sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
            {{-- Nav Spacer --}}
            <div class="relative pt-6 px-4 sm:px-6 lg:px-8"></div>

            <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                <div class="sm:text-center lg:text-left">
                    <h1 class="text-4xl tracking-tight font-extrabold text-chow-brown-800 dark:text-content-primary sm:text-5xl md:text-6xl">
                        <span class="block xl:inline">Homemade meals,</span>
                        <span class="block text-appetizing xl:inline">delivered to you.</span>
                    </h1>
                    <p class="mt-3 text-base text-chow-brown-600 dark:text-content-secondary sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                        Experience the joy of authentic home cooking. Connect with talented local chefs in your neighborhood and get fresh, healthy meals delivered to your doorstep.
                    </p>
                    
                    {{-- SEARCH BAR --}}
                    <div class="mt-8 sm:mt-10 sm:flex sm:justify-center lg:justify-start">
                        <form action="{{ route('chef.index') }}" method="GET" class="w-full sm:max-w-lg flex shadow-2xl rounded-full relative z-20 ring-1 ring-chow-cream-200 dark:ring-dark-border">
                            <input 
                                type="text" 
                                name="search" 
                                list="chow-suggestions" 
                                placeholder="What are you craving? (e.g. Jollof, Pasta)" 
                                class="flex-1 min-w-0 block w-full px-6 py-4 rounded-l-full border-0 bg-white dark:bg-dark-card text-chow-brown-800 dark:text-content-primary placeholder-chow-brown-400 dark:placeholder-content-secondary focus:ring-2 focus:ring-inset focus:ring-accent sm:text-base outline-none"
                                autocomplete="off"
                            >
                            <datalist id="chow-suggestions">
                                @if(isset($searchSuggestions))
                                    @foreach($searchSuggestions as $suggestion)
                                        <option value="{{ $suggestion }}">
                                    @endforeach
                                @endif
                            </datalist>

                            <button type="submit" class="inline-flex items-center px-8 py-4 border border-transparent text-base font-bold rounded-r-full text-white bg-accent hover:bg-accent-hover transition-all duration-150 ease-in-out focus:outline-none shadow-lg shadow-accent/30">
                                Find Chow
                            </button>
                        </form>
                    </div>

                    {{-- STATS STRIP --}}
                    <div class="mt-10 grid grid-cols-3 gap-4 border-t border-chow-cream-200 dark:border-dark-border pt-8 sm:flex sm:justify-center lg:justify-start">
                        <div class="text-center sm:text-left">
                            <p class="text-2xl font-bold text-accent">{{ $stats['verified_chefs'] }}+</p>
                            <p class="text-xs font-medium text-chow-brown-500 dark:text-content-secondary uppercase tracking-wide">Chefs</p>
                        </div>
                        <div class="text-center sm:text-left sm:ml-8 border-l border-chow-cream-200 dark:border-dark-border pl-8">
                            <p class="text-2xl font-bold text-accent-light">{{ $stats['happy_customers'] }}+</p>
                            <p class="text-xs font-medium text-chow-brown-500 dark:text-content-secondary uppercase tracking-wide">Eaters</p>
                        </div>
                        <div class="text-center sm:text-left sm:ml-8 border-l border-chow-cream-200 dark:border-dark-border pl-8">
                            <p class="text-2xl font-bold text-chow-gold-500 dark:text-accent-light">{{ $stats['cities_covered'] }}</p>
                            <p class="text-xs font-medium text-chow-brown-500 dark:text-content-secondary uppercase tracking-wide">Cities</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    {{-- HERO IMAGE --}}
    <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
        <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full" src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Delicious Food Table">
        {{-- Warm overlay for cohesion --}}
        <div class="absolute inset-0 bg-gradient-to-l from-transparent to-chow-cream-50/30 lg:hidden"></div>
    </div>
</div>

{{-- 2. POPULAR CUISINES --}}
<section class="py-20 bg-white dark:bg-dark-section">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-base font-bold text-accent tracking-wide uppercase">Discover</h2>
            <p class="mt-1 text-3xl font-extrabold text-chow-brown-800 dark:text-content-primary sm:text-4xl sm:tracking-tight">Popular Cuisines</p>
            <p class="max-w-xl mt-5 mx-auto text-xl text-chow-brown-600 dark:text-content-secondary">Explore the flavors everyone is talking about.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            @forelse($popularCuisines as $cuisine)
                <a href="{{ route('chef.index', ['search' => $cuisine->category]) }}" class="group block no-underline decoration-0">
                    <div class="bg-chow-cream-50 dark:bg-dark-card rounded-2xl p-6 text-center shadow-sm hover:shadow-xl transform hover:-translate-y-2 transition-all duration-300 border border-chow-cream-200 dark:border-dark-border group-hover:border-accent dark:group-hover:border-accent group-hover:bg-white dark:group-hover:bg-dark-card">
                        <div class="w-16 h-16 mx-auto bg-chow-orange-100 dark:bg-accent/20 rounded-full flex items-center justify-center mb-4 group-hover:bg-accent transition-all">
                            <i class="fas 
                                {{ Str::contains(strtolower($cuisine->category), 'rice') ? 'fa-bowl-rice' : '' }}
                                {{ Str::contains(strtolower($cuisine->category), 'soup') ? 'fa-mug-hot' : '' }}
                                {{ Str::contains(strtolower($cuisine->category), 'snack') ? 'fa-cookie-bite' : '' }}
                                {{ Str::contains(strtolower($cuisine->category), 'drink') ? 'fa-glass-water' : '' }}
                                {{ Str::contains(strtolower($cuisine->category), 'meat') ? 'fa-drumstick-bite' : '' }} 
                                {{ !preg_match('/rice|soup|snack|drink|meat/i', $cuisine->category) ? 'fa-utensils' : '' }}
                                text-2xl text-accent group-hover:text-white transition-colors"></i>
                        </div>
                        <h3 class="font-bold text-chow-brown-800 dark:text-content-primary group-hover:text-accent transition-colors truncate">{{ ucfirst($cuisine->category) }}</h3>
                        <p class="text-xs text-chow-brown-500 dark:text-content-secondary mt-1">{{ $cuisine->total }} options</p>
                    </div>
                </a>
            @empty
                <div class="col-span-6 text-center text-chow-brown-400 dark:text-content-secondary py-10">
                    <i class="fas fa-utensils text-4xl mb-2 opacity-50"></i>
                    <p>Menus are being cooked up! Check back soon.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- 3. FEATURED CHEFS --}}
<section class="py-20 bg-chow-cream-50 dark:bg-dark-base">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-end mb-12 border-b border-chow-cream-200 dark:border-dark-border pb-4">
            <div>
                <h2 class="text-3xl font-extrabold text-chow-brown-800 dark:text-content-primary">Featured Home Chefs</h2>
                <p class="mt-2 text-lg text-chow-brown-600 dark:text-content-secondary">Top-rated kitchens in your area.</p>
            </div>
            <a href="{{ route('chef.index') }}" class="text-accent font-bold hover:text-accent-hover flex items-center mt-4 md:mt-0 group no-underline transition-colors">
                View All Chefs <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @forelse($featuredChefs as $chefProfile)
                @php
                    $avgRating = $chefProfile->user->receivedReviews->avg('rating');
                    $reviewCount = $chefProfile->user->receivedReviews->count();
                    $displayRating = $avgRating ? number_format($avgRating, 1) : 'New';
                @endphp

                <div class="group bg-white dark:bg-dark-card rounded-2xl shadow-sm border border-chow-cream-200 dark:border-dark-border overflow-hidden hover:shadow-2xl hover:shadow-accent/10 transition-all duration-300 transform hover:-translate-y-1 h-full flex flex-col">
                    {{-- Cover Image --}}
                    <div class="h-48 bg-chow-cream-100 dark:bg-dark-section relative overflow-hidden">
                        @if($chefProfile->cover_image)
                            <img src="{{ asset('storage/' . $chefProfile->cover_image) }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-chow-orange-50 to-chow-cream-100 dark:from-dark-section dark:to-dark-card flex items-center justify-center">
                                <i class="fas fa-utensils text-4xl text-chow-orange-200 dark:text-accent/30"></i>
                            </div>
                        @endif
                        
                        {{-- Rating Badge --}}
                        <div class="absolute top-3 right-3 bg-white dark:bg-dark-card bg-opacity-95 backdrop-blur-sm px-2 py-1 rounded-lg shadow-sm flex items-center gap-1 text-xs font-bold text-chow-brown-800 dark:text-content-primary">
                            <i class="fas fa-star text-accent-light star-glow"></i> {{ $displayRating }}
                        </div>
                    </div>

                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex items-start justify-between mb-2">
                             <div>
                                <h3 class="font-bold text-lg text-chow-brown-800 dark:text-content-primary leading-tight line-clamp-1 group-hover:text-accent transition-colors">
                                    {{ $chefProfile->business_name }}
                                </h3>
                                <p class="text-xs text-chow-brown-500 dark:text-content-secondary mt-1 flex items-center">
                                    <i class="fas fa-map-marker-alt text-accent mr-1"></i> 
                                    {{ $chefProfile->city ?: Str::limit($chefProfile->kitchen_address, 20) }}
                                </p>
                             </div>
                             {{-- Avatar --}}
                             <img src="{{ $chefProfile->profile_image ? asset('storage/' . $chefProfile->profile_image) : ($chefProfile->user->avatar_url ?? asset('images/default-avatar.png')) }}" 
                             class="w-10 h-10 rounded-full border-2 border-white dark:border-dark-card shadow-sm object-cover -mt-10 bg-white dark:bg-dark-section ring-2 ring-chow-cream-200 dark:ring-dark-border">
                        </div>
                        
                        <p class="text-chow-brown-600 dark:text-content-secondary text-sm mb-4 line-clamp-2 mt-2">
                            {{ $chefProfile->bio ?? 'Ready to serve delicious homemade meals.' }}
                        </p>

                        <div class="mt-auto pt-4 border-t border-chow-cream-100 dark:border-dark-border">
                             <a href="{{ route('chef.show', $chefProfile->slug ?? $chefProfile->id) }}" class="block w-full text-center bg-accent/10 dark:bg-accent/20 text-accent font-bold py-2.5 rounded-xl hover:bg-accent hover:text-white transition-all duration-300 no-underline">
                                View Menu
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-4 text-center py-16 bg-chow-cream-100 dark:bg-dark-card rounded-2xl border-2 border-dashed border-chow-cream-300 dark:border-dark-border">
                    <i class="fas fa-user-chef text-4xl text-chow-brown-300 dark:text-content-secondary mb-4"></i>
                    <p class="text-chow-brown-500 dark:text-content-secondary font-medium">No featured chefs yet. Check back tomorrow!</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- 4. CTA SECTION --}}
<section class="bg-gradient-to-r from-accent via-accent to-accent-light relative overflow-hidden">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/food.png')]"></div>
    
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-24 lg:px-8 lg:flex lg:items-center lg:justify-between relative z-10">
        <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
            <span class="block">Ready to taste the difference?</span>
            <span class="block text-white/80">Join thousands of happy eaters today.</span>
        </h2>
        <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0 gap-4">
            <div class="inline-flex rounded-md shadow-lg">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-bold rounded-xl text-accent bg-white hover:bg-gray-100 md:py-4 md:text-lg md:px-10 no-underline transition-all hover:scale-105 hover:shadow-xl">
                    Get Started
                </a>
            </div>
            <div class="inline-flex rounded-md">
                <a href="{{ route('chef.index') }}" class="inline-flex items-center justify-center px-8 py-3 border-2 border-white text-base font-bold rounded-xl text-white hover:bg-white hover:text-accent md:py-4 md:text-lg md:px-10 transition-all no-underline">
                    Browse Chefs
                </a>
            </div>
        </div>
    </div>
</section>

@endsection