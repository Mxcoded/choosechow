<!-- resources/views/chef/menus/edit.blade.php -->
@extends('layouts.dashboard')

@section('title', 'Edit Menu Item - Chef Dashboard')


@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="dashboard-title">Edit Menu Item ✏️</h1>
                <p class="dashboard-subtitle">Update details for "{{ $menu->name }}"</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('chef.menus') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Back to Menus
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title">Menu Item Details</h5>
                </div>
                <div class="card-body">
                    @include('chefs.menu.partials.form', ['menu' => $menu])
                </div>
            </div>
        </div>

        <!-- Preview Card -->
        <div class="col-lg-4">
            @include('chefs.menu.partials.preview', ['menu' => $menu])
        </div>
    </div>
</div>

@include('chefs.menu.partials.scripts', ['menu' => $menu])
@endsection