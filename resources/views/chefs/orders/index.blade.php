@extends('layouts.dashboard')

@section('title', 'Manage Orders - Chef Dashboard')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="dashboard-title h2 fw-bold text-dark">Order Management 📝</h1>
                <p class="text-muted">Track and update the status of all customer orders</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('chef.menus.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-utensils me-2"></i>Manage Menus
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @php
            $cardStatuses = [
                'pending' => ['label' => 'New Orders', 'icon' => 'fas fa-bell', 'color' => 'danger'],
                'confirmed' => ['label' => 'Confirmed', 'icon' => 'fas fa-check-double', 'color' => 'warning'],
                'preparing' => ['label' => 'Preparing', 'icon' => 'fas fa-fire-alt', 'color' => 'info'],
                'delivered' => ['label' => 'Delivered', 'icon' => 'fas fa-truck-loading', 'color' => 'success'],
                'all' => ['label' => 'All Orders', 'icon' => 'fas fa-list', 'color' => 'secondary'],
            ];
        @endphp

        @foreach($cardStatuses as $key => $card)
            <div class="col">
                {{-- ROUTE FIX: chef.orders.index --}}
                <a href="{{ route('chef.orders.index', ['status' => $key]) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 {{ $status == $key ? 'ring-2 ring-primary' : '' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0 fw-bold text-{{ $card['color'] }}">{{ $stats[$key] ?? 0 }}</h3>
                                    <small class="text-muted">{{ $card['label'] }}</small>
                                </div>
                                <div class="bg-{{ $card['color'] }} bg-opacity-10 text-{{ $card['color'] }} rounded-circle p-3">
                                    <i class="{{ $card['icon'] }}"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0 fw-bold">
                {{ $cardStatuses[$status]['label'] ?? 'Orders' }}
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Order #</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="ps-4 fw-bold">#{{ $order->order_number }}</td>
                                <td>{{ $order->customer->full_name ?? $order->customer->first_name }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $order->items->count() }} items</span>
                                </td>
                                <td class="fw-bold">₦{{ number_format($order->total_amount) }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->status_color }} text-uppercase">
                                        {{ str_replace('_', ' ', $order->status) }}
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    {{ $order->created_at->format('M d, H:i') }}
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('chef.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-clipboard-list fa-3x mb-3 opacity-25"></i>
                                    <p>No orders found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($orders->hasPages())
            <div class="card-footer bg-white">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection