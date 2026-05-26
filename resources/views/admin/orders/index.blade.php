@extends('layouts.dashboard')

@section('title', 'Admin - All Orders')
@section('page_title', 'Order Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Total Orders</p>
                    <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-shopping-bag text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Pending</p>
                    <p class="text-2xl font-extrabold text-yellow-600 mt-1">{{ number_format($stats['pending']) }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Completed</p>
                    <p class="text-2xl font-extrabold text-green-600 mt-1">{{ number_format($stats['completed']) }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Today's Orders</p>
                    <p class="text-2xl font-extrabold text-purple-600 mt-1">{{ number_format($stats['today_orders']) }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-calendar-day text-purple-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Total Revenue</p>
                    <p class="text-xl font-extrabold text-green-600 mt-1">₦{{ number_format($stats['total_revenue']) }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-coins text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form action="{{ route('admin.orders') }}" method="GET" class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Search by order number or customer..." 
                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            <div>
                <select name="status" class="w-full md:w-40 py-2 px-3 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="pending_payment" {{ request('status') == 'pending_payment' ? 'selected' : '' }}>Pending Payment</option>
                    <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>Preparing</option>
                    <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Ready</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <select name="payment" class="w-full md:w-40 py-2 px-3 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500">
                    <option value="">Payment Status</option>
                    <option value="paid" {{ request('payment') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ request('payment') == 'pending' ? 'selected' : '' }}>Unpaid</option>
                </select>
            </div>
            <div>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                    class="w-full py-2 px-3 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500" placeholder="From">
            </div>
            <div>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                    class="w-full py-2 px-3 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500" placeholder="To">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-gray-900 text-white px-6 py-2 rounded-lg font-bold hover:bg-gray-800 transition">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'payment', 'date_from', 'date_to']))
                    <a href="{{ route('admin.orders') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg font-bold hover:bg-gray-200 transition">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Orders Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold border-b">
                    <tr>
                        <th class="p-4">Order</th>
                        <th class="p-4">Customer</th>
                        <th class="p-4">Kitchen</th>
                        <th class="p-4">Items</th>
                        <th class="p-4">Amount</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Payment</th>
                        <th class="p-4">Date</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4">
                                <span class="font-mono font-bold text-gray-800">#{{ substr($order->order_number, -8) }}</span>
                            </td>
                            <td class="p-4">
                                <div class="font-medium text-gray-900">{{ $order->user->first_name }} {{ $order->user->last_name }}</div>
                                <div class="text-xs text-gray-400">{{ $order->user->email }}</div>
                            </td>
                            <td class="p-4 text-gray-600">
                                {{ $order->chef->chefProfile->business_name ?? 'Unknown Kitchen' }}
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">
                                    {{ $order->items->count() }} items
                                </span>
                            </td>
                            <td class="p-4 font-bold text-gray-900">
                                ₦{{ number_format($order->total_amount, 2) }}
                            </td>
                            <td class="p-4">
                                @php
                                    $colors = [
                                        'pending_payment' => 'bg-yellow-100 text-yellow-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'preparing' => 'bg-blue-100 text-blue-800',
                                        'ready' => 'bg-indigo-100 text-indigo-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                    $icons = [
                                        'pending_payment' => 'fa-credit-card',
                                        'pending' => 'fa-clock',
                                        'preparing' => 'fa-fire',
                                        'ready' => 'fa-check',
                                        'completed' => 'fa-check-double',
                                        'cancelled' => 'fa-times',
                                    ];
                                    $color = $colors[$order->status] ?? 'bg-gray-100 text-gray-800';
                                    $icon = $icons[$order->status] ?? 'fa-question';
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $color }} inline-flex items-center">
                                    <i class="fas {{ $icon }} mr-1"></i> {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td class="p-4">
                                @if($order->payment_status === 'paid')
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                        <i class="fas fa-check-circle mr-1"></i> Paid
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                        <i class="fas fa-exclamation-circle mr-1"></i> Unpaid
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-gray-400 text-xs">
                                <div>{{ $order->created_at->format('M d, Y') }}</div>
                                <div>{{ $order->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('admin.orders.show', $order->id) }}" 
                                    class="text-xs border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg hover:bg-gray-50 transition-colors font-bold inline-flex items-center">
                                    <i class="fas fa-eye mr-1"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-gray-400">
                                <i class="fas fa-shopping-bag text-4xl mb-3 block"></i>
                                No orders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t bg-gray-50">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
