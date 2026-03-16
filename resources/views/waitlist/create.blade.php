@extends('layouts.app')

@section('title', 'Join the Waitlist - ChooseChow')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-chow-cream-50 to-chow-cream-100 dark:from-dark-base dark:to-dark-section py-12">
    <div class="max-w-xl mx-auto px-4">
        
        {{-- Progress Steps --}}
        <div class="flex items-center justify-center mb-8">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-accent text-white flex items-center justify-center font-bold">1</div>
                <div class="w-16 h-1 bg-chow-cream-300 dark:bg-dark-border mx-2"></div>
                <div class="w-10 h-10 rounded-full bg-chow-cream-300 dark:bg-dark-border text-chow-brown-400 dark:text-content-secondary flex items-center justify-center font-bold">2</div>
            </div>
        </div>

        {{-- Form Card --}}
        <div class="bg-white dark:bg-dark-card rounded-2xl shadow-xl p-8 border border-chow-cream-200 dark:border-dark-border">
            
            {{-- Header --}}
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-chow-brown-800 dark:text-content-primary mb-2">Join the Waitlist</h1>
                <p class="text-chow-brown-600 dark:text-content-secondary">Be first to know when we launch in your area</p>
                
                @if($referrer)
                    <div class="mt-4 p-3 bg-accent/10 dark:bg-accent/20 rounded-lg">
                        <p class="text-sm text-accent font-medium">
                            <i class="fas fa-user-friends mr-1"></i> Referred by {{ $referrer->name }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Error Messages --}}
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg text-sm">
                    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('waitlist.store') }}" method="POST" x-data="{ 
                role: '{{ old('role', request('role') === 'vendor' ? 'vendor' : 'food_lover') }}',
                showDiscovery: {{ $hasUtm ? 'false' : 'true' }}
            }">
                @csrf
                
                {{-- Referrer Token (hidden) --}}
                @if($referrer)
                    <input type="hidden" name="ref_token" value="{{ $referrer->referral_token }}">
                @endif

                {{-- Name --}}
                <div class="mb-5">
                    <label class="block text-sm font-bold text-chow-brown-700 dark:text-content-secondary mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-3 rounded-xl bg-chow-cream-50 dark:bg-dark-section border border-chow-cream-200 dark:border-dark-border text-chow-brown-800 dark:text-content-primary focus:ring-2 focus:ring-accent focus:border-accent outline-none transition"
                        placeholder="Enter your full name">
                </div>

                {{-- Email --}}
                <div class="mb-5">
                    <label class="block text-sm font-bold text-chow-brown-700 dark:text-content-secondary mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 rounded-xl bg-chow-cream-50 dark:bg-dark-section border border-chow-cream-200 dark:border-dark-border text-chow-brown-800 dark:text-content-primary focus:ring-2 focus:ring-accent focus:border-accent outline-none transition"
                        placeholder="you@example.com">
                </div>

                {{-- Phone --}}
                <div class="mb-5">
                    <label class="block text-sm font-bold text-chow-brown-700 dark:text-content-secondary mb-2">
                        Phone Number <span class="text-chow-brown-400">(Optional)</span>
                    </label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-3 rounded-xl bg-chow-cream-50 dark:bg-dark-section border border-chow-cream-200 dark:border-dark-border text-chow-brown-800 dark:text-content-primary focus:ring-2 focus:ring-accent focus:border-accent outline-none transition"
                        placeholder="+234 800 000 0000">
                </div>

                {{-- Neighborhood --}}
                <div class="mb-5">
                    <label class="block text-sm font-bold text-chow-brown-700 dark:text-content-secondary mb-2">
                        Your Neighborhood <span class="text-red-500">*</span>
                    </label>
                    <select name="neighborhood_id" required
                        class="w-full px-4 py-3 rounded-xl bg-chow-cream-50 dark:bg-dark-section border border-chow-cream-200 dark:border-dark-border text-chow-brown-800 dark:text-content-primary focus:ring-2 focus:ring-accent focus:border-accent outline-none transition">
                        <option value="">Select your area</option>
                        @foreach($neighborhoods as $neighborhood)
                            <option value="{{ $neighborhood->id }}" {{ old('neighborhood_id') == $neighborhood->id ? 'selected' : '' }}>
                                {{ $neighborhood->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Role Selection --}}
                <div class="mb-5">
                    <label class="block text-sm font-bold text-chow-brown-700 dark:text-content-secondary mb-3">
                        I am a... <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="role" value="food_lover" x-model="role" class="sr-only peer" {{ old('role', request('role') !== 'vendor' ? 'checked' : '') }}>
                            <div class="p-4 rounded-xl border-2 border-chow-cream-200 dark:border-dark-border peer-checked:border-accent peer-checked:bg-accent/5 dark:peer-checked:bg-accent/10 transition-all text-center">
                                <i class="fas fa-utensils text-2xl text-accent mb-2"></i>
                                <p class="font-bold text-chow-brown-800 dark:text-content-primary">Food Lover</p>
                                <p class="text-xs text-chow-brown-500 dark:text-content-secondary">I want to order</p>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="role" value="vendor" x-model="role" class="sr-only peer" {{ old('role', request('role')) === 'vendor' ? 'checked' : '' }}>
                            <div class="p-4 rounded-xl border-2 border-chow-cream-200 dark:border-dark-border peer-checked:border-accent peer-checked:bg-accent/5 dark:peer-checked:bg-accent/10 transition-all text-center">
                                <i class="fas fa-store text-2xl text-accent mb-2"></i>
                                <p class="font-bold text-chow-brown-800 dark:text-content-primary">Vendor</p>
                                <p class="text-xs text-chow-brown-500 dark:text-content-secondary">I want to sell</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Vendor Type (conditional) --}}
                <div x-show="role === 'vendor'" x-transition class="mb-5">
                    <label class="block text-sm font-bold text-chow-brown-700 dark:text-content-secondary mb-2">
                        Vendor Type <span class="text-red-500">*</span>
                    </label>
                    <select name="actor_category_id"
                        class="w-full px-4 py-3 rounded-xl bg-chow-cream-50 dark:bg-dark-section border border-chow-cream-200 dark:border-dark-border text-chow-brown-800 dark:text-content-primary focus:ring-2 focus:ring-accent focus:border-accent outline-none transition"
                        :required="role === 'vendor'">
                        <option value="">Select your vendor type</option>
                        @foreach($actorCategories as $category)
                            <option value="{{ $category->id }}" {{ old('actor_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Discovery Source (if no UTM) --}}
                <div x-show="showDiscovery" x-transition class="mb-6">
                    <label class="block text-sm font-bold text-chow-brown-700 dark:text-content-secondary mb-2">
                        How did you hear about us?
                    </label>
                    <select name="discovery_source"
                        class="w-full px-4 py-3 rounded-xl bg-chow-cream-50 dark:bg-dark-section border border-chow-cream-200 dark:border-dark-border text-chow-brown-800 dark:text-content-primary focus:ring-2 focus:ring-accent focus:border-accent outline-none transition">
                        <option value="">Select an option</option>
                        @foreach($discoverySources as $key => $label)
                            <option value="{{ $key }}" {{ old('discovery_source') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Submit --}}
                <button type="submit" class="w-full py-4 px-6 bg-accent hover:bg-accent-hover text-white font-bold rounded-xl transition-all shadow-lg shadow-accent/30 hover:shadow-accent/50 hover:-translate-y-0.5">
                    <i class="fas fa-arrow-right mr-2"></i> Continue to Step 2
                </button>

                <p class="text-center text-xs text-chow-brown-400 dark:text-content-secondary mt-4">
                    By joining, you agree to our <a href="{{ route('terms') }}" class="text-accent hover:underline">Terms</a> and <a href="{{ route('privacy') }}" class="text-accent hover:underline">Privacy Policy</a>.
                </p>
            </form>
        </div>
    </div>
</div>
@endsection
