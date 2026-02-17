@extends('layouts.app')

@section('title', $chef->business_name)

@section('content')
<div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 py-8 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center gap-6">
            {{-- Chef Avatar --}}
            <div class="flex-shrink-0">
                <img src="{{ $chef->user->avatar ? asset('storage/'.$chef->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($chef->business_name).'&size=150&background=fef7ed&color=78350f' }}" 
                     class="w-28 h-28 rounded-2xl object-cover border-4 border-white dark:border-gray-700 shadow-lg" 
                     alt="{{ $chef->business_name }}">
            </div>
            
            {{-- Chef Info --}}
            <div class="flex-1 text-center md:text-left">
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-2">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">{{ $chef->business_name }}</h1>
                    @if($chef->isOpenNow())
                        <span class="inline-flex items-center gap-1.5 bg-chow-fresh-100 dark:bg-chow-fresh-900/30 text-chow-fresh-700 dark:text-chow-fresh-400 px-3 py-1 rounded-full text-sm font-bold">
                            <span class="w-2 h-2 bg-chow-fresh-500 rounded-full animate-pulse"></span>
                            Open Now
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-3 py-1 rounded-full text-sm font-bold">
                            <i class="fas fa-clock"></i>
                            Closed
                        </span>
                    @endif
                </div>
                <p class="text-gray-600 dark:text-gray-400 mb-4 max-w-2xl">{{ $chef->bio }}</p>
                <div class="flex flex-wrap gap-4 justify-center md:justify-start text-sm">
                    <span class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                        <i class="fas fa-map-marker-alt text-chow-red-500"></i>
                        {{ $chef->kitchen_address }}
                    </span>
                    <span class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                        <i class="fas fa-star text-chow-gold-400"></i>
                        <span class="font-bold">{{ $chef->rating ?? '5.0' }}</span> rating
                    </span>
                    <span class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                        <i class="fas fa-shopping-basket text-chow-orange-500"></i>
                        ₦{{ number_format($chef->minimum_order) }} min order
                    </span>
                </div>
            </div>
            
            {{-- Delivery Info Card --}}
            <div class="flex-shrink-0">
                <div class="bg-chow-cream-50 dark:bg-gray-700 rounded-2xl p-5 text-center min-w-[140px]">
                    <div class="text-chow-orange-500 mb-2">
                        <i class="fas fa-motorcycle text-3xl"></i>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Delivery Time</div>
                    <div class="text-xl font-bold text-gray-900 dark:text-white">30-45 min</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-chow-cream-50 dark:bg-gray-900 min-h-screen transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex gap-8">
            
            {{-- Sidebar - Categories --}}
            <div class="hidden lg:block w-64 flex-shrink-0">
                <div class="sticky top-24 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Menu Categories</h3>
                    <nav class="space-y-1">
                        @foreach($menus as $categoryName => $items)
                            <a href="#cat-{{ Str::slug($categoryName) }}" 
                               class="flex items-center justify-between px-3 py-2.5 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-chow-orange-50 dark:hover:bg-gray-700 hover:text-chow-orange-600 dark:hover:text-chow-orange-400 transition-colors">
                                <span class="font-medium">{{ $categoryName }}</span>
                                <span class="bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-300 text-xs font-bold px-2 py-0.5 rounded-full">{{ $items->count() }}</span>
                            </a>
                        @endforeach
                    </nav>
                </div>
            </div>

            {{-- Main Content - Menu Items --}}
            <div class="flex-1">
                @forelse($menus as $categoryName => $items)
                    <div id="cat-{{ Str::slug($categoryName) }}" class="mb-10">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 pb-3 border-b border-gray-200 dark:border-gray-700">
                            {{ $categoryName }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($items as $menu)
                                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
                                    <div class="flex">
                                        {{-- Image --}}
                                        <div class="w-32 h-32 flex-shrink-0 bg-chow-cream-100 dark:bg-gray-700">
                                            @if($menu->images && count($menu->images) > 0)
                                                <img src="{{ asset('storage/' . $menu->images[0]) }}" 
                                                     class="w-full h-full object-cover" 
                                                     alt="{{ $menu->name }}">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <i class="fas fa-utensils text-gray-300 dark:text-gray-600 text-2xl"></i>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        {{-- Details --}}
                                        <div class="flex-1 p-4 flex flex-col">
                                            <h3 class="font-bold text-gray-900 dark:text-white mb-1">{{ $menu->name }}</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2 line-clamp-2">{{ Str::limit($menu->description, 70) }}</p>
                                            <div class="mt-auto flex items-center justify-between">
                                                <span class="text-lg font-bold text-chow-orange-600 dark:text-chow-orange-400">₦{{ number_format($menu->price) }}</span>
                                                @if($chef->isOpenNow())
                                                    <form action="{{ route('cart.add', $menu->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-chow-orange-500 hover:bg-chow-orange-600 text-white text-sm font-bold rounded-full transition-colors">
                                                            <i class="fas fa-plus"></i> Add
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-400 text-sm font-bold rounded-full cursor-not-allowed">
                                                        <i class="fas fa-clock"></i> Closed
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-700">
                        <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-utensils text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-700 dark:text-white mb-2">No menu items yet</h3>
                        <p class="text-gray-500 dark:text-gray-400">This chef hasn't added any dishes to their menu.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
