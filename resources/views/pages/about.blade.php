@extends('layouts.app')

@section('title', 'About Us - ChooseChow')

@section('content')

{{-- 1. HERO SECTION --}}
<div class="relative bg-white dark:bg-gray-900 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center relative z-10">
        <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl md:text-6xl">
            We are <span class="text-red-600">ChooseChow</span>
        </h1>
        <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500 dark:text-gray-300">
            Connecting food lovers with talented home chefs, creating a community where authentic flavors meet modern convenience.
        </p>
    </div>
    
    {{-- Background decoration --}}
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-0 opacity-10">
        <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="absolute w-full h-full text-red-50 dark:text-gray-800 fill-current">
            <path d="M0 100 C 20 0 50 0 100 100 Z"></path>
        </svg>
    </div>
</div>

{{-- 2. OUR STORY & STATS --}}
<section class="py-16 bg-gray-50 dark:bg-gray-800 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            
            {{-- Text Content --}}
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Our Story</h2>
                <div class="space-y-4 text-lg text-gray-600 dark:text-gray-300">
                    <p>
                        ChooseChow was born from a simple observation: some of the most incredible meals come from passionate home cooks who pour their heart into every dish. Yet, these talented chefs often remained hidden gems in their communities.
                    </p>
                    <p>
                        Founded in 2024 in Abuja, Nigeria, we set out to bridge this gap. We wanted to create a platform where food lovers could discover authentic, home-cooked meals while supporting local culinary entrepreneurs.
                    </p>
                    <p>
                        Today, we're proud to connect verified home chefs with satisfied customers across Nigeria, fostering a vibrant community built around the love of great food.
                    </p>
                </div>
            </div>

            {{-- Dynamic Stats Card --}}
            <div class="bg-white dark:bg-gray-700 rounded-2xl shadow-xl p-8 transform hover:-translate-y-1 transition-transform duration-300">
                <div class="text-center mb-8">
                    <div class="inline-block p-3 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 mb-2">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Our Impact</h3>
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-600 rounded-lg">
                        <div class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $stats['verified_chefs'] }}+</div>
                        <div class="text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wide mt-1">Home Chefs</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-600 rounded-lg">
                        <div class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $stats['happy_customers'] }}+</div>
                        <div class="text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wide mt-1">Happy Eaters</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-600 rounded-lg">
                        <div class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $stats['cities_covered'] }}</div>
                        <div class="text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wide mt-1">Cities</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-600 rounded-lg">
                        <div class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $stats['average_rating'] }}</div>
                        <div class="text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wide mt-1">Avg Rating</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 3. MISSION & VISION --}}
<section class="py-20 bg-white dark:bg-gray-900 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-8">
            {{-- Mission --}}
            <div class="bg-red-50 dark:bg-gray-800 rounded-2xl p-8 border border-red-100 dark:border-gray-700">
                <div class="w-14 h-14 bg-red-100 dark:bg-red-900/50 rounded-xl flex items-center justify-center mb-6">
                    <i class="fas fa-bullseye text-2xl text-red-600 dark:text-red-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Our Mission</h3>
                <p class="text-lg text-gray-600 dark:text-gray-300 leading-relaxed">
                    To democratize access to authentic homemade food by empowering home chefs with the tools they need to succeed, while offering consumers healthy, diverse, and affordable meal options.
                </p>
            </div>

            {{-- Vision --}}
            <div class="bg-blue-50 dark:bg-gray-800 rounded-2xl p-8 border border-blue-100 dark:border-gray-700">
                <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center mb-6">
                    <i class="fas fa-eye text-2xl text-blue-600 dark:text-blue-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Our Vision</h3>
                <p class="text-lg text-gray-600 dark:text-gray-300 leading-relaxed">
                    To become the leading culinary marketplace in Africa, fostering a vibrant ecosystem where culture is celebrated through food and every home kitchen can become a thriving business.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- 4. CORE VALUES --}}
<section class="py-20 bg-gray-50 dark:bg-gray-800 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Our Core Values</h2>
            <p class="mt-2 text-xl text-gray-500 dark:text-gray-400">The principles that guide every order.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-white dark:bg-gray-700 p-6 rounded-xl shadow-sm text-center">
                <div class="w-16 h-16 mx-auto bg-yellow-100 dark:bg-yellow-900/50 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-star text-2xl text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Quality First</h3>
                <p class="text-sm text-gray-500 dark:text-gray-300">
                    Strict verification for every chef. We only deliver food we would eat ourselves.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-700 p-6 rounded-xl shadow-sm text-center">
                <div class="w-16 h-16 mx-auto bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-users text-2xl text-green-600 dark:text-green-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Community</h3>
                <p class="text-sm text-gray-500 dark:text-gray-300">
                    We build bridges between neighbors through the universal language of food.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-700 p-6 rounded-xl shadow-sm text-center">
                <div class="w-16 h-16 mx-auto bg-purple-100 dark:bg-purple-900/50 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-fingerprint text-2xl text-purple-600 dark:text-purple-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Authenticity</h3>
                <p class="text-sm text-gray-500 dark:text-gray-300">
                    Real food, real people. No factories, just genuine home cooking.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-700 p-6 rounded-xl shadow-sm text-center">
                <div class="w-16 h-16 mx-auto bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-rocket text-2xl text-blue-600 dark:text-blue-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Innovation</h3>
                <p class="text-sm text-gray-500 dark:text-gray-300">
                    Leveraging technology to make home food delivery seamless and reliable.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- 5. TEAM SECTION --}}
<section class="py-20 bg-white dark:bg-gray-900 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Meet The Team</h2>
            <p class="mt-2 text-xl text-gray-500 dark:text-gray-400">The passionate people behind ChooseChow.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-12 max-w-5xl mx-auto">
            <div class="text-center group">
                <div class="w-32 h-32 mx-auto bg-gray-200 dark:bg-gray-700 rounded-full mb-6 overflow-hidden border-4 border-white dark:border-gray-800 shadow-lg group-hover:scale-105 transition-transform">
                    {{-- Placeholder Icon since no real images yet --}}
                    <div class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-gray-800">
                        <i class="fas fa-user-tie text-4xl text-gray-400"></i>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Oluwaseyi Daniel</h3>
                <p class="text-red-600 font-medium mb-3">CEO & Co-Founder</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 px-4">
                    Tech veteran passionate about empowering local entrepreneurs.
                </p>
            </div>

            <div class="text-center group">
                <div class="w-32 h-32 mx-auto bg-gray-200 dark:bg-gray-700 rounded-full mb-6 overflow-hidden border-4 border-white dark:border-gray-800 shadow-lg group-hover:scale-105 transition-transform">
                    <div class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-gray-800">
                        <i class="fas fa-utensils text-4xl text-gray-400"></i>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Fatima Abdullahi</h3>
                <p class="text-red-600 font-medium mb-3">Head of Chef Relations</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 px-4">
                    Experienced culinary consultant ensuring quality standards.
                </p>
            </div>

            <div class="text-center group">
                <div class="w-32 h-32 mx-auto bg-gray-200 dark:bg-gray-700 rounded-full mb-6 overflow-hidden border-4 border-white dark:border-gray-800 shadow-lg group-hover:scale-105 transition-transform">
                    <div class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-gray-800">
                        <i class="fas fa-code text-4xl text-gray-400"></i>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Chidi Okonkwo</h3>
                <p class="text-red-600 font-medium mb-3">Head of Technology</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 px-4">
                    Building a seamless and reliable platform for our users.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- 6. CTA / CONTACT --}}
<section class="py-20 bg-red-700 dark:bg-red-900 text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/food.png')]"></div>
    <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
        <h2 class="text-3xl font-bold mb-6">Have Questions? We'd Love to Hear From You</h2>
        <p class="text-lg text-red-100 mb-8 max-w-2xl mx-auto">
            Our team is always here to help. Whether you have questions about our platform, need support, or want to share feedback.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('contact') }}" class="bg-white text-red-600 px-8 py-3 rounded-full text-lg font-bold hover:bg-gray-100 transition-colors shadow-lg">
                <i class="fas fa-envelope mr-2"></i> Contact Us
            </a>
            <a href="{{ route('chef.index') }}" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-full text-lg font-bold hover:bg-white hover:text-red-600 transition-colors">
                Browse Chefs
            </a>
        </div>
    </div>
</section>

@endsection