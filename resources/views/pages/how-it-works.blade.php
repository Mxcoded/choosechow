@extends('layouts.app')

@section('title', 'How It Works')

@section('content')
<div class="relative bg-gradient-to-br from-chow-cream-50 to-chow-cream-100 dark:from-dark-base dark:to-dark-section overflow-hidden">
    
    {{-- HEADER --}}
    <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold text-gray-900 dark:text-content-primary sm:text-5xl sm:tracking-tight lg:text-6xl">
            Food delivery, <span class="text-accent">reimagined.</span>
        </h1>
        <p class="mt-4 max-w-xl mx-auto text-xl text-gray-500 dark:text-content-secondary">
            From a chef's kitchen directly to your dining table. No factories, no frozen meals—just authentic cooking.
        </p>
    </div>

    {{-- STEPS --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
        <div class="relative">
            {{-- Connector Line (Desktop) --}}
            <div class="hidden md:block absolute top-12 left-0 w-full h-1 bg-red-100 dark:bg-dark-border -z-10"></div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
                
                {{-- STEP 1 --}}
                <div class="bg-white dark:bg-dark-base p-6">
                    <div class="w-24 h-24 bg-accent text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-6 shadow-lg shadow-accent/30 dark:shadow-accent/20 relative z-10">
                        1
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-content-primary mb-2">Browse Local Kitchens</h3>
                    <p class="text-gray-600 dark:text-content-secondary">
                        Explore verified home chefs in your neighborhood. Filter by cuisine, dietary needs, or rating.
                    </p>
                </div>

                {{-- STEP 2 --}}
                <div class="bg-white dark:bg-dark-base p-6">
                    <div class="w-24 h-24 bg-accent text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-6 shadow-lg shadow-accent/30 dark:shadow-accent/20 relative z-10">
                        2
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-content-primary mb-2">Customize & Order</h3>
                    <p class="text-gray-600 dark:text-content-secondary">
                        Select your meals for today or schedule them for the week. Pay securely via Paystack.
                    </p>
                </div>

                {{-- STEP 3 --}}
                <div class="bg-white dark:bg-dark-base p-6">
                    <div class="w-24 h-24 bg-accent text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-6 shadow-lg shadow-accent/30 dark:shadow-accent/20 relative z-10">
                        3
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-content-primary mb-2">Enjoy Fresh Food</h3>
                    <p class="text-gray-600 dark:text-content-secondary">
                        Your meal is prepared fresh upon order and delivered hot to your doorstep.
                    </p>
                </div>
            </div>
        </div>
    </div>

    
</div>
@endsection