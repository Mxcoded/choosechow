@extends('layouts.dashboard')

@section('title', 'Chef Profile - ' . $profile->business_name)

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="dashboard-title">{{ $profile->business_name }} 👑</h1>
                <p class="dashboard-subtitle">Manage your business information and operational settings</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('chef.profile.edit') }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Edit Profile
                </a>
            </div>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Business Overview</h5>
                    <span class="badge bg-{{ $profile->isVerified() ? 'success' : 'warning' }}">
                        {{ $profile->isVerified() ? 'Verified' : 'Verification Pending' }}
                    </span>
                </div>
                <div class="card-body">
                    <h6>About Us</h6>
                    <p class="text-muted">{{ $profile->bio }}</p>

                    <h6 class="mt-4">Specialties & Cuisines</h6>
                    <div class="mb-3">
                        <p class="mb-1 fw-bold">Specialties:</p>
                        @foreach($profile->specialtiesArray as $specialty)
                            <span class="badge bg-primary me-1 mb-1">{{ $specialty }}</span>
                        @endforeach
                    </div>
                    <div>
                        <p class="mb-1 fw-bold">Cuisines:</p>
                        @foreach($profile->cuisines as $cuisine)
                            <span class="badge bg-secondary me-1 mb-1">{{ $cuisine }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="dashboard-card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Operational Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <p class="mb-1 fw-bold">Years of Experience:</p>
                            <p>{{ $profile->years_of_experience }} years</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-1 fw-bold">Accepting Orders:</p>
                            <span class="badge bg-{{ $profile->isAcceptingOrders() ? 'success' : 'danger' }} fs-6">
                                {{ $profile->isAcceptingOrders() ? 'YES' : 'NO' }}
                            </span>
                        </div>
                        <div class="col-12">
                            <p class="mb-1 fw-bold">Kitchen Address:</p>
                            <p class="text-muted">{{ $profile->kitchen_address }}</p>
                            @if($profile->kitchen_latitude && $profile->kitchen_longitude)
                                <p class="small text-info">Coordinates: {{ $profile->kitchen_latitude }}, {{ $profile->kitchen_longitude }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-card mb-4">
                <div class="card-header">
                    <h5 class="card-title"><i class="fas fa-university me-2"></i>Banking Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <p class="mb-1 fw-bold">Account Name:</p>
                            <p>{{ $profile->account_name ?? 'Not Set' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-1 fw-bold">Account Number:</p>
                            <p>{{ $profile->account_number ?? 'Not Set' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-1 fw-bold">Bank Name:</p>
                            <p>{{ $profile->bank_name ?? 'Not Set' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-1 fw-bold">BVN (Secure):</p>
                            <p>{{ $profile->bvn ? Str::mask($profile->bvn, '*', 0, 7) : 'Not Verified' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Delivery Policy</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="d-flex justify-content-between mb-2">
                            <span>Delivery Radius:</span>
                            <span class="fw-bold">{{ $profile->delivery_radius_km }} KM</span>
                        </li>
                        <li class="d-flex justify-content-between mb-2">
                            <span>Minimum Order:</span>
                            <span class="fw-bold">₦{{ number_format($profile->minimum_order_amount, 2) }}</span>
                        </li>
                        <li class="d-flex justify-content-between mb-2">
                            <span>Base Delivery Fee:</span>
                            <span class="fw-bold">₦{{ number_format($profile->delivery_fee, 2) }}</span>
                        </li>
                        <li class="d-flex justify-content-between pt-2 border-top mt-2">
                            <span>Free Delivery Over:</span>
                            @if($profile->free_delivery_over_amount && $profile->free_delivery_threshold)
                                <span class="text-success fw-bold">₦{{ number_format($profile->free_delivery_threshold, 2) }}</span>
                            @else
                                <span class="text-danger fw-bold">No</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

            <div class="dashboard-card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Operating Hours</h5>
                </div>
                <div class="card-body">
                    @if($profile->operating_hours)
                        @foreach($profile->operating_hours as $day => $hours)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-medium">{{ ucfirst($day) }}:</span>
                                @if(isset($hours['is_open']) && $hours['is_open'])
                                    <span class="text-success fw-bold">{{ $hours['open_time'] }} - {{ $hours['close_time'] }}</span>
                                @else
                                    <span class="text-danger">Closed</span>
                                @endif
                            </div>
                        @endforeach
                    @else
                         <p class="text-muted text-center">Hours not set. Please update your profile.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection