@extends('layouts.app')

@section('title', $chef->business_name)

@section('content')
<div class="bg-white shadow-sm border-bottom py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-2 text-center text-md-start">
                <img src="{{ $chef->user->avatar ? asset('storage/'.$chef->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($chef->business_name) }}" 
                     class="rounded-circle border border-3 border-white shadow" style="width: 120px; height: 120px; object-fit: cover;">
            </div>
            <div class="col-md-7 mt-3 mt-md-0 text-center text-md-start">
                <h1 class="fw-bold mb-1">{{ $chef->business_name }}</h1>
                <p class="text-muted mb-2">{{ $chef->bio }}</p>
                <div class="d-flex gap-3 justify-content-center justify-content-md-start small">
                    <span class="text-muted"><i class="fas fa-map-marker-alt me-1"></i> {{ $chef->kitchen_address }}</span>
                    <span class="text-success fw-bold"><i class="fas fa-clock me-1"></i> {{ $chef->isOpenNow() ? 'Open Now' : 'Closed' }}</span>
                    <span class="text-muted"><i class="fas fa-truck me-1"></i> ₦{{ number_format($chef->minimum_order) }} Min Order</span>
                </div>
            </div>
            <div class="col-md-3 text-center text-md-end mt-3 mt-md-0">
                <div class="card bg-light border-0 p-3">
                    <small class="text-muted d-block mb-1">Delivery Time</small>
                    <span class="fw-bold h5">30-45 mins</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-md-3 d-none d-md-block">
            <div class="sticky-top" style="top: 100px;">
                <h6 class="text-uppercase text-muted fw-bold mb-3 small">Menu Categories</h6>
                <div class="list-group list-group-flush border-0">
                    @foreach($menus as $categoryName => $items)
                        <a href="#cat-{{ Str::slug($categoryName) }}" class="list-group-item list-group-item-action border-0 bg-transparent py-2 ps-0">
                            {{ $categoryName }} <span class="badge bg-light text-dark rounded-pill ms-1">{{ $items->count() }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-9">
            @forelse($menus as $categoryName => $items)
                <div id="cat-{{ Str::slug($categoryName) }}" class="mb-5">
                    <h3 class="fw-bold mb-4 pb-2 border-bottom">{{ $categoryName }}</h3>
                    <div class="row g-4">
                        @foreach($items as $menu)
                            <div class="col-lg-6">
                                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                                    <div class="row g-0 h-100">
                                        <div class="col-4">
                                            @if($menu->images && count($menu->images) > 0)
                                                <img src="{{ asset('storage/' . $menu->images[0]) }}" class="h-100 w-100 object-cover" style="object-fit: cover;" alt="{{ $menu->name }}">
                                            @else
                                                <div class="bg-light h-100 w-100 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-utensils text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-8">
                                            <div class="card-body h-100 d-flex flex-column justify-content-between">
                                                <div>
                                                    <h6 class="fw-bold mb-1">{{ $menu->name }}</h6>
                                                    <p class="text-muted small mb-2 text-truncate-2">{{ Str::limit($menu->description, 60) }}</p>
                                                    <h6 class="text-primary fw-bold mb-0">₦{{ number_format($menu->price) }}</h6>
                                                </div>
                                                <div class="mt-3 text-end">
                                                    @if($chef->isOpenNow())
                                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" 
                                                                onclick="addToCart({{ $menu->id }}, '{{ $menu->name }}', {{ $menu->price }})">
                                                            <i class="fas fa-plus me-1"></i> Add
                                                        </button>
                                                    @else
                                                        <button class="btn btn-sm btn-light text-muted rounded-pill px-3" disabled>Closed</button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <h4>No menu items available right now.</h4>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    function addToCart(id, name, price) {
        // We will implement this in the next step!
        alert(`Adding ${name} to cart... (Coming Soon)`);
    }
</script>
@endsection