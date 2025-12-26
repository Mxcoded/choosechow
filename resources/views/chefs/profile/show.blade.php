@extends('layouts.dashboard')

@section('title', 'Chef Profile - ' . $profile->business_name)

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-2">
                    <h1 class="dashboard-title h2 fw-bold text-dark mb-0">{{ $profile->business_name }}</h1>
                    @if($profile->isVerified())
                        <i class="fas fa-check-circle text-primary" title="Verified Business"></i>
                    @endif
                </div>
                <p class="text-muted">Manage your business information and operational settings</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('chef.profile.edit') }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-edit me-2"></i>Edit Profile
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

    <div class="row g-4">
        {{-- Left Column --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">Business Overview</h5>
                    <span class="badge bg-{{ $profile->isVerified() ? 'success' : 'warning' }}">
                        {{ $profile->isVerified() ? 'Verified' : 'Verification Pending' }}
                    </span>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold text-uppercase text-muted small">About Us</h6>
                    <p class="text-dark">{{ $profile->bio ?? 'No bio added yet.' }}</p>

                    <h6 class="fw-bold text-uppercase text-muted small mt-4">Cuisines & Specialties</h6>
                    <div>
                        @forelse($profile->cuisines as $cuisine)
                            <span class="badge bg-light text-dark border me-1 mb-1">{{ $cuisine->name }}</span>
                        @empty
                            <span class="text-muted small">No cuisines selected.</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold">Operational Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="mb-1 fw-bold text-muted small">Years of Experience</p>
                            <p class="fw-bold">{{ $profile->years_of_experience }} years</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 fw-bold text-muted small">Online Status</p>
                            <span class="badge bg-{{ $profile->isAcceptingOrders() ? 'success' : 'danger' }}">
                                {{ $profile->isAcceptingOrders() ? 'Accepting Orders' : 'Offline' }}
                            </span>
                        </div>
                        <div class="col-12">
                            <p class="mb-1 fw-bold text-muted small">Kitchen Address</p>
                            <p class="fw-bold mb-0">{{ $profile->kitchen_address }}</p>
                            <small class="text-muted">Used for delivery calculations</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="fas fa-university me-2 text-primary"></i>Banking Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="mb-1 fw-bold text-muted small">Bank Name</p>
                            <p class="fw-bold">{{ $profile->bank_name ?? 'Not Set' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 fw-bold text-muted small">Account Name</p>
                            <p class="fw-bold">{{ $profile->account_name ?? 'Not Set' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 fw-bold text-muted small">Account Number</p>
                            <p class="fw-bold">{{ $profile->account_number ?? 'Not Set' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold">Delivery Policy</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Delivery Radius</span>
                            <span class="fw-bold">{{ $profile->delivery_radius_km }} KM</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Min. Order</span>
                            {{-- Corrected column name: minimum_order --}}
                            <span class="fw-bold">₦{{ number_format($profile->minimum_order ?? 0, 2) }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold">Operating Hours</h5>
                </div>
                <div class="card-body">
                    @if($profile->operating_hours)
                        @foreach($profile->operating_hours as $day => $hours)
                            @php
                                // Check logic: 'closed' is explicitly true?
                                $isClosed = isset($hours['closed']) && filter_var($hours['closed'], FILTER_VALIDATE_BOOLEAN);
                            @endphp
                            <div class="d-flex justify-content-between mb-2 pb-1 border-bottom border-light">
                                <span class="fw-bold text-capitalize small">{{ $day }}</span>
                                @if(!$isClosed)
                                    <span class="text-success small fw-bold">
                                        {{ $hours['open'] ?? '09:00' }} - {{ $hours['close'] ?? '20:00' }}
                                    </span>
                                @else
                                    <span class="text-danger small">Closed</span>
                                @endif
                            </div>
                        @endforeach
                    @else
                         <div class="text-center py-4">
                            <i class="far fa-clock fa-2x text-muted mb-2"></i>
                            <p class="text-muted small mb-0">Hours not configured.</p>
                         </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection