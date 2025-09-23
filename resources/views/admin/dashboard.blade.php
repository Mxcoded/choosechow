@extends('layouts.dashboard')

@section('title', 'Admin Dashboard - ChooseChow')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="dashboard-title">Admin Dashboard 👑</h1>
                <p class="dashboard-subtitle">Manage your ChooseChow platform and monitor system performance</p>
            </div>
            <div class="col-md-6 text-end">
                <button class="btn btn-primary me-2">
                    <i class="fas fa-plus me-2"></i>Add Admin User
                </button>
                <button class="btn btn-outline-primary">
                    <i class="fas fa-download me-2"></i>Export Data
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $stats['total_users'] }}</h3>
                    <p class="stat-label">Total Users</p>
                    <small class="stat-change positive">+{{ $stats['new_users_today'] }} today</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-chef-hat"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $stats['total_chefs'] }}</h3>
                    <p class="stat-label">Active Chefs</p>
                    <small class="stat-change">{{ $stats['pending_approvals'] }} pending approval</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $stats['total_orders'] }}</h3>
                    <p class="stat-label">Total Orders</p>
                    <small class="stat-change positive">+15% this month</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">${{ number_format($stats['total_revenue'], 2) }}</h3>
                    <p class="stat-label">Total Revenue</p>
                    <small class="stat-change positive">+22% this month</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Recent Users -->
        <div class="col-lg-8 mb-4">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title">Recent User Registrations</h5>
                    <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-primary">Manage All Users</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Joined</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentUsers as $recentUser)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $recentUser->avatar_url }}" alt="Avatar" class="rounded-circle me-2" width="32" height="32">
                                            <div>
                                                <div class="fw-bold">{{ $recentUser->full_name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $recentUser->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ $recentUser->user_type === 'chef' ? 'warning' : 'primary' }}">
                                            {{ ucfirst($recentUser->user_type) }}
                                        </span>
                                    </td>
                                    <td>{{ $recentUser->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-success">Active</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary">View</button>
                                            <button class="btn btn-outline-secondary">Edit</button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                        <p class="text-muted">No recent users found</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & System Status -->
        <div class="col-lg-4 mb-4">
            <!-- Quick Actions -->
            <div class="dashboard-card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>Add New Admin
                        </button>
                        <button class="btn btn-outline-warning">
                            <i class="fas fa-user-check me-2"></i>Approve Chefs
                        </button>
                        <button class="btn btn-outline-success">
                            <i class="fas fa-chart-line me-2"></i>View Analytics
                        </button>
                        <button class="btn btn-outline-info">
                            <i class="fas fa-cogs me-2"></i>System Settings
                        </button>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="dashboard-card mb-3">
                <div class="card-header">
                    <h5 class="card-title">System Status</h5>
                </div>
                <div class="card-body">
                    <div class="status-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Server Status</span>
                            <span class="badge bg-success">Online</span>
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Database</span>
                            <span class="badge bg-success">Connected</span>
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Payment Gateway</span>
                            <span class="badge bg-success">Active</span>
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Email Service</span>
                            <span class="badge bg-warning">Limited</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Platform Stats -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title">Platform Overview</h5>
                </div>
                <div class="card-body">
                    <div class="platform-stat">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Customers</span>
                            <strong>{{ $stats['total_customers'] }}</strong>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-primary" style="width: {{ ($stats['total_customers'] / $stats['total_users']) * 100 }}%"></div>
                        </div>
                    </div>
                    <div class="platform-stat">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Chefs</span>
                            <strong>{{ $stats['total_chefs'] }}</strong>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-warning" style="width: {{ ($stats['total_chefs'] / $stats['total_users']) * 100 }}%"></div>
                        </div>
                    </div>
                    <div class="platform-stat">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Active Orders</span>
                            <strong>23</strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: 75%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title">Recent Platform Activity</h5>
                </div>
                <div class="card-body">
                    <div class="activity-timeline">
                        <div class="activity-item">
                            <div class="activity-icon bg-success">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="activity-content">
                                <h6>New Chef Registration</h6>
                                <p>Maria Rodriguez joined as a chef and is pending approval</p>
                                <small class="text-muted">2 hours ago</small>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon bg-primary">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="activity-content">
                                <h6>Large Order Placed</h6>
                                <p>Order #1234 for $156.50 placed by Sarah Johnson</p>
                                <small class="text-muted">4 hours ago</small>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon bg-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="activity-content">
                                <h6>Payment Issue Reported</h6>
                                <p>Customer reported payment processing delay</p>
                                <small class="text-muted">6 hours ago</small>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon bg-info">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="activity-content">
                                <h6>High Rating Received</h6>
                                <p>Chef Antonio received a 5-star review</p>
                                <small class="text-muted">8 hours ago</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('styles')
<style>
    .status-item {
        padding: 0.5rem 0;
        border-bottom: 1px solid #f1f3f4;
    }

    .status-item:last-child {
        border-bottom: none;
    }

    .platform-stat {
        margin-bottom: 1rem;
    }

    .platform-stat:last-child {
        margin-bottom: 0;
    }

    .activity-timeline {
        position: relative;
    }

    .activity-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .activity-item:last-child {
        margin-bottom: 0;
    }

    .activity-item::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 40px;
        bottom: -20px;
        width: 2px;
        background: #e9ecef;
    }

    .activity-item:last-child::before {
        display: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin-right: 1rem;
        position: relative;
        z-index: 1;
    }

    .activity-content h6 {
        font-weight: 600;
        margin-bottom: 0.25rem;
        color: var(--dark-color);
    }

    .activity-content p {
        color: #6c757d;
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
    }
</style>
@endsection
@endsection
