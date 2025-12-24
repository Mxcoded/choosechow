@extends('layouts.app')

@section('title', 'Login - ChooseChow')

@section('content')
<div class="auth-container">
    <div class="auth-card row g-0">
        <div class="col-lg-5 auth-left">
            <div class="brand-logo">
                <img src="{{ asset('storage/img/choosechowlogo.png') }}" alt="ChooseChow Logo" 
                     class="h-24 w-24 rounded-full shadow-md transition-transform duration-300 hover:scale-105">
            </div>
            <h1 class="brand-name">ChooseChow</h1>
            <p class="brand-tagline">Discover amazing homemade meals from talented local chefs</p>
            
            <ul class="auth-features">
                <li><i class="fas fa-utensils"></i> Fresh homemade meals</li>
                <li><i class="fas fa-star"></i> Rated local chefs</li>
                <li><i class="fas fa-truck"></i> Fast delivery</li>
                <li><i class="fas fa-heart"></i> Made with love</li>
            </ul>
        </div>
        
        <div class="col-lg-7 auth-right">
            <div class="auth-header">
                <h2 class="auth-title">Welcome Back!</h2>
                <p class="auth-subtitle">Sign in to your account to continue</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="form-floating mb-3">
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" placeholder="name@example.com" 
                           value="{{ old('email') }}" required autocomplete="email" autofocus>
                    <label for="email">Email Address</label>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" placeholder="Password" required autocomplete="current-password">
                    <label for="password">Password</label>
                    
                    <button type="button" class="password-toggle btn btn-link position-absolute top-50 end-0 translate-middle-y text-decoration-none" 
                            style="right: 10px; z-index: 5;" onclick="togglePassword('password')">
                        <i class="fas fa-eye text-muted" id="passwordEye"></i>
                    </button>
                    
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" 
                               {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label text-muted" for="remember">
                            Remember me
                        </label>
                    </div>
                    
                    @if (Route::has('password.request'))
                        <a class="auth-link small" href="{{ route('password.request') }}">
                            Forgot Password?
                        </a>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </button>

                <div class="divider text-center my-3">
                    <span class="text-muted">Don't have an account?</span>
                </div>

                <a href="{{ route('register') }}" class="btn btn-outline-primary w-100 py-2">
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </a>
            </form>
        </div>
    </div>
</div>
@endsection