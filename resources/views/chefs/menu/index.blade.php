@extends('layouts.dashboard')

@section('title', 'My Menus - Chef Dashboard')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="dashboard-title">My Menus 🍽️</h1>
                <p class="dashboard-subtitle">Manage your delicious menu items and track their performance</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('chef.menus.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Menu Item
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $stats['total_menus'] }}</h3>
                    <p class="stat-label">Total Menu Items</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $stats['active_menus'] }}</h3>
                    <p class="stat-label">Active Items</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $stats['featured_menus'] }}</h3>
                    <p class="stat-label">Featured Items</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    {{-- FIX: Use the correct Font Awesome icon name for clarity --}}
                    <i class="fas fa-dollar-sign"></i> 
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">&#8358;{{ number_format($stats['avg_price'], 2) }}</h3>
                    <p class="stat-label">Average Price</p>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-header">
            <h5 class="card-title">Menu Items</h5>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" style="width: auto;" id="categoryFilter">
                    <option value="">All Categories</option>
                    <option value="appetizer">Appetizers</option>
                    <option value="main">Main Courses</option>
                    <option value="dessert">Desserts</option>
                    <option value="beverage">Beverages</option>
                </select>
                <select class="form-select form-select-sm" style="width: auto;" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="available">Available</option>
                    <option value="unavailable">Unavailable</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            @if($menus->count() > 0)
                <div class="row">
                    @foreach($menus as $menu)
                        <div class="col-lg-4 col-md-6 mb-4 menu-item" data-category="{{ $menu->category }}" data-status="{{ $menu->is_available ? 'available' : 'unavailable' }}">
                            <div class="menu-card">
                                <div class="menu-image">
                                    @if($menu->images && count($menu->images) > 0)
                                        <img src="{{ asset('storage/' . $menu->images[0]) }}" alt="{{ $menu->name }}" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=300&h=200&fit=crop&crop=center'; this.alt='Default food image';">
                                    @else
                                        <div class="menu-placeholder">
                                            <i class="fas fa-utensils fa-2x"></i>
                                        </div>
                                    @endif
                                    <div class="menu-badges">
                                        @if($menu->is_featured)
                                            <span class="badge bg-warning">Featured</span>
                                        @endif
                                        @if(!$menu->is_available)
                                            <span class="badge bg-secondary">Unavailable</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="menu-content">
                                    <div class="menu-header">
                                        <h6 class="menu-title">{{ $menu->name }}</h6>
                                        {{-- NOTE: Assuming 'formatted_price' is an accessor on Menu model --}}
                                        <span class="menu-price">{{ $menu->formatted_price ?? '₦' . number_format($menu->price, 2) }}</span> 
                                    </div>
                                    <p class="menu-description">{{ Str::limit($menu->description, 80) }}</p>
                                    <div class="menu-meta">
                                        <span class="badge bg-light text-dark">{{ ucfirst($menu->category) }}</span>
                                        {{-- NOTE: Assuming 'prep_time' and 'prep_time_formatted' are accessors on Menu model --}}
                                        @if($menu->preparation_time_minutes)
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>{{ $menu->preparation_time_minutes }} mins
                                            </small>
                                        @endif
                                    </div>
                                    <div class="menu-actions">
                                        <div class="btn-group w-100">
                                            <a href="{{ route('chef.menus.show', $menu) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('chef.menus.edit', $menu) }}" class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-outline-{{ $menu->is_available ? 'warning' : 'success' }} btn-sm toggle-availability" 
                                                    data-menu-id="{{ $menu->id }}" 
                                                    data-available="{{ $menu->is_available ? 'true' : 'false' }}">
                                                <i class="fas fa-{{ $menu->is_available ? 'pause' : 'play' }}"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm delete-menu" data-menu-id="{{ $menu->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center">
                    {{ $menus->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No menu items yet</h5>
                    <p class="text-muted">Start building your menu by adding your first delicious item!</p>
                    <a href="{{ route('chef.menus.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Your First Menu Item
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this menu item? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Styles remain the same --}}
@section('styles')
<style>
/* ... (styles here) ... */
</style>
@endsection

@section('scripts')
<script>
    // Filter functionality (UNCHANGED)
    document.getElementById('categoryFilter').addEventListener('change', filterMenus);
    document.getElementById('statusFilter').addEventListener('change', filterMenus);

    function filterMenus() {
        const categoryFilter = document.getElementById('categoryFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const menuItems = document.querySelectorAll('.menu-item');

        menuItems.forEach(item => {
            const category = item.dataset.category;
            const status = item.dataset.status;
            
            const categoryMatch = !categoryFilter || category === categoryFilter;
            const statusMatch = !statusFilter || status === statusFilter;
            
            if (categoryMatch && statusMatch) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Toggle availability (FIXED AJAX REQUEST)
    document.querySelectorAll('.toggle-availability').forEach(button => {
        button.addEventListener('click', function() {
            const menuId = this.dataset.menuId;
            const isAvailable = this.dataset.available === 'true';
            const newAvailable = !isAvailable;
            
            fetch(`/chef/menus/${menuId}/toggle`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                // FIX: Send the new availability status in the body
                body: JSON.stringify({
                    is_available: newAvailable 
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button state immediately
                    this.dataset.available = data.is_available;
                    this.className = `btn btn-outline-${data.is_available ? 'warning' : 'success'} btn-sm toggle-availability`;
                    this.innerHTML = `<i class="fas fa-${data.is_available ? 'pause' : 'play'}"></i>`;
                    
                    // Update parent menu item and badge
                    const menuItem = this.closest('.menu-item');
                    menuItem.dataset.status = data.is_available ? 'available' : 'unavailable';
                    
                    let badge = menuItem.querySelector('.menu-badges .badge.bg-secondary');
                    if (data.is_available && badge) {
                        badge.remove();
                    } else if (!data.is_available && !badge) {
                        const badgesContainer = menuItem.querySelector('.menu-badges');
                        // Prepend the new badge to ensure 'Featured' stays on top if present
                        badgesContainer.insertAdjacentHTML('beforeend', '<span class="badge bg-secondary">Unavailable</span>');
                    }
                    
                    showAlert('success', data.message);
                } else {
                     showAlert('danger', data.message || 'Failed to update availability.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while communicating with the server.');
            });
        });
    });

    // Delete menu (UNCHANGED)
    document.querySelectorAll('.delete-menu').forEach(button => {
        button.addEventListener('click', function() {
            const menuId = this.dataset.menuId;
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `/chef/menus/${menuId}`;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        });
    });

    // Show alert function (UNCHANGED)
    function showAlert(type, message) {
        // ... (function logic remains the same) ...
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const container = document.querySelector('.dashboard-container');
        // Insert alert after the header row (first element)
        container.insertBefore(alertDiv, container.children[1]); 
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
</script>
@endsection
@endsection