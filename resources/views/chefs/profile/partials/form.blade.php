<div class="dashboard-card mb-4">
    <div class="card-header">
        <h5 class="card-title">Business & Personal Details</h5>
    </div>
    <div class="card-body">
        <div class="mb-4">
            <label for="business_name" class="form-label">Business Name *</label>
            <input type="text" class="form-control @error('business_name') is-invalid @enderror" id="business_name" name="business_name" 
                   value="{{ old('business_name', $profile->business_name) }}" required>
            @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label for="bio" class="form-label">Short Bio / About Us *</label>
            <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="4" 
                      placeholder="Tell customers about your brand, philosophy, and what makes your food unique..." required>{{ old('bio', $profile->bio) }}</textarea>
            @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <label for="specialties" class="form-label">Specialties (Key Dishes/Areas) *</label>
                @php
                    $specialties = old('specialties', is_array($profile->specialties) ? implode(', ', $profile->specialties) : $profile->specialties);
                @endphp
                <input type="text" class="form-control @error('specialties') is-invalid @enderror" id="specialties" name="specialties" 
                       value="{{ $specialties }}" placeholder="Grilling, Pastries, Vegetarian, Party Catering" required>
                @error('specialties')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Separate multiple specialties with commas</div>
            </div>
            <div class="col-md-6">
                <label for="years_of_experience" class="form-label">Years of Experience *</label>
                <input type="number" class="form-control @error('years_of_experience') is-invalid @enderror" id="years_of_experience" name="years_of_experience" 
                       value="{{ old('years_of_experience', $profile->years_of_experience) }}" min="0" required>
                @error('years_of_experience')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        
        <div class="mb-4">
            <label for="cuisines" class="form-label">Cuisines Offered *</label>
            @php
                $cuisines = old('cuisines', is_array($profile->cuisines) ? implode(', ', $profile->cuisines) : $profile->cuisines);
            @endphp
            <input type="text" class="form-control @error('cuisines') is-invalid @enderror" id="cuisines" name="cuisines" 
                   value="{{ $cuisines }}" placeholder="Nigerian, Continental, Asian, Italian" required>
            @error('cuisines')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">Separate multiple cuisines with commas</div>
        </div>
    </div>
</div>

<div class="dashboard-card mb-4">
    <div class="card-header">
        <h5 class="card-title">Location & Delivery Settings</h5>
    </div>
    <div class="card-body">
        <div class="mb-4">
            <label for="kitchen_address" class="form-label">Kitchen/Business Address *</label>
            <input type="text" class="form-control @error('kitchen_address') is-invalid @enderror" id="kitchen_address" name="kitchen_address" 
                   value="{{ old('kitchen_address', $profile->kitchen_address) }}" required>
            @error('kitchen_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">This address is used for delivery calculations.</div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-4">
                <label for="delivery_radius_km" class="form-label">Delivery Radius (KM) *</label>
                <input type="number" class="form-control @error('delivery_radius_km') is-invalid @enderror" id="delivery_radius_km" name="delivery_radius_km" 
                       value="{{ old('delivery_radius_km', $profile->delivery_radius_km) }}" min="1" max="500" required>
                @error('delivery_radius_km')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="delivery_fee" class="form-label">Base Delivery Fee (₦) *</label>
                <input type="number" class="form-control @error('delivery_fee') is-invalid @enderror" id="delivery_fee" name="delivery_fee" 
                       value="{{ old('delivery_fee', $profile->delivery_fee) }}" min="0" step="0.01" required>
                @error('delivery_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="minimum_order_amount" class="form-label">Minimum Order (₦) *</label>
                <input type="number" class="form-control @error('minimum_order_amount') is-invalid @enderror" id="minimum_order_amount" name="minimum_order_amount" 
                       value="{{ old('minimum_order_amount', $profile->minimum_order_amount) }}" min="0" step="0.01" required>
                @error('minimum_order_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="free_delivery_over_amount" 
                           name="free_delivery_over_amount" value="1" 
                           {{ old('free_delivery_over_amount', $profile->free_delivery_over_amount) ? 'checked' : '' }}>
                    <label class="form-check-label" for="free_delivery_over_amount">
                        Offer Free Delivery Over an Amount
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <label for="free_delivery_threshold" class="form-label">Threshold Amount (₦)</label>
                <input type="number" class="form-control @error('free_delivery_threshold') is-invalid @enderror" id="free_delivery_threshold" name="free_delivery_threshold" 
                       value="{{ old('free_delivery_threshold', $profile->free_delivery_threshold) }}" min="0" step="0.01" 
                       placeholder="e.g., 15000.00">
                @error('free_delivery_threshold')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <label for="kitchen_latitude" class="form-label">Latitude (Optional)</label>
                <input type="text" class="form-control @error('kitchen_latitude') is-invalid @enderror" id="kitchen_latitude" name="kitchen_latitude" 
                       value="{{ old('kitchen_latitude', $profile->kitchen_latitude) }}" placeholder="e.g., 6.5244">
                @error('kitchen_latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="kitchen_longitude" class="form-label">Longitude (Optional)</label>
                <input type="text" class="form-control @error('kitchen_longitude') is-invalid @enderror" id="kitchen_longitude" name="kitchen_longitude" 
                       value="{{ old('kitchen_longitude', $profile->kitchen_longitude) }}" placeholder="e.g., 3.3792">
                @error('kitchen_longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

<div class="dashboard-card mb-4">
    <div class="card-header">
        <h5 class="card-title">Banking & Account</h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <label for="account_name" class="form-label">Account Name</label>
                <input type="text" class="form-control @error('account_name') is-invalid @enderror" id="account_name" name="account_name" 
                       value="{{ old('account_name', $profile->account_name) }}" placeholder="Your Full Account Name">
                @error('account_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="account_number" class="form-label">Account Number</label>
                <input type="text" class="form-control @error('account_number') is-invalid @enderror" id="account_number" name="account_number" 
                       value="{{ old('account_number', $profile->account_number) }}" placeholder="0123456789">
                @error('account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label for="bank_name" class="form-label">Bank Name</label>
                <input type="text" class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name" 
                       value="{{ old('bank_name', $profile->bank_name) }}" placeholder="e.g., Zenith Bank">
                @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="bvn" class="form-label">BVN (For Verification)</label>
                <input type="text" class="form-control @error('bvn') is-invalid @enderror" id="bvn" name="bvn" 
                       value="{{ old('bvn', $profile->bvn) }}" placeholder="11-digit BVN">
                @error('bvn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Required for payout verification.</div>
            </div>
        </div>
    </div>
</div>
<div class="d-flex justify-content-end gap-2 mt-4">
    <button type="submit" class="btn btn-success btn-lg">
        <i class="fas fa-save me-2"></i>{{ $profile->exists ? 'Save Changes' : 'Create Profile' }}
    </button>
</div>