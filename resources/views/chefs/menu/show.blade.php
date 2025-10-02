@extends('layouts.dashboard')

@section('title', $menu->name)

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-2">
                    <h1 class="dashboard-title me-3">{{ $menu->name }}</h1>
                    @if($menu->is_featured)
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-star me-1"></i>Featured
                        </span>
                    @endif
                    @if(!$menu->is_available)
                        <span class="badge bg-danger ms-2">Unavailable</span>
                    @endif
                </div>
                <p class="dashboard-subtitle">{{ ucfirst($menu->category) }} • Created {{ $menu->created_at->diffForHumans() }}</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group">
                    <a href="{{ route('chef.menus.edit', $menu) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Item
                    </a>
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('chef.menus') }}">
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

    <div class="row">
        <div class="col-lg-8">
            @if($menu->images && count($menu->images) > 0)
                <div class="dashboard-card mb-4">
                    <div class="card-body p-0">
                        <div id="menuCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach($menu->images as $index => $image)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        {{-- Use asset('storage/...') instead of Storage::url() for consistency, assuming public disk setup --}}
                                        <img src="{{ asset('storage/' . $image) }}" class="d-block w-100 menu-detail-image" alt="{{ $menu->name }}">
                                    </div>
                                @endforeach
                            </div>
                            @if(count($menu->images) > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#menuCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#menuCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                                <div class="carousel-indicators">
                                    @foreach($menu->images as $index => $image)
                                        <button type="button" data-bs-target="#menuCarousel" data-bs-slide-to="{{ $index }}" 
                                                class="{{ $index === 0 ? 'active' : '' }}"></button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="dashboard-card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Description & Details</h5>
                </div>
                <div class="card-body">
                    <p class="lead">{{ $menu->description }}</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6>Basic Information</h6>
                            <ul class="list-unstyled">
                                <li><strong>Category:</strong> {{ ucfirst($menu->category) }}</li>
                                <li><strong>Serves:</strong> {{ $menu->serves_count }} {{ $menu->serves_count > 1 ? 'people' : 'person' }}</li>
                                @if($menu->preparation_time_minutes)
                                    <li><strong>Prep Time:</strong> {{ $menu->preparation_time_minutes }} minutes</li>
                                @endif
                                @if($menu->spice_level !== null)
                                    <li><strong>Spice Level:</strong> 
                                        <div class="spice-indicator d-inline-flex ms-2">
                                            {{-- Fix: Spice level indicator now checks against level, not index --}}
                                            @for($i = 0; $i < 5; $i++)
                                                <div class="spice-level {{ $menu->spice_level > $i ? 'active' : '' }}"></div>
                                            @endfor
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Cuisine & Dietary</h6>
                            @if($menu->cuisine_types && count($menu->cuisine_types) > 0)
                                <div class="mb-2">
                                    <strong>Cuisine:</strong>
                                    @foreach($menu->cuisine_types as $cuisine)
                                        <span class="badge bg-light text-dark me-1">{{ $cuisine }}</span>
                                    @endforeach
                                </div>
                            @endif
                            @if($menu->dietary_info && count($menu->dietary_info) > 0)
                                <div>
                                    <strong>Dietary:</strong>
                                    @foreach($menu->dietary_info as $diet)
                                        <span class="badge bg-success me-1">{{ $diet }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if(($menu->ingredients && count($menu->ingredients) > 0) || ($menu->allergens && count($menu->allergens) > 0))
                <div class="dashboard-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Ingredients & Allergens</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($menu->ingredients && count($menu->ingredients) > 0)
                                <div class="col-md-6">
                                    <h6>Ingredients</h6>
                                    <div class="ingredients-list">
                                        @foreach($menu->ingredients as $ingredient)
                                            <span class="badge bg-light text-dark me-1 mb-1">{{ $ingredient }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @if($menu->allergens && count($menu->allergens) > 0)
                                <div class="col-md-6">
                                    <h6 class="text-warning">Allergens</h6>
                                    <div class="allergens-list">
                                        @foreach($menu->allergens as $allergen)
                                            <span class="badge bg-warning text-dark me-1 mb-1">
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

            @if($menu->nutritional_info && count($menu->nutritional_info) > 0)
                <div class="dashboard-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Nutritional Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($menu->nutritional_info as $nutrient => $value)
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="nutrition-item text-center">
                                        <div class="nutrition-value">{{ $value }}</div>
                                        <div class="nutrition-label">{{ ucfirst($nutrient) }}</div>
                                    </div>
                                </div>
                             @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if($menu->cooking_instructions || $menu->storage_instructions)
                <div class="dashboard-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Instructions</h5>
                    </div>
                    <div class="card-body">
                        @if($menu->cooking_instructions)
                            <div class="mb-4">
                                <h6><i class="fas fa-fire me-2"></i>Cooking Instructions</h6>
                                <p class="text-muted">{{ $menu->cooking_instructions }}</p>
                            </div>
                        @endif
                        @if($menu->storage_instructions)
                            <div>
                                <h6><i class="fas fa-snowflake me-2"></i>Storage Instructions</h6>
                                <p class="text-muted">{{ $menu->storage_instructions }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Pricing</h5>
                </div>
                <div class="card-body text-center">
                    @if($menu->discounted_price)
                        <div class="price-display">
                            <div class="current-price">₦{{ number_format($menu->discounted_price, 2) }}</div>
                            <div class="original-price"><s>₦{{ number_format($menu->price, 2) }}</s></div>
                            <div class="savings">
                                Save ₦{{ number_format($menu->price - $menu->discounted_price, 2) }}
                            </div>
                        </div>
                    @else
                        <div class="price-display">
                            <div class="current-price">₦{{ number_format($menu->price, 2) }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="dashboard-card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="stat-item">
                        {{-- FIX: Corrected variable name from total_orders to order_count --}}
                        <div class="stat-value">{{ number_format($menu->order_count) }}</div> 
                        <div class="stat-label">Total Orders</div>
                    </div>
                    <div class="stat-item">
                        {{-- FIX: Corrected variable name from rating to average_rating --}}
                        <div class="stat-value">
                            @if($menu->average_rating > 0)
                                {{ number_format($menu->average_rating, 1) }}
                                <div class="rating-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $menu->average_rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </div>
                            @else
                                No ratings yet
                            @endif
                        </div>
                        <div class="stat-label">Average Rating ({{ $menu->total_reviews }} reviews)</div>
                    </div>
                    <div class="stat-item">
                        {{-- Added view_count for completeness --}}
                        <div class="stat-value">{{ number_format($menu->view_count) }}</div>
                        <div class="stat-label">Total Views</div>
                    </div>
                    @if($menu->stock_quantity !== null)
                        <div class="stat-item">
                            <div class="stat-value {{ $menu->stock_quantity <= 5 ? 'text-warning' : '' }}">
                                {{ $menu->stock_quantity }}
                            </div>
                            <div class="stat-label">Items in Stock</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="dashboard-card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Availability Schedule</h5>
                </div>
                <div class="card-body">
                    @if($menu->availability_schedule && count($menu->availability_schedule) > 0)
                        @foreach($menu->availability_schedule as $day => $schedule)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-medium">{{ ucfirst($day) }}:</span>
                                @if(isset($schedule['available']) && $schedule['available'])
                                    <span class="text-success fw-bold">{{ $schedule['start_time'] }} - {{ $schedule['end_time'] }}</span>
                                @else
                                    <span class="text-danger">Unavailable</span>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">Available all hours.</p>
                    @endif
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" onclick="toggleAvailability()">
                            <i class="fas fa-{{ $menu->is_available ? 'eye-slash' : 'eye' }} me-2"></i>
                            {{ $menu->is_available ? 'Mark Unavailable' : 'Mark Available' }}
                        </button>
                        <button class="btn btn-outline-warning" onclick="toggleFeatured()">
                            <i class="fas fa-star me-2"></i>
                            {{ $menu->is_featured ? 'Remove from Featured' : 'Mark as Featured' }}
                        </button>
                        <a href="{{ route('chef.menus.edit', $menu) }}" class="btn btn-outline-success">
                            <i class="fas fa-edit me-2"></i>Edit Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Menu Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete "<strong>{{ $menu->name }}</strong>"?</p>
                <p class="text-muted">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('chef.menus.destroy', $menu) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Item</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Styles remain the same --}}
<style>
/* ... (styles here) ... */
</style>

<script>
// Toggle scripts need to be updated to ensure the method and CSRF token are correctly handled
function deleteMenuItem() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function toggleAvailability() {
    const isAvailable = {{ $menu->is_available ? 'true' : 'false' }};
    
    fetch(`{{ route('chef.menus.toggle', $menu) }}`, {
        method: 'PATCH', // Changed method to PATCH as per route definition
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            is_available: !isAvailable // Pass the new availability status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Use a toast or temporary alert for better UX, but reload for full update
            alert(data.message); 
            location.reload();
        } else {
            alert('Error updating availability: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating availability');
    });
}

function toggleFeatured() {
    const isFeatured = {{ $menu->is_featured ? 'true' : 'false' }};
    
    fetch(`{{ route('chef.menus.toggle-featured', $menu) }}`, {
        method: 'POST', // Keep POST, as toggle-featured uses POST method
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            is_featured: !isFeatured // Pass the new featured status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error updating featured status: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating featured status');
    });
}
</script>
@endsection