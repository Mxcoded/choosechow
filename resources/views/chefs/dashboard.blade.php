@extends('layouts.dashboard')

@section('title', 'Chef Dashboard - ChooseChow')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                {{-- Use $chef variable as defined in controller --}}
                <h1 class="dashboard-title">Welcome back, Chef {{ $chef->name }}! 👨‍🍳</h1>
                <p class="dashboard-subtitle">Manage your culinary business and delight your customers</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('chef.menus.create') }}" class="btn btn-primary me-2">
                    <i class="fas fa-plus me-2"></i>Add New Menu
                </a>
                <a href="{{ route('chef.menus') }}" class="btn btn-outline-primary">
                    <i class="fas fa-utensils me-2"></i>Manage Menus
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        {{-- Total Orders Card --}}
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ number_format($stats['total_orders']) }}</h3>
                    <p class="stat-label">Total Orders</p>
                    <small class="stat-change positive">Tracked across all time</small>
                </div>
            </div>
        </div>
        {{-- Pending Orders Card --}}
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $stats['pending_orders'] }}</h3>
                    <p class="stat-label">Pending Orders</p>
                    <small class="stat-change text-danger">{{ $stats['pending_orders'] > 0 ? 'Needs immediate attention' : 'All clear!' }}</small>
                </div>
            </div>
        </div>
        {{-- Total Revenue Card --}}
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-naira-sign"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">&#8358;{{ number_format($stats['total_revenue'], 2) }}</h3>
                    <p class="stat-label">Total Revenue</p>
                    <small class="stat-change positive">Lifetime earnings</small>
                </div>
            </div>
        </div>
        {{-- Average Rating Card --}}
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ number_format($stats['avg_rating'], 1) }}</h3>
                    <p class="stat-label">Average Rating</p>
                    <small class="stat-change positive">{{ $stats['avg_rating'] >= 4.5 ? 'Excellent!' : 'Good rating' }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title">Recent Orders</h5>
                    <a href="{{ route('chef.orders') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Menu Item</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentOrders as $order)
                                    {{-- Placeholder Loop --}}
                                    <tr>
                                        <td>#ORD-{{ $order->id }}</td>
                                        <td>{{ $order->customer->name }}</td>
                                        <td>{{ Str::limit($order->menuItem->name, 25) }}</td>
                                        <td>&#8358;{{ number_format($order->amount, 2) }}</td>
                                        <td><span class="badge bg-{{ $order->status_color }}">{{ ucfirst($order->status) }}</span></td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-primary">Details</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No recent orders yet.</td>
                                    </tr>
                                @endforelse
                                {{-- Hardcoded placeholder for visual stability --}}
                                @if (count($recentOrders) == 0)
                                <tr>
                                    <td>#ORD-001</td><td>e.g Sarah Johnson</td><td>Jollof Rice</td><td>&#8358;3,000.00</td><td><span class="badge bg-warning">Preparing</span></td><td><button class="btn btn-sm btn-primary">Update</button></td>
                                </tr>
                                <tr>
                                    <td>#ORD-002</td><td>e.g Mike Chen</td><td>Meat Pie</td><td>&#8358;2,200.00</td><td><span class="badge bg-success">Ready</span></td><td><button class="btn btn-sm btn-success">Complete</button></td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="dashboard-card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('chef.menus.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create New Menu
                        </a>
                        <a href="{{ route('chef.menus') }}" class="btn btn-outline-primary">
                            <i class="fas fa-utensils me-2"></i>Manage All Menus
                        </a>
                        <a href="{{ route('chef.orders') }}" class="btn btn-outline-success">
                            <i class="fas fa-clipboard-list me-2"></i>View Orders
                        </a>
                        <a href="{{ route('chef.profile') }}" class="btn btn-outline-info">
                            <i class="fas fa-cog me-2"></i>Profile Settings
                        </a>
                    </div>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title">Popular Items</h5>
                </div>
                <div class="card-body">
                    @forelse ($popularMenus as $menu)
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                            <div>
                                <h6 class="mb-0">{{ Str::limit($menu->name, 30) }}</h6>
                                <small class="text-muted">{{ number_format($menu->order_count) }} Orders</small>
                            </div>
                            <span class="badge bg-primary fs-6">{{ number_format($menu->average_rating, 1) }} <i class="fas fa-star"></i></span>
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <i class="fas fa-chart-line fa-2x text-muted mb-2"></i>
                            <p class="text-muted small">No menu items to rank yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection