@extends('layouts.master')

@section('title', 'Find Amazing Home Chefs - ChooseChow')

@section('content')
<!-- Hero Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="text-5xl font-bold text-gray-900 mb-6">
                Find Amazing <span class="gradient-text">Home Chefs</span>
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                Discover talented home chefs in your area. Browse profiles, read reviews, 
                and order fresh, authentic meals delivered right to your door.
            </p>
        </div>

        <!-- Search and Filters -->
        <div class="bg-gray-50 rounded-2xl p-8 mb-12">
            <div class="grid lg:grid-cols-4 gap-6">
                <!-- Location Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">📍 Location</label>
                    <input type="text" placeholder="Enter your area..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>

                <!-- Cuisine Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">🍽️ Cuisine Type</label>
                    <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option>All Cuisines</option>
                        <option>Nigerian</option>
                        <option>Italian</option>
                        <option>Asian</option>
                        <option>Healthy/Vegan</option>
                        <option>International</option>
                    </select>
                </div>

                <!-- Price Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">💰 Price Range</label>
                    <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option>Any Price</option>
                        <option>₦1,000 - ₦2,500</option>
                        <option>₦2,500 - ₦4,000</option>
                        <option>₦4,000 - ₦6,000</option>
                        <option>₦6,000+</option>
                    </select>
                </div>

                <!-- Search Button -->
                <div class="flex items-end">
                    <button class="w-full bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                        🔍 Search Chefs
                    </button>
                </div>
            </div>

            <!-- Quick Filters -->
            <div class="flex flex-wrap gap-3 mt-6">
                <span class="text-sm text-gray-600">Quick filters:</span>
                <button class="px-4 py-2 bg-white border border-gray-300 rounded-full text-sm hover:bg-red-50 hover:border-red-300 transition-colors">
                    ⭐ Top Rated
                </button>
                <button class="px-4 py-2 bg-white border border-gray-300 rounded-full text-sm hover:bg-red-50 hover:border-red-300 transition-colors">
                    🚚 Fast Delivery
                </button>
                <button class="px-4 py-2 bg-white border border-gray-300 rounded-full text-sm hover:bg-red-50 hover:border-red-300 transition-colors">
                    🌱 Healthy Options
                </button>
                <button class="px-4 py-2 bg-white border border-gray-300 rounded-full text-sm hover:bg-red-50 hover:border-red-300 transition-colors">
                    💎 Premium Chefs
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Results Summary -->
<section class="pb-8">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center">
            <p class="text-gray-600">
                <span class="font-semibold text-gray-900">127 chefs</span> found in your area
            </p>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">Sort by:</span>
                <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option>Highest Rated</option>
                    <option>Nearest to You</option>
                    <option>Price: Low to High</option>
                    <option>Price: High to Low</option>
                    <option>Most Reviews</option>
                </select>
            </div>
        </div>
    </div>
</section>

<!-- Chef Listings -->
<section class="pb-20">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid lg:grid-cols-2 xl:grid-cols-3 gap-8">
            
            <!-- Chef Card 1 -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover-scale">
                <div class="h-48 bg-gradient-to-br from-red-400 to-orange-400 flex items-center justify-center relative">
                    <span class="text-6xl">👩‍🍳</span>
                    <div class="absolute top-4 right-4 bg-white px-3 py-1 rounded-full text-sm font-semibold text-red-600">
                        ⭐ 4.9
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-xl font-semibold">Chef Amina Bello</h3>
                        <span class="text-green-600 text-sm font-medium">🟢 Available</span>
                    </div>
                    <p class="text-gray-600 mb-3">Authentic Nigerian cuisine specialist. Famous for jollof rice, egusi soup, and traditional West African dishes.</p>
                    
                    <!-- Specialties -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs">Nigerian</span>
                        <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs">Spicy</span>
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs">Traditional</span>
                    </div>

                    <!-- Stats -->
                    <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                        <span>📍 Ikeja, Lagos</span>
                        <span>👥 127 reviews</span>
                        <span>🚚 30-45 min</span>
                    </div>

                    <!-- Price and Action -->
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-lg font-bold text-gray-900">₦2,500</span>
                            <span class="text-gray-600 text-sm">starting price</span>
                        </div>
                        <button class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                            View Menu
                        </button>
                    </div>
                </div>
            </div>

            <!-- Chef Card 2 -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover-scale">
                <div class="h-48 bg-gradient-to-br from-blue-400 to-purple-400 flex items-center justify-center relative">
                    <span class="text-6xl">👨‍🍳</span>
                    <div class="absolute top-4 right-4 bg-white px-3 py-1 rounded-full text-sm font-semibold text-red-600">
                        ⭐ 4.8
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-xl font-semibold">Chef David Okafor</h3>
                        <span class="text-yellow-600 text-sm font-medium">🟡 Busy</span>
                    </div>
                    <p class="text-gray-600 mb-3">International cuisine expert specializing in Italian pasta, Asian stir-fries, and fusion dishes with local ingredients.</p>
                    
                    <!-- Specialties -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">Italian</span>
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs">Asian</span>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">Fusion</span>
                    </div>

                    <!-- Stats -->
                    <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                        <span>📍 Victoria Island</span>
                        <span>👥 89 reviews</span>
                        <span>🚚 45-60 min</span>
                    </div>

                    <!-- Price and Action -->
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-lg font-bold text-gray-900">₦3,200</span>
                            <span class="text-gray-600 text-sm">starting price</span>
                        </div>
                        <button class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                            View Menu
                        </button>
                    </div>
                </div>
            </div>

            <!-- Chef Card 3 -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover-scale">
                <div class="h-48 bg-gradient-to-br from-green-400 to-teal-400 flex items-center justify-center relative">
                    <span class="text-6xl">👩‍🍳</span>
                    <div class="absolute top-4 right-4 bg-white px-3 py-1 rounded-full text-sm font-semibold text-red-600">
                        ⭐ 5.0
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-xl font-semibold">Chef Fatima Yusuf</h3>
                        <span class="text-green-600 text-sm font-medium">🟢 Available</span>
                    </div>
                    <p class="text-gray-600 mb-3">Healthy meal specialist focusing on organic ingredients, vegan options, and nutritious meal prep for busy professionals.</p>
                    
                    <!-- Specialties -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs">Healthy</span>
                        <span class="px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-xs">Vegan</span>
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">Organic</span>
                    </div>

                    <!-- Stats -->
                    <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                        <span>📍 Lekki, Lagos</span>
                        <span>👥 156 reviews</span>
                        <span>🚚 25-40 min</span>
                    </div>

                    <!-- Price and Action -->
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-lg font-bold text-gray-900">₦2,800</span>
                            <span class="text-gray-600 text-sm">starting price</span>
                        </div>
                        <button class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                            View Menu
                        </button>
                    </div>
                </div>
            </div>

            <!-- Chef Card 4 -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover-scale">
                <div class="h-48 bg-gradient-to-br from-yellow-400 to-orange-400 flex items-center justify-center relative">
                    <span class="text-6xl">👨‍🍳</span>
                    <div class="absolute top-4 right-4 bg-white px-3 py-1 rounded-full text-sm font-semibold text-red-600">
                        ⭐ 4.7
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-xl font-semibold">Chef Emmanuel Eze</h3>
                        <span class="text-green-600 text-sm font-medium">🟢 Available</span>
                    </div>
                    <p class="text-gray-600 mb-3">Gourmet chef specializing in elevated Nigerian cuisine and continental dishes. Perfect for special occasions and fine dining.</p>
                    
                    <!-- Specialties -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">Gourmet</span>
                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs">Nigerian</span>
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs">Continental</span>
                    </div>

                    <!-- Stats -->
                    <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                        <span>📍 Abuja FCT</span>
                        <span>👥 73 reviews</span>
                        <span>🚚 40-55 min</span>
                    </div>

                    <!-- Price and Action -->
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-lg font-bold text-gray-900">₦4,500</span>
                            <span class="text-gray-600 text-sm">starting price</span>
                        </div>
                        <button class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                            View Menu
                        </button>
                    </div>
                </div>
            </div>

            <!-- Chef Card 5 -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover-scale">
                <div class="h-48 bg-gradient-to-br from-pink-400 to-red-400 flex items-center justify-center relative">
                    <span class="text-6xl">👩‍🍳</span>
                    <div class="absolute top-4 right-4 bg-white px-3 py-1 rounded-full text-sm font-semibold text-red-600">
                        ⭐ 4.6
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-xl font-semibold">Chef Kemi Adebayo</h3>
                        <span class="text-green-600 text-sm font-medium">🟢 Available</span>
                    </div>
                    <p class="text-gray-600 mb-3">Home-style cooking expert known for comfort foods, family recipes, and traditional Nigerian swallow dishes with authentic soups.</p>
                    
                    <!-- Specialties -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-3 py-1 bg-pink-100 text-pink-700 rounded-full text-xs">Comfort Food</span>
                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs">Traditional</span>
                        <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs">Soups</span>
                    </div>

                    <!-- Stats -->
                    <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                        <span>📍 Ibadan, Oyo</span>
                        <span>👥 94 reviews</span>
                        <span>🚚 35-50 min</span>
                    </div>

                    <!-- Price and Action -->
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-lg font-bold text-gray-900">₦2,200</span>
                            <span class="text-gray-600 text-sm">starting price</span>
                        </div>
                        <button class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                            View Menu
                        </button>
                    </div>
                </div>
            </div>

            <!-- Chef Card 6 -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover-scale">
                <div class="h-48 bg-gradient-to-br from-indigo-400 to-blue-400 flex items-center justify-center relative">
                    <span class="text-6xl">👨‍🍳</span>
                    <div class="absolute top-4 right-4 bg-white px-3 py-1 rounded-full text-sm font-semibold text-red-600">
                        ⭐ 4.9
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-xl font-semibold">Chef Ahmed Hassan</h3>
                        <span class="text-yellow-600 text-sm font-medium">🟡 Busy</span>
                    </div>
                    <p class="text-gray-600 mb-3">Middle Eastern and Mediterranean cuisine specialist. Expert in grilled meats, rice dishes, and aromatic spice blends.</p>
                    
                    <!-- Specialties -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs">Middle Eastern</span>
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">Grilled</span>
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">Spiced</span>
                    </div>

                    <!-- Stats -->
                    <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                        <span>📍 Kano, Kano</span>
                        <span>👥 112 reviews</span>
                        <span>🚚 30-45 min</span>
                    </div>

                    <!-- Price and Action -->
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-lg font-bold text-gray-900">₦3,000</span>
                            <span class="text-gray-600 text-sm">starting price</span>
                        </div>
                        <button class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                            View Menu
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <!-- Load More -->
        <div class="text-center mt-12">
            <button class="bg-white hover:bg-gray-50 text-gray-700 px-8 py-3 rounded-lg font-semibold border-2 border-gray-200 transition-colors">
                Load More Chefs
            </button>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-red-600 to-orange-600">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-4xl font-bold text-white mb-6">Can't Find What You're Looking For?</h2>
        <p class="text-xl text-red-100 mb-8">
            Let us know what you're craving and we'll help you find the perfect chef for your needs.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button class="bg-white text-red-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-colors">
                📝 Request Custom Meal
            </button>
            <a href="{{ route('contact') }}" class="bg-red-800 hover:bg-red-900 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors">
                💬 Contact Support
            </a>
        </div>
    </div>
</section>
@endsection
