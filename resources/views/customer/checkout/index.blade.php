@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="bg-gray-50 min-h-screen py-12" x-data="{
    deliveryType: 'asap',
    scheduledDate: '',
    scheduledTime: '',
    formatDate(dateStr) {
        if (!dateStr) return '';
        const date = new Date(dateStr + 'T00:00:00');
        const options = { weekday: 'short', month: 'short', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    }
}">
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

                    {{-- NEW: Delivery Scheduling Section --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">
                            <i class="fas fa-clock text-red-500 mr-2"></i>2. Delivery Time
                        </h3>
                        
                        {{-- Delivery Type Selection --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            {{-- ASAP Option --}}
                            <label class="delivery-option relative flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all hover:border-red-300 hover:bg-red-50" 
                                   :class="deliveryType === 'asap' ? 'border-red-500 bg-red-50 ring-2 ring-red-200' : 'border-gray-200'">
                                <input type="radio" name="delivery_type" value="asap" x-model="deliveryType" 
                                       class="h-5 w-5 text-red-600 focus:ring-red-500" checked>
                                <div class="ml-4">
                                    <span class="block text-base font-bold text-gray-900 dark:text-gray-100">
                                        <i class="fas fa-bolt text-yellow-500 mr-1"></i> ASAP
                                    </span>
                                    <span class="block text-sm text-gray-500">Delivered in 30-45 minutes</span>
                                </div>
                            </label>

                            {{-- Scheduled Option --}}
                            <label class="delivery-option relative flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all hover:border-red-300 hover:bg-red-50"
                                   :class="deliveryType === 'scheduled' ? 'border-red-500 bg-red-50 ring-2 ring-red-200' : 'border-gray-200'">
                                <input type="radio" name="delivery_type" value="scheduled" x-model="deliveryType" 
                                       class="h-5 w-5 text-red-600 focus:ring-red-500">
                                <div class="ml-4">
                                    <span class="block text-base font-bold text-gray-900 dark:text-gray-100">
                                        <i class="fas fa-calendar-alt text-blue-500 mr-1"></i> Schedule
                                    </span>
                                    <span class="block text-sm text-gray-500">Choose your delivery time</span>
                                </div>
                            </label>
                        </div>

                        {{-- Scheduling Options (shown when 'scheduled' is selected) --}}
                        <div x-show="deliveryType === 'scheduled'" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="space-y-4 pt-4 border-t border-gray-100">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Date Selection --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">
                                        <i class="fas fa-calendar mr-1 text-gray-400"></i> Delivery Date
                                    </label>
                                    <select name="scheduled_date" x-model="scheduledDate"
                                            class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 text-gray-900">
                                        <option value="">Select a date</option>
                                        @php
                                            $dates = \App\Models\Order::getAvailableDates(7);
                                        @endphp
                                        @foreach($dates as $dateValue => $dateLabel)
                                            <option value="{{ $dateValue }}">{{ $dateLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Time Slot Selection --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">
                                        <i class="fas fa-clock mr-1 text-gray-400"></i> Time Slot
                                    </label>
                                    <select name="scheduled_time_slot" x-model="scheduledTime"
                                            class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 text-gray-900">
                                        <option value="">Select a time</option>
                                        @php
                                            $timeSlots = \App\Models\Order::getAvailableTimeSlots();
                                        @endphp
                                        @foreach($timeSlots as $slotValue => $slotLabel)
                                            <option value="{{ $slotValue }}">{{ $slotLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Summary of Selected Time --}}
                            <div x-show="scheduledDate && scheduledTime" 
                                 class="bg-blue-50 border border-blue-200 rounded-lg p-3 flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                                <span class="text-sm text-blue-800">
                                    Your order will be delivered on 
                                    <strong x-text="formatDate(scheduledDate)"></strong> 
                                    between <strong x-text="scheduledTime"></strong>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">3. Payment Method</h3>
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