@extends('layouts.dashboard')

@section('title', 'My Menus - Chef Dashboard')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
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

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats Cards -->
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
                    <i class="fas fa-naira-sign"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">&#8358;{{ number_format($stats['avg_price'], 2) }}</h3>
                    <p class="stat-label">Average Price</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Items Grid -->
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
                                        <span class="menu-price">{{ $menu->formatted_price }}</span>
                                    </div>
                                    <p class="menu-description">{{ Str::limit($menu->description, 80) }}</p>
                                    <div class="menu-meta">
                                        <span class="badge bg-light text-dark">{{ ucfirst($menu->category) }}</span>
                                        @if($menu->prep_time)
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>{{ $menu->prep_time_formatted }}
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

                <!-- Pagination -->
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

<!-- Delete Confirmation Modal -->
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

@section('styles')
<style>
    .menu-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .menu-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }

    .menu-image {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .menu-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .menu-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
    }

    .menu-badges {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .menu-content {
        padding: 1.25rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .menu-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.75rem;
    }

    .menu-title {
        font-weight: 600;
        color: var(--dark-color);
        margin: 0;
        flex: 1;
        margin-right: 0.5rem;
    }

    .menu-price {
        font-weight: bold;
        color: var(--primary-color);
        font-size: 1.1rem;
    }

    .menu-description {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 1rem;
        flex: 1;
    }

    .menu-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .menu-actions {
        margin-top: auto;
    }

    .menu-actions .btn-group {
        display: flex;
    }

    .menu-actions .btn {
        flex: 1;
    }

    /* Filter styles */
    .form-select-sm {
        font-size: 0.875rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .menu-actions .btn-group {
            flex-direction: column;
        }
        
        .menu-actions .btn {
            margin-bottom: 0.25rem;
        }
        
        .menu-actions .btn:last-child {
            margin-bottom: 0;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    // Filter functionality
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

    // Toggle availability
    document.querySelectorAll('.toggle-availability').forEach(button => {
        button.addEventListener('click', function() {
            const menuId = this.dataset.menuId;
            const isAvailable = this.dataset.available === 'true';
            
            fetch(`/chef/menus/${menuId}/toggle`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button
                    const newAvailable = data.is_available;
                    this.dataset.available = newAvailable;
                    this.className = `btn btn-outline-${newAvailable ? 'warning' : 'success'} btn-sm toggle-availability`;
                    this.innerHTML = `<i class="fas fa-${newAvailable ? 'pause' : 'play'}"></i>`;
                    
                    // Update parent menu item
                    const menuItem = this.closest('.menu-item');
                    menuItem.dataset.status = newAvailable ? 'available' : 'unavailable';
                    
                    // Update badge
                    const badge = menuItem.querySelector('.badge.bg-secondary');
                    if (newAvailable && badge) {
                        badge.remove();
                    } else if (!newAvailable && !badge) {
                        const badgesContainer = menuItem.querySelector('.menu-badges');
                        badgesContainer.innerHTML += '<span class="badge bg-secondary">Unavailable</span>';
                    }
                    
                    // Show success message
                    showAlert('success', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while updating the menu item.');
            });
        });
    });

    // Delete menu
    document.querySelectorAll('.delete-menu').forEach(button => {
        button.addEventListener('click', function() {
            const menuId = this.dataset.menuId;
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `/chef/menus/${menuId}`;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        });
    });

    // Show alert function
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.dashboard-container');
        container.insertBefore(alertDiv, container.firstChild.nextSibling);
        
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
