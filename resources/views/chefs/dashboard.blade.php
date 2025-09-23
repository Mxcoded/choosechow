@extends('layouts.dashboard')

@section('title', 'Chef Dashboard - ChooseChow')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="dashboard-title">Welcome back, Chef {{ $user->first_name }}! 👨‍🍳</h1>
                <p class="dashboard-subtitle">Manage your culinary business and delight your customers</p>
            </div>
            <div class="col-md-6 text-end">
                <button class="btn btn-primary me-2">
                    <i class="fas fa-plus me-2"></i>Add New Menu
                </button>
                <button class="btn btn-outline-primary">
                    <i class="fas fa-chart-line me-2"></i>View Analytics
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
                    <small class="stat-change positive">+12% this month</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $stats['pending_orders'] }}</h3>
                    <p class="stat-label">Pending Orders</p>
                    <small class="stat-change">Needs attention</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">&#8358;{{ number_format($stats['total_revenue'], 2) }}</h3>
                    <p class="stat-label">Total Revenue</p>
                    <small class="stat-change positive">+8% this month</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $stats['avg_rating'] }}</h3>
                    <p class="stat-label">Average Rating</p>
                    <small class="stat-change positive">Excellent!</small>
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
                                <tr>
                                    <td>#ORD-001</td>
                                    <td>Sarah Johnson</td>
                                    <td>Grilled Salmon Deluxe</td>
                                    <td>&#8358;28.50</td>
                                    <td><span class="badge bg-warning">Preparing</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">Update</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#ORD-002</td>
                                    <td>Mike Chen</td>
                                    <td>Pasta Carbonara</td>
                                    <td>&#8358;22.00</td>
                                    <td><span class="badge bg-success">Ready</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-success">Complete</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#ORD-003</td>
                                    <td>Emma Davis</td>
                                    <td>Vegetarian Bowl</td>
                                    <td>&#8358;18.75</td>
                                    <td><span class="badge bg-info">New</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">Accept</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Stats -->
        <div class="col-lg-4 mb-4">
            <div class="dashboard-card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create New Menu
                        </button>
                        <button class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Update Availability
                        </button>
                        <button class="btn btn-outline-success">
                            <i class="fas fa-chart-bar me-2"></i>View Reports
                        </button>
                        <button class="btn btn-outline-info">
                            <i class="fas fa-cog me-2"></i>Profile Settings
                        </button>
                    </div>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title">Performance</h5>
                </div>
                <div class="card-body">
                    <div class="performance-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Active Menus</span>
                            <strong>{{ $stats['active_menus'] }}</strong>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-primary" style="width: 80%"></div>
                        </div>
                    </div>
                    <div class="performance-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Total Customers</span>
                            <strong>{{ $stats['total_customers'] }}</strong>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-success" style="width: 65%"></div>
                        </div>
                    </div>
                    <div class="performance-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Order Completion</span>
                            <strong>94%</strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-info" style="width: 94%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
