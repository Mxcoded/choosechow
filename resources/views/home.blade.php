@extends('layouts.master')

@section('title', 'Home - ChooseChow')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white rounded-2xl shadow-sm p-8 mb-8 border border-gray-100 dark:border-gray-700 fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                        Welcome back, <span class="gradient-text">{{ Auth::user()->name ?? 'Foodie' }}</span>!
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">We're glad to see you again. What are you craving today?</p>
                </div>
                <div class="hidden md:block">
                    <span class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-red-50 text-red-600 shadow-sm">
                        <i class="fas fa-smile-beam text-3xl"></i>
                    </span>
                </div>
            </div>

            @if (session('status'))
                <div class="mt-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center shadow-sm" role="alert">
                    <i class="fas fa-check-circle mr-3"></i>
                    <span class="block sm:inline">{{ session('status') }}</span>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <a href="{{ route('chef.index') }}" class="group bg-white rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fas fa-utensils text-6xl text-orange-500"></i>
                </div>
                <div class="flex items-center space-x-4 relative z-10">
                    <div class="bg-orange-100 p-3 rounded-lg text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                        <i class="fas fa-search text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-gray-100 text-lg">Find Chefs</h3>
                        <p class="text-sm tdark:text-gray-300 mt-1">Discover local home cooks</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('subscriptions.index') }}" class="group bg-white rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fas fa-calendar-check text-6xl text-blue-500"></i>
                </div>
                <div class="flex items-center space-x-4 relative z-10">
                    <div class="bg-blue-100 p-3 rounded-lg text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <i class="fas fa-box-open text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-gray-100 text-lg">Meal Plans</h3>
                        <p class="text-sm tdark:text-gray-300 mt-1">Manage your subscriptions</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('dashboard') }}" class="group bg-white rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fas fa-id-card text-6xl text-purple-500"></i>
                </div>
                <div class="flex items-center space-x-4 relative z-10">
                    <div class="bg-purple-100 p-3 rounded-lg text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                        <i class="fas fa-user-circle text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-gray-100 text-lg">My Dashboard</h3>
                        <p class="text-sm tdark:text-gray-300 mt-1">View orders & settings</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    <i class="fas fa-clock text-gray-400 mr-2"></i> Recent Activity
                </h2>
                <a href="{{ route('dashboard') }}" class="text-sm text-red-600 hover:text-red-700 font-semibold hover:underline">
                    View All <i class="fas fa-arrow-right ml-1 text-xs"></i>
                </a>
            </div>
            <div class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                    <i class="fas fa-receipt text-gray-300 text-2xl"></i>
                </div>
                <h3 class="text-gray-900 dark:text-gray-100 font-medium mb-1">No recent activity</h3>
                <p class="tdark:text-gray-300 text-sm mb-6">Looks like you haven't made any orders yet.</p>
                <a href="{{ route('chef.index') }}" class="inline-flex items-center px-6 py-2.5 bg-red-600 text-white font-medium text-sm rounded-lg hover:bg-red-700 transition-colors shadow-sm hover:shadow">
                    Start your first order
                </a>
            </div>
        </div>

    </div>
</div>
@endsection