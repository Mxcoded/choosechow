@extends('layouts.dashboard')

@section('title', 'Admin - Subscription Plans')
@section('page_title', 'Subscription Plan Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Total Plans</p>
                    <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-layer-group text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 border-l-4 border-l-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Active</p>
                    <p class="text-2xl font-extrabold text-green-600 mt-1">{{ $stats['active'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 border-l-4 border-l-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Inactive</p>
                    <p class="text-2xl font-extrabold text-red-600 mt-1">{{ $stats['inactive'] }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-lg">
                    <i class="fas fa-pause-circle text-red-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Customer Plans</p>
                    <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ $stats['customer_plans'] }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-user text-purple-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Chef Plans</p>
                    <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ $stats['chef_plans'] }}</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-lg">
                    <i class="fas fa-utensils text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filters + Create Button --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
            <form action="{{ route('admin.subscription-plans.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 flex-1">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search plans..."
                            class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                <div>
                    <select name="user_type" class="w-full md:w-40 py-2 px-3 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500">
                        <option value="">All Types</option>
                        <option value="customer" {{ request('user_type') == 'customer' ? 'selected' : '' }}>Customer</option>
                        <option value="chef" {{ request('user_type') == 'chef' ? 'selected' : '' }}>Chef</option>
                    </select>
                </div>
                <div>
                    <select name="status" class="w-full md:w-40 py-2 px-3 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-gray-900 text-white px-6 py-2 rounded-lg font-bold hover:bg-gray-800 transition">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'user_type', 'status']))
                        <a href="{{ route('admin.subscription-plans.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg font-bold hover:bg-gray-200 transition">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
            <a href="{{ route('admin.subscription-plans.create') }}" class="bg-red-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-red-700 transition flex items-center gap-2 shrink-0">
                <i class="fas fa-plus"></i> New Plan
            </a>
        </div>
    </div>

    {{-- Plans Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold border-b">
                    <tr>
                        <th class="p-4">Plan</th>
                        <th class="p-4">Type</th>
                        <th class="p-4">Monthly</th>
                        <th class="p-4">Yearly</th>
                        <th class="p-4">Features</th>
                        <th class="p-4">Sort</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($plans as $plan)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-red-600 font-bold text-xs">{{ strtoupper(substr($plan->name, 0, 2)) }}</span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $plan->name }}</div>
                                        <div class="text-xs text-gray-400">/{{ $plan->slug }}</div>
                                        @if($plan->is_popular)
                                            <span class="text-xs font-bold text-yellow-600"><i class="fas fa-star mr-1"></i>Popular</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $plan->user_type === 'customer' ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ ucfirst($plan->user_type) }}
                                </span>
                            </td>
                            <td class="p-4 font-extrabold text-gray-900">₦{{ number_format($plan->monthly_price, 2) }}</td>
                            <td class="p-4 text-gray-600">
                                @if($plan->yearly_price)
                                    ₦{{ number_format($plan->yearly_price, 2) }}
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <div class="flex flex-wrap gap-1">
                                    @if($plan->features)
                                        @foreach(array_slice($plan->features, 0, 3) as $feature)
                                            <span class="px-1.5 py-0.5 rounded text-xs bg-gray-100 text-gray-600 whitespace-nowrap">
                                                {{ Str::limit(str_replace('_', ' ', $feature), 18) }}
                                            </span>
                                        @endforeach
                                        @if(count($plan->features) > 3)
                                            <span class="text-xs text-gray-400">+{{ count($plan->features) - 3 }} more</span>
                                        @endif
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4 text-gray-400">{{ $plan->sort_order }}</td>
                            <td class="p-4">
                                @if($plan->is_active)
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 inline-flex items-center">
                                        <i class="fas fa-circle text-[6px] mr-1 text-green-600"></i> Active
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 inline-flex items-center">
                                        <i class="fas fa-circle text-[6px] mr-1 text-gray-400"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.subscription-plans.edit', $plan->id) }}"
                                        class="bg-gray-900 text-white px-3 py-1.5 rounded-lg text-xs hover:bg-gray-800 shadow-sm font-bold">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.subscription-plans.destroy', $plan->id) }}" method="POST"
                                        class="inline-block" onsubmit="return confirm('Delete {{ $plan->name }}? This cannot be undone if no subscriptions exist.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="border border-red-200 text-red-600 px-3 py-1.5 rounded-lg text-xs hover:bg-red-50 font-bold">
                                            <i class="fas fa-trash mr-1"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-gray-400">
                                <i class="fas fa-layer-group text-4xl mb-3 block"></i>
                                No subscription plans found.
                                <a href="{{ route('admin.subscription-plans.create') }}" class="text-red-600 hover:underline block mt-2">Create your first plan</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t bg-gray-50">
            {{ $plans->links() }}
        </div>
    </div>
</div>
@endsection
