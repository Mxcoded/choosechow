@extends('layouts.dashboard')

@section('title', 'My Orders')
@section('page_title', 'Order History')

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- Orders List --}}
    @if($orders->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100 dark:border-gray-700 text-xs uppercase tdark:text-gray-300 font-semibold">
                        <tr>
                            <th class="p-4">Order Ref</th>
                            <th class="p-4">Kitchen</th>
                            <th class="p-4">Date</th>
                            <th class="p-4">Total</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50 transition-colors">
                                {{-- Order Ref --}}
                                <td class="p-4 font-medium text-gray-900 dark:text-gray-100">
                                    #{{ $order->order_number }}
                                </td>

                                {{-- Kitchen (FIXED: access chefProfile) --}}
                                <td class="p-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-bold text-xs">
                                            {{ substr($order->chef->chefProfile->business_name ?? 'C', 0, 1) }}
                                        </div>
                                        <span class="text-sm font-medium dark:text-gray-300">
                                            {{ $order->chef->chefProfile->business_name ?? 'Unknown Kitchen' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Date --}}
                                <td class="p-4 text-sm tdark:text-gray-300">
                                    {{ $order->created_at->format('M d, Y h:i A') }}
                                </td>

                                {{-- Total --}}
                                <td class="p-4 font-bold text-gray-900 dark:text-gray-100">
                                    ₦{{ number_format($order->total_amount) }}
                                </td>

                                {{-- Status Badge --}}
                                <td class="p-4">
                                    @php
                                        $statusColors = [
                                            'pending_payment' => 'bg-yellow-100 text-yellow-800',
                                            'pending' => 'bg-blue-100 text-blue-800',
                                            'preparing' => 'bg-purple-100 text-purple-800',
                                            'ready' => 'bg-indigo-100 text-indigo-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            'paid' => 'bg-green-100 text-green-800', 
                                        ];
                                        $color = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                                        
                                        // Friendly Status Label
                                        $labels = [
                                            'pending_payment' => 'Unpaid',
                                            'pending' => 'Order Placed',
                                            'preparing' => 'Cooking',
                                            'ready' => 'Ready',
                                            'completed' => 'Delivered',
                                            'cancelled' => 'Cancelled',
                                            'paid' => 'Paid',
                                        ];
                                        $label = $labels[$order->status] ?? ucfirst($order->status);
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide {{ $color }}">
                                        {{ $label }}
                                    </span>
                                </td>

                                {{-- Action Column --}}
                                <td class="p-4 text-right">
                                    
                                    {{-- 1. If Unpaid --}}
                                    @if($order->status === 'pending_payment')
                                        <form action="{{ route('customer.orders.retry', $order->id) }}" method="GET" class="inline-block">
                                            <button type="submit" class="text-xs bg-red-600 text-white px-3 py-1.5 rounded-lg font-bold hover:bg-red-700">
                                                Pay Now
                                            </button>
                                        </form>

                                    {{-- 2. If Completed (Allow Review) --}}
                                    @elseif($order->status === 'completed')
                                        {{-- Check if already reviewed --}}
                                        @php
                                            $alreadyReviewed = \App\Models\Review::where('order_id', $order->id)->exists();
                                        @endphp

                                        @if(!$alreadyReviewed)
                                            <button onclick="openReviewModal('{{ $order->id }}')" class="text-xs bg-yellow-500 text-white px-3 py-1.5 rounded-lg font-bold hover:bg-yellow-600">
                                                <i class="fas fa-star mr-1"></i> Rate
                                            </button>
                                        @else
                                            <span class="text-xs text-yellow-600 font-bold mr-2"><i class="fas fa-check"></i> Rated</span>
                                        @endif
                                        
                                        <a href="{{ route('customer.orders.show', $order->id) }}" class="text-xs bg-gray-100 text-gray-600 dark:text-gray-400 px-3 py-1.5 rounded-lg font-bold hover:bg-gray-200">
                                            Receipt
                                        </a>

                                    {{-- 3. Default (View Receipt) --}}
                                    @else
                                        <a href="{{ route('customer.orders.show', $order->id) }}" class="text-xs bg-gray-100 text-gray-600 dark:text-gray-400 px-3 py-1.5 rounded-lg font-bold hover:bg-gray-200">
                                            View
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                {{ $orders->links() }}
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <div class="text-center py-20 bg-white rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-receipt text-gray-300 text-3xl"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">No orders yet</h2>
            <p class="tdark:text-gray-300 mb-6">Hungry? Explore kitchens near you.</p>
            <a href="{{ route('chef.index') }}" class="bg-red-600 text-white font-bold py-3 px-6 rounded-xl hover:bg-red-700 transition-colors shadow-lg shadow-red-200">
                Find Chow
            </a>
        </div>
    @endif

</div>

{{-- Review Modal (Hidden by default) --}}
<div id="reviewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Rate your Meal</h3>
        
        <form action="{{ route('reviews.store') }}" method="POST">
            @csrf
            <input type="hidden" name="order_id" id="modalOrderId">
            
            <div class="mb-4">
                <label class="block text-sm font-medium dark:text-gray-300 mb-1">Rating</label>
                <div class="flex gap-4 text-2xl text-gray-300">
                    @for($i=1; $i<=5; $i++)
                        <label class="cursor-pointer hover:text-yellow-400 transition-colors">
                            <input type="radio" name="rating" value="{{ $i }}" class="hidden peer" required>
                            <i class="fas fa-star peer-checked:text-yellow-400"></i>
                        </label>
                    @endfor
                </div>
                <p class="text-xs tdark:text-gray-300 mt-1">Select a star to rate</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium dark:text-gray-300 mb-1">Comment (Optional)</label>
                <textarea name="comment" rows="3" class="w-full border rounded-lg p-2 focus:ring-red-500 focus:border-red-500" placeholder="How was the food?"></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('reviewModal').classList.add('hidden'); document.getElementById('reviewModal').classList.remove('flex')" class="px-4 py-2 tdark:text-gray-300 hover:dark:text-gray-300">Cancel</button>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-red-700">Submit Review</button>
            </div>
        </form>
    </div>
</div>

{{-- Script to handle Modal --}}
<script>
    function openReviewModal(orderId) {
        document.getElementById('modalOrderId').value = orderId;
        const modal = document.getElementById('reviewModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
</script>

@endsection