<!-- resources/views/chef/menus/partials/_preview.blade.php -->
<div class="dashboard-card sticky-top" style="top: 2rem;">
    <div class="card-header">
        <h5 class="card-title">Live Preview</h5>
    </div>
    <div class="card-body">
        <div class="menu-preview">
            <div class="preview-image" id="previewImage">
                @if($menu && $menu->images && count($menu->images) > 0)
                    <img src="{{ Storage::url($menu->images[0]) }}" alt="Preview">
                @else
                    <div class="preview-placeholder">
                        <i class="fas fa-utensils fa-2x"></i>
                        <p class="mt-2 mb-0">Image Preview</p>
                    </div>
                @endif
            </div>
            <div class="preview-content">
                <div class="preview-header">
                    <h6 id="previewName">{{ ($menu && $menu->name) ? $menu->name : 'Menu Item Name' }}</h6>
                    <div>
                        <span class="preview-price" id="previewPrice">
                            @if($menu && $menu->discounted_price)
                                ₦{{ number_format($menu->discounted_price, 2) }}
                            @elseif($menu && $menu->price)
                                ₦{{ number_format($menu->price, 2) }}
                            @else
                                ₦0.00
                            @endif
                        </span>
                        @if($menu && $menu->discounted_price && $menu->price)
                            <div id="previewDiscountedPrice" class="text-muted small">
                                <s id="previewOriginalPrice">₦{{ number_format($menu->price, 2) }}</s>
                            </div>
                        @else
                            <div id="previewDiscountedPrice" class="text-muted small" style="display: none;">
                                <s id="previewOriginalPrice">₦0.00</s>
                            </div>
                        @endif
                    </div>
                </div>
                <p class="preview-description" id="previewDescription">
                    {{ ($menu && $menu->description) ? $menu->description : 'Item description will appear here...' }}
                </p>
                <div class="preview-meta">
                    <div>
                        <span class="badge bg-light text-dark" id="previewCategory">
                            {{ ($menu && $menu->category) ? ucfirst($menu->category) : 'Category' }}
                        </span>
                        <div id="previewSpiceLevel" class="spice-indicator mt-1" style="{{ ($menu && $menu->spice_level !== null && $menu->spice_level !== '') ? 'display: flex;' : 'display: none;' }}">
                            <small class="text-muted me-1">Spice:</small>
                            @for($i = 0; $i < 5; $i++)
                                <div class="spice-level {{ ($menu && $menu->spice_level !== null && $menu->spice_level >= $i) ? 'active' : '' }}"></div>
                            @endfor
                        </div>
                    </div>
                    <div class="text-end">
                        <small class="text-muted" id="previewTime">
                            <i class="fas fa-clock me-1"></i>
                            {{ ($menu && $menu->preparation_time_minutes) ? $menu->preparation_time_minutes . ' minutes' : 'Prep time' }}
                        </small>
                        <div class="small text-muted" id="previewServes">
                            <i class="fas fa-users me-1"></i>Serves {{ ($menu && $menu->serves_count) ? $menu->serves_count : 1 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
