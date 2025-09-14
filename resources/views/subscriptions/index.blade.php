@extends('layouts.master')

@section('title', 'Subscription Plans - ChooseChow')

@section('content')
<!-- Subscription Hero Section -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h1 class="text-5xl font-bold text-gray-900 mb-6">
            Choose Your <span class="gradient-text">Subscription</span>
        </h1>
        <p class="text-xl text-gray-600 mb-8">
            Enjoy regular deliveries from your favorite chefs with exclusive benefits, 
            priority booking, and special discounts.
        </p>
        <div class="text-6xl mb-8">📦</div>
    </div>
</section>

<!-- Subscription Benefits -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Why Subscribe to ChooseChow?</h2>
            <p class="text-lg text-gray-600">Unlock exclusive benefits and never worry about meal planning again</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            
            <!-- Benefit 1 -->
            <div class="bg-white rounded-xl p-6 text-center hover-scale">
                <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">💰</span>
                </div>
                <h3 class="text-lg font-semibold mb-3">Save Money</h3>
                <p class="text-gray-600">
                    Get up to 20% off regular prices with subscription discounts and exclusive member deals.
                </p>
            </div>

            <!-- Benefit 2 -->
            <div class="bg-white rounded-xl p-6 text-center hover-scale">
                <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">⚡</span>
                </div>
                <h3 class="text-lg font-semibold mb-3">Priority Access</h3>
                <p class="text-gray-600">
                    Skip the queue with priority booking and get first access to new chefs and special menus.
                </p>
            </div>

            <!-- Benefit 3 -->
            <div class="bg-white rounded-xl p-6 text-center hover-scale">
                <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">🎯</span>
                </div>
                <h3 class="text-lg font-semibold mb-3">Personalized</h3>
                <p class="text-gray-600">
                    Customized meal recommendations based on your preferences and dietary requirements.
                </p>
            </div>

            <!-- Benefit 4 -->
            <div class="bg-white rounded-xl p-6 text-center hover-scale">
                <div class="bg-red-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">🚚</span>
                </div>
                <h3 class="text-lg font-semibold mb-3">Free Delivery</h3>
                <p class="text-gray-600">
                    Enjoy free delivery on all subscription orders, saving you money on every meal.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- Subscription Plans -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Choose Your Perfect Plan</h2>
            <p class="text-lg text-gray-600">Flexible options to fit your lifestyle and budget</p>
        </div>

        <!-- Plan Toggle -->
        <div class="flex justify-center mb-12">
            <div class="bg-gray-100 rounded-lg p-1 flex">
                <button class="px-6 py-2 bg-white text-gray-900 rounded-md font-semibold shadow-sm">Monthly</button>
                <button class="px-6 py-2 text-gray-600 rounded-md font-semibold">Yearly (Save 20%)</button>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
            
            <!-- Basic Plan -->
            <div class="bg-white border-2 border-gray-200 rounded-2xl p-8 hover-scale">
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Basic Plan</h3>
                    <p class="text-gray-600 mb-6">Perfect for individuals who want regular home-cooked meals</p>
                    <div class="text-4xl font-bold text-gray-900 mb-2">₦15,000</div>
                    <div class="text-gray-600">per month</div>
                </div>

                <div class="space-y-4 mb-8">
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span>8 meals per month</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span>10% discount on all orders</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span>Free delivery</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span>Access to 50+ chefs</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span>Flexible scheduling</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-gray-400 mr-3">✗</span>
                        <span class="text-gray-400">Priority chef booking</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-gray-400 mr-3">✗</span>
                        <span class="text-gray-400">Custom meal requests</span>
                    </div>
                </div>

                <button class="w-full bg-gray-600 hover:bg-gray-700 text-white py-3 rounded-lg font-semibold transition-colors">
                    Choose Basic Plan
                </button>
            </div>

            <!-- Premium Plan (Most Popular) -->
            <div class="bg-white border-2 border-red-500 rounded-2xl p-8 hover-scale relative">
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                    <span class="bg-red-500 text-white px-6 py-2 rounded-full text-sm font-semibold">Most Popular</span>
                </div>
                
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Premium Plan</h3>
                    <p class="text-gray-600 mb-6">Ideal for families and food enthusiasts who want variety</p>
                    <div class="text-4xl font-bold text-red-600 mb-2">₦28,000</div>
                    <div class="text-gray-600">per month</div>
                </div>

                <div class="space-y-4 mb-8">
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span>16 meals per month</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span>15% discount on all orders</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span>Free delivery</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span>Access to all chefs</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span>Priority chef booking</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span>Custom meal requests</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span>24/7 customer support</span>
                    </div>
                </div>

                <button class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-semibold transition-colors">
                    Choose Premium Plan
                </button>
            </div>

            <!-- VIP Plan -->
            <div class="bg-white border-2 border-yellow-400 rounded-2xl p-8 hover-scale">
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">VIP Plan</h3>
                    <p class="text-gray-600 mb-6">Ultimate experience for serious food lovers and busy professionals</p>
                    <div class="text-4xl font-bold text-yellow-600 mb-2">₦45,000</div>
                    <div class="text-gray-600">per month</div>
                </div>

                <div class="space-y-4 mb-8">
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span>30 meals per month</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span>20% discount on all orders</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span>Free priority delivery</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span>Exclusive chef access</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span>Personal meal curator</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span>Unlimited custom requests</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span>VIP customer support</span>
                    </div>
                </div>

                <button class="w-full bg-yellow-600 hover:bg-yellow-700 text-white py-3 rounded-lg font-semibold transition-colors">
                    Choose VIP Plan
                </button>
            </div>

        </div>

        <!-- Plan Comparison Note -->
        <div class="text-center mt-12">
            <p class="text-gray-600 mb-4">All plans include flexible pausing, easy cancellation, and no hidden fees.</p>
            <button class="text-red-600 hover:text-red-700 font-semibold underline">
                Compare All Features →
            </button>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">How Subscriptions Work</h2>
            <p class="text-lg text-gray-600">Simple, flexible, and designed around your schedule</p>
        </div>

        <div class="grid md:grid-cols-4 gap-8">
            
            <!-- Step 1 -->
            <div class="text-center">
                <div class="bg-red-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl font-bold text-red-600">1</span>
                </div>
                <h3 class="text-lg font-semibold mb-3">Choose Your Plan</h3>
                <p class="text-gray-600">
                    Select the subscription plan that fits your needs and budget. You can change anytime.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="text-center">
                <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl font-bold text-blue-600">2</span>
                </div>
                <h3 class="text-lg font-semibold mb-3">Set Preferences</h3>
                <p class="text-gray-600">
                    Tell us your dietary preferences, favorite cuisines, and delivery schedule.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="text-center">
                <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl font-bold text-green-600">3</span>
                </div>
                <h3 class="text-lg font-semibold mb-3">Get Recommendations</h3>
                <p class="text-gray-600">
                    Receive personalized meal recommendations from our curated chef network.
                </p>
            </div>

            <!-- Step 4 -->
            <div class="text-center">
                <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl font-bold text-purple-600">4</span>
                </div>
                <h3 class="text-lg font-semibold mb-3">Enjoy Your Meals</h3>
                <p class="text-gray-600">
                    Sit back and enjoy fresh, delicious meals delivered right to your door.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- Subscription Management -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Complete Control Over Your Subscription</h2>
            <p class="text-lg text-gray-600">Manage everything from your dashboard with full flexibility</p>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            
            <!-- Flexibility -->
            <div class="bg-blue-50 rounded-xl p-8">
                <h3 class="text-xl font-semibold mb-4 flex items-center">
                    <span class="text-blue-600 mr-3">🔄</span>
                    Ultimate Flexibility
                </h3>
                <ul class="space-y-3 text-gray-600">
                    <li class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        Pause anytime for vacations or breaks
                    </li>
                    <li class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        Change plans up or down as needed
                    </li>
                    <li class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        Skip weeks without penalties
                    </li>
                    <li class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        Cancel anytime with 24-hour notice
                    </li>
                </ul>
            </div>

            <!-- Customization -->
            <div class="bg-green-50 rounded-xl p-8">
                <h3 class="text-xl font-semibold mb-4 flex items-center">
                    <span class="text-green-600 mr-3">⚙️</span>
                    Full Customization
                </h3>
                <ul class="space-y-3 text-gray-600">
                    <li class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        Choose specific chefs and cuisines
                    </li>
                    <li class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        Set dietary restrictions and allergies
                    </li>
                    <li class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        Schedule deliveries for your convenience
                    </li>
                    <li class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        Rate meals to improve recommendations
                    </li>
                </ul>
            </div>

        </div>
    </div>
</section>

<!-- Customer Testimonials -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">What Our Subscribers Say</h2>
            <p class="text-lg text-gray-600">Real feedback from happy ChooseChow subscribers</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            
            <!-- Testimonial 1 -->
            <div class="bg-white rounded-xl p-6 hover-scale">
                <div class="flex items-center mb-4">
                    <span class="text-yellow-400 text-lg">⭐⭐⭐⭐⭐</span>
                </div>
                <p class="text-gray-700 mb-4">
                    "The Premium plan has been a game-changer for our family. We get amazing variety, 
                    save money, and never have to worry about what's for dinner!"
                </p>
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-200 rounded-full flex items-center justify-center mr-3">
                        <span class="text-lg">👩</span>
                    </div>
                    <div>
                        <div class="font-semibold">Mrs. Adunni Lagos</div>
                        <div class="text-gray-600 text-sm">Premium Subscriber</div>
                    </div>
                </div>
            </div>

            <!-- Testimonial 2 -->
            <div class="bg-white rounded-xl p-6 hover-scale">
                <div class="flex items-center mb-4">
                    <span class="text-yellow-400 text-lg">⭐⭐⭐⭐⭐</span>
                </div>
                <p class="text-gray-700 mb-4">
                    "As a busy professional, the VIP plan is perfect. My personal curator understands 
                    my preferences perfectly, and the priority delivery is always on time."
                </p>
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-200 rounded-full flex items-center justify-center mr-3">
                        <span class="text-lg">👨</span>
                    </div>
                    <div>
                        <div class="font-semibold">Mr. Chike Okafor</div>
                        <div class="text-gray-600 text-sm">VIP Subscriber</div>
                    </div>
                </div>
            </div>

            <!-- Testimonial 3 -->
            <div class="bg-white rounded-xl p-6 hover-scale">
                <div class="flex items-center mb-4">
                    <span class="text-yellow-400 text-lg">⭐⭐⭐⭐⭐</span>
                </div>
                <p class="text-gray-700 mb-4">
                    "Started with the Basic plan and loved it so much I upgraded to Premium. 
                    The quality is consistent and the savings really add up!"
                </p>
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-red-200 rounded-full flex items-center justify-center mr-3">
                        <span class="text-lg">👩</span>
                    </div>
                    <div>
                        <div class="font-semibold">Miss Fatima Yusuf</div>
                        <div class="text-gray-600 text-sm">Premium Subscriber</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Subscription FAQs</h2>
            <p class="text-lg text-gray-600">Common questions about our subscription plans</p>
        </div>

        <div class="space-y-6">
            
            <!-- FAQ Item 1 -->
            <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold mb-3 flex items-center">
                    <span class="text-red-600 mr-3">❓</span>
                    Can I change my subscription plan anytime?
                </h3>
                <p class="text-gray-600 ml-8">
                    Yes! You can upgrade or downgrade your plan at any time. Changes take effect from your next billing cycle, 
                    and you'll be charged the prorated difference.
                </p>
            </div>

            <!-- FAQ Item 2 -->
            <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold mb-3 flex items-center">
                    <span class="text-red-600 mr-3">❓</span>
                    What happens if I don't use all my meals in a month?
                </h3>
                <p class="text-gray-600 ml-8">
                    Unused meals roll over to the next month (up to 50% of your plan limit). This gives you flexibility 
                    for busy weeks or when you're traveling.
                </p>
            </div>

            <!-- FAQ Item 3 -->
            <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold mb-3 flex items-center">
                    <span class="text-red-600 mr-3">❓</span>
                    How do I pause my subscription?
                </h3>
                <p class="text-gray-600 ml-8">
                    Simply log into your account and click "Pause Subscription." You can pause for up to 3 months 
                    and resume anytime without losing your plan benefits.
                </p>
            </div>

            <!-- FAQ Item 4 -->
            <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold mb-3 flex items-center">
                    <span class="text-red-600 mr-3">❓</span>
                    Are there any cancellation fees?
                </h3>
                <p class="text-gray-600 ml-8">
                    No cancellation fees ever! You can cancel your subscription anytime with 24-hour notice. 
                    You'll continue to receive benefits until your current billing period ends.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-red-600 to-orange-600">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-4xl font-bold text-white mb-6">Ready to Start Your Food Journey?</h2>
        <p class="text-xl text-red-100 mb-8">
            Join thousands of satisfied subscribers who never worry about meal planning again. 
            Start with any plan and change anytime!
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button class="bg-white text-red-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-colors">
                🚀 Start Free Trial
            </button>
            <button class="bg-red-800 hover:bg-red-900 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors">
                📞 Talk to Our Team
            </button>
        </div>
        <p class="text-red-100 text-sm mt-4">
            7-day free trial • No commitment • Cancel anytime
        </p>
    </div>
</section>
@endsection
