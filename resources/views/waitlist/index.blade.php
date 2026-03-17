@extends('layouts.app')

@section('title', 'Join the Waitlist - ChooseChow')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-chow-cream-50 to-chow-cream-100 dark:from-dark-base dark:to-dark-section">
    
    {{-- Hero Section --}}
    <div class="relative overflow-hidden">
        {{-- Background Pattern --}}
        <div class="absolute inset-0 opacity-5 dark:opacity-[0.02] bg-[url('https://www.transparenttextures.com/patterns/food.png')]"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32 relative z-10">
            <div class="text-center">
                {{-- Badge --}}
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-accent/10 dark:bg-accent/20 text-accent font-semibold text-sm mb-6">
                    <i class="fas fa-rocket mr-2"></i> Coming Soon...
                </div>
                
                {{-- Headline --}}
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-chow-brown-800 dark:text-content-primary leading-tight mb-6">
                    Homemade meals from<br>
                    <span class="text-appetizing">local kitchens</span> to your door
                </h1>
                
                {{-- Subheadline --}}
                <p class="text-xl text-chow-brown-600 dark:text-content-secondary max-w-2xl mx-auto mb-10">
                    ChooseChow connects you with talented home chefs, food trucks, and local vendors in your neighborhood. 
                    Join the waitlist to be first in line when we launch!
                </p>
                
                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('waitlist.create') }}" class="inline-flex items-center justify-center px-8 py-4 text-lg font-bold rounded-xl text-white bg-accent hover:bg-accent-hover transition-all shadow-lg shadow-accent/30 hover:shadow-accent/50 hover:-translate-y-1">
                        <i class="fas fa-utensils mr-2"></i> I Want to Eat
                    </a>
                    <a href="{{ route('waitlist.create') }}?role=vendor" class="inline-flex items-center justify-center px-8 py-4 text-lg font-bold rounded-xl text-accent bg-white dark:bg-dark-card border-2 border-accent hover:bg-accent hover:text-white transition-all">
                        <i class="fas fa-store mr-2"></i> I Want to Sell
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Section --}}
    <div class="bg-white dark:bg-dark-section py-12 border-y border-chow-cream-200 dark:border-dark-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-3xl font-bold text-accent">{{ number_format($stats['total_signups']) }}+</div>
                    <div class="text-sm text-chow-brown-500 dark:text-content-secondary uppercase tracking-wide mt-1">People Waiting</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-accent-light">{{ number_format($stats['food_lovers']) }}</div>
                    <div class="text-sm text-chow-brown-500 dark:text-content-secondary uppercase tracking-wide mt-1">Food Lovers</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-chow-fresh-500">{{ number_format($stats['vendors']) }}</div>
                    <div class="text-sm text-chow-brown-500 dark:text-content-secondary uppercase tracking-wide mt-1">Vendors</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-chow-gold-500">{{ $stats['neighborhoods'] }}</div>
                    <div class="text-sm text-chow-brown-500 dark:text-content-secondary uppercase tracking-wide mt-1">Neighborhoods</div>
                </div>
            </div>
        </div>
    </div>

    {{-- How It Works --}}
    <div class="py-20 bg-chow-cream-50 dark:bg-dark-base">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-chow-brown-800 dark:text-content-primary mb-4">How ChooseChow Works</h2>
                <p class="text-lg text-chow-brown-600 dark:text-content-secondary">Fresh, homemade meals in 3 simple steps</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                {{-- Step 1 --}}
                <div class="bg-white dark:bg-dark-card rounded-2xl p-8 text-center shadow-sm border border-chow-cream-200 dark:border-dark-border">
                    <div class="w-16 h-16 mx-auto bg-accent/10 dark:bg-accent/20 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-search text-2xl text-accent"></i>
                    </div>
                    <h3 class="text-xl font-bold text-chow-brown-800 dark:text-content-primary mb-3">Discover Local Chefs</h3>
                    <p class="text-chow-brown-600 dark:text-content-secondary">Browse verified home chefs, food trucks, and markets in your neighborhood.</p>
                </div>
                
                {{-- Step 2 --}}
                <div class="bg-white dark:bg-dark-card rounded-2xl p-8 text-center shadow-sm border border-chow-cream-200 dark:border-dark-border">
                    <div class="w-16 h-16 mx-auto bg-accent-light/10 dark:bg-accent-light/20 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-shopping-bag text-2xl text-accent-light"></i>
                    </div>
                    <h3 class="text-xl font-bold text-chow-brown-800 dark:text-content-primary mb-3">Order Fresh Meals</h3>
                    <p class="text-chow-brown-600 dark:text-content-secondary">Choose from diverse menus cooked fresh when you order. No factories, just love.</p>
                </div>
                
                {{-- Step 3 --}}
                <div class="bg-white dark:bg-dark-card rounded-2xl p-8 text-center shadow-sm border border-chow-cream-200 dark:border-dark-border">
                    <div class="w-16 h-16 mx-auto bg-chow-fresh-100 dark:bg-chow-fresh-500/20 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-motorcycle text-2xl text-chow-fresh-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-chow-brown-800 dark:text-content-primary mb-3">Enjoy at Home</h3>
                    <p class="text-chow-brown-600 dark:text-content-secondary">Get your food delivered hot to your doorstep. Pay securely with Paystack.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- For Vendors Section --}}
    <div class="py-20 bg-white dark:bg-dark-section">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-chow-fresh-100 dark:bg-chow-fresh-500/20 text-chow-fresh-600 font-semibold text-sm mb-4">
                        <i class="fas fa-chart-line mr-2"></i> For Vendors
                    </div>
                    <h2 class="text-3xl font-bold text-chow-brown-800 dark:text-content-primary mb-6">Turn Your Kitchen Into a Business</h2>
                    <p class="text-lg text-chow-brown-600 dark:text-content-secondary mb-8">
                        Whether you're a home chef, food truck owner, or market vendor — ChooseChow gives you the tools to reach hungry customers in your area.
                    </p>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-chow-fresh-500 mt-1 mr-3"></i>
                            <span class="text-chow-brown-700 dark:text-content-secondary">No upfront fees — we only earn when you earn</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-chow-fresh-500 mt-1 mr-3"></i>
                            <span class="text-chow-brown-700 dark:text-content-secondary">Free menu management and order tracking</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-chow-fresh-500 mt-1 mr-3"></i>
                            <span class="text-chow-brown-700 dark:text-content-secondary">Weekly payouts directly to your bank</span>
                        </li>
                    </ul>
                    
                    <a href="{{ route('waitlist.create') }}?role=vendor" class="inline-flex items-center px-6 py-3 font-bold rounded-xl text-white bg-chow-fresh-600 hover:bg-chow-fresh-700 transition-all">
                        Join as a Vendor <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                
                <div class="relative">
                    <div class="bg-gradient-to-br from-accent/10 to-accent-light/10 dark:from-accent/20 dark:to-accent-light/20 rounded-3xl p-8 lg:p-12">
                        <img src="https://images.unsplash.com/photo-1600565193348-f74bd3c7ccdf?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Home Chef cooking" class="rounded-2xl shadow-xl w-full">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Final CTA --}}
    <div class="py-20 bg-gradient-to-r from-accent via-accent to-accent-light relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/food.png')]"></div>
        <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
            <h2 class="text-3xl font-bold text-white mb-6">Ready to Experience Real Homemade Food?</h2>
            <p class="text-lg text-white/80 mb-8 max-w-2xl mx-auto">
                Join thousands of Lagosians waiting for the future of food delivery. Be the first to know when we launch in your area!
            </p>
            <a href="{{ route('waitlist.create') }}" class="inline-flex items-center justify-center px-8 py-4 text-lg font-bold rounded-xl text-accent bg-white hover:bg-gray-100 transition-all shadow-lg hover:-translate-y-1">
                <i class="fas fa-bell mr-2"></i> Join the Waitlist
            </a>
        </div>
    </div>
</div>
@endsection
