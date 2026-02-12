@extends('layouts.app')

@section('title', 'Find Chefs Near You')

@section('content')
<div class="container py-5">
    <div class="row mb-5">
        <div class="col-md-8 mx-auto text-center">
            <h1 class="fw-bold mb-3 text-dark">Who's cooking today? 🍳</h1>
            <form action="{{ route('chef.index') }}" method="GET" class="d-flex gap-2 justify-content-center">
                <input type="text" name="search" class="form-control form-control-lg w-50" 
                       placeholder="Search chefs, cuisines (e.g. Jollof)..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary btn-lg px-4">Search</button>
            </form>
            
            <div class="mt-3 d-flex justify-content-center flex-wrap gap-2">
                <a href="{{ route('chef.index') }}" class="badge rounded-pill bg-dark text-decoration-none p-2">All</a>
                @foreach($cuisines as $cuisine)
                    <a href="{{ route('chef.index', ['cuisine' => $cuisine->slug]) }}" 
                       class="badge rounded-pill {{ request('cuisine') == $cuisine->slug ? 'bg-primary' : 'bg-secondary' }} text-decoration-none p-2">
                        {{ $cuisine->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row g-4">
        @forelse($chefs as $chef)
            <div class="col-lg-3 col-md-6">
                <a href="{{ route('chefs.show', $chef->id) }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                        <div class="position-relative">
                            <img src="{{ $chef->user->avatar ? asset('storage/'.$chef->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($chef->business_name).'&size=300' }}" 
                                 class="card-img-top object-cover" style="height: 200px;" alt="{{ $chef->business_name }}">
                            @if(!$chef->isOpenNow())
                                <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center text-white fw-bold">
                                    CLOSED NOW
                                </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-dark mb-1">{{ $chef->business_name }}</h5>
                            <div class="mb-2">
                                @foreach($chef->cuisines->take(3) as $tag)
                                    <span class="text-muted small me-1">• {{ $tag->name }}</span>
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-between align-items-center small text-muted">
                                <span><i class="fas fa-star text-warning"></i> {{ $chef->rating ?? 'New' }}</span>
                                <span><i class="fas fa-motorcycle"></i> ₦{{ number_format($chef->minimum_order) }} min</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png" style="width: 200px; opacity: 0.5;">
                <h4 class="mt-3 text-muted">No chefs found matching your criteria.</h4>
                <a href="{{ route('chef.index') }}" class="btn btn-outline-primary mt-2">Clear Filters</a>
            </div>
        @endforelse
    </div>

    <div class="mt-5 d-flex justify-content-center">
        {{ $chefs->links() }}
    </div>
</div>

<style>
    .hover-shadow:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .transition-all { transition: all 0.3s ease; }
</style>
@endsection