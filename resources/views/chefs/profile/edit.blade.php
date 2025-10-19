@extends('layouts.dashboard')

@section('title', 'Edit Chef Profile')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="dashboard-title">
                    {{ $profile->exists ? 'Edit Profile' : 'Complete Your Profile' }} ✏️
                </h1>
                <p class="dashboard-subtitle">Provide accurate details to start accepting orders.</p>
            </div>
            <div class="col-md-4 text-end">
                @if($profile->exists)
                    <a href="{{ route('chef.profile') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Profile
                    </a>
                @endif
            </div>
        </div>
    </div>
    
    <form action="{{ route('chef.profile.update') }}" method="POST">
        @csrf
        {{-- Use PUT method if updating an existing model --}}
        @if($profile->exists)
            @method('PUT') 
        @endif

        <div class="row">
            <div class="col-lg-8">
                {{-- Include form partials here --}}
                @include('chefs.profile.partials.form', ['profile' => $profile])
            </div>
            <div class="col-lg-4">
                {{-- Include specialized side fields (like operating hours or banking) --}}
                @include('chefs.profile.partials.side_fields', ['profile' => $profile])
            </div>
        </div>
    </form>
</div>
@endsection