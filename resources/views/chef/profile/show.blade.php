@extends('layouts.dashboard')

@section('title', $profile->business_name ?? 'My Profile')
@section('page_title', 'Kitchen Profile')

@section('content')
<div class="max-w-5xl mx-auto pb-20">

    {{-- 1. NAVIGATION --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('dashboard') }}" class="flex items-center tdark:text-gray-300 hover:text-gray-900 dark:text-gray-100 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>
        <a href="{{ route('chef.profile.edit') }}" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-red-700 transition-colors shadow-sm">
            <i class="fas fa-edit mr-2"></i> Edit Profile
        </a>
    </div>

    {{-- 2. HERO SECTION (Cover Image & Avatar) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-8">
        
        {{-- Cover Image --}}
        <div class="h-48 w-full relative bg-gray-200">
            @if($profile->cover_image)
                <img src="{{ asset('storage/' . $profile->cover_image) }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/20"></div> {{-- Slight overlay for text readability --}}
            @else
                {{-- Fallback Gradient --}}
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-r from-red-500 to-orange-400">
                    <i class="fas fa-store text-white/50 text-5xl"></i>
                </div>
            @endif
        </div>

        <div class="px-8 pb-8">
            <div class="relative flex justify-between items-end -mt-12 mb-6">
                {{-- User Avatar --}}
                <div class="relative">
                    <img src="{{ $profile->profile_image ? asset('storage/' . $profile->profile_image) : (Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($profile->business_name).'&background=ffffff&color=7f1d1d') }}" 
                    class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover bg-white">
                    
                    {{-- Online Status Badge --}}
                    <div class="absolute bottom-1 right-1 border-2 border-white rounded-full">
                        @if($profile->is_online)
                            <span class="block w-5 h-5 bg-green-500 rounded-full" title="Online"></span>
                        @else
                            <span class="block w-5 h-5 bg-gray-400 rounded-full" title="Offline"></span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Business Name & Bio --}}
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-gray-100">{{ $profile->business_name }}</h1>
                    
                    {{-- VERIFIED BADGE (New) --}}
                    @if($profile->is_verified)
                        <i class="fas fa-check-circle text-blue-500 text-xl" title="Verified Kitchen"></i>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-4 text-sm tdark:text-gray-300 mb-6">
                    <span class="flex items-center"><i class="fas fa-map-marker-alt text-red-500 mr-2"></i> {{ $profile->kitchen_address }}</span>
                    <span class="hidden sm:inline text-gray-300">|</span>
                    <span class="flex items-center"><i class="fas fa-star text-yellow-400 mr-2"></i> {{ $profile->years_of_experience }} Years Exp.</span>
                </div>

                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 dark:border-gray-700 dark:text-gray-300 leading-relaxed">
                    {{ $profile->bio ?? 'No bio added yet. Click Edit Profile to tell customers about your food!' }}
                </div>
            </div>
            
            {{-- Cuisines (Tags) --}}
            <div class="mt-6">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Cuisines</h4>
                <div class="flex flex-wrap gap-2">
                    @forelse(($profile->cuisines ?? []) as $cuisine)
                        <span class="bg-red-50 text-red-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border border-red-100">
                            {{ $cuisine }}
                        </span>
                    @empty
                        <span class="text-gray-400 text-sm italic">No specialties listed yet.</span>
                    @endforelse
                </div>
            </div>

    {{-- 3. DETAILED INFO GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        
        {{-- COLUMN 1: Operating Hours --}}
        <div class="md:col-span-1 bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 h-fit">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-clock text-gray-400 mr-2"></i> Operating Hours
            </h3>
            <div class="space-y-3">
                @php
                    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                    $today = strtolower(date('l'));
                @endphp

                @if($profile->operating_hours)
                    @foreach($days as $day)
                        @php 
                            $hours = $profile->operating_hours[$day] ?? null;
                            $isClosed = isset($hours['closed']) && filter_var($hours['closed'], FILTER_VALIDATE_BOOLEAN);
                        @endphp
                        <div class="flex justify-between text-sm {{ $today == $day ? 'font-bold text-red-600 bg-red-50 p-2 rounded -mx-2' : 'text-gray-600 dark:text-gray-400' }}">
                            <span class="capitalize">{{ $day }}</span>
                            <span>
                                @if($isClosed)
                                    <span class="text-gray-400">Closed</span>
                                @else
                                    {{ $hours['open'] ?? '9:00' }} - {{ $hours['close'] ?? '17:00' }}
                                @endif
                            </span>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4 tdark:text-gray-300 text-sm">
                        <i class="fas fa-calendar-times text-2xl mb-2 text-gray-300"></i>
                        <p>Hours not set.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- COLUMN 2: Stats & Bank (Spans 2 columns) --}}
        <div class="md:col-span-2 space-y-6">
            
            {{-- Order Stats --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="font-bold text-gray-800 mb-4">Kitchen Details</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg text-center border border-gray-100 dark:border-gray-700">
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">₦{{ number_format($profile->minimum_order) }}</div>
                        <div class="text-xs tdark:text-gray-300 uppercase tracking-wide mt-1">Min. Order</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg text-center border border-gray-100 dark:border-gray-700">
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $profile->is_online ? 'Open' : 'Closed' }}
                        </div>
                        <div class="text-xs tdark:text-gray-300 uppercase tracking-wide mt-1">Current Status</div>
                    </div>
                </div>
            </div>

            {{-- Bank Details --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-university text-gray-400 mr-2"></i> Payout Details
                </h3>
                @if($profile->account_number)
                    <div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-xl p-6 text-white shadow-lg relative overflow-hidden">
                        {{-- Decorative Circle --}}
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                        
                        <div class="relative z-10">
                            <div class="text-xs text-gray-400 uppercase tracking-widest mb-1">Bank Name</div>
                            <div class="font-bold text-lg mb-4">{{ $profile->bank_name }}</div>
                            
                            <div class="text-xs text-gray-400 uppercase tracking-widest mb-1">Account Number</div>
                            <div class="font-mono text-2xl font-bold tracking-widest">{{ $profile->account_number }}</div>
                            
                            <div class="mt-4 pt-4 border-t border-white/10 flex justify-between items-center">
                                <span class="text-sm text-gray-300">{{ $profile->account_name }}</span>
                                <i class="fas fa-lock text-white/50"></i>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-6 bg-yellow-50 rounded-lg border border-yellow-100 border-dashed">
                        <i class="fas fa-credit-card text-yellow-400 text-2xl mb-2"></i>
                        <p class="text-sm text-yellow-700">No payout details added yet.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection