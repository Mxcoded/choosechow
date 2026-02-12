@extends('layouts.app')

@section('title', 'Create Account')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-red-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
        
        {{-- Header --}}
        <div class="text-center">
            <img class="mx-auto h-16 w-auto" src="{{ asset('storage/img/choosechowlogo.png') }}" alt="ChooseChow">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-gray-100">
                Join ChooseChow
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Or <a href="{{ route('login') }}" class="font-medium text-red-600 hover:text-red-500">sign in to your account</a>
            </p>
        </div>

        <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Role Selector (Customer vs Chef) --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <input type="radio" name="role" id="role_customer" value="customer" class="peer hidden" checked>
                    <label for="role_customer" class="block text-center px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:border-red-200 peer-checked:border-red-600 peer-checked:bg-red-50 peer-checked:text-red-700 transition-all">
                        <i class="fas fa-user mb-1 text-lg"></i>
                        <div class="text-sm font-bold">I want to Eat</div>
                    </label>
                </div>
                <div>
                    <input type="radio" name="role" id="role_chef" value="chef" class="peer hidden">
                    <label for="role_chef" class="block text-center px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:border-red-200 peer-checked:border-red-600 peer-checked:bg-red-50 peer-checked:text-red-700 transition-all">
                        <i class="fas fa-utensils mb-1 text-lg"></i>
                        <div class="text-sm font-bold">I want to Cook</div>
                    </label>
                </div>
            </div>

            {{-- Name Fields --}}
            <div class="rounded-md shadow-sm -space-y-px">
                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="first_name" class="block text-sm font-medium dark:text-gray-300 mb-1">First Name</label>
                        <input id="first_name" name="first_name" type="text" required class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500 focus:z-10 sm:text-sm @error('first_name') border-red-500 @enderror" placeholder="John" value="{{ old('first_name') }}">
                        @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="last_name" class="block text-sm font-medium dark:text-gray-300 mb-1">Last Name</label>
                        <input id="last_name" name="last_name" type="text" required class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500 focus:z-10 sm:text-sm @error('last_name') border-red-500 @enderror" placeholder="Doe" value="{{ old('last_name') }}">
                        @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium dark:text-gray-300 mb-1">Email address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500 focus:z-10 sm:text-sm @error('email') border-red-500 @enderror" placeholder="john@example.com" value="{{ old('email') }}">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium dark:text-gray-300 mb-1">Phone Number</label>
                    <input id="phone" name="phone" type="tel" required class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500 focus:z-10 sm:text-sm @error('phone') border-red-500 @enderror" placeholder="08012345678" value="{{ old('phone') }}">
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium dark:text-gray-300 mb-1">Password</label>
                    <input id="password" name="password" type="password" required class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500 focus:z-10 sm:text-sm @error('password') border-red-500 @enderror" placeholder="••••••••">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label for="password-confirm" class="block text-sm font-medium dark:text-gray-300 mb-1">Confirm Password</label>
                    <input id="password-confirm" name="password_confirmation" type="password" required class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500 focus:z-10 sm:text-sm" placeholder="••••••••">
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-lg transform transition hover:-translate-y-0.5">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-user-plus text-red-500 group-hover:text-red-400"></i>
                    </span>
                    Create Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection