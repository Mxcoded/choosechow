@extends('layouts.dashboard')

@section('title', 'Incoming Orders')
@section('page_title', 'Manage Orders')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- Stats Cards (Optional Polish) --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-xs tdark:text-gray-300 uppercase font-bold">Pending</div>
            <div class="text-2xl font-bold text-gray-800">{{ $orders->where('status', 'pending')->count() }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-xs tdark:text-gray-300 uppercase font-bold">Cooking</div>
            <div class="text-2xl font-bold text-purple-600">{{ $orders->where('status', 'preparing')->count() }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-xs tdark:text-gray-300 uppercase font-bold">Ready</div>
            <div class="text-2xl font-bold text-green-600">{{ $orders->where('status', 'ready')->count() }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-xs tdark:text-gray-300 uppercase font-bold">Total Earnings</div>
            {{-- Simple sum of displayed orders for now --}}
            <div class="text-2xl font-bold text-gray-800">₦{{ number_format($orders->sum('total_amount')) }}</div>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        @if($orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100 dark:border-gray-700 text-xs uppercase tdark:text-gray-300 font-semibold">
                        <tr>
                            <th class="p-4">Order #</th>
                            <th class="p-4">Customer</th>
                            <th class="p-4">Items</th>
                            <th class="p-4">Total</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Date</th>
                            <th class="p-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50 transition-colors">
                                
                                {{-- ID --}}
                                <td class="p-4 font-mono text-xs tdark:text-gray-300">
                                    {{ substr($order->order_number, -8) }}
                                </td>

                                {{-- Customer (THE FIX IS HERE) --}}
                                <td class="p-4">
                                    <div class="font-bold text-gray-900 dark:text-gray-100">
                                        {{-- Use optional() or null check --}}
                                        {{ $order->user->first_name ?? 'Guest User' }} {{ $order->user->last_name ?? '' }}
                                    </div>
                                    <div class="text-xs tdark:text-gray-300">{{ $order->phone_number }}</div>
                                </td>

                                {{-- Items --}}
                                <td class="p-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $order->items->count() }} items
                                    <span class="text-xs text-gray-400 block truncate w-32">
                                        {{ $order->items->first()->menu_name ?? 'Unknown Item' }}...
                                    </span>
                                </td>

                                {{-- Total --}}
                                <td class="p-4 font-bold text-gray-900 dark:text-gray-100">
                                    ₦{{ number_format($order->total_amount) }}
                                </td>

                                {{-- Status --}}
                                <td class="p-4">
                                    @php
                                        $colors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'preparing' => 'bg-purple-100 text-purple-800',
                                            'ready' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $color = $colors[$order->status] ?? 'bg-gray-100 text-gray-600 dark:text-gray-400';
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-bold uppercase {{ $color }}">
                                        {{ $order->status }}
                                    </span>
                                </td>

                                {{-- Date --}}
                                <td class="p-4 text-xs tdark:text-gray-300">
                                    {{ $order->created_at->format('M d, H:i') }}
                                </td>

                                {{-- Action --}}
                                <td class="p-4 text-right">
                                    <a href="{{ route('chef.orders.show', $order->id) }}" class="bg-gray-900 text-white text-xs font-bold px-3 py-2 rounded-lg hover:bg-red-600 transition-colors">
                                        Manage
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                {{ $orders->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clipboard-list text-gray-300 text-3xl"></i>
                </div>
                <h3 class="tdark:text-gray-300 font-medium">No orders found.</h3>
            </div>
        @endif
    </div>

</div>
@endsection