@extends('layouts.app')

@section('title', 'Sign Up - ChooseChow')

@section('content')
<div class="auth-container">
    <div class="auth-card row g-0">
        <div class="col-lg-5 auth-left">
            <div class="brand-logo">
                <img src="{{ asset('storage/img/choosechowlogo.png') }}" alt="ChooseChow Logo" 
                     class="h-24 w-24 rounded-full shadow-md transition-transform duration-300 hover:scale-105">
            </div>
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

                <div class="user-type-selector d-flex justify-content-center gap-3 mb-4">
                    <div class="user-type-option {{ old('user_type', 'customer') == 'customer' ? 'active' : '' }}" data-type="customer">
                        <i class="fas fa-utensils fa-2x mb-2"></i>
                        <div class="title fw-bold">Customer</div>
                        <div class="description small text-muted">Order delicious meals</div>
                    </div>
                    <div class="user-type-option {{ old('user_type') == 'chef' ? 'active' : '' }}" data-type="chef">
                        <i class="fas fa-chef-hat fa-2x mb-2"></i>
                        <div class="title fw-bold">Chef</div>
                        <div class="description small text-muted">Share your culinary skills</div>
                    </div>
                </div>
                <input type="hidden" name="user_type" id="userType" value="{{ old('user_type', 'customer') }}">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
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
                        <div class="form-floating mb-3">
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

                <div class="form-floating mb-3">
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="signupEmail" name="email" placeholder="name@example.com" 
                           value="{{ old('email') }}" required autocomplete="email">
                    <label for="signupEmail">Email Address</label>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-floating mb-3">
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                           id="phone" name="phone" placeholder="Phone Number" 
                           value="{{ old('phone') }}" required autocomplete="tel">
                    <label for="phone">Phone Number</label>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="signupPassword" name="password" placeholder="Password" 
                           required autocomplete="new-password">
                    <label for="signupPassword">Password</label>
                    <button type="button" class="password-toggle btn btn-link position-absolute top-50 end-0 translate-middle-y text-decoration-none" 
                            style="right: 10px; z-index: 5;" onclick="togglePassword('signupPassword')">
                        <i class="fas fa-eye text-muted" id="signupPasswordEye"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" 
                           id="passwordConfirmation" name="password_confirmation" 
                           placeholder="Confirm Password" required autocomplete="new-password">
                    <label for="passwordConfirmation">Confirm Password</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control @error('referred_by') is-invalid @enderror" 
                           id="referralCode" name="referred_by" placeholder="Referral Code (Optional)" 
                           value="{{ old('referred_by') }}">
                    <label for="referralCode">Referral Code (Optional)</label>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input @error('terms') is-invalid @enderror" 
                           type="checkbox" name="terms" id="terms" required {{ old('terms') ? 'checked' : '' }}>
                    <label class="form-check-label" for="terms">
                        I agree to the <a href="{{ route('terms.of.service') }}" class="auth-link">Terms of Service</a> and 
                        <a href="{{ route('privacy.policy') }}" class="auth-link">Privacy Policy</a>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3 py-2">
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </button>

                <div class="divider text-center my-3">
                    <span class="text-muted">Already have an account?</span>
                </div>

                <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 py-2">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </a>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- IMPORTANT: Logic must be wrapped in script tags --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Handle User Type Selection
        const userTypeInput = document.getElementById('userType');
        const options = document.querySelectorAll('.user-type-option');

        options.forEach(option => {
            option.addEventListener('click', function() {
                // Remove active class from all
                options.forEach(opt => opt.classList.remove('active'));
                
                // Add active class to clicked
                this.classList.add('active');
                
                // Update hidden input
                userTypeInput.value = this.dataset.type;
            });
        });

        // 2. Handle Phone Number Formatting
        const phoneInput = document.getElementById('phone');
        if(phoneInput) {
            phoneInput.addEventListener('input', function(e) {
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
        }

        // 3. Password Toggle Logic
        window.togglePassword = function(id) {
            const input = document.getElementById(id);
            const icon = document.getElementById(id + 'Eye'); // Target specific icon ID
            
            if (input.type === 'password') {
                input.type = 'text';
                if(icon) {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            } else {
                input.type = 'password';
                if(icon) {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        }
    });
</script>
@endsection