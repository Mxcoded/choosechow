@extends('layouts.dashboard')

@section('title', 'My Menus - Chef Dashboard')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h2 fw-bold text-gray-800">My Menus 🍽️</h1>
                <p class="text-muted">Manage your dishes and track their performance</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('chef.menus.create') }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus me-2"></i>Add New Item
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                <div class="card-body">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Total Items</div>
                    <div class="h3 fw-bold mb-0 text-primary">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                <div class="card-body">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Active</div>
                    <div class="h3 fw-bold mb-0 text-success">{{ $stats['active'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
                <div class="card-body">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Featured</div>
                    <div class="h3 fw-bold mb-0 text-warning">{{ $stats['featured'] }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Menu Grid --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="mb-0 fw-bold">Menu Inventory</h5>
            <div class="d-flex gap-2">
                {{-- Filters --}}
                <select class="form-select form-select-sm" style="width: 150px;" id="categoryFilter">
                    <option value="">All Categories</option>
                    <option value="main-course">Main Course</option>
                    <option value="appetizer">Appetizer</option>
                    <option value="dessert">Dessert</option>
                    <option value="soup">Soup</option>
                    <option value="swallow">Swallow</option>
                </select>
                <select class="form-select form-select-sm" style="width: 130px;" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="available">Available</option>
                    <option value="unavailable">Unavailable</option>
                </select>
            </div>
        </div>
        
        <div class="card-body p-4">
            @if($menus->count() > 0)
                <div class="row g-4">
                    @foreach($menus as $menu)
                        <div class="col-xl-3 col-lg-4 col-md-6 menu-item" 
                             {{-- SAFE ACCESS: Use optional chaining or check for relation --}}
                             data-category="{{ $menu->category->slug ?? 'uncategorized' }}" 
                             data-status="{{ $menu->is_available ? 'available' : 'unavailable' }}">
                            
                            <div class="card h-100 border shadow-sm hover:shadow-md transition-all">
                                {{-- Image --}}
                                <div class="position-relative" style="height: 200px; overflow: hidden;">
                                    @if($menu->images && count($menu->images) > 0)
                                        <img src="{{ asset('storage/' . $menu->images[0]) }}" class="card-img-top w-100 h-100" style="object-fit: cover;" alt="{{ $menu->name }}">
                                    @else
                                        <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center text-muted">
                                            <i class="fas fa-utensils fa-2x"></i>
                                        </div>
                                    @endif
                                    
                                    {{-- Badges --}}
                                    <div class="position-absolute top-0 end-0 p-2 d-flex flex-column gap-1">
                                        @if($menu->is_featured)
                                            <span class="badge bg-warning text-dark shadow-sm">Featured</span>
                                        @endif
                                        @if(!$menu->is_available)
                                            <span class="badge bg-secondary shadow-sm">Unavailable</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title fw-bold mb-0 text-truncate" title="{{ $menu->name }}">{{ $menu->name }}</h6>
                                        <span class="text-success fw-bold">₦{{ number_format($menu->price) }}</span>
                                    </div>
                                    
                                    <p class="card-text text-muted small flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $menu->description }}
                                    </p>

                                    <div class="d-flex align-items-center justify-content-between mt-3 mb-3">
                                        <span class="badge bg-light text-dark border">
                                            {{ $menu->category->name ?? 'Uncategorized' }}
                                        </span>
                                        @if($menu->preparation_time_minutes)
                                            <small class="text-muted"><i class="far fa-clock me-1"></i>{{ $menu->preparation_time_minutes }}m</small>
                                        @endif
                                    </div>

                                    {{-- Actions --}}
                                    <div class="btn-group w-100" role="group">
                                        <a href="{{ route('chef.menus.edit', $menu) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('chef.menus.show', $menu) }}" class="btn btn-outline-primary btn-sm" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <button class="btn btn-outline-{{ $menu->is_available ? 'warning' : 'success' }} btn-sm toggle-btn" 
                                                data-id="{{ $menu->id }}" 
                                                data-available="{{ $menu->is_available ? '1' : '0' }}"
                                                title="Toggle Availability">
                                            <i class="fas fa-{{ $menu->is_available ? 'pause' : 'play' }}"></i>
                                        </button>
                                        
                                        <button type="button" class="btn btn-outline-danger btn-sm delete-btn" 
                                                data-id="{{ $menu->id }}" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4 d-flex justify-content-center">
                    {{ $menus->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3 text-muted">
                        <i class="fas fa-hamburger fa-4x opacity-25"></i>
                    </div>
                    <h5>No items found</h5>
                    <p class="text-muted">You haven't added any menu items yet.</p>
                    <a href="{{ route('chef.menus.create') }}" class="btn btn-primary">Create First Item</a>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="" method="POST" id="deleteForm" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title text-danger">Delete Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this menu item? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Confirm Delete</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Filter Logic
        const categorySelect = document.getElementById('categoryFilter');
        const statusSelect = document.getElementById('statusFilter');
        const items = document.querySelectorAll('.menu-item');

        function filterItems() {
            const cat = categorySelect.value;
            const status = statusSelect.value;

            items.forEach(item => {
                const itemCat = item.dataset.category;
                const itemStatus = item.dataset.status;
                
                const matchesCat = !cat || itemCat === cat;
                const matchesStatus = !status || itemStatus === status;

                item.style.display = (matchesCat && matchesStatus) ? 'block' : 'none';
            });
        }

        categorySelect.addEventListener('change', filterItems);
        statusSelect.addEventListener('change', filterItems);

        // 2. Toggle Availability Logic
        document.querySelectorAll('.toggle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const isAvailable = this.dataset.available === '1';
                const originalHtml = this.innerHTML;
                
                // Show loading
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                fetch(`/chef/menus/${id}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ is_available: !isAvailable })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        location.reload(); // Simple reload to reflect all badge changes
                    }
                })
                .catch(err => {
                    alert('Error updating status');
                    this.innerHTML = originalHtml;
                    this.disabled = false;
                });
            });
        });

        // 3. Delete Logic
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const form = document.getElementById('deleteForm');
                // Use the named route structure
                form.action = `/chef/menus/${id}`;
                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            });
        });
    });
</script>
@endsection