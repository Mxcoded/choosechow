@extends('layouts.dashboard')

@section('title', 'Order Receipt #' . $order->order_number)

@section('content')
<div class="max-w-3xl mx-auto">
    
    {{-- Back & Print Buttons --}}
    <div class="flex justify-between items-center mb-6 print:hidden">
        <a href="{{ route('customer.orders') }}" class="tdark:text-gray-300 hover:text-gray-900 dark:text-gray-100">
            <i class="fas fa-arrow-left mr-2"></i> Back to Orders
        </a>
        <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded-lg font-bold hover:bg-gray-900 shadow-lg">
            <i class="fas fa-print mr-2"></i> Print Receipt
        </button>
    </div>

    {{-- The Receipt Card --}}
    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 print:shadow-none print:border-none">
        
        {{-- Header --}}
        <div class="border-b border-gray-100 dark:border-gray-700 pb-6 mb-6 flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Receipt</h1>
                <p class="tdark:text-gray-300 text-sm">#{{ $order->order_number }}</p>
                <div class="mt-2 text-xs text-gray-400">
                    Date: {{ $order->created_at->format('M d, Y h:i A') }}
                </div>
            </div>
            <div class="text-right">
                <h2 class="font-bold text-lg text-gray-800">{{ $order->chef->chefProfile->business_name ?? 'Kitchen' }}</h2>
                <p class="text-sm tdark:text-gray-300">{{ $order->chef->chefProfile->kitchen_address ?? '' }}</p>
            </div>
        </div>

        {{-- Customer Info --}}
        <div class="mb-8 grid grid-cols-2 gap-8">
            <div>
                <span class="text-xs uppercase text-gray-400 font-bold block mb-1">Billed To</span>
                <p class="font-bold text-gray-800">{{ $order->user->first_name }} {{ $order->user->last_name }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $order->phone_number }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $order->delivery_address }}</p>
            </div>
            <div class="text-right">
                <span class="text-xs uppercase text-gray-400 font-bold block mb-1">Payment Status</span>
                @if($order->payment_status === 'paid')
                    <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">PAID</span>
                @else
                    <span class="inline-block bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">UNPAID</span>
                @endif
            </div>
        </div>

        {{-- Items Table --}}
        <table class="w-full text-left mb-8">
            <thead class="bg-gray-50 tdark:text-gray-300 text-xs uppercase font-bold">
                <tr>
                    <th class="p-3">Item</th>
                    <th class="p-3 text-center">Qty</th>
                    <th class="p-3 text-right">Price</th>
                    <th class="p-3 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($order->items as $item)
                <tr>
                    <td class="p-3 font-medium text-gray-800">{{ $item->menu_name }}</td>
                    <td class="p-3 text-center text-gray-600 dark:text-gray-400">{{ $item->quantity }}</td>
                    <td class="p-3 text-right text-gray-600 dark:text-gray-400">₦{{ number_format($item->price) }}</td>
                    <td class="p-3 text-right font-bold text-gray-900 dark:text-gray-100">₦{{ number_format($item->price * $item->quantity) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="border-t border-gray-100 dark:border-gray-700 pt-6">
            <div class="flex justify-end mb-2">
                <span class="text-gray-600 dark:text-gray-400 w-32">Subtotal:</span>
                <span class="font-bold text-gray-900 dark:text-gray-100 text-right w-32">₦{{ number_format($order->total_amount - 1500) }}</span> {{-- Approx calculation --}}
            </div>
            <div class="flex justify-end mb-2">
                <span class="text-gray-600 dark:text-gray-400 w-32">Delivery Fee:</span>
                <span class="font-bold text-gray-900 dark:text-gray-100 text-right w-32">₦{{ number_format(1500) }}</span> {{-- Replace with actual fee if stored --}}
            </div>
            <div class="flex justify-end pt-2 border-t border-dashed border-gray-200 dark:border-gray-700">
                <span class="text-lg font-bold text-gray-800 w-32">Total:</span>
                <span class="text-lg font-bold text-red-600 text-right w-32">₦{{ number_format($order->total_amount) }}</span>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-12 text-center text-xs text-gray-400 print:mt-20">
            <p>Thank you for ordering with ChooseChow!</p>
            <p>For support, contact support@choosechow.com</p>
        </div>

    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .max-w-3xl, .max-w-3xl * {
            visibility: visible;
        }
        .max-w-3xl {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .print\:hidden {
            display: none !important;
        }
    }
</style>
@endsection