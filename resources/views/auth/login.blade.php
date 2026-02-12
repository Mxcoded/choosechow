@extends('layouts.app')

@section('title', 'Sign In')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-red-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
        
        {{-- Header --}}
        <div class="text-center">
            <a href="{{ route('welcome') }}">
                <img class="mx-auto h-16 w-auto" src="{{ asset('storage/img/choosechowlogo.png') }}" alt="ChooseChow">
            </a>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-gray-100">
                Welcome Back
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                New here? <a href="{{ route('register') }}" class="font-medium text-red-600 hover:text-red-500">Create an account</a>
            </p>
        </div>

        <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
            @csrf

            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium dark:text-gray-300 mb-1">Email address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required class="appearance-none block w-full pl-10 pr-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm @error('email') border-red-500 @enderror" placeholder="you@example.com" value="{{ old('email') }}">
                    </div>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium dark:text-gray-300 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none block w-full pl-10 pr-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm @error('password') border-red-500 @enderror" placeholder="••••••••">
                    </div>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded cursor-pointer">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-900 dark:text-gray-100 cursor-pointer">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="font-medium text-red-600 hover:text-red-500">
                            Forgot password?
                        </a>
                    @endif
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-lg transform transition hover:-translate-y-0.5">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-sign-in-alt text-red-500 group-hover:text-red-400"></i>
                    </span>
                    Sign in
                </button>
            </div>
        </form>
    </div>
</div>
@endsection