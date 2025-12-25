<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image Preview Logic
        const imageInput = document.querySelector('input[name="images[]"]');
        if(imageInput) {
            imageInput.addEventListener('change', function(e) {
                // You can implement preview logic here if needed
                if(this.files.length > 5) {
                    alert("You can only upload a maximum of 5 images.");
                    this.value = ""; // Clear input
                }
            });
        }
    });
    console.log('Menu form scripts loaded.');
</script>