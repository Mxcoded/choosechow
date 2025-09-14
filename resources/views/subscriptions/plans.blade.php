<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Plans - ChooseChow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-text {
            background: linear-gradient(135deg, #ef4444, #f97316);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hover-scale {
            transition: transform 0.2s ease;
        }
        .hover-scale:hover {
            transform: scale(1.05);
        }
        .plan-card {
            transition: all 0.3s ease;
        }
        .plan-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .popular-badge {
            background: linear-gradient(135deg, #ef4444, #f97316);
        }
        .toggle-switch {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-50">
@include('layouts.header')
<!-- Hero Section -->
<section class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4 text-center">
        <h1 class="text-5xl font-bold text-gray-900 mb-6">
            Choose Your <span class="gradient-text">Plan</span>
        </h1>
        <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
            Whether you're a food lover or a chef entrepreneur, we have the perfect plan to enhance your ChooseChow experience. 
            Get more value with our subscription plans.
        </p>
        <div class="text-6xl mb-8">💎</div>
        
        <!-- Billing Toggle -->
        <div class="flex items-center justify-center mb-12">
            <span class="text-gray-600 mr-4">Monthly</span>
            <div class="relative">
                <input type="checkbox" id="billingToggle" class="sr-only" onchange="toggleBilling()">
                <label for="billingToggle" class="flex items-center cursor-pointer">
                    <div class="relative">
                        <div class="block bg-gray-300 w-14 h-8 rounded-full"></div>
                        <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition toggle-switch"></div>
                    </div>
                </label>
            </div>
            <span class="text-gray-600 ml-4">Yearly</span>
            <span class="bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full ml-3">Save 20%</span>
        </div>
    </div>
</section>

<!-- Customer Plans -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Customer Plans</h2>
            <p class="text-lg text-gray-600">Enjoy exclusive benefits and savings on your food orders</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            
            <!-- Basic Plan -->
            <div class="bg-white rounded-2xl p-8 plan-card border-2 border-gray-200">
                <div class="text-center mb-8">
                    <div class="text-4xl mb-4">🍽️</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Basic</h3>
                    <p class="text-gray-600 mb-6">Perfect for occasional food lovers</p>
                    <div class="text-4xl font-bold text-gray-900 mb-2">
                        <span class="monthly-price">Free</span>
                        <span class="yearly-price hidden">Free</span>
                    </div>
                    <p class="text-gray-500">Always free</p>
                </div>

                <div class="space-y-4 mb-8">
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Browse all chefs and menus</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Standard delivery options</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Basic customer support</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Order tracking</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Reviews and ratings</span>
                    </div>
                </div>

                <button class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 py-3 rounded-lg font-semibold transition-colors">
                    Current Plan
                </button>
            </div>

            <!-- Premium Plan -->
            <div class="bg-white rounded-2xl p-8 plan-card border-2 border-red-500 relative">
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                    <span class="popular-badge text-white px-6 py-2 rounded-full text-sm font-semibold">Most Popular</span>
                </div>
                
                <div class="text-center mb-8">
                    <div class="text-4xl mb-4">🌟</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Premium</h3>
                    <p class="text-gray-600 mb-6">Great for regular food enthusiasts</p>
                    <div class="text-4xl font-bold text-gray-900 mb-2">
                        <span class="monthly-price">₦2,500</span>
                        <span class="yearly-price hidden">₦24,000</span>
                        <span class="text-lg text-gray-500">/month</span>
                    </div>
                    <p class="text-gray-500 yearly-savings hidden">Save ₦6,000 yearly</p>
                </div>

                <div class="space-y-4 mb-8">
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Everything in Basic</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Free delivery on orders over ₦2,000</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">10% discount on all orders</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Priority customer support</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Early access to new chefs</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Monthly exclusive deals</span>
                    </div>
                </div>

                <button class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-semibold transition-colors">
                    Choose Premium
                </button>
            </div>

            <!-- VIP Plan -->
            <div class="bg-white rounded-2xl p-8 plan-card border-2 border-purple-500">
                <div class="text-center mb-8">
                    <div class="text-4xl mb-4">👑</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">VIP</h3>
                    <p class="text-gray-600 mb-6">Ultimate experience for food connoisseurs</p>
                    <div class="text-4xl font-bold text-gray-900 mb-2">
                        <span class="monthly-price">₦5,000</span>
                        <span class="yearly-price hidden">₦48,000</span>
                        <span class="text-lg text-gray-500">/month</span>
                    </div>
                    <p class="text-gray-500 yearly-savings hidden">Save ₦12,000 yearly</p>
                </div>

                <div class="space-y-4 mb-8">
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Everything in Premium</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Free delivery on all orders</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">15% discount on all orders</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">24/7 VIP support hotline</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Exclusive chef experiences</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Personal food concierge</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Monthly chef meetups</span>
                    </div>
                </div>

                <button class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-lg font-semibold transition-colors">
                    Choose VIP
                </button>
            </div>

        </div>
    </div>
</section>

<!-- Chef Plans -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Chef Plans</h2>
            <p class="text-lg text-gray-600">Grow your culinary business with our professional tools</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            
            <!-- Starter Chef -->
            <div class="bg-white rounded-2xl p-8 plan-card border-2 border-gray-200">
                <div class="text-center mb-8">
                    <div class="text-4xl mb-4">👨‍🍳</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Starter Chef</h3>
                    <p class="text-gray-600 mb-6">Perfect for new chefs getting started</p>
                    <div class="text-4xl font-bold text-gray-900 mb-2">
                        <span class="monthly-price">₦3,000</span>
                        <span class="yearly-price hidden">₦28,800</span>
                        <span class="text-lg text-gray-500">/month</span>
                    </div>
                    <p class="text-gray-500">
                        <span class="monthly-commission">+ 8% commission</span>
                        <span class="yearly-commission hidden">+ 7% commission</span>
                    </p>
                    <p class="text-gray-500 yearly-savings hidden">Save ₦7,200 yearly</p>
                </div>

                <div class="space-y-4 mb-8">
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Basic chef profile</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Up to 20 menu items</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Order management tools</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Basic analytics</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Standard support</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Weekly payouts</span>
                    </div>
                </div>

                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition-colors">
                    Start Cooking
                </button>
            </div>

            <!-- Professional Chef -->
            <div class="bg-white rounded-2xl p-8 plan-card border-2 border-green-500 relative">
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                    <span class="bg-green-500 text-white px-6 py-2 rounded-full text-sm font-semibold">Recommended</span>
                </div>
                
                <div class="text-center mb-8">
                    <div class="text-4xl mb-4">🔥</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Professional Chef</h3>
                    <p class="text-gray-600 mb-6">For established culinary professionals</p>
                    <div class="text-4xl font-bold text-gray-900 mb-2">
                        <span class="monthly-price">₦6,000</span>
                        <span class="yearly-price hidden">₦57,600</span>
                        <span class="text-lg text-gray-500">/month</span>
                    </div>
                    <p class="text-gray-500">
                        <span class="monthly-commission">+ 6% commission</span>
                        <span class="yearly-commission hidden">+ 5% commission</span>
                    </p>
                    <p class="text-gray-500 yearly-savings hidden">Save ₦14,400 yearly</p>
                </div>

                <div class="space-y-4 mb-8">
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Everything in Starter</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Unlimited menu items</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Advanced analytics & insights</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Priority listing in search</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Marketing tools & promotions</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Daily payouts</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Dedicated account manager</span>
                    </div>
                </div>

                <button class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold transition-colors">
                    Go Professional
                </button>
            </div>

            <!-- Master Chef -->
            <div class="bg-white rounded-2xl p-8 plan-card border-2 border-yellow-500">
                <div class="text-center mb-8">
                    <div class="text-4xl mb-4">⭐</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Master Chef</h3>
                    <p class="text-gray-600 mb-6">For culinary entrepreneurs and brands</p>
                    <div class="text-4xl font-bold text-gray-900 mb-2">
                        <span class="monthly-price">₦12,000</span>
                        <span class="yearly-price hidden">₦115,200</span>
                        <span class="text-lg text-gray-500">/month</span>
                    </div>
                    <p class="text-gray-500">
                        <span class="monthly-commission">+ 4% commission</span>
                        <span class="yearly-commission hidden">+ 3% commission</span>
                    </p>
                    <p class="text-gray-500 yearly-savings hidden">Save ₦28,800 yearly</p>
                </div>

                <div class="space-y-4 mb-8">
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Everything in Professional</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Multiple restaurant locations</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">White-label delivery options</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Custom branding & storefront</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">API access for integrations</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Instant payouts</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">24/7 priority support</span>
                    </div>
                </div>

                <button class="w-full bg-yellow-600 hover:bg-yellow-700 text-white py-3 rounded-lg font-semibold transition-colors">
                    Become Master
                </button>
            </div>

        </div>
    </div>
</section>

<!-- Features Comparison -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Compare All Features</h2>
            <p class="text-lg text-gray-600">See what's included in each plan</p>
        </div>

        <div class="bg-white rounded-2xl overflow-hidden shadow-lg">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left p-6 font-semibold text-gray-900">Features</th>
                            <th class="text-center p-6 font-semibold text-gray-900">Basic</th>
                            <th class="text-center p-6 font-semibold text-red-600">Premium</th>
                            <th class="text-center p-6 font-semibold text-purple-600">VIP</th>
                            <th class="text-center p-6 font-semibold text-blue-600">Starter Chef</th>
                            <th class="text-center p-6 font-semibold text-green-600">Pro Chef</th>
                            <th class="text-center p-6 font-semibold text-yellow-600">Master Chef</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <td class="p-6 font-medium">Order Tracking</td>
                            <td class="p-6 text-center"><span class="text-green-500">✓</span></td>
                            <td class="p-6 text-center"><span class="text-green-500">✓</span></td>
                            <td class="p-6 text-center"><span class="text-green-500">✓</span></td>
                            <td class="p-6 text-center"><span class="text-green-500">✓</span></td>
                            <td class="p-6 text-center"><span class="text-green-500">✓</span></td>
                            <td class="p-6 text-center"><span class="text-green-500">✓</span></td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="p-6 font-medium">Free Delivery Threshold</td>
                            <td class="p-6 text-center text-gray-400">None</td>
                            <td class="p-6 text-center">₦2,000+</td>
                            <td class="p-6 text-center">All Orders</td>
                            <td class="p-6 text-center text-gray-400">N/A</td>
                            <td class="p-6 text-center text-gray-400">N/A</td>
                            <td class="p-6 text-center text-gray-400">N/A</td>
                        </tr>
                        <tr>
                            <td class="p-6 font-medium">Order Discount</td>
                            <td class="p-6 text-center text-gray-400">None</td>
                            <td class="p-6 text-center">10%</td>
                            <td class="p-6 text-center">15%</td>
                            <td class="p-6 text-center text-gray-400">N/A</td>
                            <td class="p-6 text-center text-gray-400">N/A</td>
                            <td class="p-6 text-center text-gray-400">N/A</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="p-6 font-medium">Customer Support</td>
                            <td class="p-6 text-center">Basic</td>
                            <td class="p-6 text-center">Priority</td>
                            <td class="p-6 text-center">24/7 VIP</td>
                            <td class="p-6 text-center">Standard</td>
                            <td class="p-6 text-center">Dedicated</td>
                            <td class="p-6 text-center">24/7 Priority</td>
                        </tr>
                        <tr>
                            <td class="p-6 font-medium">Menu Items Limit</td>
                            <td class="p-6 text-center text-gray-400">N/A</td>
                            <td class="p-6 text-center text-gray-400">N/A</td>
                            <td class="p-6 text-center text-gray-400">N/A</td>
                            <td class="p-6 text-center">20</td>
                            <td class="p-6 text-center">Unlimited</td>
                            <td class="p-6 text-center">Unlimited</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="p-6 font-medium">Commission Rate</td>
                            <td class="p-6 text-center text-gray-400">N/A</td>
                            <td class="p-6 text-center text-gray-400">N/A</td>
                            <td class="p-6 text-center text-gray-400">N/A</td>
                            <td class="p-6 text-center">8%</td>
                            <td class="p-6 text-center">6%</td>
                            <td class="p-6 text-center">4%</td>
                        </tr>
                        <tr>
                            <td class="p-6 font-medium">Analytics Dashboard</td>
                            <td class="p-6 text-center text-gray-400">None</td>
                            <td class="p-6 text-center text-gray-400">None</td>
                            <td class="p-6 text-center text-gray-400">None</td>
                            <td class="p-6 text-center">Basic</td>
                            <td class="p-6 text-center">Advanced</td>
                            <td class="p-6 text-center">Advanced</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="p-6 font-medium">Payout Frequency</td>
                            <td class="p-6 text-center text-gray-400">N/A</td>
                            <td class="p-6 text-center text-gray-400">N/A</td>
                            <td class="p-6 text-center text-gray-400">N/A</td>
                            <td class="p-6 text-center">Weekly</td>
                            <td class="p-6 text-center">Daily</td>
                            <td class="p-6 text-center">Instant</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
            <p class="text-lg text-gray-600">Everything you need to know about our subscription plans</p>
        </div>

        <div class="space-y-6">
            <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold mb-3 flex items-center">
                    <span class="text-blue-600 mr-3">❓</span>
                    Can I change my plan anytime?
                </h3>
                <p class="text-gray-700">
                    Yes! You can upgrade or downgrade your plan at any time. Changes take effect immediately for upgrades, 
                    or at the end of your current billing cycle for downgrades.
                </p>
            </div>

            <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold mb-3 flex items-center">
                    <span class="text-green-600 mr-3">💳</span>
                    What payment methods do you accept?
                </h3>
                <p class="text-gray-700">
                    We accept all major credit cards, debit cards, bank transfers, and mobile money payments. 
                    All payments are processed securely through our certified payment partners.
                </p>
            </div>

            <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold mb-3 flex items-center">
                    <span class="text-red-600 mr-3">🔄</span>
                    Is there a refund policy?
                </h3>
                <p class="text-gray-700">
                    We offer a 7-day money-back guarantee for all new subscriptions. If you're not satisfied, 
                    contact our support team within 7 days for a full refund.
                </p>
            </div>

            <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold mb-3 flex items-center">
                    <span class="text-purple-600 mr-3">👨‍🍳</span>
                    Do chef plans include customer features?
                </h3>
                <p class="text-gray-700">
                    Chef plans focus on business tools and lower commission rates. If you want customer benefits too, 
                    you can subscribe to both a chef plan and a customer plan.
                </p>
            </div>

            <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold mb-3 flex items-center">
                    <span class="text-yellow-600 mr-3">📞</span>
                    How do I get support?
                </h3>
                <p class="text-gray-700">
                    All subscribers get access to our support team. Premium and VIP customers get priority support, 
                    while VIP members have access to our 24/7 hotline.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-red-600 to-orange-600">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-4xl font-bold text-white mb-6">Ready to Get Started?</h2>
        <p class="text-xl text-red-100 mb-8">
            Join thousands of satisfied customers and chefs who've upgraded their ChooseChow experience.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button class="bg-white text-red-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-colors">
                🍽️ Choose Customer Plan
            </button>
            <button class="bg-red-800 hover:bg-red-900 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors">
                👨‍🍳 Choose Chef Plan
            </button>
        </div>
        <p class="text-red-100 text-sm mt-6">
            30-day free trial • No setup fees • Cancel anytime
        </p>
    </div>
</section>

<script>
function toggleBilling() {
    const toggle = document.getElementById('billingToggle');
    const monthlyPrices = document.querySelectorAll('.monthly-price');
    const yearlyPrices = document.querySelectorAll('.yearly-price');
    const monthlyCommissions = document.querySelectorAll('.monthly-commission');
    const yearlyCommissions = document.querySelectorAll('.yearly-commission');
    const yearlySavings = document.querySelectorAll('.yearly-savings');
    const dot = document.querySelector('.dot');
    
    if (toggle.checked) {
        // Show yearly pricing
        monthlyPrices.forEach(price => price.classList.add('hidden'));
        yearlyPrices.forEach(price => price.classList.remove('hidden'));
        monthlyCommissions.forEach(commission => commission.classList.add('hidden'));
        yearlyCommissions.forEach(commission => commission.classList.remove('hidden'));
        yearlySavings.forEach(saving => saving.classList.remove('hidden'));
        dot.style.transform = 'translateX(24px)';
        dot.style.backgroundColor = '#ef4444';
    } else {
        // Show monthly pricing
        monthlyPrices.forEach(price => price.classList.remove('hidden'));
        yearlyPrices.forEach(price => price.classList.add('hidden'));
        monthlyCommissions.forEach(commission => commission.classList.remove('hidden'));
        yearlyCommissions.forEach(commission => commission.classList.add('hidden'));
        yearlySavings.forEach(saving => saving.classList.add('hidden'));
        dot.style.transform = 'translateX(0px)';
        dot.style.backgroundColor = '#ffffff';
    }
}

// Add click handlers for plan selection
document.addEventListener('DOMContentLoaded', function() {
    const planButtons = document.querySelectorAll('button');
    
    planButtons.forEach(button => {
        if (button.textContent.includes('Choose') || button.textContent.includes('Start') || 
            button.textContent.includes('Go') || button.textContent.includes('Become')) {
            button.addEventListener('click', function() {
                const planName = this.closest('.plan-card').querySelector('h3').textContent;
                alert(`You selected the ${planName} plan! Redirecting to checkout...`);
                // Here you would typically redirect to a checkout page
            });
        }
    });
});
</script>

<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'97ee6574e4c547cf',t:'MTc1NzgzNzMwNS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
