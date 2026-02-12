@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 mb-8">Secure Checkout 🔒</h1>

        {{-- ERROR DISPLAY BLOCK (Reveals why it reloads) --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                <strong class="font-bold">Please check the form:</strong>
                <ul class="list-disc list-inside mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf
            
            <div class="flex flex-col lg:flex-row gap-8">
                
                {{-- LEFT: Delivery Details --}}
                <div class="lg:w-2/3">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">1. Delivery Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Name (Read only) --}}
                            <div>
                                <label class="block text-sm font-bold dark:text-gray-300 mb-1">Full Name</label>
                                <input type="text" value="{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}" 
                                       class="w-full bg-gray-100 border-gray-300 rounded-lg tdark:text-gray-300 cursor-not-allowed" readonly>
                            </div>

                            {{-- Phone --}}
                            <div>
                                <label class="block text-sm font-bold dark:text-gray-300 mb-1">Phone Number <span class="text-red-600">*</span></label>
                                <input type="tel" name="phone_number" value="{{ Auth::user()->phone }}" required
                                       class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" 
                                       placeholder="080...">
                            </div>

                            {{-- Address (FIXED NAME: 'address') --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold dark:text-gray-300 mb-1">Delivery Address <span class="text-red-600">*</span></label>
                                <textarea name="address" rows="3" required
                                          class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" 
                                          placeholder="House Number, Street Name, Area, City...">{{ old('address', Auth::user()->address) }}</textarea>
                            </div>

                            {{-- Notes --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold dark:text-gray-300 mb-1">Note to Chef (Optional)</label>
                                <textarea name="notes" rows="2" 
                                          class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" 
                                          placeholder="e.g. Please make it extra spicy..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">2. Payment Method</h3>
                         {{-- Only Paystack for now to test Wallet --}}
                         <label class="flex items-center p-4 border rounded-lg cursor-pointer bg-red-50 border-red-200">
                            <input type="radio" name="payment_method" value="paystack" checked class="h-4 w-4 text-red-600 focus:ring-red-500">
                            <div class="ml-3">
                                <span class="block text-sm font-medium text-gray-900 dark:text-gray-100">Pay Online (Card/Transfer)</span>
                                <span class="block text-sm tdark:text-gray-300">Secured by Paystack</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- RIGHT: Order Summary --}}
                <div class="lg:w-1/3">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 sticky top-24">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Order Summary</h3>
                        
                        <div class="space-y-3 mb-6 max-h-60 overflow-y-auto">
                            @foreach($cart as $item)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ $item['quantity'] }}x {{ $item['name'] }}</span>
                                    <span class="font-medium">₦{{ number_format($item['price'] * $item['quantity']) }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t pt-4 space-y-2">
                            {{-- FIXED SUBTOTAL DISPLAY --}}
                            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                <span>Subtotal</span>
                                <span>₦{{ number_format($subtotal) }}</span> 
                            </div>
                            
                            {{-- ADDED DELIVERY FEE DISPLAY --}}
                            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                <span>Delivery Fee</span>
                                <span>₦{{ number_format($deliveryFee) }}</span>
                            </div>

                            <div class="flex justify-between items-center text-xl font-bold text-gray-900 dark:text-gray-100 mt-4 pt-4 border-t">
                                <span>Total</span>
                                <span class="text-red-600">₦{{ number_format($total) }}</span>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-red-600 text-white font-bold py-4 rounded-xl mt-6 hover:bg-red-700 shadow-md transition-all transform hover:-translate-y-1 flex justify-center items-center">
                            Pay Now <i class="fas fa-lock ml-2"></i>
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection