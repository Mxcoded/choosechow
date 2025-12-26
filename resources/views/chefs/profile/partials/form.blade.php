<div class="row g-3">
    <div class="col-md-12">
        <label class="form-label">Business Name / Brand Name *</label>
        <input type="text" name="business_name" class="form-control" value="{{ old('business_name', $profile->business_name) }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">First Name</label>
        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $chef->first_name) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Last Name</label>
        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $chef->last_name) }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Phone Number</label>
        <input type="tel" name="phone" class="form-control" value="{{ old('phone', $chef->phone) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Years of Experience</label>
        <input type="number" name="years_of_experience" class="form-control" value="{{ old('years_of_experience', $profile->years_of_experience) }}" min="0">
    </div>

    <div class="col-12">
        <label class="form-label">Cuisine Specialties (Select all that apply)</label>
        <div class="card p-3 border bg-light">
            <div class="row">
                @foreach($cuisines as $cuisine)
                    <div class="col-md-4 col-6 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="cuisine_ids[]" 
                                   value="{{ $cuisine->id }}" id="spec_{{ $cuisine->id }}"
                                   {{ ($profile->cuisines->contains($cuisine->id) || (is_array(old('cuisine_ids')) && in_array($cuisine->id, old('cuisine_ids')))) ? 'checked' : '' }}>
                            <label class="form-check-label" for="spec_{{ $cuisine->id }}">{{ $cuisine->name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @error('cuisine_ids') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Minimum Order Amount (₦)</label>
        <input type="number" name="minimum_order" class="form-control" value="{{ old('minimum_order', $profile->minimum_order ?? 0) }}">
        <div class="form-text">Customers cannot place orders below this amount.</div>
    </div>

    <div class="col-12">
        <label class="form-label">Chef Bio / Story</label>
        <textarea name="bio" class="form-control" rows="4" placeholder="Tell customers about your culinary journey...">{{ old('bio', $profile->bio) }}</textarea>
    </div>
</div>