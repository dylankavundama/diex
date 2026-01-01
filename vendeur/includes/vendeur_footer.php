            </main>
        </div>
    </div>
    
    <script>
        function toggleSidebar() {
            document.getElementById('adminSidebar').classList.toggle('active');
        }
        
        // Fermer la sidebar en cliquant à l'extérieur sur mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('adminSidebar');
            const toggle = document.querySelector('.mobile-menu-toggle');
            
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !toggle.contains(event.target) && sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                }
            }
        });
    </script>
</body>
</html>

