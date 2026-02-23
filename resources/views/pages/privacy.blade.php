@extends('layouts.app')

@section('title', 'Privacy Policy - ChooseChow')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12 min-h-[60vh] dark:bg-dark-base">
    
    {{-- Top Back Link --}}
    <div class="mb-8">
        <a href="{{ route('welcome') }}" class="text-accent hover:text-accent-hover font-bold flex items-center transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Back to Home
        </a>
    </div>

    <h1 class="text-4xl font-extrabold text-gray-900 dark:text-content-primary mb-2">Privacy Policy</h1>
    <p class="text-gray-500 dark:text-content-secondary mb-8">Last updated: {{ date('F Y') }}</p>

    <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-content-secondary space-y-6">
        <section>
            <h3 class="text-xl font-bold text-gray-900 dark:text-content-primary mb-2">1. Information We Collect</h3>
            <p>We collect information you provide directly to us, such as your name, email address, phone number, and delivery address when you create an account or place an order.</p>
        </section>

        <section>
            <h3 class="text-xl font-bold text-gray-900 dark:text-content-primary mb-2">2. How We Use Your Information</h3>
            <p>We use your information to facilitate order delivery, communicate with you about your orders, improve our platform, and ensure the safety of our community.</p>
        </section>

        <section>
            <h3 class="text-xl font-bold text-gray-900 dark:text-content-primary mb-2">3. Data Security</h3>
            <p>We implement appropriate technical and organizational measures to protect your personal data against unauthorized access, alteration, disclosure, or destruction.</p>
        </section>
    </div>

    {{-- Bottom Action Button --}}
    <div class="mt-12 pt-8 border-t border-gray-100 dark:border-dark-border">
        <a href="{{ route('welcome') }}" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-bold rounded-full text-white bg-accent hover:bg-accent-hover transition-transform transform hover:-translate-y-0.5 shadow-md">
            Return to Homepage
        </a>
    </div>
</div>
@endsection