@extends('layouts.dashboard')

@section('title', 'Dashboard - ChooseChow')
@section('page_title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    {{-- === 1. WELCOME CARD === --}}
    <div class="col-span-1 md:col-span-2 lg:col-span-4 bg-gradient-to-r from-red-600 to-orange-500 rounded-xl shadow-lg text-white p-6 relative overflow-hidden">
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-2">Welcome back, {{ Auth::user()->first_name }}! 👋</h2>
            <p class="opacity-90">Here's what's happening in your kitchen today.</p>
        </div>
        {{-- Decorative Icon --}}
        <div class="absolute right-0 bottom-0 opacity-10 transform translate-x-4 translate-y-4">
            <i class="fas fa-utensils text-9xl"></i>
        </div>
    </div>

    {{-- === 2. CHEF STATS (Only visible to Chefs) === --}}
    @if(Auth::user()->hasRole('chef'))
        
        {{-- Pending Orders --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-yellow-100 text-yellow-600 w-12 h-12 rounded-full flex items-center justify-center text-xl">
                    <i class="fas fa-clock"></i>
                </div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pending</span>
            </div>
            @php 
                $pendingOrders = \App\Models\Order::where('chef_id', Auth::id())->where('status', 'pending')->count(); 
            @endphp
            <div class="text-3xl font-bold text-gray-800">{{ $pendingOrders }}</div>
            <div class="text-sm tdark:text-gray-300 mt-1">Orders to prepare</div>
        </div>

        {{-- Active Orders --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-blue-100 text-blue-600 w-12 h-12 rounded-full flex items-center justify-center text-xl">
                    <i class="fas fa-fire"></i>
                </div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Cooking</span>
            </div>
            @php 
                $activeOrders = \App\Models\Order::where('chef_id', Auth::id())->whereIn('status', ['pending', 'preparing', 'ready'])->count(); 
            @endphp
            <div class="text-3xl font-bold text-gray-800">{{ $activeOrders }}</div>
            <div class="text-sm tdark:text-gray-300 mt-1">Currently serving</div>
        </div>

        {{-- Total Revenue (Simple Calculation) --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-green-100 text-green-600 w-12 h-12 rounded-full flex items-center justify-center text-xl">
                    <i class="fas fa-wallet"></i>
                </div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Revenue</span>
            </div>
            <div class="text-3xl font-bold text-gray-800">₦0.00</div>
            <div class="text-sm tdark:text-gray-300 mt-1">Today's earnings</div>
        </div>

        {{-- Menu Items --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-purple-100 text-purple-600 w-12 h-12 rounded-full flex items-center justify-center text-xl">
                    <i class="fas fa-book-open"></i>
                </div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Menu</span>
            </div>
            @php 
                $menuCount = \App\Models\Menu::where('user_id', Auth::id())->count(); 
            @endphp
            <div class="text-3xl font-bold text-gray-800">{{ $menuCount }}</div>
            <div class="text-sm tdark:text-gray-300 mt-1">Active dishes</div>
        </div>

    @else
        {{-- CUSTOMER VIEW --}}
        <div class="col-span-full bg-white rounded-xl p-8 text-center border border-dashed border-gray-300">
            <i class="fas fa-search text-gray-300 text-4xl mb-4"></i>
            <h3 class="text-lg font-bold text-gray-800">Hungry?</h3>
            <p class="tdark:text-gray-300 mb-6">Find the best chefs near you and order now.</p>
            <a href="{{ route('chef.index') }}" class="bg-red-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-red-700 transition-colors">
                Find Chow
            </a>
        </div>
    @endif
    
</div>
@endsection