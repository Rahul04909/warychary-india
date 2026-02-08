        </div> <!-- End Admin Content -->
        
        <footer class="admin-footer">
            <div class="footer-left">
                &copy; <?php echo date('Y'); ?> WaryChary. All rights reserved.
            </div>
            <div class="footer-right">
                User Panel
            </div>
        </footer>

    </main>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Mobile Sidebar Toggle Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('active');
            });

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(e) {
                if (sidebar.classList.contains('active') && 
                    !sidebar.contains(e.target) && 
                    e.target !== sidebarToggle &&
                    !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('active');
                }
            });
        }
    });
</script>

</body>
</html>
