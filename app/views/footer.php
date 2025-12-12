    </div> <!-- Close the container div -->
    
    <!-- Add Bootstrap JS Bundle (WITH Popper for modals) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Optional: Simple footer -->
    <footer style="background: #5d23e6; color: white; padding: 20px; margin-top: 40px;">
        <div style="max-width: 1200px; margin: 0 auto; text-align: center;">
            <p>&copy; <?php echo date('Y'); ?> RafiaShop. Custom fashion and home products from Niger.</p>
            <small>All rights reserved.</small>
        </div>
    </footer>
    
    <!-- Auto-dismiss alerts after 5 seconds -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-dismiss Bootstrap alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Enable Bootstrap tooltips (if you add data-bs-toggle="tooltip" to elements)
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(function(tooltip) {
            new bootstrap.Tooltip(tooltip);
        });
    });
    </script>
</body>
</html>