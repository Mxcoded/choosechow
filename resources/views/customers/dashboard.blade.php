@extends('layouts.dashboard')

@section('title', 'Customer Dashboard - ChooseChow')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="dashboard-title h3 fw-bold">Welcome back, {{ $user->first_name }}! 🍽️</h1>
                <p class="text-muted">Discover amazing meals from talented local chefs</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('chefs.index') }}" class="btn btn-primary me-2">
                    <i class="fas fa-search me-2"></i>Find Food
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3 text-primary">
                        <i class="fas fa-shopping-bag fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold">{{ $stats['total_orders'] }}</h3>
                        <p class="text-muted small mb-0">Total Orders</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3 text-success">
                        <i class="fas fa-heart fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold">{{ $stats['favorite_chefs'] }}</h3>
                        <p class="text-muted small mb-0">Favorite Chefs</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3 text-warning">
                        <i class="fas fa-wallet fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold">₦{{ number_format($stats['total_spent']) }}</h3>
                        <p class="text-muted small mb-0">Total Spent</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">Recent Orders</h5>
                    <a href="{{ route('customer.orders') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentOrders as $order)
                            <div class="list-group-item p-3">
                                <div class="d-flex align-items-center">
                                    {{-- Chef Avatar --}}
                                    <img src="{{ $order->chef->avatar ? asset('storage/'.$order->chef->avatar) : 'https://ui-avatars.com/api/?name='.$order->chef->first_name }}" 
                                         class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                    
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="mb-0 fw-bold">
                                                {{ $order->chef->chefProfile->business_name ?? 'Chef ' . $order->chef->last_name }}
                                            </h6>
                                            <span class="fw-bold">₦{{ number_format($order->total_amount) }}</span>
                                        </div>
                                        <small class="text-muted">
                                            {{ $order->items->count() }} Items • {{ $order->created_at->format('M d, Y') }}
                                        </small>
                                        <div class="mt-1">
                                            <span class="badge bg-{{ $order->status_color }}">
                                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-utensils fa-3x text-muted mb-3 opacity-25"></i>
                                <p class="text-muted">You haven't placed any orders yet.</p>
                                <a href="{{ route('chefs.index') }}" class="btn btn-primary">Find Food Now</a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('chefs.index') }}" class="btn btn-primary text-start">
                            <i class="fas fa-search me-2"></i>Browse Chefs
                        </a>
                        <a href="{{ route('customer.profile') }}" class="btn btn-outline-secondary text-start">
                            <i class="fas fa-user-cog me-2"></i>Profile Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection