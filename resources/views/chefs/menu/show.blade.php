@extends('layouts.dashboard')

@section('title', $menu->name)

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-2">
                    <h1 class="dashboard-title h2 fw-bold me-3 text-dark">{{ $menu->name }}</h1>
                    @if($menu->is_featured)
                        <span class="badge bg-warning text-dark rounded-pill shadow-sm">
                            <i class="fas fa-star me-1"></i>Featured
                        </span>
                    @endif
                    @if(!$menu->is_available)
                        <span class="badge bg-danger ms-2 rounded-pill shadow-sm">Unavailable</span>
                    @endif
                </div>
                {{-- RELATIONSHIP FIX: Use category->name --}}
                <p class="text-muted mb-0">
                    {{ $menu->category->name ?? 'Uncategorized' }} • Created {{ $menu->created_at->diffForHumans() }}
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="btn-group shadow-sm">
                    <a href="{{ route('chef.menus.edit', $menu) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Item
                    </a>
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        {{-- ROUTE FIX: chef.menus -> chef.menus.index --}}
                        <li><a class="dropdown-item" href="{{ route('chef.menus.index') }}">
                            <i class="fas fa-arrow-left me-2"></i>Back to Menus
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteMenuItem()">
                            <i class="fas fa-trash me-2"></i>Delete Item
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            @if($menu->images && count($menu->images) > 0)
                <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                    <div id="menuCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($menu->images as $index => $image)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ asset('storage/' . $image) }}" class="d-block w-100" style="height: 400px; object-fit: cover;" alt="{{ $menu->name }}">
                                </div>
                            @endforeach
                        </div>
                        @if(count($menu->images) > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#menuCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon bg-dark rounded-circle p-3"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#menuCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon bg-dark rounded-circle p-3"></span>
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Description & Details</h5></div>
                <div class="card-body">
                    <p class="lead text-dark">{{ $menu->description }}</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3">Basic Information</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-tag me-2 text-primary"></i><strong>Category:</strong> {{ $menu->category->name ?? 'None' }}</li>
                                <li class="mb-2"><i class="fas fa-users me-2 text-primary"></i><strong>Serves:</strong> {{ $menu->serves_count }} people</li>
                                @if($menu->preparation_time_minutes)
                                    <li class="mb-2"><i class="fas fa-clock me-2 text-primary"></i><strong>Prep Time:</strong> {{ $menu->preparation_time_minutes }} mins</li>
                                @endif
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3">Classifications</h6>
                            
                            {{-- RELATIONSHIP FIX: Loop through Pivot Collections --}}
                            <div class="mb-3">
                                <strong class="d-block mb-1">Cuisines:</strong>
                                @forelse($menu->cuisines as $cuisine)
                                    <span class="badge bg-light text-dark border me-1">{{ $cuisine->name }}</span>
                                @empty
                                    <span class="text-muted small">None selected</span>
                                @endforelse
                            </div>

                            <div>
                                <strong class="d-block mb-1">Dietary Info:</strong>
                                @forelse($menu->dietaryPreferences as $diet)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success me-1">{{ $diet->name }}</span>
                                @empty
                                    <span class="text-muted small">None selected</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(($menu->ingredients && count($menu->ingredients) > 0) || ($menu->allergens && count($menu->allergens) > 0))
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row">
                            @if($menu->ingredients)
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-2">Ingredients</h6>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($menu->ingredients as $ingredient)
                                            <span class="badge bg-light text-secondary border">{{ $ingredient }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @if($menu->allergens)
                                <div class="col-md-6 mt-3 mt-md-0">
                                    <h6 class="fw-bold text-danger mb-2">Allergens</h6>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($menu->allergens as $allergen)
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                                <i class="fas fa-exclamation-triangle me-1"></i>{{ $allergen }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4 text-center">
                <div class="card-body py-4">
                    <h6 class="text-muted text-uppercase small fw-bold mb-3">Current Price</h6>
                    @if($menu->discounted_price)
                        <h2 class="text-success fw-bold mb-0">₦{{ number_format($menu->discounted_price, 2) }}</h2>
                        <div class="text-decoration-line-through text-muted mt-1">₦{{ number_format($menu->price, 2) }}</div>
                    @else
                        <h2 class="text-primary fw-bold mb-0">₦{{ number_format($menu->price, 2) }}</h2>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Performance</h5></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted">Total Orders</span>
                        <span class="fw-bold">{{ number_format($menu->order_count) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted">Views</span>
                        <span class="fw-bold">{{ number_format($menu->view_count) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Rating</span>
                        <div>
                            <span class="fw-bold me-1">{{ number_format($menu->average_rating, 1) }}</span>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Quick Actions</h5></div>
                <div class="card-body d-grid gap-2">
                    <button class="btn btn-outline-{{ $menu->is_available ? 'secondary' : 'success' }}" onclick="toggleAvailability()">
                        <i class="fas fa-{{ $menu->is_available ? 'eye-slash' : 'eye' }} me-2"></i>
                        {{ $menu->is_available ? 'Mark Unavailable' : 'Mark Available' }}
                    </button>
                    
                    <button class="btn btn-outline-warning" onclick="toggleFeatured()">
                        <i class="fas fa-{{ $menu->is_featured ? 'star-half-alt' : 'star' }} me-2"></i>
                        {{ $menu->is_featured ? 'Remove Featured' : 'Mark as Featured' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Delete Item?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong>{{ $menu->name }}</strong>? This cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                {{-- ROUTE FIX: Correct named route for destroy --}}
                <form action="{{ route('chef.menus.destroy', $menu) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function deleteMenuItem() {
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    function toggleAvailability() {
        // Optimistic UI update could go here
        fetch(`{{ route('chef.menus.toggle', $menu) }}`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ is_available: {{ $menu->is_available ? 'false' : 'true' }} })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) location.reload();
        })
        .catch(err => alert('Error updating status'));
    }

    function toggleFeatured() {
        fetch(`{{ route('chef.menus.toggle-featured', $menu) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            // Note: Controller toggles state, so body isn't strictly needed if controller handles it
            body: JSON.stringify({}) 
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) location.reload();
        })
        .catch(err => alert('Error updating featured status'));
    }
</script>
@endsection

@endsection