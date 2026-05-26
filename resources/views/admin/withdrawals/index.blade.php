@extends('layouts.dashboard')

@section('title', 'Admin - Payouts')
@section('page_title', 'Payout Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Total Requests</p>
                    <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ number_format($stats['total_requests']) }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-file-invoice-dollar text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 border-l-4 border-l-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Pending</p>
                    <p class="text-2xl font-extrabold text-yellow-600 mt-1">{{ number_format($stats['pending']) }}</p>
                    <p class="text-xs text-gray-400 mt-1">₦{{ number_format($stats['pending_amount']) }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Paid Out</p>
                    <p class="text-2xl font-extrabold text-green-600 mt-1">{{ number_format($stats['approved']) }}</p>
                    <p class="text-xs text-gray-400 mt-1">₦{{ number_format($stats['approved_amount']) }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Rejected</p>
                    <p class="text-2xl font-extrabold text-red-600 mt-1">{{ number_format($stats['rejected']) }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-lg">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Pending Alert --}}
    @if($stats['pending'] > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-center justify-between">
        <div class="flex items-center">
            <div class="bg-yellow-100 p-2 rounded-lg mr-4">
                <i class="fas fa-exclamation-triangle text-yellow-600"></i>
            </div>
            <div>
                <p class="font-bold text-yellow-800">{{ $stats['pending'] }} Pending Payout Request{{ $stats['pending'] > 1 ? 's' : '' }}</p>
                <p class="text-sm text-yellow-600">Total amount: ₦{{ number_format($stats['pending_amount']) }}</p>
            </div>
        </div>
        <a href="{{ route('admin.withdrawals.index', ['status' => 'pending']) }}" class="bg-yellow-600 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-yellow-700 transition">
            Review Now
        </a>
    </div>
    @endif

    {{-- Search & Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form action="{{ route('admin.withdrawals.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Search by chef name or email..." 
                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            <div>
                <select name="status" class="w-full md:w-40 py-2 px-3 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Paid</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-gray-900 text-white px-6 py-2 rounded-lg font-bold hover:bg-gray-800 transition">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.withdrawals.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg font-bold hover:bg-gray-200 transition">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Withdrawals Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold border-b">
                    <tr>
                        <th class="p-4">Chef</th>
                        <th class="p-4">Amount</th>
                        <th class="p-4">Bank Details</th>
                        <th class="p-4">Requested</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($withdrawals as $request)
                        <tr class="hover:bg-gray-50 {{ $request->status == 'pending' ? 'bg-yellow-50/50' : '' }}">
                            {{-- Chef Info --}}
                            <td class="p-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-orange-600 font-bold">{{ strtoupper(substr($request->user->first_name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $request->user->first_name }} {{ $request->user->last_name }}</div>
                                        <div class="text-xs text-gray-400">{{ $request->user->email }}</div>
                                        @if($request->user->chefProfile)
                                            <div class="text-xs text-gray-500">{{ $request->user->chefProfile->business_name }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            
                            {{-- Amount --}}
                            <td class="p-4">
                                <span class="text-lg font-extrabold text-gray-900">₦{{ number_format($request->amount, 2) }}</span>
                            </td>
                            
                            {{-- Bank Info --}}
                            <td class="p-4 text-gray-600">
                                <div class="font-bold text-xs uppercase text-gray-800">{{ $request->bank_name }}</div>
                                <div class="font-mono text-sm">{{ $request->account_number }}</div>
                                <div class="text-xs text-gray-400">{{ $request->account_name }}</div>
                            </td>

                            {{-- Requested Date --}}
                            <td class="p-4 text-gray-400 text-xs">
                                <div>{{ $request->created_at->format('M d, Y') }}</div>
                                <div>{{ $request->created_at->format('h:i A') }}</div>
                                <div class="text-gray-500 mt-1">{{ $request->created_at->diffForHumans() }}</div>
                            </td>
                            
                            {{-- Status Badge --}}
                            <td class="p-4">
                                @if($request->status == 'pending')
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 inline-flex items-center">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </span>
                                @elseif($request->status == 'approved')
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 inline-flex items-center">
                                        <i class="fas fa-check-circle mr-1"></i> Paid
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 inline-flex items-center">
                                        <i class="fas fa-times-circle mr-1"></i> Rejected
                                    </span>
                                @endif
                            </td>
                            
                            {{-- Action Buttons --}}
                            <td class="p-4 text-right">
                                @if($request->status == 'pending')
                                    <div class="flex items-center justify-end gap-2">
                                        <form action="{{ route('admin.withdrawals.approve', $request->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Mark this payout as PAID? This confirms you have sent the money.');">
                                            @csrf
                                            <button type="submit" class="bg-green-600 text-white px-3 py-1.5 rounded-lg text-xs hover:bg-green-700 shadow-sm font-bold">
                                                <i class="fas fa-check mr-1"></i> Mark Paid
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.withdrawals.reject', $request->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Reject this request? The amount will be refunded to the chef\'s wallet.');">
                                            @csrf
                                            <button type="submit" class="border border-red-200 text-red-600 px-3 py-1.5 rounded-lg text-xs hover:bg-red-50 font-bold">
                                                <i class="fas fa-times mr-1"></i> Reject
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs italic">
                                        @if($request->status == 'approved')
                                            Completed {{ $request->updated_at->diffForHumans() }}
                                        @else
                                            Rejected {{ $request->updated_at->diffForHumans() }}
                                        @endif
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-400">
                                <i class="fas fa-money-bill-wave text-4xl mb-3 block"></i>
                                No withdrawal requests found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t bg-gray-50">
            {{ $withdrawals->links() }}
        </div>
    </div>
</div>
@endsection
