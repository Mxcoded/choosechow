@extends('layouts.master')

@section('title', 'Chef Amina Bello - ChooseChow')

@section('content')
<!-- Chef Hero Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid lg:grid-cols-3 gap-12">
            <!-- Chef Info -->
            <div class="lg:col-span-2">
                <div class="flex flex-col md:flex-row gap-8">
                    <!-- Chef Photo -->
                    <div class="flex-shrink-0">
                        <div class="w-48 h-48 bg-gradient-to-br from-red-400 to-orange-400 rounded-2xl flex items-center justify-center">
                            <span class="text-8xl">👩‍🍳</span>
                        </div>
                    </div>

                    <!-- Chef Details -->
                    <div class="flex-1">
                        <div class="flex items-center gap-4 mb-4">
                            <h1 class="text-4xl font-bold text-gray-900">Chef Amina Bello</h1>
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">🟢 Available Now</span>
                        </div>

                        <div class="flex items-center gap-6 mb-6">
                            <div class="flex items-center">
                                <span class="text-yellow-400 text-xl">⭐⭐⭐⭐⭐</span>
                                <span class="text-gray-600 ml-2 font-semibold">4.9 (127 reviews)</span>
                            </div>
                            <div class="text-gray-600">📍 Ikeja, Lagos</div>
                            <div class="text-gray-600">🚚 30-45 min delivery</div>
                        </div>

                        <p class="text-lg text-gray-600 mb-6">
                            Passionate Nigerian cuisine specialist with over 8 years of experience. Known for authentic 
                            jollof rice, rich egusi soup, and traditional West African dishes that bring families together.
                        </p>

                        <!-- Specialties -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-3">Specialties</h3>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-4 py-2 bg-red-100 text-red-700 rounded-full">Nigerian Cuisine</span>
                                <span class="px-4 py-2 bg-orange-100 text-orange-700 rounded-full">Spicy Dishes</span>
                                <span class="px-4 py-2 bg-green-100 text-green-700 rounded-full">Traditional Soups</span>
                                <span class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-full">Rice Dishes</span>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="grid grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-600">500+</div>
                                <div class="text-gray-600 text-sm">Orders Completed</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-600">98%</div>
                                <div class="text-gray-600 text-sm">Response Rate</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-600">2 hrs</div>
                                <div class="text-gray-600 text-sm">Avg Response</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary Card -->
            <div class="lg:col-span-1">
                <div class="bg-gray-50 rounded-2xl p-6 sticky top-6">
                    <h3 class="text-xl font-semibold mb-4">Order from Chef Amina</h3>
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Starting Price:</span>
                            <span class="font-semibold">₦2,500</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Delivery Fee:</span>
                            <span class="font-semibold">₦500</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Delivery Time:</span>
                            <span class="font-semibold">30-45 min</span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <button class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-semibold transition-colors">
                            🛒 Order Now
                        </button>
                        <button class="w-full bg-white hover:bg-gray-50 text-gray-700 py-3 rounded-lg font-semibold border-2 border-gray-200 transition-colors">
                            💬 Message Chef
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Chef -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid lg:grid-cols-3 gap-12">
            <div class="lg:col-span-2">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">About Chef Amina</h2>
                <div class="prose prose-lg text-gray-600 space-y-4">
                    <p>
                        Born and raised in Lagos, I discovered my passion for cooking at my grandmother's side, 
                        learning the secrets of authentic Nigerian cuisine. For over 8 years, I've been perfecting 
                        traditional recipes while adding my own creative touches.
                    </p>
                    <p>
                        My specialty is bringing families together through food. Whether it's my signature jollof rice 
                        that's been featured in local food blogs, or my rich egusi soup that customers say tastes 
                        "just like home," every dish is prepared with love and the finest ingredients.
                    </p>
                    <p>
                        I believe food is more than sustenance – it's culture, memory, and connection. When you order 
                        from me, you're not just getting a meal; you're experiencing a piece of Nigerian heritage 
                        crafted with care and authenticity.
                    </p>
                </div>

                <!-- Delivery Info -->
                <div class="mt-8 bg-blue-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4">📍 Delivery Information</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-medium mb-2">Areas I Serve:</h4>
                            <ul class="text-gray-600 space-y-1">
                                <li>• Ikeja and surrounding areas</li>
                                <li>• Ogba, Agege, Alimosho</li>
                                <li>• Parts of Lagos Mainland</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">Operating Hours:</h4>
                            <ul class="text-gray-600 space-y-1">
                                <li>• Monday - Friday: 10am - 8pm</li>
                                <li>• Saturday: 9am - 9pm</li>
                                <li>• Sunday: 12pm - 7pm</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Menu Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Chef Amina's Menu</h2>

        <!-- Menu Categories -->
        <div class="flex flex-wrap justify-center gap-4 mb-12">
            <button class="px-6 py-3 bg-red-600 text-white rounded-lg font-semibold">All Items</button>
            <button class="px-6 py-3 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors">Main Dishes</button>
            <button class="px-6 py-3 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors">Soups & Stews</button>
            <button class="px-6 py-3 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors">Sides</button>
            <button class="px-6 py-3 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors">Beverages</button>
        </div>

        <!-- Menu Items -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- Menu Item 1 -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover-scale">
                <div class="h-48 bg-gradient-to-br from-red-300 to-orange-300 flex items-center justify-center">
                    <span class="text-4xl">🍛</span>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-xl font-semibold">Signature Jollof Rice</h3>
                        <span class="text-green-600 text-sm">🌶️ Spicy</span>
                    </div>
                    <p class="text-gray-600 mb-4">
                        My famous jollof rice cooked with premium basmati rice, fresh tomatoes, and secret spice blend. 
                        Served with fried plantain and choice of protein.
                    </p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-2xl font-bold text-gray-900">₦3,500</span>
                        <div class="flex items-center space-x-2">
                            <button class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300">-</button>
                            <span class="w-8 text-center">1</span>
                            <button class="w-8 h-8 bg-red-600 text-white rounded-full flex items-center justify-center hover:bg-red-700">+</button>
                        </div>
                    </div>
                    <button class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-semibold transition-colors">
                        Add to Cart
                    </button>
                </div>
            </div>

            <!-- Menu Item 2 -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover-scale">
                <div class="h-48 bg-gradient-to-br from-green-300 to-teal-300 flex items-center justify-center">
                    <span class="text-4xl">🥣</span>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-xl font-semibold">Rich Egusi Soup</h3>
                        <span class="text-blue-600 text-sm">🥬 Traditional</span>
                    </div>
                    <p class="text-gray-600 mb-4">
                        Traditional egusi soup made with ground melon seeds, fresh spinach, assorted meat, 
                        and stockfish. Served with pounded yam or eba.
                    </p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-2xl font-bold text-gray-900">₦4,200</span>
                        <div class="flex items-center space-x-2">
                            <button class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300">-</button>
                            <span class="w-8 text-center">1</span>
                            <button class="w-8 h-8 bg-red-600 text-white rounded-full flex items-center justify-center hover:bg-red-700">+</button>
                        </div>
                    </div>
                    <button class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-semibold transition-colors">
                        Add to Cart
                    </button>
                </div>
            </div>

            <!-- Menu Item 3 -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover-scale">
                <div class="h-48 bg-gradient-to-br from-yellow-300 to-orange-300 flex items-center justify-center">
                    <span class="text-4xl">🍗</span>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-xl font-semibold">Pepper Chicken</h3>
                        <span class="text-red-600 text-sm">🌶️🌶️ Very Spicy</span>
                    </div>
                    <p class="text-gray-600 mb-4">
                        Tender chicken pieces in spicy pepper sauce with bell peppers, onions, and aromatic spices. 
                        A perfect balance of heat and flavor.
                    </p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-2xl font-bold text-gray-900">₦3,800</span>
                        <div class="flex items-center space-x-2">
                            <button class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300">-</button>
                            <span class="w-8 text-center">1</span>
                            <button class="w-8 h-8 bg-red-600 text-white rounded-full flex items-center justify-center hover:bg-red-700">+</button>
                        </div>
                    </div>
                    <button class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-semibold transition-colors">
                        Add to Cart
                    </button>
                </div>
            </div>

            <!-- Menu Item 4 -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover-scale">
                <div class="h-48 bg-gradient-to-br from-purple-300 to-pink-300 flex items-center justify-center">
                    <span class="text-4xl">🍲</span>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-xl font-semibold">Banga Soup</h3>
                        <span class="text-orange-600 text-sm">🥥 Palm Nut</span>
                    </div>
                    <p class="text-gray-600 mb-4">
                        Authentic Delta-style banga soup made with fresh palm nut extract, catfish, 
                        and traditional spices. Served with starch or rice.
                    </p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-2xl font-bold text-gray-900">₦4,500</span>
                        <div class="flex items-center space-x-2">
                            <button class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300">-</button>
                            <span class="w-8 text-center">1</span>
                            <button class="w-8 h-8 bg-red-600 text-white rounded-full flex items-center justify-center hover:bg-red-700">+</button>
                        </div>
                    </div>
                    <button class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-semibold transition-colors">
                        Add to Cart
                    </button>
                </div>
            </div>

            <!-- Menu Item 5 -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover-scale">
                <div class="h-48 bg-gradient-to-br from-blue-300 to-indigo-300 flex items-center justify-center">
                    <span class="text-4xl">🍌</span>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-xl font-semibold">Fried Plantain</h3>
                        <span class="text-yellow-600 text-sm">🍌 Sweet</span>
                    </div>
                    <p class="text-gray-600 mb-4">
                        Perfectly ripe plantains sliced and fried to golden perfection. 
                        Sweet, caramelized, and the perfect side dish.
                    </p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-2xl font-bold text-gray-900">₦1,500</span>
                        <div class="flex items-center space-x-2">
                            <button class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300">-</button>
                            <span class="w-8 text-center">1</span>
                            <button class="w-8 h-8 bg-red-600 text-white rounded-full flex items-center justify-center hover:bg-red-700">+</button>
                        </div>
                    </div>
                    <button class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-semibold transition-colors">
                        Add to Cart
                    </button>
                </div>
            </div>

            <!-- Menu Item 6 -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover-scale">
                <div class="h-48 bg-gradient-to-br from-teal-300 to-green-300 flex items-center justify-center">
                    <span class="text-4xl">🥤</span>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-xl font-semibold">Fresh Zobo Drink</h3>
                        <span class="text-green-600 text-sm">🌿 Herbal</span>
                    </div>
                    <p class="text-gray-600 mb-4">
                        Refreshing hibiscus drink blended with ginger, cucumber, watermelon, 
                        and natural spices. Healthy and delicious.
                    </p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-2xl font-bold text-gray-900">₦800</span>
                        <div class="flex items-center space-x-2">
                            <button class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300">-</button>
                            <span class="w-8 text-center">1</span>
                            <button class="w-8 h-8 bg-red-600 text-white rounded-full flex items-center justify-center hover:bg-red-700">+</button>
                        </div>
                    </div>
                    <button class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-semibold transition-colors">
                        Add to Cart
                    </button>
                </div>
            </div>

        </div>

        <!-- Custom Order -->
        <div class="mt-12 bg-white rounded-2xl p-8 text-center">
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Don't See What You Want?</h3>
            <p class="text-gray-600 mb-6">
                I'm happy to prepare custom meals based on your preferences. Just let me know what you're craving!
            </p>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors">
                📝 Request Custom Meal
            </button>
        </div>
    </div>
</section>

<!-- Reviews Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Customer Reviews</h2>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- Review 1 -->
            <div class="bg-gray-50 rounded-xl p-6 hover-scale">
                <div class="flex items-center mb-4">
                    <span class="text-yellow-400 text-lg">⭐⭐⭐⭐⭐</span>
                    <span class="text-gray-600 ml-2 text-sm">2 days ago</span>
                </div>
                <p class="text-gray-700 mb-4">
                    "Chef Amina's jollof rice is absolutely incredible! It tastes exactly like my grandmother's recipe. 
                    The delivery was on time and the food was still hot. Will definitely order again!"
                </p>
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-200 rounded-full flex items-center justify-center mr-3">
                        <span class="text-lg">👩</span>
                    </div>
                    <div>
                        <div class="font-semibold">Sarah Okafor</div>
                        <div class="text-gray-600 text-sm">Verified Customer</div>
                    </div>
                </div>
            </div>

            <!-- Review 2 -->
            <div class="bg-gray-50 rounded-xl p-6 hover-scale">
                <div class="flex items-center mb-4">
                    <span class="text-yellow-400 text-lg">⭐⭐⭐⭐⭐</span>
                    <span class="text-gray-600 ml-2 text-sm">1 week ago</span>
                </div>
                <p class="text-gray-700 mb-4">
                    "The egusi soup was phenomenal! Rich, flavorful, and authentic. Chef Amina really knows her craft. 
                    The portion size was generous and worth every naira."
                </p>
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-200 rounded-full flex items-center justify-center mr-3">
                        <span class="text-lg">👨</span>
                    </div>
                    <div>
                        <div class="font-semibold">Michael Adebayo</div>
                        <div class="text-gray-600 text-sm">Verified Customer</div>
                    </div>
                </div>
            </div>

            <!-- Review 3 -->
            <div class="bg-gray-50 rounded-xl p-6 hover-scale">
                <div class="flex items-center mb-4">
                    <span class="text-yellow-400 text-lg">⭐⭐⭐⭐⭐</span>
                    <span class="text-gray-600 ml-2 text-sm">2 weeks ago</span>
                </div>
                <p class="text-gray-700 mb-4">
                    "Outstanding service and even better food! Chef Amina was very responsive to my special dietary 
                    requests. The pepper chicken had the perfect amount of spice. Highly recommended!"
                </p>
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-red-200 rounded-full flex items-center justify-center mr-3">
                        <span class="text-lg">👩</span>
                    </div>
                    <div>
                        <div class="font-semibold">Fatima Hassan</div>
                        <div class="text-gray-600 text-sm">Verified Customer</div>
                    </div>
                </div>
            </div>

        </div>

        <div class="text-center mt-8">
            <button class="bg-white hover:bg-gray-50 text-gray-700 px-8 py-3 rounded-lg font-semibold border-2 border-gray-200 transition-colors">
                View All 127 Reviews
            </button>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-red-600 to-orange-600">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-4xl font-bold text-white mb-6">Ready to Order from Chef Amina?</h2>
        <p class="text-xl text-red-100 mb-8">
            Experience authentic Nigerian cuisine prepared with love and delivered fresh to your door.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button class="bg-white text-red-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-colors">
                🛒 Order Now
            </button>
            <button class="bg-red-800 hover:bg-red-900 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors">
                💬 Message Chef
            </button>
        </div>
    </div>
</section>
@endsection
