@extends('layouts.dashboard')

@section('title', 'Add New Menu Item - Chef Dashboard')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="dashboard-title h2 fw-bold text-dark">Add New Menu Item 🍽️</h1>
                <p class="text-muted">Create a delicious new item for your menu with complete details</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                {{-- FIXED ROUTE: chef.menus -> chef.menus.index --}}
                <a href="{{ route('chef.menus.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Menus
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Main Form Column --}}
        <div class="col-lg-8">
            {{-- We remove the .dashboard-card wrapper here because the form partial 
                 now includes its own cards for better organization --}}
            @include('chefs.menu.partials.form', ['menu' => null])
        </div>

        {{-- Preview Column --}}
        <div class="col-lg-4">
            {{-- Sticky Preview Card --}}
            <div class="sticky-top" style="top: 100px; z-index: 900;">
                <h5 class="mb-3 fw-bold text-muted">Live Preview</h5>
                @include('chefs.menu.partials.preview', ['menu' => null])
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @include('chefs.menu.partials.scripts')
@endsection