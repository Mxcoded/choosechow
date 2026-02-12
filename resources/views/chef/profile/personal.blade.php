@extends('layouts.dashboard')

@section('title', 'Personal Settings')
@section('page_title', 'Personal Settings')

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Tabs --}}
    <div class="flex gap-4 mb-8 border-b border-gray-200 dark:border-gray-700">
        <a href="{{ route('chef.profile.edit') }}" class="pb-3 tdark:text-gray-300 hover:text-gray-800 font-medium transition-colors">
            Kitchen Profile
        </a>
        <a href="{{ route('chef.personal.edit') }}" class="pb-3 border-b-2 border-red-600 text-red-600 font-bold">
            Personal Details
        </a>
    </div>

    {{-- Error Handling --}}
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
            <ul class="list-disc list-inside text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
        <form action="{{ route('chef.personal.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- First Name --}}
                <div>
                    <label class="block text-sm font-medium dark:text-gray-300 mb-1">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                </div>

                {{-- Last Name --}}
                <div>
                    <label class="block text-sm font-medium dark:text-gray-300 mb-1">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                {{-- Phone --}}
                <div>
                    <label class="block text-sm font-medium dark:text-gray-300 mb-1">Phone Number</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium dark:text-gray-300 mb-1">Email</label>
                    <input type="email" value="{{ $user->email }}" readonly 
                           class="w-full rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 tdark:text-gray-300 cursor-not-allowed">
                </div>
            </div>

            <h3 class="text-lg font-bold text-gray-800 mb-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                Change Password
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-medium dark:text-gray-300 mb-1">New Password</label>
                    <input type="password" name="password" 
                           class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium dark:text-gray-300 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" 
                           class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-red-600 text-white font-bold py-3 px-8 rounded-xl hover:bg-red-700 transition-colors shadow-lg shadow-red-200">
                    Update Details
                </button>
            </div>
        </form>
    </div>
</div>
@endsection