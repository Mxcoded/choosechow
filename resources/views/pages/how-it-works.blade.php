@extends('layouts.master')

@section('title', 'How It Works - ChooseChow')

@section('content')
<!-- Hero Section -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h1 class="text-5xl font-bold text-gray-900 mb-6">
            How <span class="gradient-text">ChooseChow</span> Works
        </h1>
        <p class="text-xl text-gray-600 mb-8">
            Connecting you with amazing home chefs is simple, transparent, and delicious. 
            Here's how our platform brings fresh, authentic meals right to your door.
        </p>
    </div>
</section>

<!-- Main Process Steps -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Simple Steps to Great Food</h2>
            <p class="text-xl text-gray-600">From discovery to delivery in three easy steps</p>
        </div>

        <div class="grid md:grid-cols-3 gap-12">
            <!-- Step 1 -->
            <div class="text-center fade-in">
                <div class="bg-red-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-8">
                    <span class="text-4xl">🔍</span>
                </div>
                <div class="bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center mx-auto mb-4 text-sm font-bold">1</div>
                <h3 class="text-2xl font-semibold mb-6">Discover Amazing Chefs</h3>
                <div class="text-left space-y-4">
                    <p class="text-gray-600">• Browse verified home chef profiles in your area</p>
                    <p class="text-gray-600">• View specialties, ratings, and customer reviews</p>
                    <p class="text-gray-600">• Check out sample menus and pricing</p>
                    <p class="text-gray-600">• Filter by cuisine type, dietary preferences, and location</p>
                    <p class="text-gray-600">• Read chef stories and cooking philosophies</p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="text-center fade-in">
                <div class="bg-blue-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-8">
                    <span class="text-4xl">🍽️</span>
                </div>
                <div class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mx-auto mb-4 text-sm font-bold">2</div>
                <h3 class="text-2xl font-semibold mb-6">Order Your Perfect Meal</h3>
                <div class="text-left space-y-4">
                    <p class="text-gray-600">• Choose from daily menus or request custom meals</p>
                    <p class="text-gray-600">• Set your preferred delivery time and location</p>
                    <p class="text-gray-600">• Specify dietary restrictions and preferences</p>
                    <p class="text-gray-600">• Add special instructions or requests</p>
                    <p class="text-gray-600">• Secure payment through our platform</p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="text-center fade-in">
                <div class="bg-green-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-8">
                    <span class="text-4xl">🚚</span>
                </div>
                <div class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mx-auto mb-4 text-sm font-bold">3</div>
                <h3 class="text-2xl font-semibold mb-6">Enjoy Fresh, Delicious Meals</h3>
                <div class="text-left space-y-4">
                    <p class="text-gray-600">• Receive freshly prepared meals at your chosen time</p>
                    <p class="text-gray-600">• Track your order in real-time</p>
                    <p class="text-gray-600">• Enjoy authentic, home-cooked flavors</p>
                    <p class="text-gray-600">• Rate your experience and leave feedback</p>
                    <p class="text-gray-600">• Reorder favorites or discover new chefs</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose ChooseChow -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Why Choose ChooseChow?</h2>
            <p class="text-xl text-gray-600">More than just food delivery - we're building a community</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Quality Assurance -->
            <div class="bg-gray-50 rounded-xl p-8 hover-scale">
                <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mb-6">
                    <span class="text-2xl">✅</span>
                </div>
                <h3 class="text-xl font-semibold mb-4">Quality Assurance</h3>
                <p class="text-gray-600">All our chefs are verified and rated by the community. We ensure consistent quality and food safety standards through regular reviews and feedback.</p>
            </div>

            <!-- Flexible Ordering -->
            <div class="bg-gray-50 rounded-xl p-8 hover-scale">
                <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mb-6">
                    <span class="text-2xl">🔄</span>
                </div>
                <h3 class="text-xl font-semibold mb-4">Flexible Ordering</h3>
                <p class="text-gray-600">Order individual meals when you want them, or subscribe to regular deliveries. Pause, modify, or cancel your subscription anytime with no penalties.</p>
            </div>

            <!-- Support Local Chefs -->
            <div class="bg-gray-50 rounded-xl p-8 hover-scale">
                <div class="bg-red-100 rounded-full w-16 h-16 flex items-center justify-center mb-6">
                    <span class="text-2xl">❤️</span>
                </div>
                <h3 class="text-xl font-semibold mb-4">Support Local Chefs</h3>
                <p class="text-gray-600">Help talented home chefs build their businesses while enjoying authentic, home-cooked meals. Every order supports local culinary entrepreneurs.</p>
            </div>

            <!-- Fresh & Authentic -->
            <div class="bg-gray-50 rounded-xl p-8 hover-scale">
                <div class="bg-yellow-100 rounded-full w-16 h-16 flex items-center justify-center mb-6">
                    <span class="text-2xl">🌟</span>
                </div>
                <h3 class="text-xl font-semibold mb-4">Fresh & Authentic</h3>
                <p class="text-gray-600">Every meal is prepared fresh to order using quality ingredients. Experience authentic flavors and traditional cooking methods from passionate home chefs.</p>
            </div>

            <!-- Easy & Convenient -->
            <div class="bg-gray-50 rounded-xl p-8 hover-scale">
                <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mb-6">
                    <span class="text-2xl">📱</span>
                </div>
                <h3 class="text-xl font-semibold mb-4">Easy & Convenient</h3>
                <p class="text-gray-600">Our user-friendly platform makes ordering simple. Browse, order, and track your meals with just a few clicks. Available on web and mobile.</p>
            </div>

            <!-- Affordable Pricing -->
            <div class="bg-gray-50 rounded-xl p-8 hover-scale">
                <div class="bg-orange-100 rounded-full w-16 h-16 flex items-center justify-center mb-6">
                    <span class="text-2xl">💰</span>
                </div>
                <h3 class="text-xl font-semibold mb-4">Affordable Pricing</h3>
                <p class="text-gray-600">Enjoy restaurant-quality meals at home-cooking prices. No hidden fees, transparent pricing, and great value for authentic, freshly prepared food.</p>
            </div>
        </div>
    </div>
</section>

<!-- For Customers & Chefs -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid lg:grid-cols-2 gap-16">
            <!-- For Customers -->
            <div class="fade-in">
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <div class="flex items-center mb-6">
                        <div class="bg-red-100 rounded-full w-16 h-16 flex items-center justify-center mr-4">
                            <span class="text-2xl">👥</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">For Food Lovers</h3>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start">
                            <span class="text-green-500 mr-3 mt-1">✓</span>
                            <span class="text-gray-600">Access to hundreds of verified home chefs</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-3 mt-1">✓</span>
                            <span class="text-gray-600">Fresh, authentic meals delivered to your door</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-3 mt-1">✓</span>
                            <span class="text-gray-600">Flexible ordering and subscription options</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-3 mt-1">✓</span>
                            <span class="text-gray-600">Discover new cuisines and flavors</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-3 mt-1">✓</span>
                            <span class="text-gray-600">Support local culinary entrepreneurs</span>
                        </li>
                    </ul>
                    <a href="{{ route('chefs.index') }}" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors inline-block">
                        Start Exploring Chefs
                    </a>
                </div>
            </div>

            <!-- For Chefs -->
            <div class="fade-in">
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <div class="flex items-center mb-6">
                        <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mr-4">
                            <span class="text-2xl">👨‍🍳</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">For Home Chefs</h3>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start">
                            <span class="text-green-500 mr-3 mt-1">✓</span>
                            <span class="text-gray-600">Turn your passion for cooking into income</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-3 mt-1">✓</span>
                            <span class="text-gray-600">Flexible schedule - cook when you want</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-3 mt-1">✓</span>
                            <span class="text-gray-600">Build your customer base and reputation</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-3 mt-1">✓</span>
                            <span class="text-gray-600">Set your own prices and menu</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-3 mt-1">✓</span>
                            <span class="text-gray-600">Marketing and platform support included</span>
                        </li>
                    </ul>
                    <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors inline-block">
                        Become a Chef
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-red-600 to-orange-600">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-4xl font-bold text-white mb-6">Ready to Get Started?</h2>
        <p class="text-xl text-red-100 mb-8">
            Join thousands of food lovers who have discovered the joy of authentic, home-cooked meals delivered fresh to their door.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('chefs.index') }}" class="bg-white text-red-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-colors">
                🔍 Find Chefs Near Me
            </a>
            <a href="{{ route('subscriptions.index') }}" class="bg-red-800 hover:bg-red-900 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors">
                📋 View Subscription Plans
            </a>
        </div>
    </div>
</section>
@endsection
