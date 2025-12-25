@extends('layouts.dashboard')

@section('title', 'Chef Dashboard - ChooseChow')

@section('content')
<div class="dashboard-container">
    {{-- Header --}}
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="dashboard-title h2 fw-bold">Welcome back, Chef {{ $chef->first_name }}! 👨‍🍳</h1>
                <p class="text-muted">Here's what's happening in your kitchen today.</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('chef.menus.create') }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus me-2"></i>New Menu Item
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        {{-- Pending Orders (Priority) --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted small text-uppercase fw-bold">Pending Orders</div>
                        <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded p-2">
                            <i class="fas fa-bell fa-lg"></i>
                        </div>
                    </div>
                    <h2 class="mb-0 fw-bold">{{ $stats['pending_orders'] }}</h2>
                    <small class="text-{{ $stats['pending_orders'] > 0 ? 'danger' : 'muted' }}">
                        {{ $stats['pending_orders'] > 0 ? 'Action required!' : 'All caught up' }}
                    </small>
                </div>
            </div>
        </div>

        {{-- Total Revenue --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted small text-uppercase fw-bold">Total Revenue</div>
                        <div class="icon-shape bg-success bg-opacity-10 text-success rounded p-2">
                            <i class="fas fa-wallet fa-lg"></i>
                        </div>
                    </div>
                    <h2 class="mb-0 fw-bold">&#8358;{{ number_format($stats['total_revenue']) }}</h2>
                    <small class="text-success">Lifetime Earnings</small>
                </div>
            </div>
        </div>

        {{-- Total Orders --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted small text-uppercase fw-bold">Total Orders</div>
                        <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded p-2">
                            <i class="fas fa-shopping-bag fa-lg"></i>
                        </div>
                    </div>
                    <h2 class="mb-0 fw-bold">{{ number_format($stats['total_orders']) }}</h2>
                    <small class="text-muted">Completed orders</small>
                </div>
            </div>
        </div>

        {{-- Rating --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted small text-uppercase fw-bold">Rating</div>
                        <div class="icon-shape bg-info bg-opacity-10 text-info rounded p-2">
                            <i class="fas fa-star fa-lg"></i>
                        </div>
                    </div>
                    <h2 class="mb-0 fw-bold">{{ $stats['avg_rating'] }}</h2>
                    <small class="text-muted">Average from reviews</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Recent Orders Table --}}
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">Recent Orders</h5>
                    <a href="{{ route('chef.orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Order ID</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentOrders as $order)
                                <tr>
                                    <td class="ps-4 fw-bold">#{{ $order->order_number }}</td>
                                    <td>{{ $order->customer->full_name ?? 'Guest' }}</td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 150px;" title="{{ $order->items->pluck('name')->join(', ') }}">
                                            @if($order->items->count() > 0)
                                                {{ $order->items->first()->name }}
                                                @if($order->items->count() > 1)
                                                    <span class="text-muted small">+{{ $order->items->count() - 1 }} more</span>
                                                @endif
                                            @else
                                                <span class="text-muted">No items</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="fw-bold">&#8358;{{ number_format($order->total_amount) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status_color }} rounded-pill">
                                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('chef.orders.show', $order) }}" class="btn btn-sm btn-light border">
                                            Details
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                            <p>No orders received yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Popular Menus / Quick Actions --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('chef.menus.index') }}" class="btn btn-outline-primary text-start">
                            <i class="fas fa-utensils me-2"></i> Manage Menus
                        </a>
                        <a href="{{ route('chef.orders.index') }}" class="btn btn-outline-success text-start">
                            <i class="fas fa-list-check me-2"></i> Manage Orders
                        </a>
                        <a href="{{ route('chef.profile.edit') }}" class="btn btn-outline-secondary text-start">
                            <i class="fas fa-cog me-2"></i> Chef Profile
                        </a>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold">Top Performing Items</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse ($popularMenus as $menu)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                                <div>
                                    <div class="fw-bold text-truncate" style="max-width: 180px;">{{ $menu->name }}</div>
                                    <small class="text-muted">{{ $menu->order_count }} orders</small>
                                </div>
                                <span class="badge bg-light text-dark border">
                                    {{ $menu->average_rating }} <i class="fas fa-star text-warning small"></i>
                                </span>
                            </li>
                        @empty
                            <li class="list-group-item text-center py-4 text-muted">
                                No data available yet.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Simple "Realtime" Polling
    // Reloads the page every 60 seconds to check for new orders
    setTimeout(function() {
        window.location.reload();
    }, 60000);
</script>
@endsection