@extends('layouts.master')

@section('title', 'ChooseChow - Connect with Amazing Home Chefs')

@section('content')
<!-- Hero Section -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center fade-in">
            <h1 class="text-6xl font-bold text-gray-900 mb-6">
                Discover Amazing
                <span class="gradient-text">Home Chefs</span>
                Near You
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                Connect with talented home chefs in your area. Enjoy fresh, delicious, home-cooked meals 
                delivered right to your door. From traditional Nigerian dishes to international cuisine.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                <a href="{{ route('chefs.index') }}" class="bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors hover-scale">
                    <i class="fas fa-search"></i> Find Chefs Near Me
                </a>
                <a href="{{ route('subscriptions.plans') }}" class="bg-white hover:bg-gray-50 text-gray-700 px-8 py-4 rounded-lg text-lg font-semibold border-2 border-gray-200 transition-colors hover-scale">
                    <i class="fas fa-clipboard-list"></i> View Meal Plans
                </a>
            </div>
            
            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto">
                <div class="text-center">
                    <div class="text-3xl font-bold text-red-600 mb-2">500+</div>
                    <div class="text-gray-600">Verified Chefs</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-red-600 mb-2">10K+</div>
                    <div class="text-gray-600">Happy Customers</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-red-600 mb-2">25+</div>
                    <div class="text-gray-600">Cities Covered</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-red-600 mb-2">4.9★</div>
                    <div class="text-gray-600">Average Rating</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">How ChooseChow Works</h2>
            <p class="text-xl text-gray-600">Simple steps to delicious home-cooked meals</p>
        </div>

        <div class="grid md:grid-cols-3 gap-12">
            <div class="text-center fade-in hover-scale">
                <div class="bg-red-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6">
                    <span class="text-3xl"><i class="fas fa-search"></i></span>
                </div>
                <h3 class="text-2xl font-semibold mb-4">1. Discover Chefs</h3>
                <p class="text-gray-600 text-lg">Browse profiles of verified home chefs in your area. View their specialties, ratings, and sample menus.</p>
            </div>

            <div class="text-center fade-in hover-scale">
                <div class="bg-blue-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6">
                    <span class="text-3xl"><i class="fas fa-utensils"></i></span>
                </div>
                <h3 class="text-2xl font-semibold mb-4">2. Order Your Meal</h3>
                <p class="text-gray-600 text-lg">Choose from daily menus or request custom meals. Set your delivery time and dietary preferences.</p>
            </div>

            <div class="text-center fade-in hover-scale">
                <div class="bg-green-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6">
                    <span class="text-3xl"><i class="fas fa-truck"></i></span>
                </div>
                <h3 class="text-2xl font-semibold mb-4">3. Enjoy Fresh Meals</h3>
                <p class="text-gray-600 text-lg">Receive freshly prepared meals delivered to your door. Rate your experience and discover new favorites.</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Chefs -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Featured Home Chefs</h2>
            <p class="text-xl text-gray-600">Meet some of our top-rated culinary artists</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover-scale">
                <div class="h-48 bg-gradient-to-br from-red-400 to-orange-400 flex items-center justify-center">
                    <span class="text-6xl"><i class="fas fa-chef"></i></span>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-2">Chef Amina</h3>
                    <p class="text-gray-600 mb-3">Specializes in authentic Nigerian cuisine and West African dishes</p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-yellow-400">⭐⭐⭐⭐⭐</span>
                            <span class="text-gray-600 ml-2">4.9 (127 reviews)</span>
                        </div>
                        <span class="text-red-600 font-semibold">₦2,500+</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover-scale">
                <div class="h-48 bg-gradient-to-br from-blue-400 to-purple-400 flex items-center justify-center">
                    <span class="text-6xl"><i class="fas fa-chef"></i></span>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-2">Chef David</h3>
                    <p class="text-gray-600 mb-3">International cuisine expert with Italian and Asian specialties</p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-yellow-400">⭐⭐⭐⭐⭐</span>
                            <span class="text-gray-600 ml-2">4.8 (89 reviews)</span>
                        </div>
                        <span class="text-red-600 font-semibold">₦3,000+</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover-scale">
                <div class="h-48 bg-gradient-to-br from-green-400 to-teal-400 flex items-center justify-center">
                    <span class="text-6xl"><i class="fas fa-chef"></i></span>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-2">Chef Fatima</h3>
                    <p class="text-gray-600 mb-3">Healthy meal specialist focusing on organic and vegan options</p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-yellow-400">⭐⭐⭐⭐⭐</span>
                            <span class="text-gray-600 ml-2">5.0 (156 reviews)</span>
                        </div>
                        <span class="text-red-600 font-semibold">₦2,800+</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('chefs.index') }}" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors">
                View All Chefs
            </a>
        </div>
    </div>
</section>

<!-- Popular Cuisines -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Popular Cuisines</h2>
            <p class="text-xl text-gray-600">Explore diverse flavors from around the world</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-red-500 to-orange-500 rounded-xl p-6 text-white text-center hover-scale cursor-pointer">
                <div class="text-4xl mb-3">🍛</div>
                <h3 class="text-lg font-semibold">Nigerian</h3>
                <p class="text-sm opacity-90">Jollof, Egusi, Suya</p>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-teal-500 rounded-xl p-6 text-white text-center hover-scale cursor-pointer">
                <div class="text-4xl mb-3"><i class="fas fa-pizza-slice"></i></div>
                <h3 class="text-lg font-semibold">Italian</h3>
                <p class="text-sm opacity-90">Pasta, Pizza, Risotto</p>
            </div>

            <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl p-6 text-white text-center hover-scale cursor-pointer">
                <div class="text-4xl mb-3">🍜</div>
                <h3 class="text-lg font-semibold">Asian</h3>
                <p class="text-sm opacity-90">Stir-fry, Ramen, Curry</p>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl p-6 text-white text-center hover-scale cursor-pointer">
                <div class="text-4xl mb-3"><i class="fas fa-carrot"></i></div>
                <h3 class="text-lg font-semibold">Healthy</h3>
                <p class="text-sm opacity-90">Salads, Smoothies, Vegan</p>
            </div>
        </div>
    </div>
</section>

<!-- Subscription Plans Preview -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Meal Subscription Plans</h2>
            <p class="text-xl text-gray-600">Regular deliveries of your favorite meals</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg p-8 text-center hover-scale">
                <div class="bg-gray-100 text-gray-600 px-4 py-2 rounded-full text-sm font-medium mb-6 inline-block">
                    Basic Plan
                </div>
                <h3 class="text-2xl font-bold mb-4">₦8,000<span class="text-lg text-gray-600">/week</span></h3>
                <ul class="text-left space-y-3 mb-8">
                    <li class="flex items-center"><span class="text-green-500 mr-3">✓</span>3 meals per week</li>
                    <li class="flex items-center"><span class="text-green-500 mr-3">✓</span>Standard variety</li>
                    <li class="flex items-center"><span class="text-green-500 mr-3">✓</span>Regular delivery</li>
                </ul>
                <button class="w-full bg-gray-600 hover:bg-gray-700 text-white py-3 rounded-lg font-semibold transition-colors">
                    Choose Basic
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-8 text-center border-2 border-red-200 hover-scale">
                <div class="bg-red-100 text-red-600 px-4 py-2 rounded-full text-sm font-medium mb-6 inline-block">
                    Most Popular
                </div>
                <h3 class="text-2xl font-bold mb-4">₦21,000<span class="text-lg text-gray-600">/week</span></h3>
                <ul class="text-left space-y-3 mb-8">
                    <li class="flex items-center"><span class="text-green-500 mr-3">✓</span>5 meals per week</li>
                    <li class="flex items-center"><span class="text-green-500 mr-3">✓</span>Premium variety</li>
                    <li class="flex items-center"><span class="text-green-500 mr-3">✓</span>Priority delivery</li>
                    <li class="flex items-center"><span class="text-green-500 mr-3">✓</span>Meal customization</li>
                </ul>
                <button class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-semibold transition-colors">
                    Choose Premium
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-8 text-center hover-scale">
                <div class="bg-yellow-100 text-yellow-600 px-4 py-2 rounded-full text-sm font-medium mb-6 inline-block">
                    Best Value
                </div>
                <h3 class="text-2xl font-bold mb-4">₦30,000<span class="text-lg text-gray-600">/week</span></h3>
                <ul class="text-left space-y-3 mb-8">
                    <li class="flex items-center"><span class="text-green-500 mr-3">✓</span>7 meals per week</li>
                    <li class="flex items-center"><span class="text-green-500 mr-3">✓</span>Gourmet selection</li>
                    <li class="flex items-center"><span class="text-green-500 mr-3">✓</span>Express delivery</li>
                    <li class="flex items-center"><span class="text-green-500 mr-3">✓</span>Chef consultation</li>
                </ul>
                <button class="w-full bg-yellow-600 hover:bg-yellow-700 text-white py-3 rounded-lg font-semibold transition-colors">
                    Choose Deluxe
                </button>
            </div>
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('subscriptions.index') }}" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors">
                View All Plans
            </a>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">What Our Customers Say</h2>
            <p class="text-xl text-gray-600">Real reviews from satisfied food lovers</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-gray-50 rounded-xl p-8 hover-scale">
                <div class="flex items-center mb-4">
                    <span class="text-yellow-400 text-xl">⭐⭐⭐⭐⭐</span>
                </div>
                <p class="text-gray-700 mb-6">"ChooseChow has completely changed how I eat! The quality is amazing and the convenience is unmatched. Chef Amina's jollof rice is the best I've ever had!"</p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-200 rounded-full flex items-center justify-center mr-4">
                        <span class="text-xl"><i class="fas fa-user"></i></span>
                    </div>
                    <div>
                        <div class="font-semibold">Sarah Johnson</div>
                        <div class="text-gray-600 text-sm">Lagos, Nigeria</div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-8 hover-scale">
                <div class="flex items-center mb-4">
                    <span class="text-yellow-400 text-xl">⭐⭐⭐⭐⭐</span>
                </div>
                <p class="text-gray-700 mb-6">"As a busy professional, ChooseChow saves me so much time. The meal subscription is perfect - fresh, delicious food delivered right to my office!"</p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-200 rounded-full flex items-center justify-center mr-4">
                        <span class="text-xl"><i class="fas fa-user"></i></span>
                    </div>
                    <div>
                        <div class="font-semibold">Michael Okafor</div>
                        <div class="text-gray-600 text-sm">Abuja, Nigeria</div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-8 hover-scale">
                <div class="flex items-center mb-4">
                    <span class="text-yellow-400 text-xl">⭐⭐⭐⭐⭐</span>
                </div>
                <p class="text-gray-700 mb-6">"The variety of cuisines available is incredible! I've discovered so many new favorite dishes. The chefs are truly talented and passionate about their craft."</p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-200 rounded-full flex items-center justify-center mr-4">
                        <span class="text-xl"><i class="fas fa-user"></i></span>
                    </div>
                    <div>
                        <div class="font-semibold">Kemi Adebayo</div>
                        <div class="text-gray-600 text-sm">Port Harcourt, Nigeria</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-red-600 to-orange-600">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-4xl font-bold text-white mb-6">Ready to Start Your Food Journey?</h2>
        <p class="text-xl text-red-100 mb-8">Join thousands of food lovers discovering amazing home chefs in their area</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('chefs.index') }}" class="bg-white text-red-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-colors">
                <i class="fas fa-search"></i> Find Chefs Near Me
            </a>
            <a href="{{ route('subscriptions.index') }}" class="bg-red-800 hover:bg-red-900 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors">
                <i class="fas fa-clipboard-list"></i> Start Meal Subscription
            </a>
        </div>
    </div>
</section>
@endsection
