@extends('layouts.app')

@section('title', 'Contact Us - ChooseChow')

@section('content')
<div class="bg-gray-50 dark:bg-gray-900 min-h-screen py-12 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="text-center mb-16">
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white">Get in touch</h1>
            <p class="mt-4 text-xl text-gray-500 dark:text-gray-300">We'd love to hear from you. Here is how you can reach us.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            
            {{-- Contact Info Cards --}}
            <div class="space-y-8">
                {{-- Phone --}}
                <div class="flex items-start bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="bg-red-100 dark:bg-red-900/50 p-3 rounded-full text-red-600 dark:text-red-400">
                        <i class="fas fa-phone-alt text-xl"></i>
                    </div>
                    <div class="ml-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Phone</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">Mon-Fri from 8am to 5pm.</p>
                        <p class="text-red-600 dark:text-red-400 font-bold mt-2">+234 816 041 9132</p>
                    </div>
                </div>

                {{-- Email --}}
                <div class="flex items-start bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="bg-red-100 dark:bg-red-900/50 p-3 rounded-full text-red-600 dark:text-red-400">
                        <i class="fas fa-envelope text-xl"></i>
                    </div>
                    <div class="ml-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Email</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">Our friendly team is here to help.</p>
                        <p class="text-red-600 dark:text-red-400 font-bold mt-2">support@choosechow.com</p>
                    </div>
                </div>

                {{-- Address --}}
                <div class="flex items-start bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="bg-red-100 dark:bg-red-900/50 p-3 rounded-full text-red-600 dark:text-red-400">
                        <i class="fas fa-map-marker-alt text-xl"></i>
                    </div>
                    <div class="ml-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Office</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">Come say hello at our office HQ.</p>
                        <p class="text-red-600 dark:text-red-400 font-bold mt-2">Abuja, Nigeria.</p>
                    </div>
                </div>
            </div>

            {{-- Contact Form --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 border border-gray-100 dark:border-gray-700">
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
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Your Name</label>
                        <input type="text" name="name" required class="w-full px-4 py-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                        <input type="email" name="email" required class="w-full px-4 py-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Subject</label>
                        <select name="subject" class="w-full px-4 py-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 outline-none transition">
                            <option>General Inquiry</option>
                            <option>Become a Chef</option>
                            <option>Order Issue</option>
                            <option>Partnership</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Message</label>
                        <textarea name="message" rows="4" required class="w-full px-4 py-3 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 outline-none transition"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-lg shadow-md transition transform hover:-translate-y-1">
                        Send Message
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection