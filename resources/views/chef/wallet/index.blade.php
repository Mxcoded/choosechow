@extends('layouts.dashboard')

@section('title', 'My Wallet')
@section('page_title', 'Wallet & Payouts')

@section('content')
<div class="max-w-6xl mx-auto" x-data="{ showWithdrawModal: false }">

    {{-- 1. BALANCE CARD --}}
    <div class="bg-gradient-to-r from-gray-900 to-gray-800 rounded-2xl shadow-xl p-8 text-white mb-8 relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-10 transform translate-x-10 -translate-y-10">
            <i class="fas fa-wallet text-9xl"></i>
        </div>
        
        <div class="relative z-10">
            <h2 class="text-gray-400 font-medium mb-1">Available Balance</h2>
            <div class="text-4xl font-bold mb-6">₦{{ number_format($wallet->balance, 2) }}</div>
            
            <div class="flex gap-4">
                {{-- Button Triggers Modal --}}
                <button @click="showWithdrawModal = true" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition-transform transform hover:scale-105 flex items-center">
                    <i class="fas fa-university mr-2"></i> Request Payout
                </button>
            </div>
            <p class="text-xs tdark:text-gray-300 mt-4">* Minimum withdrawal is ₦1,000. Payouts are processed within 24 hours.</p>
        </div>
    </div>

    {{-- 2. TRANSACTION HISTORY --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 text-lg">Transaction History</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-xs uppercase tdark:text-gray-300 font-semibold">
                    <tr>
                        <th class="p-4">Reference</th>
                        <th class="p-4">Date</th>
                        <th class="p-4">Description</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 font-mono text-xs tdark:text-gray-300">{{ $trx->reference }}</td>
                            <td class="p-4 text-sm text-gray-600 dark:text-gray-400">{{ $trx->created_at->format('M d, Y') }}</td>
                            <td class="p-4 text-sm text-gray-800 font-medium">{{ $trx->description }}</td>
                            <td class="p-4">
                                @if($trx->status == 'completed')
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">Completed</span>
                                @elseif($trx->status == 'pending')
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">Pending</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">Failed</span>
                                @endif
                            </td>
                            <td class="p-4 text-right font-bold {{ $trx->type == 'earning' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $trx->type == 'earning' ? '+' : '-' }}₦{{ number_format($trx->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center tdark:text-gray-300">
                                <p>No transactions yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $transactions->links() }}
        </div>
    </div>

    {{-- 3. WITHDRAWAL MODAL (Alpine.js) --}}
    <div x-show="showWithdrawModal" 
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 transform transition-all" @click.away="showWithdrawModal = false">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Request Payout</h3>
                <button @click="showWithdrawModal = false" class="text-gray-400 hover:text-gray-600 dark:text-gray-400">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('chef.wallet.withdraw') }}" method="POST">
                @csrf
                
                {{-- Amount --}}
                <div class="mb-4">
                    <label class="block text-sm font-bold dark:text-gray-300 mb-1">Amount (₦)</label>
                    <input type="number" name="amount" min="1000" step="0.01" max="{{ $wallet->balance }}" required placeholder="Enter amount..."
                           class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                    <p class="text-xs tdark:text-gray-300 mt-1">Available: ₦{{ number_format($wallet->balance, 2) }}</p>
                </div>

                {{-- Bank Name --}}
                <div class="mb-4">
                    <label class="block text-sm font-bold dark:text-gray-300 mb-1">Bank Name</label>
                    <select name="bank_name" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                        <option value="">Select Bank</option>
                        <option value="Access Bank">Access Bank</option>
                        <option value="GTBank">Guaranty Trust Bank (GTB)</option>
                        <option value="First Bank">First Bank</option>
                        <option value="UBA">United Bank for Africa (UBA)</option>
                        <option value="Zenith Bank">Zenith Bank</option>
                        <option value="Kuda">Kuda Microfinance</option>
                        <option value="OPay">OPay</option>
                        <option value="PalmPay">PalmPay</option>
                    </select>
                </div>

                {{-- Account Number --}}
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-bold dark:text-gray-300 mb-1">Account Number</label>
                        <input type="text" name="account_number" required placeholder="0123456789"
                               class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold dark:text-gray-300 mb-1">Account Name</label>
                        <input type="text" name="account_name" required placeholder="John Doe"
                               class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                    </div>
                </div>

                <button type="submit" class="w-full bg-red-600 text-white font-bold py-3 rounded-xl hover:bg-red-700 shadow-lg">
                    Submit Request
                </button>
            </form>
        </div>
    </div>

</div>
@endsection