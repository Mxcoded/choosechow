@extends('layouts.dashboard')

@section('title', 'Edit Profile')
@section('page_title', 'Edit Kitchen Profile')

@section('content')
<div class="max-w-5xl mx-auto pb-20">
    
    <div class="mb-6">
        <a href="{{ route('chef.profile') }}" class="tdark:text-gray-300 hover:text-gray-900 dark:text-gray-100 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Cancel & Go Back
        </a>
    </div>

    {{-- Global Error Alert --}}
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        There were problems with your submission:
                    </h3>
                    <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex gap-4 mb-8 border-b border-gray-200 dark:border-gray-700">
        <a href="{{ route('chef.profile.edit') }}" class="pb-3 border-b-2 border-red-600 text-red-600 font-bold">
            Kitchen Profile
        </a>
        <a href="{{ route('chef.personal.edit') }}" class="pb-3 tdark:text-gray-300 hover:text-gray-800 font-medium transition-colors">
            Personal Details
        </a>
    </div>

    <form action="{{ route('chef.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- COLUMN 1: Main Info (Spans 2 columns) --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Business Details --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Business Details</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300 mb-1">Business Name</label>
                            <input type="text" name="business_name" value="{{ old('business_name', $profile->business_name) }}" class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block dark:text-gray-300 text-sm font-medium mb-1">Delivery Fee (₦)</label>
                                <input type="number" name="delivery_fee" value="{{ old('delivery_fee', $profile->delivery_fee ?? 0) }}" 
                                    class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                            </div>
                            <div>
                                <label class="block dark:text-gray-300 text-sm font-medium mb-1">City</label>
                                <select name="city" class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                                    <option value="">Select City</option>
                                    @foreach(['Abuja', 'Lagos', 'Port Harcourt', 'Ibadan', 'Enugu'] as $city)
                                        <option value="{{ $city }}" {{ old('city', $profile->city) == $city ? 'selected' : '' }}>{{ $city }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300 mb-1">Kitchen Address</label>
                            <input type="text" name="kitchen_address" value="{{ old('kitchen_address', $profile->kitchen_address) }}" class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300 mb-1">About Your Kitchen (Bio)</label>
                            <textarea name="bio" rows="4" class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500" placeholder="Tell customers what makes your food special...">{{ old('bio', $profile->bio) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Specialties & Cuisines --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 flex items-center">
                        <i class="fas fa-utensils text-gray-400 mr-2"></i> Specialties & Cuisines
                    </h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @php
                        $allCuisines = ['Rice Dishes', 'Swallow & Soups', 'Pasta', 'Grills & BBQ', 'Pastries', 'Breakfast', 'Continental', 'Vegetarian', 'Drinks & Smoothies', 'Fast Food', 'Local Delicacies', 'Seafood'];
                        // Convert Eloquent Collection to array of cuisine names
                        $myCuisines = $profile->cuisines ? $profile->cuisines->pluck('name')->toArray() : [];
                    @endphp

                        @foreach($allCuisines as $cuisine)
                            <label class="flex items-center space-x-2 cursor-pointer p-2 rounded hover:bg-red-50 transition-colors border border-transparent hover:border-red-100">
                                <input type="checkbox" name="cuisines[]" value="{{ $cuisine }}" 
                                    class="rounded text-red-600 focus:ring-red-500 w-4 h-4"
                                    {{ in_array($cuisine, $myCuisines) ? 'checked' : '' }}>
                                <span class="text-sm dark:text-gray-300">{{ $cuisine }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Operating Hours --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 flex items-center">
                        <i class="fas fa-clock text-gray-400 mr-2"></i> Operating Hours
                    </h3>
                    <div class="space-y-3">
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                            @php
                                $hours = $profile->operating_hours[$day] ?? [];
                                $isClosed = isset($hours['closed']) && ($hours['closed'] == 'true' || $hours['closed'] == '1');
                            @endphp
                            <div class="grid grid-cols-12 gap-3 items-center">
                                <div class="col-span-3 text-sm font-medium capitalize dark:text-gray-300">{{ $day }}</div>
                                
                                <div class="col-span-4">
                                    <input type="time" name="operating_hours[{{ $day }}][open]" value="{{ $hours['open'] ?? '09:00' }}" 
                                           class="w-full text-sm rounded-md border-gray-300 focus:border-red-500 focus:ring-red-500 disabled:bg-gray-100 disabled:text-gray-400">
                                </div>
                                
                                <div class="col-span-4">
                                    <input type="time" name="operating_hours[{{ $day }}][close]" value="{{ $hours['close'] ?? '17:00' }}" 
                                           class="w-full text-sm rounded-md border-gray-300 focus:border-red-500 focus:ring-red-500 disabled:bg-gray-100 disabled:text-gray-400">
                                </div>
                                
                                <div class="col-span-1 flex justify-center">
                                    <label class="flex flex-col items-center cursor-pointer group">
                                        <input type="checkbox" name="operating_hours[{{ $day }}][closed]" value="true" {{ $isClosed ? 'checked' : '' }} class="rounded text-red-600 focus:ring-red-500 w-4 h-4">
                                        <span class="text-[10px] text-gray-400 mt-0.5 group-hover:text-red-500">Off</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Banking Details --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Payout Details</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300 mb-1">Bank Name</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name', $profile->bank_name) }}" class="w-full rounded-lg border-gray-300">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium dark:text-gray-300 mb-1">Account Number</label>
                                <input type="text" name="account_number" value="{{ old('account_number', $profile->account_number) }}" class="w-full rounded-lg border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium dark:text-gray-300 mb-1">Account Name</label>
                                <input type="text" name="account_name" value="{{ old('account_name', $profile->account_name) }}" class="w-full rounded-lg border-gray-300">
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Submit Button (Mobile) --}}
                <div class="lg:hidden">
                    <button type="submit" class="w-full bg-red-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-red-700 transition-colors shadow-md">
                        Save Changes
                    </button>
                </div>
            </div>

            {{-- COLUMN 2: Sidebar Stats & Save --}}
            <div class="space-y-6">
                
                {{-- Submit Button (Desktop) --}}
                <div class="hidden lg:block sticky top-24 z-10">
                    <button type="submit" class="w-full bg-red-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-red-700 transition-colors shadow-lg transform hover:-translate-y-1">
                        Save All Changes
                    </button>
                </div>

                {{-- Status Card --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold text-gray-800 mb-4">Kitchen Status</h3>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium dark:text-gray-300">Accepting Orders?</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_online" value="1" class="sr-only peer" {{ $profile->is_online ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                </div>

                {{-- Images --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 space-y-6">
                    <h3 class="font-bold text-gray-800">Kitchen Branding</h3>

                    {{-- 1. Store Logo --}}
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-300 mb-2">Profile Picture</label>
                        <div class="flex items-center space-x-4">
                            @if($profile->profile_image)
                                <img src="{{ asset('storage/' . $profile->profile_image) }}" class="w-16 h-16 rounded-full object-cover border border-gray-200 dark:border-gray-700">
                            @else
                                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                    <i class="fas fa-camera"></i>
                                </div>
                            @endif
                            <input type="file" name="profile_image" class="block w-full text-sm tdark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                        </div>
                    </div>

                    <hr class="border-gray-100 dark:border-gray-700">

                    {{-- 2. Cover Image --}}
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-300 mb-2">Cover Banner</label>
                        @if($profile->cover_image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $profile->cover_image) }}" class="w-full h-24 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
                            </div>
                        @endif
                        <input type="file" name="cover_image" class="block w-full text-sm tdark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                        <p class="text-xs text-gray-400 mt-1">Recommended size: 1200x400px</p>
                    </div>
                </div>

                {{-- Quick Stats --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold text-gray-800 mb-4">Quick Stats</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300 mb-1">Years of Experience</label>
                            <input type="number" name="years_of_experience" value="{{ old('years_of_experience', $profile->years_of_experience) }}" class="w-full rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300 mb-1">Minimum Order (₦)</label>
                            <input type="number" name="minimum_order" value="{{ old('minimum_order', $profile->minimum_order) }}" class="w-full rounded-lg border-gray-300">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection