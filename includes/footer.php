</main>
<script>
    // Toggle Menú Móvil
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('nav-menu');
    
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
        
        // Dropdowns en móvil
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function(e) {
                if (window.innerWidth <= 768 && this.querySelector('.dropdown-menu')) {
                    e.preventDefault();
                    this.classList.toggle('dropdown-active');
                }
            });
        });
    }
</script>
</body>
</html>