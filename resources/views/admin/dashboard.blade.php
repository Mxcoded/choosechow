@extends('layouts.dashboard')

@section('title', 'Admin Overview')
@section('page_title', 'Business Overview')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    
    {{-- 1. METRICS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        
        {{-- Revenue Card --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-green-100 p-3 rounded-lg text-green-600">
                    <i class="fas fa-coins text-xl"></i>
                </div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Net Profit</span>
            </div>
            <div class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">₦{{ number_format($stats['revenue']) }}</div>
            <p class="text-xs tdark:text-gray-300 mt-1">From ₦{{ number_format($stats['total_flow']) }} total volume</p>
        </div>

        {{-- Users Card --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-blue-100 p-3 rounded-lg text-blue-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Users</span>
            </div>
            <div class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ number_format($stats['total_users']) }}</div>
            <p class="text-xs text-green-600 mt-1 font-bold"> <i class="fas fa-arrow-up"></i> Growing</p>
        </div>

        {{-- Kitchens Card --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-orange-100 p-3 rounded-lg text-orange-600">
                    <i class="fas fa-utensils text-xl"></i>
                </div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Active Kitchens</span>
            </div>
            <div class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ number_format($stats['total_chefs']) }}</div>
            @if($stats['pending_verifications'] > 0)
                <a href="{{ route('admin.chef') }}" class="text-xs text-orange-600 mt-1 font-bold hover:underline">
                    {{ $stats['pending_verifications'] }} waiting verification
                </a>
            @else
                <p class="text-xs tdark:text-gray-300 mt-1">All verified</p>
            @endif
        </div>

        {{-- Action Required Card --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-red-100 p-3 rounded-lg text-red-600">
                    <i class="fas fa-bell text-xl"></i>
                </div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pending Actions</span>
            </div>
            <div class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">
                {{ $stats['pending_payouts'] + $stats['pending_verifications'] + $stats['pending_contacts'] }}
            </div>
            <div class="mt-3 space-y-2 text-xs">
                @if($stats['pending_payouts'] > 0)
                    <a href="{{ route('admin.withdrawals.index') }}" class="block text-red-600 font-bold hover:underline">
                        ⚡ {{ $stats['pending_payouts'] }} Payout Request{{ $stats['pending_payouts'] > 1 ? 's' : '' }}
                    </a>
                @endif
                @if($stats['pending_verifications'] > 0)
                    <a href="{{ route('admin.chef') }}" class="block text-orange-600 font-bold hover:underline">
                        👨‍🍳 {{ $stats['pending_verifications'] }} Chef Verification{{ $stats['pending_verifications'] > 1 ? 's' : '' }}
                    </a>
                @endif
                @if($stats['pending_contacts'] > 0)
                    <a href="{{ route('admin.contact-submissions') }}" class="block text-yellow-600 font-bold hover:underline">
                        💬 {{ $stats['pending_contacts'] }} Contact Message{{ $stats['pending_contacts'] > 1 ? 's' : '' }}
                    </a>
                @endif
            </div>
        </div>

        {{-- Newsletter Subscribers Card --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-purple-100 p-3 rounded-lg text-purple-600">
                    <i class="fas fa-envelope text-xl"></i>
                </div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Newsletter</span>
            </div>
            <div class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ number_format($stats['newsletter_subscribers']) }}</div>
            <a href="{{ route('admin.newsletters') }}" class="text-xs text-purple-600 mt-1 font-bold hover:underline">
                View Subscribers &rarr;
            </a>
        </div>
    </div>

    {{-- 2. RECENT ACTIVITY TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Recent Orders</h3>
            <a href="{{ route('admin.orders') }}" class="text-sm text-red-600 hover:text-red-700 font-medium">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase tdark:text-gray-300">
                    <tr>
                        <th class="px-6 py-3">Order ID</th>
                        <th class="px-6 py-3">Customer</th>
                        <th class="px-6 py-3">Kitchen</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-mono font-medium dark:text-gray-300">#{{ substr($order->order_number, -8) }}</td>
                            <td class="px-6 py-4">{{ $order->user->first_name }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $order->chef->chefProfile->business_name ?? 'Kitchen' }}</td>
                            <td class="px-6 py-4 font-bold">₦{{ number_format($order->total_amount) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center tdark:text-gray-300">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection