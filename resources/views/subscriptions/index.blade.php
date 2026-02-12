@extends('layouts.app')

@section('title', 'Meal Plans')

@section('content')
<div class="bg-gray-50 dark:bg-gray-900 min-h-screen py-12 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- HEADER --}}
        <div class="text-center">
            <h2 class="text-base font-semibold text-red-600 dark:text-red-400 tracking-wide uppercase">Pricing</h2>
            <p class="mt-1 text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl sm:tracking-tight lg:text-6xl">
                Simple, transparent meal plans.
            </p>
            <p class="max-w-xl mt-5 mx-auto text-xl text-gray-500 dark:text-gray-300">
                Save money and time by subscribing to a weekly or monthly plan.
            </p>
        </div>

        {{-- PLANS GRID --}}
        <div class="mt-16 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:gap-8">
            
            {{-- PLAN 1: BASIC --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm flex flex-col transition-colors duration-300">
                <div class="p-6 md:p-8">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white">Basic Eater</h3>
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Perfect for the occasional foodie.</p>
                    <div class="mt-8">
                        <span class="text-4xl font-extrabold text-gray-900 dark:text-white">₦0</span>
                        <span class="text-base font-medium text-gray-500 dark:text-gray-400">/mo</span>
                    </div>
                    <a href="{{ route('register') }}" class="mt-8 block w-full bg-red-50 dark:bg-gray-700 border border-red-200 dark:border-gray-600 rounded-md py-2 text-sm font-semibold text-red-700 dark:text-red-400 text-center hover:bg-red-100 dark:hover:bg-gray-600">
                        Sign up for free
                    </a>
                </div>
                <div class="pt-6 pb-8 px-6 md:px-8 bg-gray-50 dark:bg-gray-800/50 rounded-b-2xl border-t border-gray-100 dark:border-gray-700 flex-1">
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span class="text-sm text-gray-600 dark:text-gray-300">Access to all chefs</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span class="text-sm text-gray-600 dark:text-gray-300">Pay per order</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span class="text-sm text-gray-600 dark:text-gray-300">Standard delivery fees</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- PLAN 2: PRO (Highlighted) --}}
            <div class="bg-white dark:bg-gray-800 border-2 border-red-600 rounded-2xl shadow-lg flex flex-col relative transform scale-105 z-10 transition-colors duration-300">
                <div class="absolute top-0 right-0 -mr-1 -mt-1 w-24 h-24 overflow-hidden rounded-tr-2xl">
                    <div class="absolute transform rotate-45 bg-red-600 text-white text-xs font-bold py-1 right-[-35px] top-[32px] w-[170px] text-center">
                        POPULAR
                    </div>
                </div>
                <div class="p-6 md:p-8">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white">Chow Master</h3>
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">For the daily diner.</p>
                    <div class="mt-8">
                        <span class="text-4xl font-extrabold text-gray-900 dark:text-white">₦5,000</span>
                        <span class="text-base font-medium text-gray-500 dark:text-gray-400">/mo</span>
                    </div>
                    <a href="#" class="mt-8 block w-full bg-red-600 border border-transparent rounded-md py-2 text-sm font-semibold text-white text-center hover:bg-red-700 shadow-md">
                        Subscribe Now
                    </a>
                </div>
                <div class="pt-6 pb-8 px-6 md:px-8 bg-gray-50 dark:bg-gray-800/50 rounded-b-2xl border-t border-gray-100 dark:border-gray-700 flex-1">
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span class="text-sm text-gray-600 dark:text-gray-300">Free delivery on orders > ₦3000</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span class="text-sm text-gray-600 dark:text-gray-300">Priority order processing</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span class="text-sm text-gray-600 dark:text-gray-300">5% Discount on all meals</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- PLAN 3: FAMILY --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm flex flex-col transition-colors duration-300">
                <div class="p-6 md:p-8">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white">Family Plan</h3>
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Feeding the whole house.</p>
                    <div class="mt-8">
                        <span class="text-4xl font-extrabold text-gray-900 dark:text-white">₦15,000</span>
                        <span class="text-base font-medium text-gray-500 dark:text-gray-400">/mo</span>
                    </div>
                    <a href="#" class="mt-8 block w-full bg-red-50 dark:bg-gray-700 border border-red-200 dark:border-gray-600 rounded-md py-2 text-sm font-semibold text-red-700 dark:text-red-400 text-center hover:bg-red-100 dark:hover:bg-gray-600">
                        Contact Sales
                    </a>
                </div>
                <div class="pt-6 pb-8 px-6 md:px-8 bg-gray-50 dark:bg-gray-800/50 rounded-b-2xl border-t border-gray-100 dark:border-gray-700 flex-1">
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span class="text-sm text-gray-600 dark:text-gray-300">Bulk order discounts (10%)</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span class="text-sm text-gray-600 dark:text-gray-300">Dedicated Account Manager</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span class="text-sm text-gray-600 dark:text-gray-300">Custom meal scheduling</span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection