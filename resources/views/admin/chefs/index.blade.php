@extends('layouts.dashboard')

@section('title', 'Admin - Kitchen Management')
@section('page_title', 'Kitchen & Vendor Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Total Kitchens</p>
                    <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-lg">
                    <i class="fas fa-utensils text-orange-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Verified</p>
                    <p class="text-2xl font-extrabold text-green-600 mt-1">{{ number_format($stats['verified']) }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Pending Verification</p>
                    <p class="text-2xl font-extrabold text-yellow-600 mt-1">{{ number_format($stats['pending']) }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Total Sales</p>
                    <p class="text-2xl font-extrabold text-blue-600 mt-1">₦{{ number_format($stats['total_revenue']) }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-coins text-blue-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form action="{{ route('admin.chef') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Search by kitchen name, owner, or email..." 
                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            <div>
                <select name="verification" class="w-full md:w-48 py-2 px-3 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500">
                    <option value="">All Status</option>
                    <option value="verified" {{ request('verification') == 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="pending" {{ request('verification') == 'pending' ? 'selected' : '' }}>Pending Verification</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-gray-900 text-white px-6 py-2 rounded-lg font-bold hover:bg-gray-800 transition">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'verification']))
                    <a href="{{ route('admin.chef') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg font-bold hover:bg-gray-200 transition">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Kitchens Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold border-b">
                    <tr>
                        <th class="p-4">Kitchen</th>
                        <th class="p-4">Owner</th>
                        <th class="p-4">Performance</th>
                        <th class="p-4">Location</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($chefs as $chef)
                        <tr class="hover:bg-gray-50">
                            {{-- Kitchen Info --}}
                            <td class="p-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                        @if($chef->chefProfile && $chef->chefProfile->logo_url)
                                            <img src="{{ $chef->chefProfile->logo_url }}" alt="Logo" class="w-12 h-12 rounded-lg object-cover">
                                        @else
                                            <i class="fas fa-store text-orange-600 text-lg"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $chef->chefProfile->business_name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            @if(!empty($chef->chefProfile->cuisines) && is_array($chef->chefProfile->cuisines))
                                                {{ implode(', ', array_slice($chef->chefProfile->cuisines, 0, 2)) }}
                                            @else
                                                <span class="italic">No cuisines listed</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Owner Info --}}
                            <td class="p-4 text-gray-600">
                                <div class="font-medium text-gray-900">{{ $chef->first_name }} {{ $chef->last_name }}</div>
                                <div class="text-xs text-gray-400">{{ $chef->email }}</div>
                                <div class="text-xs text-gray-400">{{ $chef->phone }}</div>
                            </td>

                            {{-- Performance --}}
                            <td class="p-4">
                                <div class="space-y-1">
                                    <div class="text-sm font-bold text-green-600">₦{{ number_format($chef->chef_orders_sum_total_amount ?? 0) }}</div>
                                    <div class="text-xs text-gray-400">{{ $chef->chef_orders_count ?? 0 }} orders</div>
                                </div>
                            </td>

                            {{-- Location --}}
                            <td class="p-4 text-gray-500">
                                @if($chef->chefProfile && $chef->chefProfile->city)
                                    <i class="fas fa-map-marker-alt text-red-400 mr-1"></i> {{ $chef->chefProfile->city }}
                                @else
                                    <span class="italic text-gray-400">Unknown</span>
                                @endif
                            </td>

                            {{-- Verification Badge --}}
                            <td class="p-4">
                                @if($chef->chefProfile && $chef->chefProfile->is_verified)
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 inline-flex items-center">
                                        <i class="fas fa-check-circle mr-1"></i> Verified
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 inline-flex items-center">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </span>
                                @endif
                            </td>

                            {{-- ACTIONS COLUMN --}}
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($chef->chefProfile && !$chef->chefProfile->is_verified)
                                        <form action="{{ route('admin.chef.verify', $chef->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to verify this kitchen?');">
                                            @csrf
                                            <button type="submit" class="text-xs bg-green-600 text-white px-3 py-1.5 rounded-lg hover:bg-green-700 shadow-sm transition-colors font-bold">
                                                <i class="fas fa-check mr-1"></i> Verify
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <a href="{{ route('chef.show', $chef->id) }}" target="_blank" 
                                        class="text-xs border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg hover:bg-gray-50 transition-colors font-bold inline-flex items-center">
                                        <i class="fas fa-external-link-alt mr-1"></i> View
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-400">
                                <i class="fas fa-store text-4xl mb-3 block"></i>
                                No kitchens registered yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t bg-gray-50">
            {{ $chefs->links() }}
        </div>
    </div>
</div>
@endsection
