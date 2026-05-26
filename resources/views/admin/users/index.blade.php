@extends('layouts.dashboard')

@section('title', 'Admin - Customers')
@section('page_title', 'Customer Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Total Customers</p>
                    <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Active</p>
                    <p class="text-2xl font-extrabold text-green-600 mt-1">{{ number_format($stats['active']) }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-user-check text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Blocked</p>
                    <p class="text-2xl font-extrabold text-red-600 mt-1">{{ number_format($stats['blocked']) }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-lg">
                    <i class="fas fa-user-slash text-red-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">New This Month</p>
                    <p class="text-2xl font-extrabold text-purple-600 mt-1">{{ number_format($stats['new_this_month']) }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-user-plus text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form action="{{ route('admin.users') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Search by name, email, or phone..." 
                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            <div>
                <select name="status" class="w-full md:w-40 py-2 px-3 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-gray-900 text-white px-6 py-2 rounded-lg font-bold hover:bg-gray-800 transition">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.users') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg font-bold hover:bg-gray-200 transition">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold border-b">
                    <tr>
                        <th class="p-4">Customer</th>
                        <th class="p-4">Contact</th>
                        <th class="p-4">Orders</th>
                        <th class="p-4">Joined</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-red-600 font-bold">{{ strtoupper(substr($user->first_name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</div>
                                        <div class="text-xs text-gray-400">ID: #{{ $user->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-gray-600">
                                <div>{{ $user->email }}</div>
                                <div class="text-xs text-gray-400">{{ $user->phone ?? 'No phone' }}</div>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-bold">
                                    {{ $user->orders_count ?? 0 }} orders
                                </span>
                            </td>
                            <td class="p-4 text-gray-500">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="p-4">
                                @if($user->status === 'blocked')
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                        <i class="fas fa-ban mr-1"></i> Blocked
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                        <i class="fas fa-check-circle mr-1"></i> Active
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @if($user->status === 'active' || $user->status === null)
                                        <button type="submit" class="text-xs border border-red-200 text-red-600 px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors" 
                                            onclick="return confirm('Are you sure you want to BLOCK this user?');">
                                            <i class="fas fa-ban mr-1"></i> Block
                                        </button>
                                    @else
                                        <button type="submit" class="text-xs bg-green-600 text-white px-3 py-1.5 rounded-lg hover:bg-green-700 shadow-sm transition-colors" 
                                            onclick="return confirm('Re-activate this user?');">
                                            <i class="fas fa-check mr-1"></i> Activate
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-400">
                                <i class="fas fa-users text-4xl mb-3 block"></i>
                                No customers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t bg-gray-50">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
