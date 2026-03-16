@extends('layouts.app')

@section('title', 'You\'re on the List! - ChooseChow')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-chow-cream-50 to-chow-cream-100 dark:from-dark-base dark:to-dark-section py-12">
    <div class="max-w-xl mx-auto px-4">

        {{-- Success Card --}}
        <div class="bg-white dark:bg-dark-card rounded-2xl shadow-xl p-8 border border-chow-cream-200 dark:border-dark-border text-center">
            
            {{-- Success Icon --}}
            <div class="w-20 h-20 mx-auto bg-chow-fresh-100 dark:bg-chow-fresh-500/20 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-check text-4xl text-chow-fresh-500"></i>
            </div>
            
            {{-- Headline --}}
            <h1 class="text-2xl font-bold text-chow-brown-800 dark:text-content-primary mb-2">
                You're on the List, {{ explode(' ', $signup->name)[0] }}! 🎉
            </h1>
            
            <p class="text-chow-brown-600 dark:text-content-secondary mb-6">
                We'll notify you at <strong class="text-accent">{{ $signup->email }}</strong> when ChooseChow launches in your area.
            </p>

            {{-- Position Badge --}}
            <div class="inline-block bg-accent/10 dark:bg-accent/20 rounded-xl px-6 py-4 mb-8">
                <p class="text-sm text-chow-brown-500 dark:text-content-secondary mb-1">Your position</p>
                <p class="text-4xl font-bold text-accent">#{{ number_format($position) }}</p>
            </div>

            {{-- Referral Section --}}
            <div class="bg-chow-cream-50 dark:bg-dark-section rounded-xl p-6 mb-6">
                <h2 class="text-lg font-bold text-chow-brown-800 dark:text-content-primary mb-2">
                    <i class="fas fa-gift text-accent mr-2"></i> Move Up the Line!
                </h2>
                <p class="text-sm text-chow-brown-600 dark:text-content-secondary mb-4">
                    Share your referral link and get early access when friends join.
                </p>
                
                {{-- Referral Link --}}
                <div class="relative mb-4">
                    <input type="text" readonly value="{{ $signup->referral_link }}" id="referral-link"
                        class="w-full px-4 py-3 pr-24 rounded-xl bg-white dark:bg-dark-card border border-chow-cream-200 dark:border-dark-border text-chow-brown-800 dark:text-content-primary text-sm">
                    <button onclick="copyReferralLink()" class="absolute right-2 top-1/2 -translate-y-1/2 px-4 py-2 bg-accent text-white text-sm font-bold rounded-lg hover:bg-accent-hover transition">
                        <i class="fas fa-copy mr-1"></i> Copy
                    </button>
                </div>

                {{-- Social Share --}}
                <div class="flex justify-center gap-3">
                    <a href="https://wa.me/?text={{ urlencode('Join me on ChooseChow! Get homemade meals delivered from local chefs. ' . $signup->referral_link) }}" target="_blank"
                        class="w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-center hover:bg-green-600 transition">
                        <i class="fab fa-whatsapp text-lg"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode('I just joined the ChooseChow waitlist! Get homemade meals from local chefs. Join me: ' . $signup->referral_link) }}" target="_blank"
                        class="w-10 h-10 rounded-full bg-black text-white flex items-center justify-center hover:bg-gray-800 transition">
                        <i class="fab fa-x-twitter text-lg"></i>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($signup->referral_link) }}" target="_blank"
                        class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition">
                        <i class="fab fa-facebook-f text-lg"></i>
                    </a>
                </div>

                {{-- Referral Count --}}
                @if($referralCount > 0)
                    <div class="mt-4 pt-4 border-t border-chow-cream-200 dark:border-dark-border">
                        <p class="text-sm text-chow-brown-600 dark:text-content-secondary">
                            <i class="fas fa-users text-accent mr-1"></i>
                            You've referred <strong class="text-accent">{{ $referralCount }}</strong> {{ Str::plural('person', $referralCount) }}!
                        </p>
                    </div>
                @endif
            </div>

            {{-- What's Next --}}
            <div class="text-left bg-white dark:bg-dark-section rounded-xl p-6 border border-chow-cream-200 dark:border-dark-border">
                <h3 class="font-bold text-chow-brown-800 dark:text-content-primary mb-4">What happens next?</h3>
                <ul class="space-y-3 text-sm text-chow-brown-600 dark:text-content-secondary">
                    <li class="flex items-start">
                        <i class="fas fa-envelope text-accent mt-1 mr-3"></i>
                        <span>Check your email for confirmation</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-bell text-accent-light mt-1 mr-3"></i>
                        <span>We'll notify you when we launch in {{ $signup->neighborhood?->name ?? 'your area' }}</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-gift text-chow-fresh-500 mt-1 mr-3"></i>
                        <span>Early access and exclusive perks for waitlist members</span>
                    </li>
                </ul>
            </div>

            {{-- Back to Home --}}
            <a href="{{ route('welcome') }}" class="inline-block mt-6 text-accent hover:text-accent-hover font-medium">
                <i class="fas fa-arrow-left mr-1"></i> Back to Home
            </a>
        </div>
    </div>
</div>

<script>
function copyReferralLink() {
    const input = document.getElementById('referral-link');
    input.select();
    document.execCommand('copy');
    
    // Show feedback
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check mr-1"></i> Copied!';
    btn.classList.add('bg-chow-fresh-500');
    btn.classList.remove('bg-accent');
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.classList.remove('bg-chow-fresh-500');
        btn.classList.add('bg-accent');
    }, 2000);
}
</script>
@endsection
