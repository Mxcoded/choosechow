@extends('layouts.dashboard')

@section('title', 'Edit Store Profile')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header mb-4">
        <h1 class="h3 fw-bold text-dark">Store Settings 🏪</h1>
        <p class="text-muted">Manage your business profile, hours, and banking details.</p>
    </div>

    @if(session('error'))
        <div class="alert alert-danger mb-4">{{ session('error') }}</div>
    @endif

    <form action="{{ route('chef.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row g-4">
            {{-- Left Sidebar: Avatar & Status --}}
            <div class="col-lg-4">
                @include('chefs.profile.partials.side_fields')
            </div>

            {{-- Right Content: Tabs --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    {{-- Tabs Header --}}
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                        <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active fw-bold" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true">
                                    General Info
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link fw-bold" id="hours-tab" data-bs-toggle="tab" data-bs-target="#hours" type="button" role="tab" aria-controls="hours" aria-selected="false">
                                    Operating Hours
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link fw-bold" id="bank-tab" data-bs-toggle="tab" data-bs-target="#bank" type="button" role="tab" aria-controls="bank" aria-selected="false">
                                    Bank Details
                                </button>
                            </li>
                        </ul>
                    </div>
                    
                    {{-- Tabs Content --}}
                    <div class="card-body p-4">
                        <div class="tab-content" id="profileTabsContent">
                            <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                                @include('chefs.profile.partials.form')
                            </div>

                            <div class="tab-pane fade" id="hours" role="tabpanel" aria-labelledby="hours-tab">
                                @include('chefs.profile.partials.operating_hours')
                            </div>

                            <div class="tab-pane fade" id="bank" role="tabpanel" aria-labelledby="bank-tab">
                                <div class="alert alert-secondary bg-light border-0 mb-3">
                                    <small><i class="fas fa-lock me-1"></i> Your banking details are secure and used only for payouts.</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Bank Name</label>
                                    <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $profile->bank_name) }}">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Account Number</label>
                                        <input type="text" name="account_number" class="form-control" value="{{ old('account_number', $profile->account_number) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Account Name</label>
                                        <input type="text" name="account_name" class="form-control" value="{{ old('account_name', $profile->account_name) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Save Button (Global) --}}
                    <div class="card-footer bg-white p-4 border-top-0">
                        <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">
                            <i class="fas fa-save me-2"></i>Save All Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection