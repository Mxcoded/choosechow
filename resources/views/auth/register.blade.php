@extends('layouts.app')

@section('title', 'Sign Up - ChooseChow')

@section('content')
<div class="auth-container">
    <div class="auth-card row g-0">
        <div class="col-lg-5 auth-left">
            <div class="brand-logo"><img src="{{ asset('storage/img/choosechowlogo.png') }}" alt="ChooseChow Logo" 
                         class="h-24 w-24 rounded-full shadow-md transition-transform duration-300 hover:scale-105"></div>
            <h1 class="brand-name">ChooseChow</h1>
            <p class="brand-tagline">Join our community of food lovers and talented chefs</p>
            
            <ul class="auth-features">
                <li><i class="fas fa-users"></i> Join our community</li>
                <li><i class="fas fa-chef-hat"></i> Become a chef</li>
                <li><i class="fas fa-mobile-alt"></i> Order on the go</li>
                <li><i class="fas fa-gift"></i> Exclusive offers</li>
            </ul>
        </div>
        
        <div class="col-lg-7 auth-right">
            <div class="auth-header">
                <h2 class="auth-title">Create Account</h2>
                <p class="auth-subtitle">Join ChooseChow and start your culinary journey</p>
            </div>

            <!-- Laravel Validation Errors -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="signupForm">
                @csrf

                <!-- User Type Selection -->
                <div class="user-type-selector">
                    <div class="user-type-option active" data-type="customer">
                        <i class="fas fa-utensils"></i>
                        <div class="title">Customer</div>
                        <div class="description">Order delicious meals</div>
                    </div>
                    <div class="user-type-option" data-type="chef">
                        <i class="fas fa-chef-hat"></i>
                        <div class="title">Chef</div>
                        <div class="description">Share your culinary skills</div>
                    </div>
                </div>

                <input type="hidden" name="user_type" id="userType" value="{{ old('user_type', 'customer') }}">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                   id="firstName" name="first_name" placeholder="First Name" 
                                   value="{{ old('first_name') }}" required autocomplete="given-name">
                            <label for="firstName">First Name</label>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                   id="lastName" name="last_name" placeholder="Last Name" 
                                   value="{{ old('last_name') }}" required autocomplete="family-name">
                            <label for="lastName">Last Name</label>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-floating">
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="signupEmail" name="email" placeholder="name@example.com" 
                           value="{{ old('email') }}" required autocomplete="email">
                    <label for="signupEmail">Email Address</label>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-floating">
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                           id="phone" name="phone" placeholder="Phone Number" 
                           value="{{ old('phone') }}" required autocomplete="tel">
                    <label for="phone">Phone Number</label>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-floating">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="signupPassword" name="password" placeholder="Password" 
                           required autocomplete="new-password">
                    <label for="signupPassword">Password</label>
                    <button type="button" class="password-toggle" onclick="togglePassword('signupPassword')">
                        <i class="fas fa-eye" id="signupPasswordEye"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-floating">
                    <input type="password" class="form-control" 
                           id="passwordConfirmation" name="password_confirmation" 
                           placeholder="Confirm Password" required autocomplete="new-password">
                    <label for="passwordConfirmation">Confirm Password</label>
                    <button type="button" class="password-toggle" onclick="togglePassword('passwordConfirmation')">
                        <i class="fas fa-eye" id="passwordConfirmationEye"></i>
                    </button>
                </div>

                <!-- Optional Referral Code -->
                <div class="form-floating">
                    <input type="text" class="form-control @error('referred_by') is-invalid @enderror" 
                           id="referralCode" name="referred_by" placeholder="Referral Code (Optional)" 
                           value="{{ old('referred_by') }}" autocomplete="off">
                    <label for="referralCode">Referral Code (Optional)</label>
                    @error('referred_by')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input @error('terms') is-invalid @enderror" 
                           type="checkbox" name="terms" id="terms" required {{ old('terms') ? 'checked' : '' }}>
                    <label class="form-check-label" for="terms">
                        I agree to the <a href="{{ route('privacy.policy') }}" class="auth-link">Terms of Service</a> and 
                        <a href="{{ route('privacy.policy') }}" class="auth-link">Privacy Policy</a>
                    </label>
                    @error('terms')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary mb-3">
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </button>

                <div class="divider">
                    <span>Already have an account?</span>
                </div>

                <a href="{{ route('login') }}" class="btn btn-outline-primary">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </a>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
// User type selection
document.querySelectorAll('.user-type-option').forEach(option => {
    option.addEventListener('click', function() {
        // Remove active class from all options
        document.querySelectorAll('.user-type-option').forEach(opt => {
            opt.classList.remove('active');
        });
        
        // Add active class to clicked option
        this.classList.add('active');
        
        // Update hidden input
        document.getElementById('userType').value = this.dataset.type;
    });
});

// Phone number formatting
document.getElementById('phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 10) {
        value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
    } else if (value.length >= 6) {
        value = value.replace(/(\d{3})(\d{3})/, '($1) $2-');
    } else if (value.length >= 3) {
        value = value.replace(/(\d{3})/, '($1) ');
    }
    e.target.value = value;
});

// Set user type on page load if old value exists
@if(old('user_type'))
    document.addEventListener('DOMContentLoaded', function() {
        const userType = '{{ old('user_type') }}';
        document.querySelectorAll('.user-type-option').forEach(opt => {
            opt.classList.remove('active');
        });
        document.querySelector(`[data-type="${userType}"]`).classList.add('active');
        document.getElementById('userType').value = userType;
    });
@endif
@endsection