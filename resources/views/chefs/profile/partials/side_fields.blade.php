<div class="card border-0 shadow-sm mb-4">
    <div class="card-body text-center p-4">
        <div class="position-relative d-inline-block mb-3">
            <img src="{{ $chef->avatar ? asset('storage/' . $chef->avatar) : 'https://ui-avatars.com/api/?name='.$chef->first_name.'&background=random' }}" 
                 class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;" id="avatarPreview">
            <label for="avatarUpload" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 cursor-pointer shadow" style="width: 40px; height: 40px;">
                <i class="fas fa-camera"></i>
            </label>
            <input type="file" id="avatarUpload" name="avatar" class="d-none" accept="image/*" onchange="previewImage(this)">
        </div>
        
        <h5 class="fw-bold mb-1">{{ $chef->first_name }} {{ $chef->last_name }}</h5>
        <p class="text-muted small mb-3">Chef ID: #{{ $chef->id }}</p>

        <div class="form-check form-switch d-flex justify-content-center align-items-center gap-2 border p-2 rounded bg-light">
            <input class="form-check-input" type="checkbox" id="is_online" name="is_online" value="1" 
                   {{ old('is_online', $profile->is_online) ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="is_online">Accepting Orders</label>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>