@extends('layouts.dashboard')

@section('title', 'My Orders')
@section('page_title', 'Order History')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    @if($orders->count() > 0)
        <div class="divide-y divide-gray-100">
            @foreach($orders as $order)
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-4">
                        <div>
                            <span class="text-xs font-bold tdark:text-gray-300 uppercase tracking-wider">Order #{{ $order->order_number }}</span>
                            <div class="font-bold text-gray-900 dark:text-gray-100 mt-1">
                                {{ $order->items->first()->menu_name }} 
                                @if($order->items->count() > 1)
                                    <span class="tdark:text-gray-300 text-sm font-normal">+ {{ $order->items->count() - 1 }} others</span>
                                @endif
                            </div>
                            <div class="text-sm tdark:text-gray-300 mt-1">
                                From: <span class="font-medium dark:text-gray-300">{{ $order->chef->chefProfile->business_name ?? 'Chef ' . $order->chef->first_name }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <div class="font-bold text-gray-900 dark:text-gray-100">₦{{ number_format($order->total_amount) }}</div>
                                <div class="text-xs tdark:text-gray-300">{{ $order->created_at->format('M d, Y') }}</div>
                            </div>
                            
                            {{-- STATUS BADGES --}}
                            @if($order->status == 'pending_payment')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">Unpaid</span>
                            @elseif($order->status == 'pending')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Preparing</span>
                            @elseif($order->status == 'completed')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">Delivered</span>
                            @elseif($order->status == 'cancelled')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 tdark:text-gray-300">Cancelled</span>
                            @endif
                        </div>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                        
                        {{-- 1. RETRY PAYMENT (If Unpaid) --}}
                        @if($order->status == 'pending_payment')
                            <a href="{{ route('customer.orders.retry', $order->id) }}" class="px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-700">
                                Pay Now
                            </a>
                        @endif

                        {{-- 2. CANCEL BUTTON (If Unpaid OR Pending) --}}
                        @if(in_array($order->status, ['pending_payment', 'pending']))
                            <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-4 py-2 bg-white border border-gray-300 dark:text-gray-300 text-sm font-bold rounded-lg hover:bg-red-50 hover:text-red-600 hover:border-red-200">
                                    Cancel Order
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="p-6">
            {{ $orders->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-receipt text-4xl text-gray-300 mb-3"></i>
            <p class="tdark:text-gray-300">No orders found.</p>
        </div>
    @endif
</div>
@endsection