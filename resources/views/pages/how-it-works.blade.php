@extends('layouts.app')

@section('title', 'How It Works')

@section('content')
<div class="bg-white dark:bg-gray-900 transition-colors duration-300">
    
    {{-- HEADER --}}
    <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl sm:tracking-tight lg:text-6xl">
            Food delivery, <span class="text-red-600">reimagined.</span>
        </h1>
        <p class="mt-4 max-w-xl mx-auto text-xl text-gray-500 dark:text-gray-300">
            From a chef's kitchen directly to your dining table. No factories, no frozen meals—just authentic cooking.
        </p>
    </div>

    {{-- STEPS --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
        <div class="relative">
            {{-- Connector Line (Desktop) --}}
            <div class="hidden md:block absolute top-12 left-0 w-full h-1 bg-red-100 dark:bg-gray-700 -z-10"></div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
                
                {{-- STEP 1 --}}
                <div class="bg-white dark:bg-gray-900 p-6">
                    <div class="w-24 h-24 bg-red-600 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-6 shadow-lg shadow-red-200 dark:shadow-none relative z-10">
                        1
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Browse Local Kitchens</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Explore verified home chefs in your neighborhood. Filter by cuisine, dietary needs, or rating.
                    </p>
                </div>

                {{-- STEP 2 --}}
                <div class="bg-white dark:bg-gray-900 p-6">
                    <div class="w-24 h-24 bg-red-600 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-6 shadow-lg shadow-red-200 dark:shadow-none relative z-10">
                        2
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Customize & Order</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Select your meals for today or schedule them for the week. Pay securely via Paystack.
                    </p>
                </div>

                {{-- STEP 3 --}}
                <div class="bg-white dark:bg-gray-900 p-6">
                    <div class="w-24 h-24 bg-red-600 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-6 shadow-lg shadow-red-200 dark:shadow-none relative z-10">
                        3
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Enjoy Fresh Food</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Your meal is prepared fresh upon order and delivered hot to your doorstep.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- FAQ SECTION --}}
    <div class="bg-gray-50 dark:bg-gray-800 py-16 transition-colors duration-300">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white text-center mb-8">Frequently Asked Questions</h2>
            
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm p-6">
                    <h4 class="font-bold text-lg text-gray-900 dark:text-white mb-2">Is the food really homemade?</h4>
                    <p class="text-gray-600 dark:text-gray-400">Yes! Every chef on ChooseChow cooks from their own inspected home kitchen or a dedicated small commercial space.</p>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm p-6">
                    <h4 class="font-bold text-lg text-gray-900 dark:text-white mb-2">How long does delivery take?</h4>
                    <p class="text-gray-600 dark:text-gray-400">Since meals are cooked fresh, preparation usually takes 30-45 mins. Delivery depends on your distance, but we aim for under an hour total.</p>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm p-6">
                    <h4 class="font-bold text-lg text-gray-900 dark:text-white mb-2">Can I order for the whole week?</h4>
                    <p class="text-gray-600 dark:text-gray-400">Absolutely. You can place pre-orders for specific days or subscribe to a weekly meal plan.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection