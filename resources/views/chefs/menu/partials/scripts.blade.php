<!-- resources/views/chef/menus/partials/_scripts.blade.php -->
<script>
    // Format currency as Naira
    function formatNaira(amount) {
        return '₦' + parseFloat(amount).toLocaleString('en-NG', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Live preview functionality
    document.getElementById('name').addEventListener('input', function() {
        document.getElementById('previewName').textContent = this.value || 'Menu Item Name';
    });

    document.getElementById('price').addEventListener('input', function() {
        const price = parseFloat(this.value) || 0;
        const discountedPrice = parseFloat(document.getElementById('discounted_price').value) || 0;
        
        if (discountedPrice > 0 && discountedPrice < price) {
            document.getElementById('previewPrice').textContent = formatNaira(discountedPrice);
            document.getElementById('previewOriginalPrice').textContent = formatNaira(price);
            document.getElementById('previewDiscountedPrice').style.display = 'block';
        } else {
            document.getElementById('previewPrice').textContent = formatNaira(price);
            document.getElementById('previewDiscountedPrice').style.display = 'none';
        }
    });

    document.getElementById('discounted_price').addEventListener('input', function() {
        // Trigger price update
        document.getElementById('price').dispatchEvent(new Event('input'));
    });

    document.getElementById('description').addEventListener('input', function() {
        const desc = this.value || 'Item description will appear here...';
        document.getElementById('previewDescription').textContent = desc.length > 100 ? desc.substring(0, 100) + '...' : desc;
        document.getElementById('descriptionCount').textContent = this.value.length;
    });

    document.getElementById('category').addEventListener('change', function() {
        const category = this.value ? this.options[this.selectedIndex].text : 'Category';
        document.getElementById('previewCategory').textContent = category;
    });

    document.getElementById('preparation_time_minutes').addEventListener('input', function() {
        const time = parseInt(this.value);
        const timeText = time ? `${time} minutes` : 'Prep time';
        document.getElementById('previewTime').innerHTML = `<i class="fas fa-clock me-1"></i>${timeText}`;
    });

    document.getElementById('serves_count').addEventListener('input', function() {
        const serves = parseInt(this.value) || 1;
        document.getElementById('previewServes').innerHTML = `<i class="fas fa-users me-1"></i>Serves ${serves}`;
    });

    document.getElementById('spice_level').addEventListener('change', function() {
        const level = parseInt(this.value);
        const spiceIndicator = document.getElementById('previewSpiceLevel');
        
        if (this.value === '') {
            spiceIndicator.style.display = 'none';
        } else {
            spiceIndicator.style.display = 'flex';
            const levels = spiceIndicator.querySelectorAll('.spice-level');
            levels.forEach((levelEl, index) => {
                levelEl.classList.toggle('active', index <= level);
            });
        }
    });

    // Image upload and preview
    document.getElementById('images').addEventListener('change', function() {
        const files = this.files;
        const previewContainer = document.getElementById('imagePreview');
        const previewImage = document.getElementById('previewImage');
        
        if (files.length > 5) {
            alert('You can only upload up to 5 images');
            this.value = '';
            return;
        }
        
        // Clear previous previews
        previewContainer.innerHTML = '';
        
        if (files.length > 0) {
            // Show first image in main preview
            const firstFile = files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            };
            reader.readAsDataURL(firstFile);
            
            // Show all images in grid
            Array.from(files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imageDiv = document.createElement('div');
                    imageDiv.className = 'current-image';
                    imageDiv.innerHTML = `<img src="${e.target.result}" alt="Preview ${index + 1}">`;
                    previewContainer.appendChild(imageDiv);
                };
                reader.readAsDataURL(file);
            });
        } else {
            // Reset to placeholder if no files
            @if(!$menu || !$menu->images || count($menu->images) === 0)
                previewImage.innerHTML = `
                    <div class="preview-placeholder">
                        <i class="fas fa-utensils fa-2x"></i>
                        <p class="mt-2 mb-0">Image Preview</p>
                    </div>
                `;
            @endif
        }
    });

    // Auto-enable featured until when featured is checked
    document.getElementById('is_featured').addEventListener('change', function() {
        const featuredUntil = document.getElementById('featured_until');
        if (this.checked && !featuredUntil.value) {
            const date = new Date();
            date.setDate(date.getDate() + 30);
            featuredUntil.value = date.toISOString().split('T')[0];
        }
    });

    // Form submission with Laravel validation
    document.getElementById('menuForm').addEventListener('submit', function(e) {
        // Let Laravel handle validation, but do basic client-side checks
        const name = document.getElementById('name').value.trim();
        const price = document.getElementById('price').value;
        const description = document.getElementById('description').value.trim();
        const category = document.getElementById('category').value;

        if (!name || !price || !description || !category) {
            alert('Please fill in all required fields.');
            e.preventDefault();
            return false;
        }

        if (parseFloat(price) <= 0) {
            alert('Price must be greater than 0.');
            e.preventDefault();
            return false;
        }

        const discountedPrice = parseFloat(document.getElementById('discounted_price').value);
        if (discountedPrice && discountedPrice >= parseFloat(price)) {
            alert('Sale price must be less than regular price.');
            e.preventDefault();
            return false;
        }

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const isUpdate = {{ $menu ? 'true' : 'false' }};
        submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${isUpdate ? 'Updating...' : 'Creating...'}`;
        submitBtn.disabled = true;
    });

    // Initialize preview with existing data
    document.addEventListener('DOMContentLoaded', function() {
        // Trigger events to populate preview
        document.getElementById('serves_count').dispatchEvent(new Event('input'));
        document.getElementById('description').dispatchEvent(new Event('input'));
        
        @if($menu)
            @if($menu->name)
                document.getElementById('name').dispatchEvent(new Event('input'));
            @endif
            @if($menu->price)
                document.getElementById('price').dispatchEvent(new Event('input'));
            @endif
            @if($menu->category)
                document.getElementById('category').dispatchEvent(new Event('change'));
            @endif
            @if($menu->preparation_time_minutes)
                document.getElementById('preparation_time_minutes').dispatchEvent(new Event('input'));
            @endif
            @if($menu->spice_level !== null)
                document.getElementById('spice_level').dispatchEvent(new Event('change'));
            @endif
        @endif
    });
</script>