@extends('layouts.app')

@section('title', 'Your Cart')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 mb-8 flex items-center">
            <i class="fas fa-shopping-cart text-red-600 mr-3"></i> Your Food Cart
        </h1>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        @if(session('cart') && count(session('cart')) > 0)
            <div class="flex flex-col lg:flex-row gap-8">
                
                {{-- LEFT: Cart Items --}}
                <div class="lg:w-2/3">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        {{-- Header (Chef Name) --}}
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                            <span class="tdark:text-gray-300 font-medium">Ordering from:</span>
                            <span class="font-bold text-gray-900 dark:text-gray-100">
                                <i class="fas fa-store text-red-500 mr-1"></i> 
                                {{ $chef ? $chef->chefProfile->business_name : 'Chef' }}
                            </span>
                        </div>

                        {{-- Items List --}}
                        <div class="divide-y divide-gray-100">
                            @foreach(session('cart') as $id => $details)
                                <div class="p-6 flex items-center">
                                    {{-- Image --}}
                                    <img src="{{ $details['image'] ? asset('storage/' . $details['image']) : 'https://via.placeholder.com/80' }}" 
                                         class="w-20 h-20 rounded-lg object-cover border border-gray-200 dark:border-gray-700 hidden sm:block">
                                    
                                    {{-- Info --}}
                                    <div class="ml-0 sm:ml-4 flex-1">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $details['name'] }}</h3>
                                        <div class="tdark:text-gray-300 text-sm mb-2">Price: ₦{{ number_format($details['price']) }}</div>
                                        
                                        <div class="flex items-center justify-between">
                                            {{-- Quantity Badge --}}
                                            <span class="bg-gray-100 text-gray-800 text-xs font-bold px-3 py-1 rounded-full">
                                                Qty: {{ $details['quantity'] }}
                                            </span>

                                            {{-- Remove Button --}}
                                            <form action="{{ route('remove.from.cart') }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $id }}">
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium transition-colors">
                                                    <i class="fas fa-trash-alt mr-1"></i> Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    {{-- Subtotal --}}
                                    <div class="text-right ml-4">
                                        <span class="block font-bold text-lg text-gray-900 dark:text-gray-100">
                                            ₦{{ number_format($details['price'] * $details['quantity']) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    {{-- Continue Shopping --}}
                    <div class="mt-6">
                        <a href="{{ route('chef.index') }}" class="tdark:text-gray-300 hover:text-gray-900 dark:text-gray-100 font-medium flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i> Continue Shopping
                        </a>
                    </div>
                </div>

                {{-- RIGHT: Summary --}}
                <div class="lg:w-1/3">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 sticky top-24">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">Order Summary</h3>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                <span>Subtotal</span>
                                <span>₦{{ number_format($total) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                <span>Service Fee</span>
                                <span>₦0.00</span>
                            </div>
                            <div class="border-t pt-3 flex justify-between items-center">
                                <span class="font-bold text-gray-900 dark:text-gray-100 text-lg">Total</span>
                                <span class="font-bold text-red-600 text-2xl">₦{{ number_format($total) }}</span>
                            </div>
                        </div>

                        {{-- Checkout Action --}}
                        @auth
                            <a href="{{ route('checkout.index') }}" class="block w-full text-center bg-red-600 text-white font-bold py-4 rounded-xl hover:bg-red-700 shadow-md transition-all transform hover:-translate-y-1">
                                Proceed to Checkout <i class="fas fa-chevron-right ml-2"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="block w-full text-center bg-gray-900 text-white font-bold py-4 rounded-xl hover:bg-gray-800 shadow-md transition-all">
                                Login to Checkout
                            </a>
                            <p class="text-xs text-center tdark:text-gray-300 mt-2">You must have an account to place an order.</p>
                        @endauth
                    </div>
                </div>

            </div>
        @else
            {{-- Empty Cart State --}}
            <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-300">
                <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-shopping-basket text-4xl text-red-200"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Your cart is empty</h2>
                <p class="tdark:text-gray-300 mb-8">Looks like you haven't added any delicious food yet.</p>
                <a href="{{ route('chef.index') }}" class="bg-red-600 text-white font-bold py-3 px-8 rounded-full hover:bg-red-700 transition-colors shadow-lg">
                    Start Browsing
                </a>
            </div>
        @endif

    </div>
</div>
@endsection