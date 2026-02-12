<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/img/choosechowlogo.png') }}">
    
    <title>@yield('title', 'ChooseChow - Connect with Amazing Home Chefs')</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Smooth Scrolling */
        html { scroll-behavior: smooth; }
        
        /* Fade In Animation */
        .fade-in { animation: fadeIn 0.8s ease-in forwards; opacity: 0; transform: translateY(30px); }
        @keyframes fadeIn { to { opacity: 1; transform: translateY(0); } }
        
        /* Hover Effects */
        .hover-scale { transition: transform 0.3s ease; }
        .hover-scale:hover { transform: scale(1.05); }
        
        /* Text Gradients */
        .gradient-text { 
            background: linear-gradient(135deg, #dc2626, #ea580c); 
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent; 
        }
        
        /* Button Ripple Effect */
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

        /* Mobile Menu Transition */
        .mobile-menu-enter { opacity: 0; transform: scale(0.95); }
        .mobile-menu-enter-active { opacity: 1; transform: scale(1); transition: all 0.2s ease-out; }
        
        @yield('styles')
    </style>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    @include('layouts.header')

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <img src="{{ asset('storage/img/choosechowlogo.png') }}" alt="ChooseChow Logo" 
                             class="h-12 w-12 rounded-full border-2 border-gray-700">
                        <span class="text-2xl font-bold tracking-tight">ChooseChow</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">
                        Connecting food lovers with amazing home chefs across Nigeria. 
                        Experience the taste of authentic, home-cooked meals delivered to your doorstep.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-red-600 hover:text-white transition-all">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-pink-600 hover:text-white transition-all">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-blue-400 hover:text-white transition-all">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-6 text-red-500">For Customers</h3>
                    <ul class="space-y-3 text-gray-400 text-sm">
                        <li><a href="{{ route('chef.index') }}" class="hover:text-white hover:translate-x-1 transition-all inline-block">Find Chefs</a></li>
                        <li><a href="{{ route('subscriptions.plans') }}" class="hover:text-white hover:translate-x-1 transition-all inline-block">Meal Subscriptions</a></li>
                        <li><a href="{{ route('how-it-works') }}" class="hover:text-white hover:translate-x-1 transition-all inline-block">How It Works</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white hover:translate-x-1 transition-all inline-block">Customer Support</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-6 text-red-500">For Chefs</h3>
                    <ul class="space-y-3 text-gray-400 text-sm">
                        <li><a href="{{ route('register') }}" class="hover:text-white hover:translate-x-1 transition-all inline-block">Become a Chef</a></li>
                        <li><a href="#" class="hover:text-white hover:translate-x-1 transition-all inline-block">Chef Resources</a></li>
                        <li><a href="#" class="hover:text-white hover:translate-x-1 transition-all inline-block">Success Stories</a></li>
                        <li><a href="#" class="hover:text-white hover:translate-x-1 transition-all inline-block">Partner Support</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-6 text-red-500">Contact Us</h3>
                    <ul class="space-y-4 text-gray-400 text-sm">
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-envelope mt-1 text-red-500"></i>
                            <span>hello@choosechow.com</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-phone mt-1 text-red-500"></i>
                            <span>+234-800-CHOOSE</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-map-marker-alt mt-1 text-red-500"></i>
                            <span>Abuja, Nigeria</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center text-sm tdark:text-gray-300">
                <p>&copy; {{ date('Y') }} ChooseChow. All rights reserved.</p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="{{ route('privacy.policy') }}" class="hover:text-white transition-colors">Privacy</a>
                    <a href="{{ route('terms.of.service') }}" class="hover:text-white transition-colors">Terms</a>
                    <a href="#" class="hover:text-white transition-colors">Cookies</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Intersection Observer for Fade In ---
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationPlayState = 'running';
                        entry.target.classList.remove('opacity-0'); 
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.fade-in').forEach(el => {
                // el.style.animationPlayState = 'paused'; // Optional: Pause until in view
                observer.observe(el);
            });

            // --- Button Ripple Effect ---
            document.querySelectorAll('button').forEach(button => {
                button.addEventListener('click', function(e) {
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
                    setTimeout(() => ripple.remove(), 600);
                });
            });
        });
    </script>

    @yield('scripts')
</body>
</html>