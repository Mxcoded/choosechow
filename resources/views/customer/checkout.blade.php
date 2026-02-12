@extends('layouts.dashboard')

@section('title', 'Checkout')
@section('page_title', 'Checkout')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        {{-- LEFT: Delivery Details Form --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center">
                <i class="fas fa-map-marker-alt text-red-500 mr-2"></i> Delivery Details
            </h3>

            <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                @csrf
                
                {{-- Address --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium dark:text-gray-300 mb-1">Delivery Address</label>
                    <textarea name="address" rows="3" required
                        class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"
                        placeholder="e.g. Block B, Flat 4, University Hostel"></textarea>
                </div>

                {{-- Phone --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium dark:text-gray-300 mb-1">Phone Number</label>
                    <input type="tel" name="phone_number" required value="{{ Auth::user()->phone_number ?? '' }}"
                        class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                </div>

                {{-- Notes --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium dark:text-gray-300 mb-1">Delivery Notes (Optional)</label>
                    <input type="text" name="notes"
                        class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"
                        placeholder="e.g. Call when outside">
                </div>

                <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 rounded-xl hover:bg-green-700 transition-colors shadow-lg shadow-green-200">
                    Pay ₦{{ number_format($total) }} Now
                </button>
            </form>
        </div>

        {{-- RIGHT: Order Summary --}}
        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 dark:border-gray-700 h-fit">
            <h3 class="font-bold text-lg text-gray-800 mb-4">Your Order</h3>
            
            <div class="space-y-4 mb-6">
                {{-- Loop through SESSION CART ($cart) instead of Database Object ($cartItems) --}}
                @foreach($cart as $id => $details)
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded bg-white border border-gray-200 dark:border-gray-700 flex items-center justify-center overflow-hidden">
                                @if(isset($details['image']))
                                    <img src="{{ asset('storage/' . $details['image']) }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fas fa-utensils text-gray-300"></i>
                                @endif
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-800">{{ $details['name'] }}</div>
                                <div class="text-xs tdark:text-gray-300">Qty: {{ $details['quantity'] }}</div>
                            </div>
                        </div>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            ₦{{ number_format($details['price'] * $details['quantity']) }}
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2">
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                    <span>Subtotal</span>
                    <span>₦{{ number_format($subtotal) }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                    <span>Delivery Fee</span>
                    <span>₦{{ number_format($deliveryFee) }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-gray-100 pt-2 border-t border-gray-200 dark:border-gray-700 mt-2">
                    <span>Total</span>
                    <span>₦{{ number_format($total) }}</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection