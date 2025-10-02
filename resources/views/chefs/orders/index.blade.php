@extends('layouts.dashboard')

@section('title', 'Manage Orders - Chef Dashboard')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="dashboard-title">Order Management 📝</h1>
                <p class="dashboard-subtitle">Track and update the status of all customer orders</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('chef.menus') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-utensils me-2"></i>Manage Menus
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        @php
            $cardStatuses = [
                'pending' => ['label' => 'New Orders', 'icon' => 'fas fa-bell', 'color' => 'danger'],
                'confirmed' => ['label' => 'Confirmed', 'icon' => 'fas fa-check-double', 'color' => 'warning'],
                'preparing' => ['label' => 'Preparing', 'icon' => 'fas fa-fire-alt', 'color' => 'info'],
                'delivered' => ['label' => 'Delivered', 'icon' => 'fas fa-truck-loading', 'color' => 'success'],
                'all' => ['label' => 'Total Orders', 'icon' => 'fas fa-shopping-bag', 'color' => 'primary'],
            ];
        @endphp

        @foreach($cardStatuses as $key => $card)
            <div class="col-lg-2-4 col-md-6 mb-3"> {{-- Use col-lg-2-4 for a nice 5-column layout --}}
                <a href="{{ route('chef.orders', ['status' => $key]) }}" class="text-decoration-none">
                    <div class="dashboard-card stat-card {{ $status == $key ? 'border-' . $card['color'] . ' border-3 shadow' : '' }}">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="stat-number mb-0 text-{{ $card['color'] }}">{{ $stats[$key] ?? 0 }}</h3>
                                <p class="stat-label mb-0">{{ $card['label'] }}</p>
                            </div>
                            <div class="stat-icon bg-{{ $card['color'] }} opacity-75">
                                <i class="{{ $card['icon'] }}"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="dashboard-card">
        <div class="card-header">
            <h5 class="card-title">
                {{ $cardStatuses[$status]['label'] ?? 'Order List' }}
                <span class="badge bg-secondary ms-2">{{ $stats[$status] ?? 0 }}</span>
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if($orders->count() > 0)
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td class="fw-bold">{{ $order->order_number }}</td>
                                    <td>{{ $order->customer->name }}</td>
                                    <td>{{ $order->items->count() }} item(s)</td>
                                    <td>&#8358;{{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        {{-- NOTE: This assumes Order model has getStatusColorAttribute implemented --}}
                                        <span class="badge bg-{{ $order->status_color }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('chef.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-box-open fa-3x mb-3"></i>
                        <p>No {{ strtolower($cardStatuses[$status]['label'] ?? 'orders') }} found.</p>
                    </div>
                @endif
            </div>

            <div class="d-flex justify-content-center">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>

<style>
/* Add Custom Styles to support the 5-column layout and card consistency */

/* Custom class to simulate col-2.4 (5 equal columns in a 12-column grid) */
@media (min-width: 992px) {
    .col-lg-2-4 {
        width: 20%; 
        flex: 0 0 auto;
        padding-right: var(--bs-gutter-x, 0.75rem);
        padding-left: var(--bs-gutter-x, 0.75rem);
    }
}

/* Base Stat Card Style (consistent with dashboard structure) */
.dashboard-card.stat-card {
    transition: all 0.2s ease-in-out;
    padding: 10px;
    min-height: 95px;
}

.dashboard-card.stat-card:hover {
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
}

.dashboard-card.stat-card .stat-number {
    font-size: 1.8rem;
    font-weight: bold;
    line-height: 1.2;
}

.dashboard-card.stat-card .stat-label {
    font-size: 0.9rem;
    color: #6c757d;
}

.dashboard-card.stat-card .stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.3rem;
    /* Opacity is set in the template for better color blending */
}
</style>
@endsection