@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <div>
            <h1 class="text-6xl font-extrabold text-red-600 mb-4">403</h1>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Access Denied</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">You don't have permission to access this resource.</p>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-left">
                <h3 class="font-bold text-blue-900 mb-2">🔧 Troubleshooting</h3>
                <ul class="text-sm text-blue-800 space-y-2">
                    <li>• Are you logged in as the right user type?</li>
                    <li>• Do you have the required role/permissions?</li>
                    <li>• Try visiting <a href="/diagnose" class="underline font-bold">/diagnose</a> to check your status</li>
                    <li>• If you're a chef, visit <a href="/become-chef" class="underline font-bold">/become-chef</a></li>
                </ul>
            </div>
            
            <a href="/" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition-colors">
                Go Home
            </a>
        </div>
    </div>
</div>
@endsection
