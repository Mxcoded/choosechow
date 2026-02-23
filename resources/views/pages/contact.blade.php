@extends('layouts.app')

@section('title', 'Contact Us - ChooseChow')

@section('content')
<div class="relative bg-gradient-to-br from-chow-cream-50 to-chow-cream-100 dark:from-dark-base dark:to-dark-section overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="text-center mb-16">
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-content-primary">Get in touch</h1>
            <p class="mt-4 text-xl text-gray-500 dark:text-content-secondary">We'd love to hear from you. Here is how you can reach us.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            
            {{-- Contact Info Cards --}}
            <div class="space-y-8">
                {{-- Phone --}}
                <div class="flex items-start bg-white dark:bg-dark-card p-6 rounded-xl shadow-sm border border-gray-100 dark:border-dark-border">
                    <div class="bg-red-100 dark:bg-accent/20 p-3 rounded-full text-red-600 dark:text-accent">
                        <i class="fas fa-phone-alt text-xl"></i>
                    </div>
                    <div class="ml-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-content-primary">Phone</h3>
                        <p class="text-gray-500 dark:text-content-secondary mt-1">Mon-Fri from 8am to 5pm.</p>
                        <p class="text-red-600 dark:text-accent font-bold mt-2">+234 816 041 9132</p>
                    </div>
                </div>

                {{-- Email --}}
                <div class="flex items-start bg-white dark:bg-dark-card p-6 rounded-xl shadow-sm border border-gray-100 dark:border-dark-border">
                    <div class="bg-red-100 dark:bg-accent/20 p-3 rounded-full text-red-600 dark:text-accent">
                        <i class="fas fa-envelope text-xl"></i>
                    </div>
                    <div class="ml-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-content-primary">Email</h3>
                        <p class="text-gray-500 dark:text-content-secondary mt-1">Our friendly team is here to help.</p>
                        <p class="text-red-600 dark:text-accent font-bold mt-2">support@choosechow.com</p>
                    </div>
                </div>

                {{-- Address --}}
                <div class="flex items-start bg-white dark:bg-dark-card p-6 rounded-xl shadow-sm border border-gray-100 dark:border-dark-border">
                    <div class="bg-red-100 dark:bg-accent/20 p-3 rounded-full text-red-600 dark:text-accent">
                        <i class="fas fa-map-marker-alt text-xl"></i>
                    </div>
                    <div class="ml-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-content-primary">Office</h3>
                        <p class="text-gray-500 dark:text-content-secondary mt-1">Come say hello at our office HQ.</p>
                        <p class="text-red-600 dark:text-accent font-bold mt-2">Abuja, Nigeria.</p>
                    </div>
                </div>
            </div>

            {{-- Contact Form --}}
            <div class="bg-white dark:bg-dark-card rounded-2xl shadow-lg p-8 border border-gray-100 dark:border-dark-border">
                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    @if(session('success'))
                        <div class="bg-green-50 text-green-700 p-4 rounded-lg mb-4 text-sm font-bold border border-green-200">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-4 text-sm font-bold border border-red-200">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-content-secondary mb-2">Your Name</label>
                        <input type="text" name="name" required class="w-full px-4 py-3 rounded-lg bg-gray-50 dark:bg-dark-section border border-gray-300 dark:border-dark-border text-gray-900 dark:text-content-primary focus:ring-2 focus:ring-accent outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-content-secondary mb-2">Email Address</label>
                        <input type="email" name="email" required class="w-full px-4 py-3 rounded-lg bg-gray-50 dark:bg-dark-section border border-gray-300 dark:border-dark-border text-gray-900 dark:text-content-primary focus:ring-2 focus:ring-accent outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-content-secondary mb-2">Subject</label>
                        <select name="subject" class="w-full px-4 py-3 rounded-lg bg-gray-50 dark:bg-dark-section border border-gray-300 dark:border-dark-border text-gray-900 dark:text-content-primary focus:ring-2 focus:ring-accent outline-none transition">
                            <option>General Inquiry</option>
                            <option>Become a Chef</option>
                            <option>Order Issue</option>
                            <option>Partnership</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-content-secondary mb-2">Message</label>
                        <textarea name="message" rows="4" required class="w-full px-4 py-3 rounded-lg bg-gray-50 dark:bg-dark-section border border-gray-300 dark:border-dark-border text-gray-900 dark:text-content-primary focus:ring-2 focus:ring-accent outline-none transition"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-accent hover:bg-accent-hover text-white font-bold py-4 rounded-lg shadow-md transition transform hover:-translate-y-1">
                        Send Message
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection