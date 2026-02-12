@extends('layouts.dashboard')

@section('title', 'Admin - Orders')
@section('page_title', 'All Orders')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-xs uppercase tdark:text-gray-300 font-semibold border-b">
                    <tr>
                        <th class="p-4">Order ID</th>
                        <th class="p-4">Customer</th>
                        <th class="p-4">Kitchen</th>
                        <th class="p-4">Amount</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 font-mono font-bold dark:text-gray-300">
                                #{{ substr($order->order_number, -8) }}
                            </td>
                            <td class="p-4 text-gray-600 dark:text-gray-400">
                                {{ $order->user->first_name }} {{ $order->user->last_name }}
                            </td>
                            <td class="p-4 text-gray-600 dark:text-gray-400">
                                {{ $order->chef->chefProfile->business_name ?? 'Unknown Kitchen' }}
                            </td>
                            <td class="p-4 font-bold text-gray-900 dark:text-gray-100">
                                ₦{{ number_format($order->total_amount, 2) }}
                            </td>
                            <td class="p-4">
                                @php
                                    $colors = [
                                        'pending_payment' => 'bg-yellow-100 text-yellow-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'preparing' => 'bg-blue-100 text-blue-800',
                                        'ready' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                    $color = $colors[$order->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $color }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="p-4 text-gray-400 text-xs">
                                {{ $order->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center tdark:text-gray-300">No orders placed yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection