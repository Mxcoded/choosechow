@extends('layouts.dashboard')

@section('title', 'Find Chow - ChooseChow')
@section('page_title', 'Find a Kitchen')

@section('content')
<div class="max-w-7xl mx-auto pb-12">
    
    {{-- 1. Search & Filter Header --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 mb-8">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-utensils text-red-500 mr-2"></i> Nearby Kitchens
            </h2>
            
            <div class="relative w-full md:w-96">
                <input type="text" placeholder="Search for rice, pasta, grills..." class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 focus:border-red-500 focus:ring-red-500 bg-gray-50 focus:bg-white transition-colors">
                <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
            </div>
        </div>
    </div>

    {{-- 2. Chefs Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($chefs as $chef)
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100 dark:border-gray-700 overflow-hidden group">
                
                {{-- Cover Image --}}
                <div class="h-40 bg-gray-200 relative overflow-hidden">
                    @if($chef->cover_image)
                        <img src="{{ asset('storage/' . $chef->cover_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-r from-red-500 to-orange-400">
                            <i class="fas fa-utensils text-white/30 text-4xl"></i>
                        </div>
                    @endif

                    {{-- Status Badge --}}
                    <div class="absolute top-3 right-3">
                        @if($chef->isAcceptingOrders())
                            <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full shadow-sm">
                                Open
                            </span>
                        @else
                            <span class="bg-gray-500 text-white text-xs font-bold px-2 py-1 rounded-full shadow-sm">
                                Closed
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-6 pt-12 relative">
                    
                    {{-- Avatar (Floating) --}}
                    <div class="absolute -top-10 left-6">
                        <img src="{{ $chef->profile_image ? asset('storage/' . $chef->profile_image) : ($chef->user->avatar ? asset('storage/' . $chef->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($chef->business_name)) }}" 
                             class="w-20 h-20 rounded-full border-4 border-white shadow-md object-cover bg-white">
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 leading-tight mb-1">{{ $chef->business_name }}</h3>
                        <div class="text-sm tdark:text-gray-300 flex items-center">
                            <i class="fas fa-map-marker-alt text-red-500 mr-1.5"></i> {{ Str::limit($chef->kitchen_address, 30) }}
                        </div>
                    </div>

                    {{-- Dynamic Rating Logic --}}
                    @php
                        $avgRating = $chef->user->receivedReviews->avg('rating'); // Calculate Average
                        $reviewCount = $chef->user->receivedReviews->count();     // Count Reviews
                        $displayRating = $avgRating ? number_format($avgRating, 1) : 'New'; // Format
                    @endphp

                    <div class="flex items-center text-sm mb-2">
                        <i class="fas fa-star text-yellow-400 mr-1"></i>
                        <span class="font-bold text-gray-800">{{ $displayRating }}</span>
                        
                        @if($reviewCount > 0)
                            <span class="text-gray-400 text-xs ml-1">({{ $reviewCount }})</span>
                        @else
                            <span class="text-gray-400 text-xs ml-1">(No reviews)</span>
                        @endif
                    </div>
                    {{-- Cuisines (The Fix) --}}
                    <div class="flex flex-wrap gap-2 mb-6 h-12 overflow-hidden">
                        {{-- FIX: We verify if it is an array, then wrap in collect() to use take() --}}
                        @if(is_array($chef->cuisines) || is_object($chef->cuisines))
                            @foreach(collect($chef->cuisines)->take(3) as $cuisine)
                                <span class="bg-red-50 text-red-600 text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wide">
                                    {{ $cuisine }}
                                </span>
                            @endforeach
                            @if(count($chef->cuisines ?? []) > 3)
                                <span class="text-xs text-gray-400 self-center">+{{ count($chef->cuisines) - 3 }}</span>
                            @endif
                        @endif
                    </div>

                    {{-- Action Button --}}
                    <a href="{{ route('chef.show', $chef->slug ?? $chef->id) }}" class="block w-full text-center bg-gray-900 text-white font-bold py-3 rounded-xl hover:bg-red-600 transition-colors shadow-lg shadow-gray-200">
                        View Menu
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center">
                <div class="bg-gray-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-search text-gray-400 text-4xl"></i>
                </div>
                <h3 class="text-xl font-bold dark:text-gray-300">No kitchens found nearby.</h3>
                <p class="tdark:text-gray-300 mt-2">Try checking back later for new chefs!</p>
            </div>
        @endforelse
    </div>

</div>
@endsection