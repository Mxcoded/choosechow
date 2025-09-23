@extends('layouts.dashboard')

@section('title', 'Customer Dashboard - ChooseChow')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="dashboard-title">Welcome back, {{ $user->first_name }}! 🍽️</h1>
                <p class="dashboard-subtitle">Discover amazing meals from talented local chefs</p>
            </div>
            <div class="col-md-6 text-end">
                <button class="btn btn-primary me-2">
                    <i class="fas fa-search me-2"></i>Find Chefs
                </button>
                <button class="btn btn-outline-primary">
                    <i class="fas fa-heart me-2"></i>My Favorites
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $stats['total_orders'] }}</h3>
                    <p class="stat-label">Total Orders</p>
                    <small class="stat-change positive">+3 this month</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $stats['favorite_chefs'] }}</h3>
                    <p class="stat-label">Favorite Chefs</p>
                    <small class="stat-change">Following</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">&#8358;{{ number_format($stats['total_spent'], 2) }}</h3>
                    <p class="stat-label">Total Spent</p>
                    <small class="stat-change positive">Great taste!</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $stats['loyalty_points'] }}</h3>
                    <p class="stat-label">Loyalty Points</p>
                    <small class="stat-change positive">Keep earning!</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-8 mb-4">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title">Recent Orders</h5>
                    <a href="{{ route('customer.orders') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="order-list">
                        <div class="order-item">
                            <div class="order-image">
                                <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=80&h=80&fit=crop&crop=center" alt="Grilled Salmon">
                            </div>
                            <div class="order-details">
                                <h6 class="order-title">Grilled Salmon Deluxe</h6>
                                <p class="order-chef">Chef Maria Rodriguez</p>
                                <small class="order-date">Ordered on March 15, 2024</small>
                            </div>
                            <div class="order-status">
                                <span class="badge bg-success">Delivered</span>
                                <p class="order-price">&#8358;28.50</p>
                            </div>
                            <div class="order-actions">
                                <button class="btn btn-sm btn-outline-primary">Reorder</button>
                                <button class="btn btn-sm btn-outline-secondary">Review</button>
                            </div>
                        </div>

                        <div class="order-item">
                            <div class="order-image">
                                <img src="https://images.unsplash.com/photo-1621996346565-e3dbc353d2e5?w=80&h=80&fit=crop&crop=center" alt="Pasta Carbonara">
                            </div>
                            <div class="order-details">
                                <h6 class="order-title">Pasta Carbonara</h6>
                                <p class="order-chef">Chef Antonio Rossi</p>
                                <small class="order-date">Ordered on March 12, 2024</small>
                            </div>
                            <div class="order-status">
                                <span class="badge bg-warning">Preparing</span>
                                <p class="order-price">&#8358;22.00</p>
                            </div>
                            <div class="order-actions">
                                <button class="btn btn-sm btn-outline-info">Track</button>
                            </div>
                        </div>

                        <div class="order-item">
                            <div class="order-image">
                                <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=80&h=80&fit=crop&crop=center" alt="Vegetarian Bowl">
                            </div>
                            <div class="order-details">
                                <h6 class="order-title">Healthy Vegetarian Bowl</h6>
                                <p class="order-chef">Chef Sarah Green</p>
                                <small class="order-date">Ordered on March 10, 2024</small>
                            </div>
                            <div class="order-status">
                                <span class="badge bg-success">Delivered</span>
                                <p class="order-price">&#8358;18.75</p>
                            </div>
                            <div class="order-actions">
                                <button class="btn btn-sm btn-outline-primary">Reorder</button>
                                <button class="btn btn-sm btn-outline-secondary">Review</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4 mb-4">
            <!-- Recommended Meals -->
            <div class="dashboard-card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Recommended for You</h5>
                </div>
                <div class="card-body">
                    <div class="recommended-meal">
                        <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=100&h=80&fit=crop&crop=center" alt="Pizza Margherita">
                        <div class="meal-info">
                            <h6>Pizza Margherita</h6>
                            <p>Chef Luigi Bianchi</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="price">&#8358;24.00</span>
                                <button class="btn btn-sm btn-primary">Order</button>
                            </div>
                        </div>
                    </div>

                    <div class="recommended-meal">
                        <img src="https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=100&h=80&fit=crop&crop=center" alt="Pancakes">
                        <div class="meal-info">
                            <h6>Fluffy Pancakes</h6>
                            <p>Chef Emma Sweet</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="price">&#8358;16.50</span>
                                <button class="btn btn-sm btn-primary">Order</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Browse Chefs
                        </button>
                        <button class="btn btn-outline-primary">
                            <i class="fas fa-heart me-2"></i>My Favorites
                        </button>
                        <button class="btn btn-outline-success">
                            <i class="fas fa-history me-2"></i>Order History
                        </button>
                        <button class="btn btn-outline-info">
                            <i class="fas fa-user me-2"></i>Profile Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection