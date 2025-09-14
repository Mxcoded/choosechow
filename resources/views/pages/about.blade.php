@extends('layouts.master')

@section('title', 'About Us - ChooseChow')

@section('content')
<!-- Hero Section -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h1 class="text-5xl font-bold text-gray-900 mb-6">
            About <span class="gradient-text">ChooseChow</span>
        </h1>
        <p class="text-xl text-gray-600 mb-8">
            We're passionate about connecting food lovers with talented home chefs, creating a community 
            where authentic flavors meet modern convenience.
        </p>

        <!-- Centered Logo -->
        <div class="mb-8">
            <img src="{{ asset('storage/img/choosechowlogo.png') }}" alt="ChooseChow Logo"
                 class="h-24 w-24 mx-auto rounded-full shadow-lg transition-transform duration-300 hover:scale-105">
        </div>
    </div>
</section>

<!-- Our Story -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="fade-in">
                <h2 class="text-4xl font-bold text-gray-900 mb-6">Our Story</h2>
                <div class="space-y-6 text-lg text-gray-600">
                    <p>
                        ChooseChow was born from a simple observation: some of the most incredible meals come from 
                        passionate home cooks who pour their heart into every dish. Yet, these talented chefs often 
                        remained hidden gems in their communities.
                    </p>
                    <p>
                        Founded in 2024 in Lagos, Nigeria, we set out to bridge this gap. We wanted to create a 
                        platform where food lovers could discover authentic, home-cooked meals while supporting 
                        local culinary entrepreneurs.
                    </p>
                    <p>
                        Today, we're proud to connect hundreds of verified home chefs with thousands of satisfied 
                        customers across Nigeria, fostering a vibrant community built around the love of great food.
                    </p>
                </div>
            </div>
            <div class="fade-in">
                <div class="bg-gradient-to-br from-red-100 to-orange-100 rounded-2xl p-12 text-center">
                    <div class="text-6xl mb-6">🌟</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Our Impact</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <div class="text-3xl font-bold text-red-600 mb-2">500+</div>
                            <div class="text-gray-600">Home Chefs</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-red-600 mb-2">10K+</div>
                            <div class="text-gray-600">Happy Customers</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-red-600 mb-2">25+</div>
                            <div class="text-gray-600">Cities Served</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-red-600 mb-2">50K+</div>
                            <div class="text-gray-600">Meals Delivered</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid lg:grid-cols-2 gap-16">
            <!-- Mission -->
            <div class="fade-in">
                <div class="bg-red-50 rounded-2xl p-8">
                    <div class="bg-red-100 rounded-full w-16 h-16 flex items-center justify-center mb-6">
                        <span class="text-2xl">🎯</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Our Mission</h3>
                    <p class="text-lg text-gray-600 leading-relaxed">
                        To connect food lovers with talented home chefs, creating a community where authentic, 
                        delicious meals are accessible to everyone while supporting local culinary entrepreneurs 
                        in building sustainable businesses.
                    </p>
                </div>
            </div>

            <!-- Vision -->
            <div class="fade-in">
                <div class="bg-blue-50 rounded-2xl p-8">
                    <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mb-6">
                        <span class="text-2xl">🔮</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Our Vision</h3>
                    <p class="text-lg text-gray-600 leading-relaxed">
                        To become Nigeria's leading platform for home-cooked meal delivery, fostering a vibrant 
                        ecosystem of culinary creativity and cultural exchange that celebrates our diverse food 
                        heritage while embracing innovation.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Values -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Our Core Values</h2>
            <p class="text-xl text-gray-600">The principles that guide everything we do</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Quality First -->
            <div class="text-center fade-in hover-scale">
                <div class="bg-yellow-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6">
                    <span class="text-3xl">⭐</span>
                </div>
                <h3 class="text-xl font-semibold mb-4">Quality First</h3>
                <p class="text-gray-600">
                    We maintain the highest standards for food quality, safety, and chef verification. 
                    Every meal reflects our commitment to excellence.
                </p>
            </div>

            <!-- Community Focused -->
            <div class="text-center fade-in hover-scale">
                <div class="bg-green-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6">
                    <span class="text-3xl">🤝</span>
                </div>
                <h3 class="text-xl font-semibold mb-4">Community Focused</h3>
                <p class="text-gray-600">
                    Building strong connections between chefs and customers in local communities. 
                    We believe food brings people together.
                </p>
            </div>

            <!-- Cultural Celebration -->
            <div class="text-center fade-in hover-scale">
                <div class="bg-purple-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6">
                    <span class="text-3xl">🌍</span>
                </div>
                <h3 class="text-xl font-semibold mb-4">Cultural Celebration</h3>
                <p class="text-gray-600">
                    Promoting and preserving diverse culinary traditions and cultural heritage 
                    through authentic home-cooked meals.
                </p>
            </div>

            <!-- Innovation Driven -->
            <div class="text-center fade-in hover-scale">
                <div class="bg-blue-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6">
                    <span class="text-3xl">🚀</span>
                </div>
                <h3 class="text-xl font-semibold mb-4">Innovation Driven</h3>
                <p class="text-gray-600">
                    Continuously improving our platform to enhance user experience and convenience 
                    while staying true to our core mission.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Meet Our Team -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Meet Our Team</h2>
            <p class="text-xl text-gray-600">The passionate people behind ChooseChow</p>
        </div>

        <div class="grid md:grid-cols-3 gap-12 max-w-5xl mx-auto">
            <!-- CEO -->
            <div class="text-center fade-in hover-scale">
                <div class="bg-gradient-to-br from-blue-400 to-purple-400 rounded-full w-32 h-32 flex items-center justify-center mx-auto mb-6">
                    <span class="text-5xl">👨‍💼</span>
                </div>
                <h3 class="text-xl font-semibold mb-2">Adebayo Johnson</h3>
                <p class="text-red-600 font-medium mb-4">CEO & Founder</p>
                <p class="text-gray-600">
                    Passionate about connecting communities through food and supporting local entrepreneurs. 
                    Previously worked in tech and hospitality sectors.
                </p>
            </div>

            <!-- Head of Chef Relations -->
            <div class="text-center fade-in hover-scale">
                <div class="bg-gradient-to-br from-green-400 to-teal-400 rounded-full w-32 h-32 flex items-center justify-center mx-auto mb-6">
                    <span class="text-5xl">👩‍🍳</span>
                </div>
                <h3 class="text-xl font-semibold mb-2">Fatima Abdullahi</h3>
                <p class="text-red-600 font-medium mb-4">Head of Chef Relations</p>
                <p class="text-gray-600">
                    Experienced chef and culinary consultant dedicated to supporting our chef community. 
                    Ensures quality standards and chef success.
                </p>
            </div>

            <!-- Head of Technology -->
            <div class="text-center fade-in hover-scale">
                <div class="bg-gradient-to-br from-red-400 to-orange-400 rounded-full w-32 h-32 flex items-center justify-center mx-auto mb-6">
                    <span class="text-5xl">👨‍💻</span>
                </div>
                <h3 class="text-xl font-semibold mb-2">Chidi Okonkwo</h3>
                <p class="text-red-600 font-medium mb-4">Head of Technology</p>
                <p class="text-gray-600">
                    Tech enthusiast focused on creating seamless user experiences and platform reliability. 
                    Leads our development and innovation efforts.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Why We Started -->
<section class="py-20">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-2xl p-12">
            <div class="text-5xl mb-6">💡</div>
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Why We Started ChooseChow</h2>
            <div class="text-lg text-gray-600 space-y-4">
                <p>
                    "We noticed that some of the best meals we'd ever had came from friends' homes, family gatherings, 
                    and small local cooks who weren't running restaurants. These talented chefs had incredible skills 
                    but limited ways to share their passion with a broader audience."
                </p>
                <p>
                    "ChooseChow was created to solve this problem - connecting these amazing home chefs with food lovers 
                    who appreciate authentic, freshly prepared meals. We're not just a delivery platform; we're a 
                    community that celebrates culinary creativity and cultural diversity."
                </p>
            </div>
            <div class="mt-8">
                <span class="text-gray-500 italic">- The ChooseChow Team</span>
            </div>
        </div>
    </div>
</section>

<!-- Join Our Community -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="fade-in">
                <h2 class="text-4xl font-bold text-gray-900 mb-6">Join Our Growing Community</h2>
                <p class="text-lg text-gray-600 mb-8">
                    Whether you're a food lover looking for authentic meals or a talented home chef ready to 
                    share your culinary skills, there's a place for you in the ChooseChow community.
                </p>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-600">Connect with like-minded food enthusiasts</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-600">Discover new flavors and cooking styles</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-600">Support local culinary entrepreneurs</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-600">Be part of Nigeria's food revolution</span>
                    </div>
                </div>
            </div>
            <div class="fade-in">
                <div class="bg-gradient-to-br from-red-100 to-orange-100 rounded-2xl p-8 text-center">
                    <div class="text-4xl mb-6">🎉</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Ready to Get Started?</h3>
                    <div class="space-y-4">
                        <a href="{{ route('chefs.index') }}" class="block bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors">
                            🔍 Explore Amazing Chefs
                        </a>
                        <a href="#" class="block bg-white hover:bg-gray-50 text-gray-700 px-8 py-3 rounded-lg font-semibold border-2 border-gray-200 transition-colors">
                            👨‍🍳 Become a Chef Partner
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Us -->
<section class="py-20 bg-gradient-to-r from-red-600 to-orange-600">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-4xl font-bold text-white mb-6">Have Questions? We'd Love to Hear From You</h2>
        <p class="text-xl text-red-100 mb-8">
            Our team is always here to help. Whether you have questions about our platform, 
            need support, or want to share feedback, don't hesitate to reach out.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('contact') }}" class="bg-white text-red-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-colors">
                📧 Contact Our Team
            </a>
            <a href="{{ route('how-it-works') }}" class="bg-red-800 hover:bg-red-900 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors">
                📋 Learn How It Works
            </a>
        </div>
    </div>
</section>
@endsection
