@extends('layouts.app')

@section('title', 'Quick Survey - ChooseChow Waitlist')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-chow-cream-50 to-chow-cream-100 dark:from-dark-base dark:to-dark-section py-12">
    <div class="max-w-xl mx-auto px-4">
        
        {{-- Progress Steps --}}
        <div class="flex items-center justify-center mb-8">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-chow-fresh-500 text-white flex items-center justify-center font-bold">
                    <i class="fas fa-check"></i>
                </div>
                <div class="w-16 h-1 bg-accent mx-2"></div>
                <div class="w-10 h-10 rounded-full bg-accent text-white flex items-center justify-center font-bold">2</div>
            </div>
        </div>

        {{-- Form Card --}}
        <div class="bg-white dark:bg-dark-card rounded-2xl shadow-xl p-8 border border-chow-cream-200 dark:border-dark-border">
            
            {{-- Header --}}
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-chow-brown-800 dark:text-content-primary mb-2">Quick Survey</h1>
                <p class="text-chow-brown-600 dark:text-content-secondary">Help us understand what you love to eat (optional)</p>
            </div>

            {{-- Error Messages --}}
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg text-sm">
                    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('waitlist.survey.store', $signup->referral_token) }}" method="POST">
                @csrf

                {{-- Favorite Meals --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-chow-brown-700 dark:text-content-secondary mb-2">
                        What are your favorite homemade meals?
                    </label>
                    <p class="text-xs text-chow-brown-400 dark:text-content-secondary mb-3">Enter meal names separated by comma (e.g., Jollof Rice, Egusi Soup, Fried Rice)</p>
                    <input type="text" id="favorite_meals_input"
                        class="w-full px-4 py-3 rounded-xl bg-chow-cream-50 dark:bg-dark-section border border-chow-cream-200 dark:border-dark-border text-chow-brown-800 dark:text-content-primary focus:ring-2 focus:ring-accent focus:border-accent outline-none transition"
                        placeholder="Jollof Rice, Egusi Soup, Suya..."
                        x-data
                        x-on:change="
                            const meals = $el.value.split(',').map(m => m.trim()).filter(m => m);
                            document.querySelectorAll('input[name=\'favorite_meals[]\']').forEach(el => el.remove());
                            meals.forEach(meal => {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'favorite_meals[]';
                                input.value = meal;
                                $el.parentNode.appendChild(input);
                            });
                        ">
                </div>

                {{-- Dietary Preferences --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-chow-brown-700 dark:text-content-secondary mb-3">
                        Any dietary preferences?
                    </label>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $dietaryOptions = ['Vegetarian', 'Vegan', 'Halal', 'Keto', 'Low Carb', 'Gluten Free', 'Nut Free', 'Traditional'];
                        @endphp
                        @foreach($dietaryOptions as $option)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="dietary_preferences[]" value="{{ $option }}" class="sr-only peer">
                                <span class="inline-block px-4 py-2 rounded-full border border-chow-cream-200 dark:border-dark-border text-sm text-chow-brown-600 dark:text-content-secondary peer-checked:bg-accent peer-checked:text-white peer-checked:border-accent transition-all">
                                    {{ $option }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Preferred Price Range --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-chow-brown-700 dark:text-content-secondary mb-3">
                        Preferred price range per meal?
                    </label>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach($priceRanges as $value => $label)
                            <label class="cursor-pointer">
                                <input type="radio" name="preferred_price_range" value="{{ $value }}" class="sr-only peer">
                                <div class="p-3 rounded-xl border border-chow-cream-200 dark:border-dark-border text-center peer-checked:border-accent peer-checked:bg-accent/5 dark:peer-checked:bg-accent/10 transition-all">
                                    <p class="text-sm font-medium text-chow-brown-800 dark:text-content-primary">{{ ucfirst($value) }}</p>
                                    <p class="text-xs text-chow-brown-500 dark:text-content-secondary">{{ str_replace(['Budget (', 'Mid-Range (', 'Premium (', ')'], '', $label) }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Meals Per Week --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-chow-brown-700 dark:text-content-secondary mb-2">
                        How many times per week would you order?
                    </label>
                    <select name="meals_per_week"
                        class="w-full px-4 py-3 rounded-xl bg-chow-cream-50 dark:bg-dark-section border border-chow-cream-200 dark:border-dark-border text-chow-brown-800 dark:text-content-primary focus:ring-2 focus:ring-accent focus:border-accent outline-none transition">
                        <option value="">Select frequency</option>
                        <option value="1">1-2 times</option>
                        <option value="3">3-4 times</option>
                        <option value="5">5-7 times</option>
                        <option value="10">Daily (multiple times)</option>
                    </select>
                </div>

                {{-- Reason for Choosing --}}
                <div class="mb-8">
                    <label class="block text-sm font-bold text-chow-brown-700 dark:text-content-secondary mb-2">
                        Why would you choose homemade over fast food?
                    </label>
                    <textarea name="reason_for_choosing" rows="3"
                        class="w-full px-4 py-3 rounded-xl bg-chow-cream-50 dark:bg-dark-section border border-chow-cream-200 dark:border-dark-border text-chow-brown-800 dark:text-content-primary focus:ring-2 focus:ring-accent focus:border-accent outline-none transition resize-none"
                        placeholder="Tell us what matters to you..."></textarea>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" class="flex-1 py-4 px-6 bg-accent hover:bg-accent-hover text-white font-bold rounded-xl transition-all shadow-lg shadow-accent/30 hover:shadow-accent/50">
                        <i class="fas fa-check mr-2"></i> Complete Survey
                    </button>
                    <a href="{{ route('waitlist.survey.skip', $signup->referral_token) }}" class="flex-1 py-4 px-6 bg-chow-cream-100 dark:bg-dark-section text-chow-brown-600 dark:text-content-secondary font-bold rounded-xl transition-all text-center hover:bg-chow-cream-200 dark:hover:bg-dark-border">
                        Skip for Now
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
