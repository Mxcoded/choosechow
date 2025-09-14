@extends('layouts.master')

@section('title', 'Privacy Policy - ChooseChow')

@section('content')
<!-- Privacy Hero Section -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h1 class="text-5xl font-bold text-gray-900 mb-6">
            Privacy <span class="gradient-text">Policy</span>
        </h1>
        <p class="text-xl text-gray-600 mb-8">
            Your privacy matters to us. Learn how we collect, use, and protect your personal information 
            when you use ChooseChow services.
        </p>
        <div class="text-6xl mb-8">🔒</div>
        <div class="bg-blue-50 rounded-xl p-6 text-left">
            <div class="flex items-center mb-3">
                <span class="text-blue-600 text-xl mr-3">ℹ️</span>
                <h3 class="text-lg font-semibold">Quick Summary</h3>
            </div>
            <p class="text-gray-700">
                <strong>Last Updated:</strong> December 15, 2024 • 
                <strong>Effective Date:</strong> January 1, 2025 • 
                <strong>Version:</strong> 2.1
            </p>
        </div>
    </div>
</section>

<!-- Privacy Overview -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Privacy Commitment</h2>
            <p class="text-lg text-gray-600">We believe in transparency and your right to control your personal data</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            
            <!-- Commitment 1 -->
            <div class="bg-white rounded-xl p-8 text-center hover-scale">
                <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl">🛡️</span>
                </div>
                <h3 class="text-xl font-semibold mb-4">Data Protection</h3>
                <p class="text-gray-600">
                    We use industry-standard security measures to protect your personal information 
                    from unauthorized access, disclosure, or misuse.
                </p>
            </div>

            <!-- Commitment 2 -->
            <div class="bg-white rounded-xl p-8 text-center hover-scale">
                <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl">👁️</span>
                </div>
                <h3 class="text-xl font-semibold mb-4">Transparency</h3>
                <p class="text-gray-600">
                    We clearly explain what data we collect, why we collect it, and how we use it. 
                    No hidden practices or unclear terms.
                </p>
            </div>

            <!-- Commitment 3 -->
            <div class="bg-white rounded-xl p-8 text-center hover-scale">
                <div class="bg-red-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl">⚙️</span>
                </div>
                <h3 class="text-xl font-semibold mb-4">Your Control</h3>
                <p class="text-gray-600">
                    You have the right to access, update, delete, or export your data at any time. 
                    Your privacy settings are always in your hands.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- Main Privacy Content -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4">
        
        <!-- Table of Contents -->
        <div class="bg-gray-50 rounded-xl p-6 mb-12">
            <h3 class="text-lg font-semibold mb-4">📋 Table of Contents</h3>
            <div class="grid md:grid-cols-2 gap-2 text-sm">
                <a href="#information-we-collect" class="text-red-600 hover:text-red-700 py-1">1. Information We Collect</a>
                <a href="#how-we-use" class="text-red-600 hover:text-red-700 py-1">2. How We Use Your Information</a>
                <a href="#information-sharing" class="text-red-600 hover:text-red-700 py-1">3. Information Sharing</a>
                <a href="#data-security" class="text-red-600 hover:text-red-700 py-1">4. Data Security</a>
                <a href="#your-rights" class="text-red-600 hover:text-red-700 py-1">5. Your Privacy Rights</a>
                <a href="#cookies" class="text-red-600 hover:text-red-700 py-1">6. Cookies & Tracking</a>
                <a href="#data-retention" class="text-red-600 hover:text-red-700 py-1">7. Data Retention</a>
                <a href="#international-transfers" class="text-red-600 hover:text-red-700 py-1">8. International Transfers</a>
                <a href="#children-privacy" class="text-red-600 hover:text-red-700 py-1">9. Children's Privacy</a>
                <a href="#policy-changes" class="text-red-600 hover:text-red-700 py-1">10. Policy Changes</a>
                <a href="#contact-us" class="text-red-600 hover:text-red-700 py-1">11. Contact Information</a>
            </div>
        </div>

        <!-- Section 1: Information We Collect -->
        <div id="information-we-collect" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">1. Information We Collect</h2>
            
            <div class="space-y-6">
                <div class="bg-blue-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-blue-600 mr-3">👤</span>
                        Personal Information You Provide
                    </h3>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <span class="text-green-500 mr-3 mt-1">•</span>
                            <span><strong>Account Information:</strong> Name, email address, phone number, password</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-3 mt-1">•</span>
                            <span><strong>Profile Details:</strong> Profile picture, dietary preferences, allergies, delivery addresses</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-3 mt-1">•</span>
                            <span><strong>Payment Information:</strong> Credit/debit card details, billing address (processed securely by our payment partners)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-3 mt-1">•</span>
                            <span><strong>Communication Data:</strong> Messages with chefs, customer support interactions, reviews and ratings</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-green-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-green-600 mr-3">📱</span>
                        Information Collected Automatically
                    </h3>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <span class="text-green-500 mr-3 mt-1">•</span>
                            <span><strong>Device Information:</strong> IP address, browser type, operating system, device identifiers</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-3 mt-1">•</span>
                            <span><strong>Usage Data:</strong> Pages visited, time spent on site, click patterns, search queries</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-3 mt-1">•</span>
                            <span><strong>Location Data:</strong> GPS coordinates (with permission), delivery addresses, general location for service availability</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-3 mt-1">•</span>
                            <span><strong>Order History:</strong> Past orders, preferences, delivery times, chef interactions</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-yellow-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-yellow-600 mr-3">🤝</span>
                        Information from Third Parties
                    </h3>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <span class="text-green-500 mr-3 mt-1">•</span>
                            <span><strong>Social Media:</strong> Profile information when you sign up using Facebook, Google, or other social platforms</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-3 mt-1">•</span>
                            <span><strong>Payment Processors:</strong> Transaction confirmations and payment status updates</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-3 mt-1">•</span>
                            <span><strong>Delivery Partners:</strong> Delivery status updates and location tracking during delivery</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Section 2: How We Use Your Information -->
        <div id="how-we-use" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">2. How We Use Your Information</h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-red-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4">🍽️ Service Delivery</h3>
                    <ul class="space-y-2 text-gray-700 text-sm">
                        <li>• Process and fulfill your food orders</li>
                        <li>• Connect you with available chefs</li>
                        <li>• Coordinate delivery logistics</li>
                        <li>• Handle payments and billing</li>
                        <li>• Provide customer support</li>
                    </ul>
                </div>

                <div class="bg-blue-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4">🎯 Personalization</h3>
                    <ul class="space-y-2 text-gray-700 text-sm">
                        <li>• Recommend chefs and dishes</li>
                        <li>• Customize your experience</li>
                        <li>• Remember your preferences</li>
                        <li>• Suggest relevant content</li>
                        <li>• Improve our algorithms</li>
                    </ul>
                </div>

                <div class="bg-green-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4">📞 Communication</h3>
                    <ul class="space-y-2 text-gray-700 text-sm">
                        <li>• Send order confirmations and updates</li>
                        <li>• Notify you about delivery status</li>
                        <li>• Share promotional offers (with consent)</li>
                        <li>• Respond to your inquiries</li>
                        <li>• Send important service announcements</li>
                    </ul>
                </div>

                <div class="bg-purple-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4">🔧 Platform Improvement</h3>
                    <ul class="space-y-2 text-gray-700 text-sm">
                        <li>• Analyze usage patterns and trends</li>
                        <li>• Improve our website and app</li>
                        <li>• Develop new features</li>
                        <li>• Ensure platform security</li>
                        <li>• Conduct research and analytics</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Section 3: Information Sharing -->
        <div id="information-sharing" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">3. Information Sharing</h2>
            
            <div class="bg-yellow-50 rounded-xl p-6 mb-6">
                <div class="flex items-center mb-3">
                    <span class="text-yellow-600 text-xl mr-3">⚠️</span>
                    <h3 class="text-lg font-semibold">Important Note</h3>
                </div>
                <p class="text-gray-700">
                    <strong>We never sell your personal information to third parties.</strong> We only share your data in the specific circumstances outlined below, and always with appropriate safeguards.
                </p>
            </div>

            <div class="space-y-6">
                <div class="border-l-4 border-red-500 pl-6">
                    <h3 class="text-lg font-semibold mb-3">👨‍🍳 With Our Chef Partners</h3>
                    <p class="text-gray-700 mb-2">We share necessary information to fulfill your orders:</p>
                    <ul class="text-gray-600 text-sm space-y-1">
                        <li>• Your name and contact information for order coordination</li>
                        <li>• Delivery address and special instructions</li>
                        <li>• Order details and dietary preferences</li>
                        <li>• Payment confirmation (not payment details)</li>
                    </ul>
                </div>

                <div class="border-l-4 border-blue-500 pl-6">
                    <h3 class="text-lg font-semibold mb-3">🚚 With Service Providers</h3>
                    <p class="text-gray-700 mb-2">We work with trusted partners who help us operate our platform:</p>
                    <ul class="text-gray-600 text-sm space-y-1">
                        <li>• Payment processors (Paystack, Flutterwave) for secure transactions</li>
                        <li>• Delivery services for order fulfillment</li>
                        <li>• Cloud hosting providers for data storage</li>
                        <li>• Analytics services for platform improvement</li>
                        <li>• Customer support tools for better service</li>
                    </ul>
                </div>

                <div class="border-l-4 border-green-500 pl-6">
                    <h3 class="text-lg font-semibold mb-3">⚖️ Legal Requirements</h3>
                    <p class="text-gray-700 mb-2">We may disclose information when required by law:</p>
                    <ul class="text-gray-600 text-sm space-y-1">
                        <li>• To comply with legal obligations or court orders</li>
                        <li>• To protect our rights, property, or safety</li>
                        <li>• To protect the rights and safety of our users</li>
                        <li>• To prevent fraud or illegal activities</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Section 4: Data Security -->
        <div id="data-security" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">4. Data Security</h2>
            
            <div class="bg-green-50 rounded-xl p-6 mb-6">
                <div class="flex items-center mb-4">
                    <span class="text-green-600 text-2xl mr-3">🔒</span>
                    <h3 class="text-lg font-semibold">Our Security Measures</h3>
                </div>
                <p class="text-gray-700 mb-4">
                    We implement multiple layers of security to protect your personal information:
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="bg-white border-2 border-gray-200 rounded-xl p-4">
                        <h4 class="font-semibold mb-2 flex items-center">
                            <span class="text-blue-600 mr-2">🛡️</span>
                            Technical Safeguards
                        </h4>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li>• SSL/TLS encryption for data transmission</li>
                            <li>• AES-256 encryption for stored data</li>
                            <li>• Regular security audits and penetration testing</li>
                            <li>• Secure cloud infrastructure with AWS/Google Cloud</li>
                        </ul>
                    </div>

                    <div class="bg-white border-2 border-gray-200 rounded-xl p-4">
                        <h4 class="font-semibold mb-2 flex items-center">
                            <span class="text-green-600 mr-2">👥</span>
                            Access Controls
                        </h4>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li>• Multi-factor authentication for staff</li>
                            <li>• Role-based access permissions</li>
                            <li>• Regular access reviews and updates</li>
                            <li>• Principle of least privilege</li>
                        </ul>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="bg-white border-2 border-gray-200 rounded-xl p-4">
                        <h4 class="font-semibold mb-2 flex items-center">
                            <span class="text-red-600 mr-2">🚨</span>
                            Monitoring & Response
                        </h4>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li>• 24/7 security monitoring</li>
                            <li>• Automated threat detection</li>
                            <li>• Incident response procedures</li>
                            <li>• Regular backup and recovery testing</li>
                        </ul>
                    </div>

                    <div class="bg-white border-2 border-gray-200 rounded-xl p-4">
                        <h4 class="font-semibold mb-2 flex items-center">
                            <span class="text-purple-600 mr-2">📚</span>
                            Staff Training
                        </h4>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li>• Regular privacy and security training</li>
                            <li>• Confidentiality agreements</li>
                            <li>• Background checks for sensitive roles</li>
                            <li>• Ongoing security awareness programs</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-red-50 rounded-xl p-6 mt-6">
                <div class="flex items-center mb-3">
                    <span class="text-red-600 text-xl mr-3">🚨</span>
                    <h3 class="text-lg font-semibold">Data Breach Notification</h3>
                </div>
                <p class="text-gray-700">
                    In the unlikely event of a data breach that affects your personal information, we will notify you within 72 hours via email and provide clear information about what happened, what data was involved, and what steps we're taking to address the issue.
                </p>
            </div>
        </div>

        <!-- Section 5: Your Privacy Rights -->
        <div id="your-rights" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">5. Your Privacy Rights</h2>
            
            <div class="bg-blue-50 rounded-xl p-6 mb-6">
                <div class="flex items-center mb-3">
                    <span class="text-blue-600 text-xl mr-3">⚖️</span>
                    <h3 class="text-lg font-semibold">Your Rights Under Nigerian Law</h3>
                </div>
                <p class="text-gray-700">
                    Under the Nigeria Data Protection Regulation (NDPR) and other applicable laws, you have several important rights regarding your personal data.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-white border-2 border-green-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-green-600 mr-3">👁️</span>
                        Right to Access
                    </h3>
                    <p class="text-gray-600 mb-3">You can request a copy of all personal data we hold about you.</p>
                    <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                        Request My Data
                    </button>
                </div>

                <div class="bg-white border-2 border-blue-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-blue-600 mr-3">✏️</span>
                        Right to Rectification
                    </h3>
                    <p class="text-gray-600 mb-3">You can update or correct any inaccurate personal information.</p>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                        Update My Info
                    </button>
                </div>

                <div class="bg-white border-2 border-red-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-red-600 mr-3">🗑️</span>
                        Right to Erasure
                    </h3>
                    <p class="text-gray-600 mb-3">You can request deletion of your personal data in certain circumstances.</p>
                    <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                        Delete My Account
                    </button>
                </div>

                <div class="bg-white border-2 border-purple-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-purple-600 mr-3">📤</span>
                        Right to Portability
                    </h3>
                    <p class="text-gray-600 mb-3">You can export your data in a machine-readable format.</p>
                    <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                        Export My Data
                    </button>
                </div>

                <div class="bg-white border-2 border-yellow-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-yellow-600 mr-3">⏸️</span>
                        Right to Restrict Processing
                    </h3>
                    <p class="text-gray-600 mb-3">You can limit how we process your data in certain situations.</p>
                    <button class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                        Restrict Processing
                    </button>
                </div>

                <div class="bg-white border-2 border-gray-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-gray-600 mr-3">🚫</span>
                        Right to Object
                    </h3>
                    <p class="text-gray-600 mb-3">You can object to certain types of data processing, including marketing.</p>
                    <button class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                        Manage Preferences
                    </button>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-6 mt-6">
                <h3 class="text-lg font-semibold mb-3">📞 How to Exercise Your Rights</h3>
                <p class="text-gray-700 mb-4">
                    To exercise any of these rights, you can:
                </p>
                <ul class="text-gray-600 space-y-2">
                    <li class="flex items-center">
                        <span class="text-green-500 mr-3">•</span>
                        <span>Use the buttons above for quick actions</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-green-500 mr-3">•</span>
                        <span>Email us at <strong>privacy@choosechow.ng</strong></span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-green-500 mr-3">•</span>
                        <span>Contact our Data Protection Officer via the contact form</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-green-500 mr-3">•</span>
                        <span>Call our privacy hotline at <strong>+234 901 234 5680</strong></span>
                    </li>
                </ul>
                <p class="text-gray-600 text-sm mt-4">
                    <strong>Response Time:</strong> We will respond to your request within 30 days. For complex requests, we may extend this by an additional 60 days with notification.
                </p>
            </div>
        </div>

        <!-- Section 6: Cookies & Tracking -->
        <div id="cookies" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">6. Cookies & Tracking Technologies</h2>
            
            <div class="bg-orange-50 rounded-xl p-6 mb-6">
                <div class="flex items-center mb-3">
                    <span class="text-orange-600 text-xl mr-3">🍪</span>
                    <h3 class="text-lg font-semibold">What Are Cookies?</h3>
                </div>
                <p class="text-gray-700">
                    Cookies are small text files stored on your device that help us provide and improve our services. We use different types of cookies for various purposes.
                </p>
            </div>

            <div class="space-y-6">
                <div class="bg-white border-2 border-green-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-green-600 mr-3">✅</span>
                        Essential Cookies (Always Active)
                    </h3>
                    <p class="text-gray-600 mb-3">These cookies are necessary for the website to function properly:</p>
                    <ul class="text-gray-600 text-sm space-y-1">
                        <li>• Authentication and login status</li>
                        <li>• Shopping cart contents</li>
                        <li>• Security and fraud prevention</li>
                        <li>• Basic website functionality</li>
                    </ul>
                </div>

                <div class="bg-white border-2 border-blue-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-blue-600 mr-3">📊</span>
                        Analytics Cookies (Optional)
                    </h3>
                    <p class="text-gray-600 mb-3">Help us understand how you use our website:</p>
                    <ul class="text-gray-600 text-sm space-y-1">
                        <li>• Page views and user behavior</li>
                        <li>• Popular content and features</li>
                        <li>• Website performance metrics</li>
                        <li>• Error tracking and debugging</li>
                    </ul>
                    <div class="mt-4">
                        <label class="flex items-center">
                            <input type="checkbox" class="text-blue-600 focus:ring-blue-500 mr-3" checked>
                            <span class="text-sm">Allow analytics cookies</span>
                        </label>
                    </div>
                </div>

                <div class="bg-white border-2 border-purple-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-purple-600 mr-3">🎯</span>
                        Marketing Cookies (Optional)
                    </h3>
                    <p class="text-gray-600 mb-3">Used to show you relevant advertisements:</p>
                    <ul class="text-gray-600 text-sm space-y-1">
                        <li>• Personalized ads on other websites</li>
                        <li>• Social media integration</li>
                        <li>• Email marketing optimization</li>
                        <li>• Conversion tracking</li>
                    </ul>
                    <div class="mt-4">
                        <label class="flex items-center">
                            <input type="checkbox" class="text-purple-600 focus:ring-purple-500 mr-3">
                            <span class="text-sm">Allow marketing cookies</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-6 mt-6">
                <h3 class="text-lg font-semibold mb-3">⚙️ Managing Your Cookie Preferences</h3>
                <p class="text-gray-700 mb-4">You can control cookies through:</p>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-medium mb-2">Browser Settings:</h4>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li>• Chrome: Settings > Privacy > Cookies</li>
                            <li>• Firefox: Options > Privacy > Cookies</li>
                            <li>• Safari: Preferences > Privacy</li>
                            <li>• Edge: Settings > Privacy > Cookies</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-medium mb-2">Our Cookie Center:</h4>
                        <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors mb-2">
                            Manage Cookie Preferences
                        </button>
                        <p class="text-gray-600 text-xs">Update your choices anytime</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 7: Data Retention -->
        <div id="data-retention" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">7. Data Retention</h2>
            
            <div class="bg-blue-50 rounded-xl p-6 mb-6">
                <div class="flex items-center mb-3">
                    <span class="text-blue-600 text-xl mr-3">⏰</span>
                    <h3 class="text-lg font-semibold">How Long We Keep Your Data</h3>
                </div>
                <p class="text-gray-700">
                    We only retain your personal information for as long as necessary to provide our services and comply with legal obligations.
                </p>
            </div>

            <div class="space-y-4">
                <div class="bg-white border-l-4 border-green-500 p-6">
                    <h3 class="font-semibold mb-2">📱 Active Account Data</h3>
                    <p class="text-gray-600 text-sm mb-2">While your account is active and for service provision</p>
                    <div class="text-gray-500 text-xs">Includes: Profile, preferences, order history, communications</div>
                </div>

                <div class="bg-white border-l-4 border-yellow-500 p-6">
                    <h3 class="font-semibold mb-2">💳 Financial Records</h3>
                    <p class="text-gray-600 text-sm mb-2">7 years from last transaction (legal requirement)</p>
                    <div class="text-gray-500 text-xs">Includes: Payment records, invoices, tax-related information</div>
                </div>

                <div class="bg-white border-l-4 border-blue-500 p-6">
                    <h3 class="font-semibold mb-2">📞 Support Communications</h3>
                    <p class="text-gray-600 text-sm mb-2">3 years from last interaction</p>
                    <div class="text-gray-500 text-xs">Includes: Chat logs, emails, phone call records</div>
                </div>

                <div class="bg-white border-l-4 border-red-500 p-6">
                    <h3 class="font-semibold mb-2">🗑️ Deleted Account Data</h3>
                    <p class="text-gray-600 text-sm mb-2">30 days retention period for account recovery</p>
                    <div class="text-gray-500 text-xs">After 30 days: Permanent deletion (except legal obligations)</div>
                </div>

                <div class="bg-white border-l-4 border-purple-500 p-6">
                    <h3 class="font-semibold mb-2">📊 Analytics Data</h3>
                    <p class="text-gray-600 text-sm mb-2">26 months (anonymized after 14 months)</p>
                    <div class="text-gray-500 text-xs">Used for service improvement and business analytics</div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-6 mt-6">
                <h3 class="text-lg font-semibold mb-3">🔄 Automated Deletion</h3>
                <p class="text-gray-700 mb-4">
                    We have automated systems that regularly review and delete data that has exceeded its retention period. You'll receive notifications before any significant data deletion.
                </p>
                <div class="flex flex-wrap gap-3">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                        View My Data Timeline
                    </button>
                    <button class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                        Request Early Deletion
                    </button>
                </div>
            </div>
        </div>

        <!-- Section 8: International Transfers -->
        <div id="international-transfers" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">8. International Data Transfers</h2>
            
            <div class="bg-yellow-50 rounded-xl p-6 mb-6">
                <div class="flex items-center mb-3">
                    <span class="text-yellow-600 text-xl mr-3">🌍</span>
                    <h3 class="text-lg font-semibold">Cross-Border Data Processing</h3>
                </div>
                <p class="text-gray-700">
                    Some of our service providers and partners are located outside Nigeria. When we transfer your data internationally, we ensure appropriate safeguards are in place.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-white border-2 border-blue-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4">🛡️ Safeguards We Use</h3>
                    <ul class="space-y-2 text-gray-600 text-sm">
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">•</span>
                            <span>Standard Contractual Clauses (SCCs)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">•</span>
                            <span>Adequacy decisions by Nigerian authorities</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">•</span>
                            <span>Binding Corporate Rules (BCRs)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">•</span>
                            <span>Certification schemes and codes of conduct</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-white border-2 border-green-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4">🌐 Our International Partners</h3>
                    <ul class="space-y-2 text-gray-600 text-sm">
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2 mt-1">•</span>
                            <span><strong>Cloud Services:</strong> AWS (Ireland), Google Cloud (EU)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2 mt-1">•</span>
                            <span><strong>Analytics:</strong> Google Analytics (US - Privacy Shield)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2 mt-1">•</span>
                            <span><strong>Support Tools:</strong> Zendesk (US), Intercom (EU)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2 mt-1">•</span>
                            <span><strong>Email Services:</strong> SendGrid (US), Mailchimp (US)</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-red-50 rounded-xl p-6 mt-6">
                <h3 class="text-lg font-semibold mb-3">🚨 Your Rights for International Transfers</h3>
                <p class="text-gray-700 mb-4">
                    You have the right to obtain information about international transfers and request copies of the safeguards we have in place. You can also object to transfers in certain circumstances.
                </p>
                <button class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                    Request Transfer Information
                </button>
            </div>
        </div>

        <!-- Section 9: Children's Privacy -->
        <div id="children-privacy" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">9. Children's Privacy</h2>
            
            <div class="bg-red-50 rounded-xl p-6 mb-6">
                <div class="flex items-center mb-3">
                    <span class="text-red-600 text-xl mr-3">👶</span>
                    <h3 class="text-lg font-semibold">Age Restrictions</h3>
                </div>
                <p class="text-gray-700">
                    <strong>ChooseChow is not intended for children under 18 years of age.</strong> We do not knowingly collect personal information from children under 18 without parental consent.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-white border-2 border-orange-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4">🛡️ Our Commitment</h3>
                    <ul class="space-y-2 text-gray-600 text-sm">
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">•</span>
                            <span>Age verification during account creation</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">•</span>
                            <span>Immediate deletion of underage accounts</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">•</span>
                            <span>No targeted advertising to minors</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">•</span>
                            <span>Regular monitoring and compliance checks</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-white border-2 border-blue-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4">👨‍👩‍👧‍👦 For Parents</h3>
                    <p class="text-gray-600 text-sm mb-3">
                        If you believe your child has provided us with personal information, please contact us immediately:
                    </p>
                    <ul class="space-y-2 text-gray-600 text-sm">
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2 mt-1">•</span>
                            <span>Email: <strong>privacy@choosechow.ng</strong></span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2 mt-1">•</span>
                            <span>Phone: <strong>+234 901 234 5680</strong></span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2 mt-1">•</span>
                            <span>We will delete the information within 48 hours</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-yellow-50 rounded-xl p-6 mt-6">
                <h3 class="text-lg font-semibold mb-3">👨‍👩‍👧 Family Accounts</h3>
                <p class="text-gray-700 mb-4">
                    Adults can create family accounts to order food for their households, including minors. In these cases:
                </p>
                <ul class="text-gray-600 space-y-2">
                    <li class="flex items-center">
                        <span class="text-green-500 mr-3">•</span>
                        <span>The adult account holder is responsible for all data</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-green-500 mr-3">•</span>
                        <span>Dietary preferences can be set for family members</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-green-500 mr-3">•</span>
                        <span>No separate accounts are created for minors</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-green-500 mr-3">•</span>
                        <span>All communications go through the adult account holder</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Section 10: Policy Changes -->
        <div id="policy-changes" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">10. Changes to This Privacy Policy</h2>
            
            <div class="bg-blue-50 rounded-xl p-6 mb-6">
                <div class="flex items-center mb-3">
                    <span class="text-blue-600 text-xl mr-3">🔄</span>
                    <h3 class="text-lg font-semibold">How We Handle Updates</h3>
                </div>
                <p class="text-gray-700">
                    We may update this Privacy Policy from time to time to reflect changes in our practices, technology, legal requirements, or other factors.
                </p>
            </div>

            <div class="space-y-6">
                <div class="bg-white border-l-4 border-green-500 p-6">
                    <h3 class="font-semibold mb-3">📢 How We Notify You</h3>
                    <ul class="space-y-2 text-gray-600 text-sm">
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">•</span>
                            <span><strong>Email Notification:</strong> For significant changes that affect your rights</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">•</span>
                            <span><strong>Website Banner:</strong> Prominent notice on our homepage</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">•</span>
                            <span><strong>In-App Notification:</strong> Alert when you next use our mobile app</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">•</span>
                            <span><strong>30-Day Notice:</strong> Advance warning for material changes</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-white border-l-4 border-blue-500 p-6">
                    <h3 class="font-semibold mb-3">📋 Types of Changes</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-medium text-green-600 mb-2">Minor Changes:</h4>
                            <ul class="text-gray-600 text-sm space-y-1">
                                <li>• Clarifications and corrections</li>
                                <li>• Contact information updates</li>
                                <li>• Minor process improvements</li>
                                <li>• Formatting and readability</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium text-red-600 mb-2">Material Changes:</h4>
                            <ul class="text-gray-600 text-sm space-y-1">
                                <li>• New data collection practices</li>
                                <li>• Changes to data sharing</li>
                                <li>• New purposes for data use</li>
                                <li>• Changes to your rights</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-white border-l-4 border-yellow-500 p-6">
                    <h3 class="font-semibold mb-3">⏰ Version History</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <div>
                                <span class="font-medium">Version 2.1</span>
                                <span class="text-gray-600 text-sm ml-2">Current</span>
                            </div>
                            <div class="text-gray-600 text-sm">December 15, 2024</div>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <div>
                                <span class="font-medium">Version 2.0</span>
                                <span class="text-gray-600 text-sm ml-2">Major update</span>
                            </div>
                            <div class="text-gray-600 text-sm">September 1, 2024</div>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <div>
                                <span class="font-medium">Version 1.0</span>
                                <span class="text-gray-600 text-sm ml-2">Initial version</span>
                            </div>
                            <div class="text-gray-600 text-sm">January 1, 2024</div>
                        </div>
                    </div>
                    <button class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors mt-4">
                        View Full Version History
                    </button>
                </div>
            </div>
        </div>

        <!-- Section 11: Contact Information -->
        <div id="contact-us" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">11. Contact Information</h2>
            
            <div class="bg-red-50 rounded-xl p-6 mb-6">
                <div class="flex items-center mb-3">
                    <span class="text-red-600 text-xl mr-3">📞</span>
                    <h3 class="text-lg font-semibold">Questions About This Policy?</h3>
                </div>
                <p class="text-gray-700">
                    If you have any questions, concerns, or requests regarding this Privacy Policy or our data practices, please don't hesitate to contact us.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <div class="bg-white border-2 border-blue-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4">🏢 Data Controller</h3>
                        <div class="space-y-2 text-gray-600">
                            <p><strong>ChooseChow Nigeria Limited</strong></p>
                            <p>Plot 123, Admiralty Way</p>
                            <p>Lekki Phase 1, Lagos State</p>
                            <p>Nigeria</p>
                            <p><strong>Registration:</strong> RC 1234567</p>
                        </div>
                    </div>

                    <div class="bg-white border-2 border-green-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4">👨‍💼 Data Protection Officer</h3>
                        <div class="space-y-2 text-gray-600">
                            <p><strong>Adebayo Ogundimu</strong></p>
                            <p>Chief Privacy Officer</p>
                            <p>📧 dpo@choosechow.ng</p>
                            <p>📞 +234 901 234 5680</p>
                            <p>🕒 Mon-Fri: 9am-5pm WAT</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white border-2 border-purple-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4">📧 Contact Methods</h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <span class="text-purple-600 mr-3">📧</span>
                                <div>
                                    <div class="font-medium">General Privacy Inquiries</div>
                                    <div class="text-gray-600 text-sm">privacy@choosechow.ng</div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <span class="text-blue-600 mr-3">🚨</span>
                                <div>
                                    <div class="font-medium">Data Breach Reports</div>
                                    <div class="text-gray-600 text-sm">security@choosechow.ng</div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <span class="text-green-600 mr-3">⚖️</span>
                                <div>
                                    <div class="font-medium">Legal & Compliance</div>
                                    <div class="text-gray-600 text-sm">legal@choosechow.ng</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border-2 border-yellow-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4">🏛️ Regulatory Authority</h3>
                        <div class="space-y-2 text-gray-600 text-sm">
                            <p>If you're not satisfied with our response, you can contact:</p>
                            <p><strong>Nigeria Data Protection Commission (NDPC)</strong></p>
                            <p>📧 info@ndpc.gov.ng</p>
                            <p>📞 +234 9 461 9368</p>
                            <p>🌐 www.ndpc.gov.ng</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-6 mt-8">
                <h3 class="text-lg font-semibold mb-4">⏱️ Response Times</h3>
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">24 hrs</div>
                        <div class="text-gray-600 text-sm">Acknowledgment</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">7 days</div>
                        <div class="text-gray-600 text-sm">Simple Requests</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">30 days</div>
                        <div class="text-gray-600 text-sm">Complex Requests</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-red-600 to-orange-600">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-4xl font-bold text-white mb-6">Your Privacy Matters to Us</h2>
        <p class="text-xl text-red-100 mb-8">
            We're committed to protecting your personal information and being transparent about our practices. 
            If you have any questions, we're here to help.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button class="bg-white text-red-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-colors">
                📧 Contact Privacy Team
            </button>
            <button class="bg-red-800 hover:bg-red-900 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors">
                ⚙️ Manage My Data
            </button>
        </div>
    </div>
</section>
@endsection
