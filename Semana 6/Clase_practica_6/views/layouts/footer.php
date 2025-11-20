</main>
    
    <footer class="footer">
        <p>&copy; 2024 Hotel Luxury - Sistema de Reservas. Todos los derechos reservados.</p>
    </footer>
    
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html>