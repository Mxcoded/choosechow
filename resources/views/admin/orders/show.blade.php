@extends('layouts.dashboard')
@section('title', 'Order #' . $order->order_number)

@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ route('admin.orders') }}" class="tdark:text-gray-300 hover:text-red-600 text-sm mb-4 inline-block">&larr; Back to Orders</a>
    
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
            <h1 class="text-lg font-bold text-gray-800">Order #{{ $order->order_number }}</h1>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 uppercase">{{ $order->status }}</span>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-xs uppercase font-bold text-gray-400 mb-2">Customer Details</h3>
                <p class="font-bold text-gray-900 dark:text-gray-100">{{ $order->user->first_name }} {{ $order->user->last_name }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $order->phone_number }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 bg-gray-50 p-2 rounded border">{{ $order->delivery_address }}</p>
            </div>
            <div>
                <h3 class="text-xs uppercase font-bold text-gray-400 mb-2">Kitchen Details</h3>
                <p class="font-bold text-gray-900 dark:text-gray-100">{{ $order->chef->chefProfile->business_name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $order->chef->email }}</p>
            </div>
        </div>

        <div class="border-t border-gray-100 dark:border-gray-700">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs tdark:text-gray-300 uppercase">
                    <tr>
                        <th class="px-6 py-3">Item</th>
                        <th class="px-6 py-3 text-right">Price</th>
                        <th class="px-6 py-3 text-right">Qty</th>
                        <th class="px-6 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="px-6 py-4 font-medium">{{ $item->menu_name }}</td>
                            <td class="px-6 py-4 text-right">₦{{ number_format($item->price) }}</td>
                            <td class="px-6 py-4 text-right">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-right font-bold">₦{{ number_format($item->price * $item->quantity) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-600 dark:text-gray-400">Total Paid</td>
                        <td class="px-6 py-4 text-right font-extrabold text-gray-900 dark:text-gray-100 text-lg">₦{{ number_format($order->total_amount) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection