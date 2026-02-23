@extends('layouts.app')

@section('title', 'Terms of Service - ChooseChow')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12 min-h-[60vh] dark:bg-dark-base">
    
    {{-- Top Back Link --}}
    <div class="mb-8">
        <a href="{{ route('welcome') }}" class="text-accent hover:text-accent-hover font-bold flex items-center transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Back to Home
        </a>
    </div>

    <h1 class="text-4xl font-extrabold text-gray-900 dark:text-content-primary mb-2">Terms of Service</h1>
    <p class="text-gray-500 dark:text-content-secondary mb-8">Last updated: {{ date('F Y') }}</p>

    <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-content-secondary space-y-6">
        <section>
            <h3 class="text-xl font-bold text-gray-900 dark:text-content-primary mb-2">1. Acceptance of Terms</h3>
            <p>By accessing or using the ChooseChow platform, you agree to be bound by these Terms of Service. If you disagree with any part of the terms, you may not access our services.</p>
        </section>

        <section>
            <h3 class="text-xl font-bold text-gray-900 dark:text-content-primary mb-2">2. Services</h3>
            <p>ChooseChow connects users ("Eaters") with independent home chefs ("Chefs") for the purchase and delivery of homemade meals. We act as an intermediary platform and are not the direct supplier of the food.</p>
        </section>

        <section>
            <h3 class="text-xl font-bold text-gray-900 dark:text-content-primary mb-2">3. User Accounts</h3>
            <p>To use certain features, you must register for an account. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</p>
        </section>

        <section>
            <h3 class="text-xl font-bold text-gray-900 dark:text-content-primary mb-2">4. Orders & Payments</h3>
            <p>All orders are subject to acceptance by the Chef. Payments are processed securely through our third-party payment providers. Refunds are handled in accordance with our Refund Policy.</p>
        </section>
    </div>

    {{-- Bottom Action Button --}}
    <div class="mt-12 pt-8 border-t border-gray-100 dark:border-dark-border">
        <a href="{{ route('welcome') }}" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-bold rounded-full text-white bg-accent hover:bg-accent-hover transition-transform transform hover:-translate-y-0.5 shadow-md">
            I Understand, Return Home
        </a>
    </div>
</div>
@endsection