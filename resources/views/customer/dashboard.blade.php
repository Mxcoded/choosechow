@extends('layouts.dashboard')

@section('title', 'Customer Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 pb-20">

    {{-- 1. HEADER & ACTION --}}
    <div class="flex flex-col md:flex-row justify-between items-md-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Welcome back, {{ $user->first_name }}! 🍽️</h1>
            <p class="tdark:text-gray-300">Discover amazing meals from talented local chefs.</p>
        </div>
        <div>
            <a href="{{ route('chef.index') }}" class="inline-flex items-center bg-red-600 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-red-700 transition-transform hover:-translate-y-0.5">
                <i class="fas fa-search mr-2"></i> Find Chow Now
            </a>
        </div>
    </div>

    {{-- 2. STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Total Orders --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center">
            <div class="rounded-full bg-red-50 p-4 mr-4 text-red-600">
                <i class="fas fa-shopping-bag fa-lg"></i>
            </div>
            <div>
                <h3 class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ $stats['total_orders'] }}</h3>
                <p class="text-sm tdark:text-gray-300 font-medium">Total Orders</p>
            </div>
        </div>
        {{-- Favorite Chefs --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center">
            <div class="rounded-full bg-green-50 p-4 mr-4 text-green-600">
                <i class="fas fa-heart fa-lg"></i>
            </div>
            <div>
                <h3 class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ $stats['favorite_chefs'] }}</h3>
                <p class="text-sm tdark:text-gray-300 font-medium">Kitchens Tried</p>
            </div>
        </div>
        {{-- Total Spent --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center">
            <div class="rounded-full bg-yellow-50 p-4 mr-4 text-yellow-600">
                <i class="fas fa-wallet fa-lg"></i>
            </div>
            <div>
                <h3 class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">₦{{ number_format($stats['total_spent']) }}</h3>
                <p class="text-sm tdark:text-gray-300 font-medium">Total Spent</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- 3. RECENT ORDERS LIST --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50">
                    <h5 class="font-bold text-gray-800">Recent Orders</h5>
                    <a href="{{ route('customer.orders') }}" class="text-sm font-bold text-red-600 hover:text-red-700">View All</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentOrders as $order)
                        <div class="p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center">
                                {{-- Chef Avatar --}}
                                <img src="{{ $order->chef->chefProfile->profile_image ? asset('storage/'.$order->chef->chefProfile->profile_image) : 'https://ui-avatars.com/api/?name='.urlencode($order->chef->first_name.' '.$order->chef->last_name) }}" 
                                     class="w-12 h-12 rounded-full mr-4 object-cover border border-gray-200 dark:border-gray-700">
                                
                                <div class="flex-grow">
                                    <div class="flex justify-between items-start mb-1">
                                        <h6 class="font-bold text-gray-900 dark:text-gray-100">
                                            {{ $order->chef->chefProfile->business_name ?? $order->chef->first_name . ' ' . $order->chef->last_name }}
                                        </h6>
                                        <span class="font-extrabold text-gray-900 dark:text-gray-100">₦{{ number_format($order->total_amount) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="tdark:text-gray-300">
                                            {{ $order->items->count() }} Items • {{ $order->created_at->format('M d, Y') }}
                                        </span>
                                        {{-- Status Badge --}}
                                        @php
                                            $statusClasses = match($order->status) {
                                                'pending_payment' => 'bg-yellow-100 text-yellow-800',
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'preparing' => 'bg-blue-100 text-blue-800',
                                                'ready' => 'bg-blue-100 text-blue-800',
                                                'completed' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <span class="px-2 py-1 rounded-full text-xs font-bold uppercase {{ $statusClasses }}">
                                            {{ str_replace('_', ' ', $order->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 px-4">
                            <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-utensils fa-2x text-gray-300"></i>
                            </div>
                            <p class="tdark:text-gray-300 font-medium mb-4">You haven't placed any orders yet.</p>
                            <a href="{{ route('chef.index') }}" class="inline-block bg-red-600 text-white font-bold py-2 px-6 rounded-full hover:bg-red-700 transition-colors">
                                Find Chow Now
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- 4. QUICK ACTIONS SIDEBAR --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden sticky top-24">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50">
                    <h5 class="font-bold text-gray-800">Quick Actions</h5>
                </div>
                <div class="p-4 space-y-3">
                    <a href="{{ route('chef.index') }}" class="block w-full text-left px-4 py-3 bg-red-50 text-red-700 font-bold rounded-lg hover:bg-red-100 transition-colors flex items-center">
                        <i class="fas fa-search mr-3"></i> Browse Chefs & Menus
                    </a>
                    <a href="{{ route('customer.profile') }}" class="block w-full text-left px-4 py-3 bg-gray-50 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-100 transition-colors flex items-center border border-gray-200 dark:border-gray-700">
                        <i class="fas fa-user-cog mr-3"></i> Profile Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection