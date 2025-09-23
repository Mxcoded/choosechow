<form action="{{ $menu ? route('chef.menus.update', $menu) : route('chef.menus.store') }}" method="POST" id="menuForm" enctype="multipart/form-data">
    @csrf
    @if($menu)
        @method('PUT')
    @endif
    
    <!-- Basic Information Section -->
    <div class="form-section">
        <div class="form-section-title">Basic Information</div>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <label for="name" class="form-label">Item Name *</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                       value="{{ old('name', $menu->name ?? '') }}" placeholder="e.g., Jollof Rice with Grilled Chicken" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label for="price" class="form-label">Price (₦) *</label>
                <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" 
                       value="{{ old('price', $menu->price ?? '') }}" step="0.01" min="0.01" max="9999.99" placeholder="2500.00" required>
                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label for="discounted_price" class="form-label">Sale Price (₦)</label>
                <input type="number" class="form-control @error('discounted_price') is-invalid @enderror" id="discounted_price" name="discounted_price" 
                       value="{{ old('discounted_price', $menu->discounted_price ?? '') }}" step="0.01" min="0.01" max="9999.99" placeholder="2000.00">
                @error('discounted_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mb-4">
            <label for="description" class="form-label">Description *</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" 
                      maxlength="2000" placeholder="Describe your dish, its flavors, and what makes it special..." required>{{ old('description', $menu->description ?? '') }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="character-counter">
                <span id="descriptionCount">0</span>/2000 characters
            </div>
        </div>
    </div>

    <!-- Category & Details Section -->
    <div class="form-section">
        <div class="form-section-title">Category & Details</div>
        
        <div class="row mb-4">
            <div class="col-md-4">
                <label for="category" class="form-label">Category *</label>
                <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                    <option value="">Select Category</option>
                    @php
                        $categories = [
                            'appetizer' => 'Appetizer',
                            'main' => 'Main Course',
                            'dessert' => 'Dessert',
                            'beverage' => 'Beverage',
                            'snack' => 'Snack'
                        ];
                        $selectedCategory = old('category', $menu->category ?? '');
                    @endphp
                    @foreach($categories as $value => $label)
                        <option value="{{ $value }}" {{ $selectedCategory == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="preparation_time_minutes" class="form-label">Prep Time (minutes)</label>
                <input type="number" class="form-control @error('preparation_time_minutes') is-invalid @enderror" id="preparation_time_minutes" 
                       name="preparation_time_minutes" value="{{ old('preparation_time_minutes', $menu->preparation_time_minutes ?? '') }}" min="1" max="1440" placeholder="30">
                @error('preparation_time_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="serves_count" class="form-label">Serves *</label>
                <input type="number" class="form-control @error('serves_count') is-invalid @enderror" id="serves_count" name="serves_count" 
                       value="{{ old('serves_count', $menu->serves_count ?? 1) }}" min="1" max="50" placeholder="1" required>
                @error('serves_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <label for="cuisine_types" class="form-label">Cuisine Types</label>
                @php
                    $cuisineTypes = old('cuisine_types', $menu ? (is_array($menu->cuisine_types) ? implode(', ', $menu->cuisine_types) : $menu->cuisine_types) : '');
                @endphp
                <input type="text" class="form-control @error('cuisine_types') is-invalid @enderror" id="cuisine_types" name="cuisine_types" 
                       value="{{ $cuisineTypes }}" placeholder="Nigerian, Continental, Asian">
                @error('cuisine_types')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Separate multiple cuisines with commas</div>
            </div>
            <div class="col-md-6">
                <label for="dietary_info" class="form-label">Dietary Information</label>
                @php
                    $dietaryInfo = old('dietary_info', $menu ? (is_array($menu->dietary_info) ? implode(', ', $menu->dietary_info) : $menu->dietary_info) : '');
                @endphp
                <input type="text" class="form-control @error('dietary_info') is-invalid @enderror" id="dietary_info" name="dietary_info" 
                       value="{{ $dietaryInfo }}" placeholder="Vegetarian, Gluten-Free, Halal">
                @error('dietary_info')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Separate multiple dietary tags with commas</div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <label for="spice_level" class="form-label">Spice Level</label>
                <select class="form-select @error('spice_level') is-invalid @enderror" id="spice_level" name="spice_level">
                    @php
                        $spiceLevels = [
                            '' => 'Not Applicable',
                            '0' => 'Mild',
                            '1' => 'Low',
                            '2' => 'Medium',
                            '3' => 'Hot',
                            '4' => 'Very Hot',
                            '5' => 'Extremely Hot'
                        ];
                        $selectedSpice = old('spice_level', $menu->spice_level ?? '');
                    @endphp
                    @foreach($spiceLevels as $value => $label)
                        <option value="{{ $value }}" {{ $selectedSpice == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('spice_level')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="stock_quantity" class="form-label">Stock Quantity</label>
                <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror" id="stock_quantity" name="stock_quantity" 
                       value="{{ old('stock_quantity', $menu->stock_quantity ?? '') }}" min="0" placeholder="Leave empty for unlimited">
                @error('stock_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Leave empty for unlimited stock</div>
            </div>
        </div>
    </div>

    <!-- Ingredients & Allergens Section -->
    <div class="form-section">
        <div class="form-section-title">Ingredients & Allergens</div>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <label for="ingredients" class="form-label">Ingredients</label>
                @php
                    $ingredients = old('ingredients', $menu ? (is_array($menu->ingredients) ? implode(', ', $menu->ingredients) : $menu->ingredients) : '');
                @endphp
                <textarea class="form-control @error('ingredients') is-invalid @enderror" id="ingredients" name="ingredients" rows="3"
                          placeholder="Rice, chicken, tomatoes, onions, spices">{{ $ingredients }}</textarea>
                @error('ingredients')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Separate ingredients with commas</div>
            </div>
            <div class="col-md-6">
                <label for="allergens" class="form-label">Allergens</label>
                @php
                    $allergens = old('allergens', $menu ? (is_array($menu->allergens) ? implode(', ', $menu->allergens) : $menu->allergens) : '');
                @endphp
                <textarea class="form-control @error('allergens') is-invalid @enderror" id="allergens" name="allergens" rows="3"
                          placeholder="Gluten, dairy, nuts">{{ $allergens }}</textarea>
                @error('allergens')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Separate allergens with commas</div>
            </div>
        </div>
    </div>

    <!-- Images Section -->
    <div class="form-section">
        <div class="form-section-title">Images</div>
        
        @if($menu && $menu->images && count($menu->images) > 0)
            <div class="mb-3">
                <label class="form-label">Current Images</label>
                <div class="current-images">
                    @foreach($menu->images as $image)
                        <div class="current-image">
                            <img src="{{ Storage::url($image) }}" alt="Current image">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <div class="mb-4">
            <label for="images" class="form-label">{{ $menu ? 'Replace Images' : 'Menu Item Images' }}</label>
            <div class="image-upload-area" onclick="document.getElementById('images').click()">
                <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
                <p class="mb-1">Click to upload images or drag and drop</p>
                <small class="text-muted">Upload up to 5 images (JPEG, PNG, WebP, max 2MB each)</small>
                @if($menu)
                    <small class="d-block text-warning">Uploading new images will replace all current images</small>
                @endif
            </div>
            <input type="file" class="form-control d-none @error('images.*') is-invalid @enderror" id="images" name="images[]" 
                   multiple accept="image/jpeg,image/png,image/jpg,image/webp">
            @error('images.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div id="imagePreview" class="current-images"></div>
        </div>
    </div>

    <!-- Availability & Schedule Section -->
    <div class="form-section">
        <div class="form-section-title">Availability & Schedule</div>
        
        <div class="mb-4">
            <label for="availability_schedule" class="form-label">Availability Schedule</label>
            @php
                $schedule = old('availability_schedule', $menu ? (is_array($menu->availability_schedule) ? implode(', ', array_map(fn($k, $v) => "$k: $v", array_keys($menu->availability_schedule), $menu->availability_schedule)) : $menu->availability_schedule) : '');
            @endphp
            <input type="text" class="form-control @error('availability_schedule') is-invalid @enderror" id="availability_schedule" name="availability_schedule" 
                   value="{{ $schedule }}" placeholder="Mon-Fri: 9AM-5PM, Sat: 10AM-3PM">
            @error('availability_schedule')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">Format: Day: Time, Day: Time</div>
        </div>
    </div>

    <!-- Additional Information Section -->
    <div class="form-section">
        <div class="form-section-title">Additional Information</div>
        
        <div class="mb-4">
            <label for="nutritional_info" class="form-label">Nutritional Information</label>
            @php
                $nutrition = old('nutritional_info', $menu ? (is_array($menu->nutritional_info) ? implode(', ', array_map(fn($k, $v) => "$k: $v", array_keys($menu->nutritional_info), $menu->nutritional_info)) : $menu->nutritional_info) : '');
            @endphp
            <textarea class="form-control @error('nutritional_info') is-invalid @enderror" id="nutritional_info" name="nutritional_info" rows="3"
                      placeholder="Calories: 450, Protein: 35g, Carbs: 20g, Fat: 25g">{{ $nutrition }}</textarea>
            @error('nutritional_info')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">Format: Nutrient: Value, Nutrient: Value</div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <label for="cooking_instructions" class="form-label">Cooking Instructions</label>
                <textarea class="form-control @error('cooking_instructions') is-invalid @enderror" id="cooking_instructions" name="cooking_instructions" 
                          rows="4" maxlength="5000" placeholder="Detailed cooking instructions for the chef...">{{ old('cooking_instructions', $menu->cooking_instructions ?? '') }}</textarea>
                @error('cooking_instructions')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="storage_instructions" class="form-label">Storage Instructions</label>
                <textarea class="form-control @error('storage_instructions') is-invalid @enderror" id="storage_instructions" name="storage_instructions" 
                          rows="4" maxlength="1000" placeholder="How to store this item properly...">{{ old('storage_instructions', $menu->storage_instructions ?? '') }}</textarea>
                @error('storage_instructions')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <!-- Options Section -->
    <div class="form-section">
        <div class="form-section-title">Options</div>
        
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_available" 
                           name="is_available" {{ old('is_available', $menu->is_available ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_available">
                        Available for ordering
                    </label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_featured" 
                           name="is_featured" {{ old('is_featured', $menu->is_featured ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_featured">
                        Featured item
                    </label>
                </div>
            </div>
            <div class="col-md-4">
                <label for="featured_until" class="form-label">Featured Until</label>
                <input type="date" class="form-control @error('featured_until') is-invalid @enderror" id="featured_until" name="featured_until"
                       value="{{ old('featured_until', $menu && $menu->featured_until ? $menu->featured_until->format('Y-m-d') : '') }}">
                @error('featured_until')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <!-- Submit Buttons -->
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>{{ $menu ? 'Update' : 'Create' }} Menu Item
        </button>
        <a href="{{ route('chef.menus') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

