@extends('layouts.app')

@section('title', 'Login - ChooseChow')

@section('content')
<div class="auth-container">
    <div class="auth-card row g-0">
        <div class="col-lg-5 auth-left">
            <div class="brand-logo"><img src="{{ asset('storage/img/choosechowlogo.png') }}" alt="ChooseChow Logo" 
                         class="h-24 w-24 rounded-full shadow-md transition-transform duration-300 hover:scale-105"></div>
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

            <!-- Success Message -->
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="form-floating">
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" placeholder="name@example.com" 
                           value="{{ old('email') }}" required autocomplete="email" autofocus>
                    <label for="email">Email Address</label>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-floating">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" placeholder="Password" required autocomplete="current-password">
                    <label for="password">Password</label>
                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye" id="passwordEye"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" 
                           {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>

                <button type="submit" class="btn btn-primary mb-3">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </button>

                <div class="text-center">
                    @if (Route::has('password.request'))
                        <a class="auth-link" href="{{ route('password.request') }}">
                            Forgot your password?
                        </a>
                    @endif
                </div>

                <div class="divider">
                    <span>Don't have an account?</span>
                </div>

                <a href="{{ route('register') }}" class="btn btn-outline-primary">
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </a>
            </form>
        </div>
    </div>
</div>
@endsection