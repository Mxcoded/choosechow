<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/img/choosechowlogo.png') }}">
    <title>@yield('title', 'ChooseChow - Connect with Amazing Home Chefs')</title>
    <link  rel="stylesheet"  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .fade-in { animation: fadeIn 0.8s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .hover-scale { transition: transform 0.3s ease; }
        .hover-scale:hover { transform: scale(1.05); }
        .gradient-text { background: linear-gradient(135deg, #dc2626, #ea580c); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        button { position: relative; overflow: hidden; }
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }
        @keyframes ripple-animation {
            to { transform: scale(4); opacity: 0; }
        }
        @yield('styles')
    </style>
</head>
<body class="bg-gradient-to-br from-red-50 to-orange-50 min-h-screen">
@include('layouts.header')

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-6">
                        <img src="{{ asset('storage/img/choosechowlogo.png') }}" alt="ChooseChow Logo" 
                         class="h-14 w-14 rounded-full shadow-md transition-transform duration-300 hover:scale-105">
                        <span class="text-2xl font-bold">ChooseChow</span>
                    </div>
                    <p class="text-gray-400 mb-4">Connecting food lovers with amazing home chefs across Nigeria. Fresh, delicious, home-cooked meals delivered to your door.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><span class="fab fa-facebook-f"></span></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><span class="fab fa-instagram"></span></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><span class="fab fa-twitter"></span></a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-6">For Customers</h3>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="{{ route('chefs.index') }}" class="hover:text-white transition-colors">Find Chefs</a></li>
                        <li><a href="{{ route('subscriptions.index') }}" class="hover:text-white transition-colors">Meal Subscriptions</a></li>
                        <li><a href="{{ route('how-it-works') }}" class="hover:text-white transition-colors">How It Works</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Customer Support</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-6">For Chefs</h3>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Become a Chef</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Chef Resources</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Success Stories</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Chef Support</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-6">Contact Us</h3>
                    <ul class="space-y-3 text-gray-400">
                        <li><i class="fas fa-envelope"></i> hello@choosechow.com</li>
                        <li><i class="fas fa-phone"></i> +234-800-CHOOSE</li>
                        <li><i class="fas fa-map-marker-alt"></i> Nigeria</li>
                        <li><i class="fas fa-clock"></i> 24/7 Customer Support</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400">&copy; 2024 ChooseChow. All rights reserved.</p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="{{ route('privacy.policy') }}" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a>
                    <a href="{{ route('terms.of.service') }}" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a>
                    <a href="{{ route('privacy.policy') }}" class="text-gray-400 hover:text-white transition-colors">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Add smooth scrolling and interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Add fade-in animation on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe all fade-in elements
            document.querySelectorAll('.fade-in').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                el.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
                observer.observe(el);
            });

            // Add click effects to buttons
            document.querySelectorAll('button').forEach(button => {
                button.addEventListener('click', function(e) {
                    // Create ripple effect
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';
                    ripple.classList.add('ripple');
                    
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });

        @yield('scripts')
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'97edaafa27c7ef37',t:'MTc1NzgyOTY2Ny4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
