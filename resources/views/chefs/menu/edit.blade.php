@extends('layouts.dashboard')

@section('title', 'Edit Menu Item - Chef Dashboard')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="dashboard-title h2 fw-bold text-dark">Edit Menu Item ✏️</h1>
                <p class="text-muted">Update details for <span class="fw-bold text-primary">{{ $menu->name }}</span></p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                {{-- FIXED ROUTE --}}
                <a href="{{ route('chef.menus.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Menus
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            @include('chefs.menu.partials.form', ['menu' => $menu])
        </div>

        <div class="col-lg-4">
            <div class="sticky-top" style="top: 100px; z-index: 900;">
                <h5 class="mb-3 fw-bold text-muted">Live Preview</h5>
                @include('chefs.menu.partials.preview', ['menu' => $menu])
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @include('chefs.menu.partials.scripts')
@endsection