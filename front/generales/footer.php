<!-- Cierra Contenido Principal -->
</main>

    <!-- Botón Flotante de WhatsApp -->
    <a href="https://wa.me/5211234567890?text=Hola,%20Vizoneweb.%20Me%20interesa%20conocer%20más%20sobre%20sus%20servicios%20de%20tecnología." class="whatsapp-float" target="_blank" rel="noopener noreferrer">
        <i class="bi bi-whatsapp"></i>
    </a>

<!-- Footer Simple y Limpio -->
<footer class="py-5 bg-white border-top border-light">
    <div class="container text-center">
        <p class="text-muted small mb-0">
            &copy;
            <?= date("Y") ?> Vizone Web. Todos los derechos reservados.
        </p>
        <div class="mt-3">
            <a href="#" class="text-muted text-decoration-none small mx-2 hover-dark">Privacidad</a>
            <a href="#" class="text-muted text-decoration-none small mx-2 hover-dark">Términos</a>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>

<!-- AOS JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- Inicialización de Scripts -->
<script>
    // Inicializar animaciones al hacer scroll (AOS)
    document.addEventListener('DOMContentLoaded', function () {
        AOS.init({
            duration: 800,      // Duración de la animación en ms
            easing: 'ease-out-cubic', // Curvatura de aceleración suave
            once: true,         // Animar solo una vez al hacer scroll hacia abajo
            offset: 50          // Offset (en px) desde el elemento original
        });
    });
</script>
</body>

</html>