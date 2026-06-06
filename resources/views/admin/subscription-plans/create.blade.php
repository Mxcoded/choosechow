@extends('layouts.dashboard')

@section('title', 'Admin - Create Subscription Plan')
@section('page_title', 'Create Subscription Plan')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.subscription-plans.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Name & Slug --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Plan Name</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500"
                        placeholder="e.g. Basic" required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug') }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500"
                        placeholder="e.g. basic" required>
                    @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- User Type & Sort Order --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">User Type</label>
                    <select name="user_type" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500" required>
                        <option value="customer" {{ old('user_type') === 'customer' ? 'selected' : '' }}>Customer</option>
                        <option value="chef" {{ old('user_type') === 'chef' ? 'selected' : '' }}>Chef</option>
                    </select>
                    @error('user_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500"
                        placeholder="0">
                    @error('sort_order') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500"
                    placeholder="Brief description of this plan">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Pricing --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Monthly Price (₦)</label>
                    <input type="number" step="0.01" min="0" name="monthly_price" value="{{ old('monthly_price') }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500"
                        placeholder="0.00" required>
                    @error('monthly_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Yearly Price (₦) <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input type="number" step="0.01" min="0" name="yearly_price" value="{{ old('yearly_price') }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500"
                        placeholder="0.00">
                    @error('yearly_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Features (JSON) --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">
                    Features
                    <span class="text-gray-400 font-normal text-xs">(JSON array, e.g. ["feature_1", "feature_2"])</span>
                </label>
                <textarea name="features" rows="4"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500 font-mono text-sm"
                    placeholder='["unlimited_free_delivery", "20%_all_discount", "dedicated_rider"]'>{{ old('features') }}</textarea>
                @error('features') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Limits (JSON) --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">
                    Limits
                    <span class="text-gray-400 font-normal text-xs">(JSON object, e.g. {"free_deliveries": 1})</span>
                </label>
                <textarea name="limits" rows="3"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500 font-mono text-sm"
                    placeholder='{"free_deliveries": 1, "delivery_discount_percent": 10}'>{{ old('limits') }}</textarea>
                @error('limits') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Toggles --}}
            <div class="flex flex-wrap gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_popular" value="1" {{ old('is_popular') ? 'checked' : '' }}
                        class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="text-sm font-medium text-gray-700">Mark as Popular</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                </label>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t">
                <a href="{{ route('admin.subscription-plans.index') }}" class="px-6 py-2 rounded-lg font-bold text-gray-600 hover:bg-gray-100 transition">
                    Cancel
                </a>
                <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-red-700 transition">
                    <i class="fas fa-save mr-1"></i> Create Plan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
