@extends('layouts.dashboard')

@section('title', $chef->business_name . ' - ChooseChow')

@section('content')
<div class="max-w-7xl mx-auto pb-20">

    {{-- 1. HEADER IMAGE & INFO --}}
    <div class="relative bg-white rounded-b-3xl shadow-sm border-b border-gray-100 dark:border-gray-700 mb-8 overflow-hidden">
        
        {{-- Cover Image --}}
        <div class="h-64 md:h-80 w-full relative">
            @if($chef->cover_image)
                <img src="{{ asset('storage/' . $chef->cover_image) }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
            @else
                <div class="w-full h-full bg-gray-800 flex items-center justify-center">
                    <i class="fas fa-store dark:text-gray-300 text-6xl"></i>
                </div>
            @endif

            {{-- Back Button --}}
            <a href="{{ route('chef.index') }}" class="absolute top-6 left-6 bg-white/20 backdrop-blur-md text-white px-4 py-2 rounded-full text-sm font-bold hover:bg-white/30 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>

        {{-- Chef Info --}}
        <div class="px-6 md:px-10 pb-8 -mt-20 relative">
            <div class="flex flex-col md:flex-row items-end gap-6">
                
                {{-- Avatar --}}
                <img src="{{ $chef->profile_image ? asset('storage/' . $chef->profile_image) : ($chef->user->avatar ? asset('storage/' . $chef->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($chef->business_name)) }}" 
                     class="w-32 h-32 md:w-40 md:h-40 rounded-full border-4 border-white shadow-xl object-cover bg-white">

                {{-- Text Info --}}
                <div class="flex-1 text-white md:text-gray-900 dark:text-gray-100 mb-2">
                    <h1 class="text-3xl md:text-4xl font-extrabold shadow-black drop-shadow-md md:drop-shadow-none">{{ $chef->business_name }}</h1>
                    
                    {{-- Dynamic Rating Calculation --}}
                    @php
                        $avgRating = $chef->user->receivedReviews->avg('rating');
                        $reviewCount = $chef->user->receivedReviews->count();
                    @endphp

                    <div class="flex flex-wrap items-center gap-4 text-sm font-medium mt-2 text-white/90 md:tdark:text-gray-300">
                        <span><i class="fas fa-map-marker-alt text-red-500 mr-1"></i> {{ $chef->kitchen_address }}</span>
                        <span>•</span>
                        
                        {{-- REAL RATING DISPLAY --}}
                        <div class="flex items-center text-yellow-400">
                            @if($reviewCount > 0)
                                <span class="font-bold mr-1">{{ number_format($avgRating, 1) }}</span>
                                <div class="flex text-xs mr-1">
                                    @for($i=1; $i<=5; $i++)
                                        @if($i <= round($avgRating))
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star text-gray-300"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-xs">({{ $reviewCount }} reviews)</span>
                            @else
                                <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded text-xs font-bold">New Kitchen</span>
                            @endif
                        </div>

                        <span>•</span>
                        <span class="{{ $chef->isAcceptingOrders() ? 'text-green-500 font-bold' : 'text-red-500 font-bold' }}">
                            {{ $chef->isAcceptingOrders() ? 'Open Now' : 'Closed' }}
                        </span>
                    </div>
                </div>

                {{-- Order Info Box --}}
                <div class="bg-white p-4 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 text-center min-w-[150px] hidden md:block">
                    <div class="text-xs text-gray-400 uppercase tracking-wider mb-1">Min. Order</div>
                    <div class="text-xl font-bold text-gray-900 dark:text-gray-100">₦{{ number_format($chef->minimum_order) }}</div>
                    <div class="text-xs tdark:text-gray-300 mt-1">Delivery: ₦{{ number_format($chef->delivery_fee ?? 0) }}</div>
                </div>
            </div>

            {{-- Bio & Cuisines --}}
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="md:col-span-2">
                    <h3 class="font-bold text-gray-800 text-lg mb-2">About the Kitchen</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">{{ $chef->bio }}</p>

                    {{-- SAFE Cuisines Display --}}
                    @if(!empty($chef->cuisines))
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($chef->cuisines as $cuisine)
                                <span class="bg-gray-100 text-gray-600 dark:text-gray-400 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">
                                    {{ $cuisine }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 2. MENU GRID --}}
    <div class="px-6 md:px-10">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-book-open text-red-500 mr-3"></i> Menu
        </h2>

        @if(isset($menus) && $menus->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Iterate through the menus (Flattened to handle groups) --}}
                @foreach($menus->flatten() as $menu)
                    <div class="bg-white rounded-xl p-4 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-all flex gap-4 group">
                        
                        {{-- Menu Image --}}
                        <div class="w-24 h-24 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden relative">
                            @if($menu->image)
                                <img src="{{ asset('storage/' . $menu->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <i class="fas fa-utensils"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start">
                                    <h3 class="font-bold text-gray-900 dark:text-gray-100 line-clamp-1">{{ $menu->name }}</h3>
                                    <span class="font-bold text-red-600 text-sm">₦{{ number_format($menu->price) }}</span>
                                </div>
                                <p class="text-xs tdark:text-gray-300 mt-1 line-clamp-2">{{ $menu->description }}</p>
                            </div>
                            
                            {{-- Add to Cart Button --}}
                            <div class="mt-3 flex justify-end">
                                <a href="{{ route('add.to.cart', $menu->id) }}" class="bg-gray-900 text-white text-xs font-bold px-3 py-2 rounded-lg hover:bg-red-600 transition-colors flex items-center">
                                    <i class="fas fa-plus mr-1"></i> Add
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                <i class="fas fa-hamburger text-gray-300 text-4xl mb-3"></i>
                <p class="tdark:text-gray-300 font-medium">No menu items available yet.</p>
            </div>
        @endif
    </div>

</div>
@endsection