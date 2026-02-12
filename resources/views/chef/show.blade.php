@extends('layouts.dashboard')

{{-- FIX 1: Use optional() here to prevent crash if profile is null --}}
@section('title', optional($profile)->business_name ?? 'My Kitchen Profile')
@section('page_title', 'Kitchen Profile')

@section('content')
<div class="max-w-4xl mx-auto">
    
    {{-- ALERT: Profile Incomplete (Shows if $profile is NULL) --}}
    @if(!$profile)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 mb-6 rounded-r-lg shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-yellow-400 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-yellow-800">Your profile is incomplete</h3>
                    <p class="text-sm text-yellow-700 mt-1">
                        Customers cannot find your kitchen until you set up your profile details.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('chef.profile.edit') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-md text-yellow-800 bg-yellow-200 hover:bg-yellow-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            Create Profile Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-r-lg shadow-sm flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
        @endif

        {{-- MAIN PROFILE CARD (Only shows if $profile exists) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            
            {{-- Cover Image --}}
            <div class="h-48 bg-gray-200 w-full relative">
                @if($profile->cover_image)
                    <img src="{{ asset('storage/' . $profile->cover_image) }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100">
                        <div class="text-center">
                            <i class="fas fa-image text-4xl mb-2"></i>
                            <p class="text-sm">No Cover Image</p>
                        </div>
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
            </div>

            <div class="px-8 pb-8">
                <div class="relative flex justify-between items-end -mt-12 mb-6">
                    {{-- Avatar --}}
                    <div class="relative group">
                        <img class="w-24 h-24 rounded-full border-4 border-white shadow-md object-cover bg-white" 
                             src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.Auth::user()->first_name }}">
                    </div>

                    {{-- Edit Button --}}
                    <a href="{{ route('chef.profile.edit') }}" class="bg-white dark:text-gray-300 font-bold py-2 px-4 rounded-lg border border-gray-300 hover:border-red-500 hover:text-red-600 transition-all shadow-sm z-10">
                        <i class="fas fa-edit mr-2"></i> Edit Details
                    </a>
                </div>

                {{-- Main Info --}}
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-1">
                        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-gray-100">{{ $profile->business_name }}</h1>
                        @if($profile->is_online)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                                <span class="w-2 h-2 bg-green-500 rounded-full inline-block mr-1"></span> Online
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700">
                                Offline
                            </span>
                        @endif
                    </div>
                    
                    <p class="tdark:text-gray-300 flex items-center gap-2 text-sm font-medium">
                        <i class="fas fa-map-marker-alt text-red-500"></i> {{ $profile->kitchen_address }}
                    </p>
                    
                    <div class="mt-6 prose prose-red max-w-none text-gray-600 dark:text-gray-400 bg-gray-50 p-4 rounded-lg border border-gray-100 dark:border-gray-700">
                        {{ $profile->bio }}
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-100 dark:border-gray-700 pt-8">
                    {{-- Kitchen Stats --}}
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-gray-100 mb-4 text-xs uppercase tracking-wider tdark:text-gray-300">Kitchen Details</h3>
                        <ul class="space-y-4">
                            <li class="flex items-center justify-between p-3 bg-white border border-gray-100 dark:border-gray-700 rounded-lg shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center text-red-500">
                                        <i class="fas fa-medal"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Experience</span>
                                </div>
                                <span class="font-bold text-gray-900 dark:text-gray-100">{{ $profile->years_of_experience }} Years</span>
                            </li>
                            <li class="flex items-center justify-between p-3 bg-white border border-gray-100 dark:border-gray-700 rounded-lg shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
                                        <i class="fas fa-shopping-basket"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Min. Order</span>
                                </div>
                                <span class="font-bold text-gray-900 dark:text-gray-100">₦{{ number_format($profile->minimum_order) }}</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Banking Info --}}
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-gray-100 mb-4 text-xs uppercase tracking-wider tdark:text-gray-300">Payout Details</h3>
                        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-5 text-white shadow-lg relative overflow-hidden">
                            <div class="absolute top-0 right-0 -mt-2 -mr-2 w-16 h-16 bg-white opacity-10 rounded-full"></div>
                            
                            <div class="flex items-center justify-between mb-6">
                                <span class="text-xs font-medium text-gray-400 uppercase">Bank Account</span>
                                <i class="fas fa-university text-gray-400"></i>
                            </div>
                            
                            <div class="mb-4">
                                <div class="text-2xl font-mono tracking-widest">{{ $profile->account_number }}</div>
                                <div class="text-xs text-gray-400 mt-1">Account Number</div>
                            </div>
                            
                            <div class="flex justify-between items-end border-t border-gray-700 pt-4 mt-4">
                                <div>
                                    <div class="text-sm font-bold">{{ $profile->account_name }}</div>
                                    <div class="text-xs text-gray-400">{{ $profile->bank_name }}</div>
                                </div>
                                <div class="text-xs bg-green-500 text-white px-2 py-0.5 rounded font-bold">
                                    VERIFIED
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection