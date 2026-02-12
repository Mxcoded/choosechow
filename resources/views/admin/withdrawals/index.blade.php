@extends('layouts.dashboard')

@section('title', 'Admin - Payouts')
@section('page_title', 'Manage Payout Requests')

@section('content')
<div class="max-w-7xl mx-auto">
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-xs uppercase tdark:text-gray-300 font-semibold border-b">
                    <tr>
                        <th class="p-4">Chef</th>
                        <th class="p-4">Amount</th>
                        <th class="p-4">Bank Details</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($withdrawals as $request)
                        <tr class="hover:bg-gray-50">
                            {{-- Chef Info --}}
                            <td class="p-4 font-medium text-gray-900 dark:text-gray-100">
                                {{ $request->user->first_name }} {{ $request->user->last_name }}<br>
                                <span class="text-xs tdark:text-gray-300">{{ $request->user->email }}</span>
                            </td>
                            
                            {{-- Amount --}}
                            <td class="p-4 font-bold text-gray-800">
                                ₦{{ number_format($request->amount, 2) }}
                            </td>
                            
                            {{-- Bank Info --}}
                            <td class="p-4 text-gray-600 dark:text-gray-400">
                                <div class="font-bold text-xs uppercase">{{ $request->bank_name }}</div>
                                <div class="font-mono">{{ $request->account_number }}</div>
                                <div class="text-xs">{{ $request->account_name }}</div>
                            </td>
                            
                            {{-- Status Badge --}}
                            <td class="p-4">
                                @if($request->status == 'pending')
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">Pending</span>
                                @elseif($request->status == 'approved')
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">Paid</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">Rejected</span>
                                @endif
                            </td>
                            
                            {{-- Action Buttons --}}
                            <td class="p-4 text-right space-x-2">
                                @if($request->status == 'pending')
                                    {{-- Approve --}}
                                    <form action="{{ route('admin.withdrawals.approve', $request->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Mark as PAID?');">
                                        @csrf
                                        <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700 shadow-sm">
                                            Mark Paid
                                        </button>
                                    </form>

                                    {{-- Reject --}}
                                    <form action="{{ route('admin.withdrawals.reject', $request->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Reject and refund?');">
                                        @csrf
                                        <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700 shadow-sm">
                                            Reject
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400 text-xs italic">Completed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center tdark:text-gray-300">No withdrawal requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t">
            {{ $withdrawals->links() }}
        </div>
    </div>
</div>
@endsection