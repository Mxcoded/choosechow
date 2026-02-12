@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')

    {{-- Welcome Card --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Welcome, {{ Auth::user()->first_name }}!</h2>
        <p class="text-gray-600 dark:text-gray-400">You are logged in as a <strong>{{ Auth::user()->getRoleNames()->first() }}</strong>.</p>
    </div>

    {{-- Chef Specific Stats --}}
    @if(Auth::user()->hasRole('chef'))
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-blue-50 p-6 rounded-lg border border-blue-100">
                <h3 class="font-bold text-blue-800">My Kitchen</h3>
                <p class="text-sm text-blue-600">Manage your menu and orders.</p>
                <div class="mt-4">
                    <a href="{{ route('chef.menus.index') }}" class="text-blue-700 font-bold hover:underline">Go to Menu &rarr;</a>
                </div>
            </div>
            
            <div class="bg-green-50 p-6 rounded-lg border border-green-100">
                <h3 class="font-bold text-green-800">Store Profile</h3>
                <p class="text-sm text-green-600">Update your business details.</p>
                <div class="mt-4">
                    <a href="{{ route('chef.profile') }}" class="text-green-700 font-bold hover:underline">View Profile &rarr;</a>
                </div>
            </div>
        </div>
    @endif

@endsection