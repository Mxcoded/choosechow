@extends('layouts.dashboard')

@section('title', 'System Settings')
@section('page_title', 'Global Configuration')

@section('content')
<div class="max-w-4xl mx-auto">

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50">
            <h3 class="font-bold text-lg text-gray-800">Platform Settings</h3>
            <p class="text-sm tdark:text-gray-300">Manage global variables for the ChooseChow platform.</p>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="p-8 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Site Name --}}
                <div class="col-span-2">
                    <label class="block text-sm font-bold dark:text-gray-300 mb-2">Platform Name</label>
                    <input type="text" name="site_name" value="{{ $settings['site_name'] ?? 'ChooseChow' }}" 
                        class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                </div>

                {{-- Commission Fee --}}
                <div>
                    <label class="block text-sm font-bold dark:text-gray-300 mb-2">Commission Fee (%)</label>
                    <div class="relative">
                        <input type="number" name="commission_fee" value="{{ $settings['commission_fee'] ?? '5' }}" 
                            class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500 pl-4 pr-8">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="tdark:text-gray-300">%</span>
                        </div>
                    </div>
                    <p class="text-xs tdark:text-gray-300 mt-1">Percentage taken from every order.</p>
                </div>

                {{-- Minimum Withdrawal --}}
                <div>
                    <label class="block text-sm font-bold dark:text-gray-300 mb-2">Min. Withdrawal (₦)</label>
                    <input type="number" name="min_withdrawal" value="{{ $settings['min_withdrawal'] ?? '5000' }}" 
                        class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                </div>

                {{-- Support Contact --}}
                <div>
                    <label class="block text-sm font-bold dark:text-gray-300 mb-2">Support Email</label>
                    <input type="email" name="support_email" value="{{ $settings['support_email'] ?? '' }}" 
                        class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <label class="block text-sm font-bold dark:text-gray-300 mb-2">Support Phone</label>
                    <input type="text" name="support_phone" value="{{ $settings['support_phone'] ?? '' }}" 
                        class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                <button type="submit" class="bg-gray-900 text-white font-bold py-3 px-8 rounded-lg hover:bg-gray-800 transition shadow-lg">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection