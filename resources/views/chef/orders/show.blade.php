@extends('layouts.dashboard')

@section('title', 'Order #' . $order->order_number)
@section('page_title', 'Order Details')

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Back Button --}}
    <a href="{{ route('chef.orders.index') }}" class="inline-flex items-center text-sm tdark:text-gray-300 hover:text-gray-900 dark:text-gray-100 mb-6 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i> Back to Orders
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- LEFT COLUMN: Order Info --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- 1. Items List --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-800">Order Items</h3>
                    <span class="text-xs font-mono tdark:text-gray-300">#{{ $order->order_number }}</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <div class="p-4 flex justify-between items-center">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center text-red-500 font-bold text-lg">
                                    {{ $item->quantity }}x
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 dark:text-gray-100">{{ $item->menu_name }}</div>
                                    <div class="text-sm tdark:text-gray-300">₦{{ number_format($item->price) }} each</div>
                                </div>
                            </div>
                            <div class="font-bold text-gray-800">
                                ₦{{ number_format($item->price * $item->quantity) }}
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="p-4 bg-gray-50 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <span class="font-bold text-gray-600 dark:text-gray-400">Total Amount</span>
                    <span class="font-bold text-xl text-gray-900 dark:text-gray-100">₦{{ number_format($order->total_amount) }}</span>
                </div>
            </div>

            {{-- 2. Status Management --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="font-bold text-gray-800 mb-4">Update Status</h3>
                
                <div class="flex flex-wrap gap-3">
                    {{-- Status: Preparing --}}
                    @if($order->status == 'pending')
                        <form action="{{ route('chef.orders.update', $order->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="preparing">
                            <button type="submit" class="bg-purple-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-purple-700 transition-colors shadow-lg shadow-purple-200">
                                <i class="fas fa-fire mr-2"></i> Start Cooking
                            </button>
                        </form>
                    @endif

                    {{-- Status: Ready --}}
                    @if($order->status == 'preparing')
                        <form action="{{ route('chef.orders.update', $order->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="ready">
                            <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-700 transition-colors shadow-lg shadow-blue-200">
                                <i class="fas fa-box mr-2"></i> Mark Ready
                            </button>
                        </form>
                    @endif

                    {{-- Status: Completed --}}
                    @if($order->status == 'ready')
                        <form action="{{ route('chef.orders.update', $order->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="bg-green-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-green-700 transition-colors shadow-lg shadow-green-200">
                                <i class="fas fa-check-circle mr-2"></i> Complete Order
                            </button>
                        </form>
                    @endif

                     {{-- Cancel Button (Always visible unless completed) --}}
                     @if($order->status != 'completed' && $order->status != 'cancelled')
                        <form action="{{ route('chef.orders.update', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="bg-gray-100 text-gray-600 dark:text-gray-400 font-bold py-2 px-6 rounded-lg hover:bg-red-50 hover:text-red-600 transition-colors">
                                Cancel
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN: Customer Details --}}
        <div class="space-y-6">
            
            {{-- Delivery Time Card (if scheduled) --}}
            @if($order->isScheduled())
            <div class="bg-blue-50 rounded-xl border-2 border-blue-200 p-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="bg-blue-100 rounded-full p-2">
                        <i class="fas fa-calendar-check text-blue-600"></i>
                    </div>
                    <h3 class="font-bold text-blue-800">Scheduled Delivery</h3>
                </div>
                <div class="text-2xl font-bold text-blue-900 mb-1">
                    {{ $order->scheduled_date?->format('D, M j') }}
                </div>
                <div class="text-lg text-blue-700">
                    {{ $order->scheduled_time_slot }}
                </div>
                <p class="text-xs text-blue-600 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Customer requested this delivery time
                </p>
            </div>
            @else
            <div class="bg-yellow-50 rounded-xl border border-yellow-200 p-4">
                <div class="flex items-center gap-2 text-yellow-800">
                    <i class="fas fa-bolt text-yellow-600"></i>
                    <span class="font-bold">ASAP Delivery</span>
                </div>
                <p class="text-xs text-yellow-600 mt-1">Deliver within 30-45 minutes</p>
            </div>
            @endif

            {{-- Customer Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Customer Details</h3>
                
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center tdark:text-gray-300">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        {{-- FIX IS HERE: Safe Operator --}}
                        <div class="font-bold text-gray-900 dark:text-gray-100">
                            {{ $order->user->first_name ?? 'Guest User' }} {{ $order->user->last_name ?? '' }}
                        </div>
                        <div class="text-sm tdark:text-gray-300">Customer</div>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-phone mt-1 text-gray-400"></i>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $order->phone_number }}</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fas fa-map-marker-alt mt-1 text-gray-400"></i>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $order->delivery_address }}</span>
                    </div>
                </div>

                @if($order->notes)
                    <div class="mt-4 p-3 bg-yellow-50 text-yellow-800 text-sm rounded-lg border border-yellow-100">
                        <strong>Note:</strong> {{ $order->notes }}
                    </div>
                @endif
            </div>

            {{-- Payment Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Payment Info</h3>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Method</span>
                    <span class="font-bold text-gray-800 capitalize">{{ $order->payment_method }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Status</span>
                    @if($order->payment_status == 'paid')
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-bold">PAID</span>
                    @else
                        <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full font-bold">UNPAID</span>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection