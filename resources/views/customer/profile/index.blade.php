@extends('layouts.dashboard')

@section('title', 'My Profile')
@section('page_title', 'My Account Settings')

@section('content')
<div class="max-w-3xl mx-auto">
    
    {{-- Error Handling Alert --}}
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Please fix the following errors:
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

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 lg:p-8">
        <h2 class="text-xl font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100 dark:border-gray-700 flex items-center">
            <i class="fas fa-user-edit mr-2 text-red-600"></i> Personal Information
        </h2>

        <form action="{{ route('customer.profile.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- First Name --}}
                <div>
                    <label class="block text-sm font-bold dark:text-gray-300 mb-1">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 shadow-sm" required>
                </div>

                {{-- Last Name --}}
                <div>
                    <label class="block text-sm font-bold dark:text-gray-300 mb-1">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 shadow-sm" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Phone --}}
                <div>
                    <label class="block text-sm font-bold dark:text-gray-300 mb-1">Phone Number</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 shadow-sm" required>
                </div>

                {{-- Email (Read Only) --}}
                <div>
                    <label class="block text-sm font-bold dark:text-gray-300 mb-1">Email Address</label>
                    <input type="email" value="{{ $user->email }}" readonly 
                           class="w-full rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 tdark:text-gray-300 cursor-not-allowed shadow-sm">
                    <p class="text-xs text-gray-400 mt-1">Email cannot be changed.</p>
                </div>
            </div>

            {{-- Address --}}
            <div class="mb-8">
                <label class="block text-sm font-bold dark:text-gray-300 mb-1">Default Delivery Address</label>
                <textarea name="address" rows="3" placeholder="Enter your full address..."
                          class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 shadow-sm">{{ old('address', $user->address ?? '') }}</textarea>
            </div>

            <h3 class="text-lg font-bold text-gray-800 mb-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center">
                <i class="fas fa-lock mr-2 text-red-600"></i> Security (Optional)
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-bold dark:text-gray-300 mb-1">New Password</label>
                    <input type="password" name="password" placeholder="Leave blank to keep current"
                           class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold dark:text-gray-300 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" placeholder="Confirm new password"
                           class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 shadow-sm">
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-700">
                <button type="submit" class="bg-red-600 text-white font-bold py-3 px-8 rounded-xl hover:bg-red-700 transition-colors shadow-lg shadow-red-200 flex items-center">
                    <i class="fas fa-save mr-2"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection