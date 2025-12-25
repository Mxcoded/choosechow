<form action="{{ $menu ? route('chef.menus.update', $menu) : route('chef.menus.store') }}" method="POST" id="menuForm" enctype="multipart/form-data">
    @csrf
    @if($menu)
        @method('PUT')
    @endif
    
    <div class="form-section">
        <div class="form-section-title">Basic Information</div>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <label for="name" class="form-label">Item Name *</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                       value="{{ old('name', $menu->name ?? '') }}" placeholder="e.g., Jollof Rice Special" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label for="price" class="form-label">Price (₦) *</label>
                <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" 
                       value="{{ old('price', $menu->price ?? '') }}" step="0.01" min="0" required>
            </div>
            <div class="col-md-3">
                <label for="discounted_price" class="form-label">Sale Price (Optional)</label>
                <input type="number" class="form-control" id="discounted_price" name="discounted_price" 
                       value="{{ old('discounted_price', $menu->discounted_price ?? '') }}" step="0.01" min="0">
            </div>
        </div>

        <div class="mb-4">
            <label for="description" class="form-label">Description *</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" 
                      required>{{ old('description', $menu->description ?? '') }}</textarea>
        </div>
    </div>

    <div class="form-section">
        <div class="form-section-title">Categorization</div>
        
        {{-- Category Dropdown --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <label for="category_id" class="form-label">Category *</label>
                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" 
                            {{ (old('category_id', $menu->category_id ?? '') == $category->id) ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Prep Time (Mins)</label>
                <input type="number" class="form-control" name="preparation_time_minutes" 
                       value="{{ old('preparation_time_minutes', $menu->preparation_time_minutes ?? 30) }}">
            </div>
             <div class="col-md-3">
                <label class="form-label">Serves (People)</label>
                <input type="number" class="form-control" name="serves_count" 
                       value="{{ old('serves_count', $menu->serves_count ?? 1) }}">
            </div>
        </div>

        {{-- Cuisines Checkboxes --}}
        <div class="mb-4">
            <label class="form-label fw-bold">Cuisines / Ethnicity</label>
            <div class="card p-3 bg-light border-0">
                <div class="row">
                    @foreach($cuisines as $cuisine)
                        <div class="col-md-4 col-sm-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="cuisine_ids[]" 
                                       value="{{ $cuisine->id }}" id="cuisine_{{ $cuisine->id }}"
                                       {{ (is_array(old('cuisine_ids')) && in_array($cuisine->id, old('cuisine_ids'))) || ($menu && $menu->cuisines->contains($cuisine->id)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="cuisine_{{ $cuisine->id }}">
                                    {{ $cuisine->name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('cuisine_ids')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Dietary Checkboxes --}}
        <div class="mb-4">
            <label class="form-label fw-bold">Dietary Preferences</label>
            <div class="card p-3 bg-light border-0">
                <div class="row">
                    @foreach($dietaries as $dietary)
                        <div class="col-md-4 col-sm-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="dietary_ids[]" 
                                       value="{{ $dietary->id }}" id="dietary_{{ $dietary->id }}"
                                       {{ (is_array(old('dietary_ids')) && in_array($dietary->id, old('dietary_ids'))) || ($menu && $menu->dietaryPreferences->contains($dietary->id)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="dietary_{{ $dietary->id }}">
                                    {{ $dietary->name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <div class="form-section">
        <div class="form-section-title">Images</div>
        
        @if($menu && $menu->images)
            <div class="d-flex gap-2 mb-3">
                @foreach($menu->images as $img)
                    <img src="{{ asset('storage/' . $img) }}" class="rounded" width="80" height="80" style="object-fit: cover;">
                @endforeach
            </div>
        @endif
        
        <input type="file" class="form-control" name="images[]" multiple accept="image/*">
        <div class="form-text">Upload up to 5 images. First image will be the cover.</div>
    </div>

    <div class="form-section">
        <div class="form-section-title">Availability</div>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="is_available" name="is_available" value="1"
                   {{ old('is_available', $menu->is_available ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_available">Available for ordering now</label>
        </div>
    </div>

    <hr class="my-4">
    
    <button type="submit" class="btn btn-primary btn-lg">
        <i class="fas fa-save me-2"></i>{{ $menu ? 'Update Menu Item' : 'Create Menu Item' }}
    </button>
</form>