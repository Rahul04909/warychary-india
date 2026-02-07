        </div> <!-- End admin-content -->
        
        <footer class="admin-footer">
            <div class="footer-left">
                © <?php echo date('Y'); ?> WaryChary. All rights reserved.
            </div>
            <div class="footer-right">
                Senior Partner Portal
            </div>
        </footer>

    </main> <!-- End admin-main -->
</div> <!-- End admin-wrapper -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS for Sidebar Toggle -->
<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
        document.querySelector('.admin-main').classList.toggle('active');
    }
    
    // Auto-close sidebar on mobile when clicking outside
    document.addEventListener('click', function(event) {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.querySelector('.sidebar-toggle');
        
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target) && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        }
    });
</script>
</body>
</html>
