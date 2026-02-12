@extends('layouts.dashboard')

@section('title', 'Admin - Chefs')
@section('page_title', 'Chef & Kitchen Management')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-xs uppercase tdark:text-gray-300 font-semibold border-b">
                    <tr>
                        <th class="p-4">Kitchen Name</th>
                        <th class="p-4">Owner</th>
                        <th class="p-4">Location</th>
                        <th class="p-4">Verification</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($chefs as $chef)
                        <tr class="hover:bg-gray-50">
                            {{-- Kitchen Info --}}
                            <td class="p-4">
                                <div class="font-bold text-gray-900 dark:text-gray-100">{{ $chef->chefProfile->business_name ?? 'N/A' }}</div>
                                {{-- ----- FIX IS HERE ----- --}}
                                <div class="text-xs tdark:text-gray-300 mt-1">
                                    @if(!empty($chef->chefProfile->cuisines) && is_array($chef->chefProfile->cuisines))
                                        {{ implode(', ', $chef->chefProfile->cuisines) }}
                                    @else
                                        <span class="italic">No cuisines listed</span>
                                    @endif
                                </div>
                                {{-- ----------------------- --}}
                            </td>

                            {{-- Owner Info --}}
                            <td class="p-4 text-gray-600 dark:text-gray-400">
                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $chef->first_name }} {{ $chef->last_name }}</div>
                                <div class="text-xs">{{ $chef->phone }}</div>
                                <div class="text-xs">{{ $chef->email }}</div>
                            </td>

                            {{-- Location --}}
                            <td class="p-4 tdark:text-gray-300">
                                @if($chef->chefProfile && $chef->chefProfile->city)
                                    {{ $chef->chefProfile->city }}
                                @else
                                    <span class="italic">Unknown</span>
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
                            <td class="p-4 text-right space-x-2">
                                @if($chef->chefProfile && !$chef->chefProfile->is_verified)
                                    <form action="{{ route('admin.chef.verify', $chef->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to verify this kitchen?');">
                                        @csrf
                                        <button type="submit" class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 shadow-sm transition-colors font-bold">
                                            Verify Now
                                        </button>
                                    </form>
                                @endif
                                
                                {{-- Link to public store page --}}
                                <a href="{{ route('chef.show', $chef->id) }}" target="_blank" class="text-xs tdark:text-gray-300 hover:text-red-600 font-bold ml-2 inline-flex items-center">
                                    View Store <i class="fas fa-external-link-alt ml-1"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center tdark:text-gray-300">No chefs registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t">
            {{ $chefs->links() }}
        </div>
    </div>
</div>
@endsection