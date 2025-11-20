</main>
    
    <footer style="text-align: center; padding: 1rem; background-color: #2c3e50; color: white; margin-top: 2rem; position: fixed; width: 100%; bottom: 0;">
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