{{-- resources/views/chefs/menu/partials/preview.blade.php --}}
<div class="card border-0 shadow-sm">
    <div class="position-relative bg-light d-flex align-items-center justify-content-center text-muted" style="height: 200px; overflow: hidden;">
        @if(isset($menu) && $menu->images && count($menu->images) > 0)
            <img src="{{ asset('storage/' . $menu->images[0]) }}" class="w-100 h-100 object-fit-cover" alt="Preview">
        @else
            <i class="fas fa-utensils fa-3x opacity-25"></i>
        @endif
        
        <span class="badge bg-warning position-absolute top-0 end-0 m-2">Featured</span>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h5 class="card-title fw-bold mb-0">{{ $menu->name ?? 'Item Name' }}</h5>
            <span class="text-success fw-bold">₦{{ number_format($menu->price ?? 0) }}</span>
        </div>
        <p class="card-text text-muted small">
            {{ Str::limit($menu->description ?? 'Delicious meal description goes here...', 80) }}
        </p>
        <hr>
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted"><i class="far fa-clock me-1"></i> {{ $menu->preparation_time_minutes ?? 30 }}m</small>
            <button class="btn btn-sm btn-primary disabled">Add to Cart</button>
        </div>
    </div>
</div>