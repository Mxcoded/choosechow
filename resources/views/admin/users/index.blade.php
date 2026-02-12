@extends('layouts.dashboard')

@section('title', 'Admin - Customers')
@section('page_title', 'Customer Management')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-xs uppercase tdark:text-gray-300 font-semibold border-b">
                    <tr>
                        <th class="p-4">Name</th>
                        <th class="p-4">Contact</th>
                        <th class="p-4">Joined</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 font-medium text-gray-900 dark:text-gray-100">
                                {{ $user->first_name }} {{ $user->last_name }}
                            </td>
                            <td class="p-4 text-gray-600 dark:text-gray-400">
                                <div>{{ $user->email }}</div>
                                <div class="text-xs text-gray-400">{{ $user->phone }}</div>
                            </td>
                            <td class="p-4 tdark:text-gray-300">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="p-4">
                                @if($user->status === 'blocked')
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                        Blocked
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                        Active
                                    </span>
                                @endif
                            </td>
                            
                            {{-- ACTIONS COLUMN --}}
                            <td class="p-4 text-right">
                                <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @if($user->status === 'active' || $user->status === null)
                                        <button type="submit" class="text-xs border border-red-200 text-red-600 px-3 py-1 rounded hover:bg-red-50 transition-colors" onclick="return confirm('Are you sure you want to BLOCK this user?');">
                                            Block User
                                        </button>
                                    @else
                                        <button type="submit" class="text-xs bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 shadow-sm transition-colors" onclick="return confirm('Re-activate this user?');">
                                            Activate User
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center tdark:text-gray-300">No customers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection