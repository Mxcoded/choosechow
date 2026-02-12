@extends('layouts.dashboard')

@section('title', 'My Dashboard')

@section('content')
<div class="max-w-6xl mx-auto pb-20">
    
    {{-- Welcome Section --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                Welcome back, {{ Auth::user()->first_name }}! 👋
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Hungry? Let's find you something delicious.</p>
        </div>
        <a href="{{ route('chef.index') }}" class="mt-4 md:mt-0 bg-red-600 text-white font-bold py-3 px-6 rounded-full hover:bg-red-700 transition-colors shadow-md">
            Find Chow <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Active Orders --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center">
            <div class="p-3 bg-red-50 text-red-600 rounded-full mr-4">
                <i class="fas fa-utensils text-xl"></i>
            </div>
            <div>
                <p class="text-sm tdark:text-gray-300 font-medium">Active Orders</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ Auth::user()->ordersPlaced()->whereIn('status', ['pending', 'accepted', 'cooking', 'ready'])->count() }}
                </p>
            </div>
        </div>

        {{-- Total Spent --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center">
            <div class="p-3 bg-green-50 text-green-600 rounded-full mr-4">
                <i class="fas fa-wallet text-xl"></i>
            </div>
            <div>
                <p class="text-sm tdark:text-gray-300 font-medium">Total Spent</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    ₦{{ number_format(Auth::user()->ordersPlaced()->where('status', 'completed')->sum('total_amount')) }}
                </p>
            </div>
        </div>

        {{-- Total Orders --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-full mr-4">
                <i class="fas fa-receipt text-xl"></i>
            </div>
            <div>
                <p class="text-sm tdark:text-gray-300 font-medium">Total Orders</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ Auth::user()->ordersPlaced()->count() }}
                </p>
            </div>
        </div>
    </div>

    {{-- Recent Orders Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Recent Orders</h3>
            <a href="{{ route('customer.orders') }}" class="text-sm text-red-600 hover:text-red-800 font-medium">View All</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold tdark:text-gray-300 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-bold tdark:text-gray-300 uppercase tracking-wider">Chef</th>
                        <th class="px-6 py-3 text-left text-xs font-bold tdark:text-gray-300 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-bold tdark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold tdark:text-gray-300 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse(Auth::user()->ordersPlaced()->latest()->take(5)->get() as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100">
                                {{ $order->order_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                {{ $order->chef->chefProfile->business_name ?? 'Unknown Chef' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100">
                                ₦{{ number_format($order->total_amount) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $colors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'accepted' => 'bg-blue-100 text-blue-800',
                                        'cooking' => 'bg-orange-100 text-orange-800',
                                        'ready' => 'bg-purple-100 text-purple-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colors[$order->status] ?? 'bg-gray-100' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm tdark:text-gray-300">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center tdark:text-gray-300">
                                No orders yet. <a href="{{ route('chef.index') }}" class="text-red-600 underline">Order something now!</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection