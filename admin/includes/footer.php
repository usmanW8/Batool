        </main>
    </div>
    
    <!-- Image Crop Modal -->
    <?php include __DIR__ . '/crop-modal.php'; ?>
    
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Image preview functionality
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).src = e.target.result;
                    document.getElementById(previewId).style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
