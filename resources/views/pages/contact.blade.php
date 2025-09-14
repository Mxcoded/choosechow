@extends('layouts.master')

@section('title', 'Contact Us - ChooseChow')

@section('content')
<!-- Contact Hero Section -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h1 class="text-5xl font-bold text-gray-900 mb-6">
            Get in <span class="gradient-text">Touch</span>
        </h1>
        <p class="text-xl text-gray-600 mb-8">
            We're here to help! Whether you have questions, need support, or want to share feedback, 
            our team is ready to assist you.
        </p>
        <div class="text-6xl mb-8">📞</div>
    </div>
</section>

<!-- Contact Methods -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid md:grid-cols-3 gap-8 mb-16">
            
            <!-- Phone Support -->
            <div class="bg-white rounded-xl p-8 text-center hover-scale">
                <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl">📞</span>
                </div>
                <h3 class="text-xl font-semibold mb-4">Phone Support</h3>
                <p class="text-gray-600 mb-4">
                    Speak directly with our support team for immediate assistance.
                </p>
                <div class="space-y-2">
                    <div class="font-semibold text-lg">+234 901 234 5678</div>
                    <div class="text-gray-600 text-sm">Mon-Fri: 8am-8pm WAT</div>
                    <div class="text-gray-600 text-sm">Sat-Sun: 10am-6pm WAT</div>
                </div>
            </div>

            <!-- Email Support -->
            <div class="bg-white rounded-xl p-8 text-center hover-scale">
                <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl">📧</span>
                </div>
                <h3 class="text-xl font-semibold mb-4">Email Support</h3>
                <p class="text-gray-600 mb-4">
                    Send us a detailed message and we'll respond within 24 hours.
                </p>
                <div class="space-y-2">
                    <div class="font-semibold">support@choosechow.ng</div>
                    <div class="text-gray-600 text-sm">Response time: 2-24 hours</div>
                    <div class="text-gray-600 text-sm">Available 24/7</div>
                </div>
            </div>

            <!-- Live Chat -->
            <div class="bg-white rounded-xl p-8 text-center hover-scale">
                <div class="bg-red-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl">💬</span>
                </div>
                <h3 class="text-xl font-semibold mb-4">Live Chat</h3>
                <p class="text-gray-600 mb-4">
                    Get instant help through our live chat feature on the website.
                </p>
                <div class="space-y-2">
                    <button class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                        Start Chat
                    </button>
                    <div class="text-gray-600 text-sm">Average response: 2 minutes</div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Contact Form -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Send Us a Message</h2>
            <p class="text-lg text-gray-600">
                Fill out the form below and we'll get back to you as soon as possible.
            </p>
        </div>

        <div class="bg-gray-50 rounded-2xl p-8">
            <form class="space-y-6">
                <!-- Contact Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">What can we help you with?</label>
                    <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option>General Inquiry</option>
                        <option>Chef Support</option>
                        <option>Technical Issue</option>
                        <option>Order Problem</option>
                        <option>Business Partnership</option>
                        <option>Feedback & Suggestions</option>
                    </select>
                </div>

                <!-- Name and Email -->
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                        <input type="text" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter your full name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input type="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter your email">
                    </div>
                </div>

                <!-- Phone and Subject -->
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter your phone number">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                        <input type="text" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Brief subject line">
                    </div>
                </div>

                <!-- Message -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                    <textarea required rows="6" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Please provide details about your inquiry..."></textarea>
                </div>

                <!-- Priority -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority Level</label>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="priority" value="low" class="text-red-600 focus:ring-red-500" checked>
                            <span class="ml-2">🟢 Low - General inquiry</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="priority" value="medium" class="text-red-600 focus:ring-red-500">
                            <span class="ml-2">🟡 Medium - Need assistance</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="priority" value="high" class="text-red-600 focus:ring-red-500">
                            <span class="ml-2">🔴 High - Urgent issue</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors">
                        📤 Send Message
                    </button>
                    <p class="text-gray-600 text-sm mt-3">
                        We typically respond within 2-24 hours depending on the inquiry type.
                    </p>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
            <p class="text-lg text-gray-600">
                Quick answers to common questions. Can't find what you're looking for? Contact us!
            </p>
        </div>

        <div class="space-y-6">
            
            <!-- FAQ Item 1 -->
            <div class="bg-white rounded-xl p-6 hover-scale">
                <h3 class="text-lg font-semibold mb-3 flex items-center">
                    <span class="text-red-600 mr-3">❓</span>
                    How do I place an order?
                </h3>
                <p class="text-gray-600 ml-8">
                    Simply browse our chef profiles, select your favorite dishes, add them to your cart, 
                    and proceed to checkout. You can schedule delivery for now or later.
                </p>
            </div>

            <!-- FAQ Item 2 -->
            <div class="bg-white rounded-xl p-6 hover-scale">
                <h3 class="text-lg font-semibold mb-3 flex items-center">
                    <span class="text-red-600 mr-3">❓</span>
                    What are your delivery areas?
                </h3>
                <p class="text-gray-600 ml-8">
                    We currently serve Lagos, Abuja, and Port Harcourt with plans to expand to more cities. 
                    Each chef has their specific delivery zones within these areas.
                </p>
            </div>

            <!-- FAQ Item 3 -->
            <div class="bg-white rounded-xl p-6 hover-scale">
                <h3 class="text-lg font-semibold mb-3 flex items-center">
                    <span class="text-red-600 mr-3">❓</span>
                    How do I become a chef partner?
                </h3>
                <p class="text-gray-600 ml-8">
                    Visit our "Become a Chef" page to learn about requirements and apply. We verify all chefs 
                    for food safety and quality before they can start serving customers.
                </p>
            </div>

            <!-- FAQ Item 4 -->
            <div class="bg-white rounded-xl p-6 hover-scale">
                <h3 class="text-lg font-semibold mb-3 flex items-center">
                    <span class="text-red-600 mr-3">❓</span>
                    What payment methods do you accept?
                </h3>
                <p class="text-gray-600 ml-8">
                    We accept bank transfers, card payments, mobile money, and cash on delivery in select areas. 
                    All online payments are secure and encrypted.
                </p>
            </div>

            <!-- FAQ Item 5 -->
            <div class="bg-white rounded-xl p-6 hover-scale">
                <h3 class="text-lg font-semibold mb-3 flex items-center">
                    <span class="text-red-600 mr-3">❓</span>
                    What if I'm not satisfied with my order?
                </h3>
                <p class="text-gray-600 ml-8">
                    Customer satisfaction is our priority. Contact us within 2 hours of delivery if there's 
                    an issue, and we'll work with you and the chef to resolve it promptly.
                </p>
            </div>

        </div>

        <div class="text-center mt-8">
            <p class="text-gray-600 mb-4">Still have questions?</p>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                📚 View All FAQs
            </button>
        </div>
    </div>
</section>

<!-- Office Information -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid lg:grid-cols-2 gap-12">
            
            <!-- Office Details -->
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Visit Our Office</h2>
                <div class="space-y-6">
                    <div class="flex items-start">
                        <span class="text-red-600 text-xl mr-4 mt-1">📍</span>
                        <div>
                            <h3 class="font-semibold mb-2">Address</h3>
                            <p class="text-gray-600">
                                ChooseChow Nigeria Limited<br>
                                Plot 123, Admiralty Way<br>
                                Lekki Phase 1, Lagos State<br>
                                Nigeria
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <span class="text-red-600 text-xl mr-4 mt-1">🕒</span>
                        <div>
                            <h3 class="font-semibold mb-2">Office Hours</h3>
                            <div class="text-gray-600 space-y-1">
                                <p>Monday - Friday: 8:00 AM - 6:00 PM</p>
                                <p>Saturday: 10:00 AM - 4:00 PM</p>
                                <p>Sunday: Closed</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <span class="text-red-600 text-xl mr-4 mt-1">📞</span>
                        <div>
                            <h3 class="font-semibold mb-2">Emergency Contact</h3>
                            <p class="text-gray-600">
                                For urgent order issues outside business hours:<br>
                                <span class="font-semibold">+234 901 234 5679</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Media & Additional Contact -->
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Connect With Us</h2>
                
                <!-- Social Media -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4">Follow Us on Social Media</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="#" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <span class="text-2xl mr-3">📘</span>
                            <div>
                                <div class="font-semibold">Facebook</div>
                                <div class="text-gray-600 text-sm">@ChooseChowNG</div>
                            </div>
                        </a>
                        <a href="#" class="flex items-center p-4 bg-pink-50 rounded-lg hover:bg-pink-100 transition-colors">
                            <span class="text-2xl mr-3">📷</span>
                            <div>
                                <div class="font-semibold">Instagram</div>
                                <div class="text-gray-600 text-sm">@choosechow_ng</div>
                            </div>
                        </a>
                        <a href="#" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <span class="text-2xl mr-3">🐦</span>
                            <div>
                                <div class="font-semibold">Twitter</div>
                                <div class="text-gray-600 text-sm">@ChooseChowNG</div>
                            </div>
                        </a>
                        <a href="#" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                            <span class="text-2xl mr-3">💬</span>
                            <div>
                                <div class="font-semibold">WhatsApp</div>
                                <div class="text-gray-600 text-sm">+234 901 234 5678</div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Newsletter -->
                <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-4">Stay Updated</h3>
                    <p class="text-gray-600 mb-4">
                        Subscribe to our newsletter for updates, new chef announcements, and special offers.
                    </p>
                    <div class="flex gap-3">
                        <input type="email" placeholder="Enter your email" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <button class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                            Subscribe
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-red-600 to-orange-600">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-4xl font-bold text-white mb-6">We're Here to Help!</h2>
        <p class="text-xl text-red-100 mb-8">
            Don't hesitate to reach out. Our friendly team is always ready to assist you with any questions or concerns.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button class="bg-white text-red-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-colors">
                📞 Call Us Now
            </button>
            <button class="bg-red-800 hover:bg-red-900 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors">
                💬 Start Live Chat
            </button>
        </div>
    </div>
</section>
@endsection