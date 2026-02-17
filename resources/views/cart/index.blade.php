@extends('layouts.app')

@section('title', 'My Cart - ChooseChow')

@section('content')
<div class="bg-gray-50 dark:bg-gray-900 min-h-screen py-12">
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">Your Cart 🛒</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Review your items before checkout</p>
    </div>

    @if(session('cart') && count(session('cart')) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- LEFT: Cart Items Table --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-chow-cream-50 border-b border-chow-cream-200 text-xs uppercase text-chow-brown-600 font-semibold">
                        <tr>
                            <th class="p-4">Dish</th>
                            <th class="p-4">Price</th>
                            <th class="p-4">Qty</th>
                            <th class="p-4">Subtotal</th>
                            <th class="p-4 text-center"><i class="fas fa-trash"></i></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-chow-cream-100">
                        @php $total = 0; @endphp
                        @foreach(session('cart') as $id => $details)
                            @php $total += $details['price'] * $details['quantity']; @endphp
                            <tr class="hover:bg-chow-orange-50/30 transition-colors group" data-id="{{ $id }}">
                                {{-- Product --}}
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-lg bg-chow-cream-100 overflow-hidden flex-shrink-0">
                                            @if($details['image'])
                                                <img src="{{ asset('storage/' . $details['image']) }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="flex items-center justify-center h-full text-chow-orange-300"><i class="fas fa-utensils"></i></div>
                                            @endif
                                        </div>
                                        <div class="font-medium text-chow-brown-800 line-clamp-1">{{ $details['name'] }}</div>
                                    </div>
                                </td>
                                
                                {{-- Price --}}
                                <td class="p-4 text-sm text-chow-brown-600">₦{{ number_format($details['price']) }}</td>
                                
                                {{-- Quantity Input --}}
                                <td class="p-4">
                                    <input type="number" value="{{ $details['quantity'] }}" min="1" 
                                           class="update-cart w-16 text-center text-sm border-chow-cream-300 rounded-lg focus:border-chow-orange-500 focus:ring-chow-orange-500 bg-chow-cream-50 focus:bg-white">
                                </td>
                                
                                {{-- Subtotal --}}
                                <td class="p-4 font-bold text-chow-brown-800">
                                    ₦{{ number_format($details['price'] * $details['quantity']) }}
                                </td>
                                
                                {{-- Remove Button --}}
                                <td class="p-4 text-center">
                                    <button class="remove-from-cart text-chow-brown-400 hover:text-chow-red-600 transition-colors p-2 rounded-full hover:bg-chow-red-50">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- RIGHT: Order Summary --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="font-bold text-lg text-chow-brown-800 mb-4 border-b border-chow-cream-200 pb-2">Order Summary</h3>
                    
                    <div class="flex justify-between items-center mb-2 text-sm text-chow-brown-600">
                        <span>Subtotal</span>
                        <span class="font-medium">₦{{ number_format($total) }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-4 text-sm text-chow-brown-600">
                        <span>Service Fee</span>
                        <span class="font-medium">₦0.00</span>
                    </div>
                    
                    <div class="border-t border-chow-cream-200 pt-4 flex justify-between items-center mb-6">
                        <span class="font-bold text-xl text-chow-brown-800">Total</span>
                        <span class="font-bold text-xl text-chow-red-600">₦{{ number_format($total) }}</span>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="block w-full text-center bg-gradient-to-r from-chow-red-600 to-chow-orange-500 text-white font-bold py-3 rounded-xl hover:from-chow-red-700 hover:to-chow-orange-600 transition-all shadow-lg shadow-chow-orange-200">
                        Proceed to Checkout <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>

                <a href="{{ route('chef.index') }}" class="block text-center text-sm text-chow-brown-500 hover:text-chow-orange-600 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Continue Shopping
                </a>
            </div>

        </div>
    @else
        {{-- Empty State --}}
        <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-xl border border-dashed border-gray-300 dark:border-gray-600">
            <div class="w-24 h-24 bg-chow-orange-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-shopping-basket text-chow-orange-300 text-4xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-chow-brown-800 mb-2">Your cart is empty</h2>
            <p class="text-chow-brown-500 mb-8">Looks like you haven't added any delicious food yet.</p>
            <a href="{{ route('chef.index') }}" class="bg-gradient-to-r from-chow-red-600 to-chow-orange-500 text-white font-bold py-3 px-8 rounded-xl hover:from-chow-red-700 hover:to-chow-orange-600 transition-all shadow-lg">
                Start Ordering
            </a>
        </div>
    @endif

</div>
</div>

{{-- AJAX SCRIPTS FOR UPDATE/REMOVE --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script type="text/javascript">
    
    // 1. Update Quantity on Input Change
    $(".update-cart").change(function (e) {
        e.preventDefault();
        var ele = $(this);
        var quantity = ele.val();
        
        if(quantity < 1) {
             alert("Quantity must be at least 1");
             return;
        }

        $.ajax({
            url: '{{ route('update.cart') }}',
            method: "patch",
            data: {
                _token: '{{ csrf_token() }}', 
                id: ele.parents("tr").attr("data-id"), 
                quantity: quantity
            },
            success: function (response) {
               window.location.reload();
            }
        });
    });

    // 2. Remove Item on Click
    $(".remove-from-cart").click(function (e) {
        e.preventDefault();
        var ele = $(this);

        if(confirm("Are you sure you want to remove this item?")) {
            $.ajax({
                url: '{{ route('remove.from.cart') }}',
                method: "DELETE",
                data: {
                    _token: '{{ csrf_token() }}', 
                    id: ele.parents("tr").attr("data-id")
                },
                success: function (response) {
                    window.location.reload();
                }
            });
        }
    });

</script>
@endsection