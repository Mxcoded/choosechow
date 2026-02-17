@extends('layouts.app')

@section('title', 'Find Chow - ChooseChow')

@section('content')
<div class="min-h-screen bg-chow-cream-50 dark:bg-gray-900 transition-colors duration-200">
    
    {{-- HERO HEADER --}}
    <div class="bg-gradient-to-br from-chow-red-600 via-chow-red-700 to-chow-orange-600 dark:from-gray-800 dark:via-gray-800 dark:to-gray-900 text-white py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-3xl md:text-5xl font-extrabold mb-4">
                    Discover Amazing Home Chefs 🍳
                </h1>
                <p class="text-lg text-white/80 mb-8 max-w-2xl mx-auto">
                    Browse {{ $chefs->total() }}+ verified home kitchens serving authentic meals in your area
                </p>
                
                {{-- Search Bar --}}
                <form action="{{ route('chef.index') }}" method="GET" class="max-w-2xl mx-auto">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </span>
                            <input type="text" name="search" 
                                   class="w-full pl-12 pr-4 py-4 rounded-xl border-0 bg-white dark:bg-gray-700 text-gray-800 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-white/30 shadow-lg text-base" 
                                   placeholder="Search for Jollof Rice, Suya, Egusi..." 
                                   value="{{ request('search') }}">
                        </div>
                        <button type="submit" class="px-8 py-4 bg-chow-gold-400 hover:bg-chow-gold-500 text-chow-brown-900 font-bold rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="fas fa-search mr-2"></i> Find Chow
                        </button>
                    </div>
                    
                    {{-- Quick Stats --}}
                    <div class="mt-6 flex justify-center gap-8 text-sm">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-chow-fresh-400"></i>
                            <span class="text-white/90">Verified Kitchens</span>
                        </span>
                        <span class="flex items-center gap-2">
                            <i class="fas fa-motorcycle text-chow-gold-400"></i>
                            <span class="text-white/90">Fast Delivery</span>
                        </span>
                        <span class="hidden sm:flex items-center gap-2">
                            <i class="fas fa-shield-alt text-chow-orange-300"></i>
                            <span class="text-white/90">Safe Payments</span>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- FILTER BAR --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-chow-cream-200 dark:border-gray-700 p-4 mb-8 transition-colors">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-chow-brown-500 dark:text-gray-400">
                        <strong class="text-chow-brown-800 dark:text-white">{{ $chefs->total() }}</strong> kitchens found
                    </span>
                </div>
            </div>
        </div>

        {{-- CHEF GRID --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($chefs as $chef)
                @php
                    $avgRating = $chef->user->receivedReviews->avg('rating');
                    $reviewCount = $chef->user->receivedReviews->count();
                    $displayRating = $avgRating ? number_format($avgRating, 1) : '5.0';
                @endphp
                <a href="{{ route('chef.show', $chef->slug ?? $chef->id) }}" class="group block no-underline">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-chow-cream-200 dark:border-gray-700 overflow-hidden hover:shadow-xl hover:shadow-chow-orange-500/10 dark:hover:shadow-chow-orange-500/5 transition-all duration-300 transform hover:-translate-y-1 h-full flex flex-col">
                        
                        {{-- Chef Image --}}
                        <div class="relative h-44 bg-chow-cream-100 dark:bg-gray-700 overflow-hidden">
                            @if($chef->cover_image)
                                <img src="{{ asset('storage/' . $chef->cover_image) }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                                     alt="{{ $chef->business_name }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-chow-red-500 to-chow-orange-500">
                                    <i class="fas fa-utensils text-white/30 text-4xl"></i>
                                </div>
                            @endif
                            
                            {{-- Status Badge --}}
                            @if($chef->isAcceptingOrders())
                                <div class="absolute top-3 left-3">
                                    <span class="inline-flex items-center gap-1.5 bg-chow-fresh-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                                        <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                                        Open Now
                                    </span>
                                </div>
                            @else
                                <div class="absolute inset-0 bg-gray-900/50 flex items-center justify-center">
                                    <span class="bg-gray-800/90 text-white px-4 py-2 rounded-full text-sm font-bold">
                                        <i class="fas fa-clock mr-1"></i> Currently Closed
                                    </span>
                                </div>
                            @endif
                            
                            {{-- Rating Badge --}}
                            <div class="absolute top-3 right-3">
                                <span class="inline-flex items-center gap-1 bg-white dark:bg-gray-800 text-chow-brown-800 dark:text-white px-2.5 py-1 rounded-full text-xs font-bold shadow-lg">
                                    <i class="fas fa-star text-chow-gold-400"></i>
                                    {{ $displayRating }}
                                </span>
                            </div>
                            
                            {{-- Avatar (Floating) --}}
                            <div class="absolute -bottom-8 left-4">
                                <img src="{{ $chef->profile_image ? asset('storage/' . $chef->profile_image) : ($chef->user->avatar ? asset('storage/' . $chef->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($chef->business_name).'&background=fef7ed&color=78350f') }}" 
                                     class="w-16 h-16 rounded-full border-4 border-white dark:border-gray-800 shadow-md object-cover bg-white">
                            </div>
                        </div>
                        
                        {{-- Chef Info --}}
                        <div class="p-5 pt-10 flex-1 flex flex-col">
                            <h3 class="font-bold text-lg text-chow-brown-800 dark:text-white mb-1 group-hover:text-chow-orange-500 transition-colors line-clamp-1">
                                {{ $chef->business_name }}
                            </h3>
                            
                            {{-- Location --}}
                            <div class="mb-3 text-sm text-chow-brown-500 dark:text-gray-400 flex items-center">
                                <i class="fas fa-map-marker-alt text-chow-red-500 mr-1.5"></i>
                                {{ Str::limit($chef->kitchen_address, 25) }}
                            </div>
                            
                            {{-- Cuisines --}}
                            <div class="mb-3 flex flex-wrap gap-1">
                                @if($chef->cuisines && $chef->cuisines->count() > 0)
                                    @foreach($chef->cuisines->take(2) as $cuisine)
                                        <span class="bg-chow-orange-50 dark:bg-chow-orange-900/30 text-chow-orange-600 dark:text-chow-orange-400 text-[10px] font-bold px-2 py-1 rounded-full">
                                            {{ $cuisine->name }}
                                        </span>
                                    @endforeach
                                @endif
                            </div>
                            
                            {{-- Footer Info --}}
                            <div class="mt-auto pt-3 border-t border-chow-cream-200 dark:border-gray-700 flex justify-between items-center text-sm">
                                <span class="flex items-center gap-1.5 text-chow-brown-600 dark:text-gray-400">
                                    <i class="fas fa-shopping-basket text-chow-orange-400"></i>
                                    <span>₦{{ number_format($chef->minimum_order) }} min</span>
                                </span>
                                <span class="flex items-center gap-1.5 text-chow-brown-600 dark:text-gray-400">
                                    <i class="fas fa-motorcycle text-chow-fresh-500"></i>
                                    <span>30-45 min</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border-2 border-dashed border-chow-cream-300 dark:border-gray-700">
                    <div class="w-24 h-24 bg-chow-cream-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-utensils text-chow-brown-300 dark:text-gray-500 text-4xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-chow-brown-700 dark:text-white mb-2">No kitchens found</h4>
                    <p class="text-chow-brown-500 dark:text-gray-400 mb-6 max-w-md mx-auto">
                        We couldn't find any chefs matching your criteria. Try adjusting your search or check back later.
                    </p>
                    <a href="{{ route('chef.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-chow-orange-500 hover:bg-chow-orange-600 text-white font-bold rounded-full transition-colors">
                        <i class="fas fa-refresh"></i> Refresh
                    </a>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($chefs->hasPages())
            <div class="mt-12 flex justify-center">
                <div class="bg-white dark:bg-gray-800 rounded-xl px-4 py-2 shadow-sm border border-chow-cream-200 dark:border-gray-700">
                    {{ $chefs->links() }}
                </div>
            </div>
        @endif
        
        {{-- CTA Section --}}
        @if($chefs->count() > 0)
            <div class="mt-16 bg-gradient-to-r from-chow-orange-500 to-chow-red-600 rounded-3xl p-8 lg:p-12 text-center text-white">
                <h3 class="text-2xl lg:text-3xl font-bold mb-4">Are you a home chef?</h3>
                <p class="text-white/90 mb-6 max-w-xl mx-auto">
                    Turn your passion for cooking into a business. Join ChooseChow and start earning from your kitchen today!
                </p>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-chow-red-600 font-bold rounded-full hover:bg-chow-cream-50 transition-colors shadow-lg">
                    <i class="fas fa-chef-hat"></i> Become a Chef
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
