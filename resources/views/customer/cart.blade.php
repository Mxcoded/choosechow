@extends('layouts.dashboard')

@section('title', 'My Cart')
@section('page_title', 'Shopping Cart')

@section('content')
<div class="max-w-5xl mx-auto pb-20">
    
    @if(session('success'))
        <div class="mb-6 bg-green-50 text-green-700 p-4 rounded-lg border border-green-200 flex items-center">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    @if($cartItems->count() > 0)
        <div class="flex flex-col lg:flex-row gap-8">
            
            {{-- LEFT: Cart Items --}}
            <div class="flex-1 bg-white rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 flex justify-between items-center">
                    <h2 class="font-bold text-gray-800">Items ({{ $cartItems->count() }})</h2>
                </div>
                
                <div class="divide-y divide-gray-100">
                    @foreach($cartItems as $item)
                        <div class="p-6 flex flex-col sm:flex-row items-center gap-6">
                            {{-- Image --}}
                            <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                @if($item->menu->image)
                                    <img src="{{ asset('storage/' . $item->menu->image) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <i class="fas fa-utensils"></i>
                                    </div>
                                @endif
                            </div>

                            {{-- Details --}}
                            <div class="flex-1 text-center sm:text-left">
                                <h3 class="font-bold text-gray-900 dark:text-gray-100">{{ $item->menu->name }}</h3>
                                <p class="text-sm tdark:text-gray-300 mb-1">
                                    by {{ $item->menu->chef->chefProfile->business_name ?? $item->menu->chef->first_name }}
                                </p>
                                <div class="font-bold text-red-600">₦{{ number_format($item->menu->price) }}</div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-4">
                                {{-- Update Form --}}
                                <form action="{{ route('update.cart') }}" method="POST" class="flex items-center">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                    <select name="quantity" onchange="this.form.submit()" class="border-gray-300 rounded-md text-sm py-1 pl-2 pr-8 focus:ring-red-500 focus:border-red-500">
                                        @for($i=1; $i<=10; $i++)
                                            <option value="{{ $i }}" {{ $item->quantity == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </form>

                                {{-- Remove Form --}}
                                <form action="{{ route('remove.from.cart') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors p-2">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- RIGHT: Summary --}}
            <div class="w-full lg:w-80">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sticky top-24">
                    <h3 class="font-bold text-gray-800 mb-4 text-lg">Order Summary</h3>
                    
                    <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                        <span class="font-bold text-gray-900 dark:text-gray-100">₦{{ number_format($total) }}</span>
                    </div>

                    <p class="text-xs tdark:text-gray-300 mb-6">Delivery fees and taxes calculated at checkout.</p>

                    <a href="{{ route('checkout.index') }}" class="block w-full bg-red-600 text-white text-center font-bold py-3 rounded-lg hover:bg-red-700 transition-colors shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Proceed to Checkout
                    </a>

                    <a href="{{ route('chef.index') }}" class="block w-full text-center tdark:text-gray-300 text-sm font-medium mt-4 hover:dark:text-gray-300">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <div class="text-center py-20 bg-white rounded-xl border border-dashed border-gray-300 max-w-2xl mx-auto">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-shopping-cart text-4xl text-gray-300"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Your cart is empty</h2>
            <p class="tdark:text-gray-300 mb-8">Looks like you haven't added any meals yet.</p>
            <a href="{{ route('chef.index') }}" class="inline-flex items-center bg-red-600 text-white px-8 py-3 rounded-full font-bold hover:bg-red-700 transition-colors shadow-lg">
                <i class="fas fa-utensils mr-2"></i> Browse Menus
            </a>
        </div>
    @endif
</div>
@endsection