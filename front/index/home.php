<!-- CSS y Tipografías Modernas -->
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap');

:root {
    --vizone-bg: #050505;
    --vizone-surface: #0a0a0a;
    --vizone-accent: #00d2ff;
}

html, body {
    background-color: var(--vizone-bg) !important;
    color: #ffffff;
    font-family: 'Inter', sans-serif !important;
    scroll-behavior: smooth;
}

/* Utils y Animaciones */
.fade-in-up {
    opacity: 0;
    transform: translateY(40px);
    transition: opacity 1s cubic-bezier(0.16, 1, 0.3, 1), transform 1s cubic-bezier(0.16, 1, 0.3, 1);
}
.fade-in-up.in-view {
    opacity: 1;
    transform: translateY(0);
}

/* Botón Hero */
.cta-magnetic-btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    padding: 18px 45px;
    background: rgba(0, 210, 255, 0.05);
    border: 1px solid rgba(0, 210, 255, 0.3);
    color: #fff;
    font-family: 'Inter', sans-serif;
    font-size: 1rem;
    font-weight: 500;
    letter-spacing: 1px;
    text-transform: uppercase;
    text-decoration: none;
    border-radius: 50px;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    backdrop-filter: blur(5px);
    overflow: hidden;
}
.cta-magnetic-btn span {
    position: relative;
    z-index: 2;
}
.cta-magnetic-btn .hover-glow {
    position: absolute;
    width: 100%;
    height: 100%;
    background: rgba(0, 210, 255, 0.2);
    left: 0;
    top: 0;
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: 1;
}
.cta-magnetic-btn:hover {
    color: #fff;
    border-color: var(--vizone-accent);
    box-shadow: 0 0 30px rgba(0, 210, 255, 0.2);
    transform: translateY(-2px);
}
.cta-magnetic-btn:hover .hover-glow {
    opacity: 1;
}

/* Tarjetas de Esencia */
.service-card {
    background: rgba(255,255,255,0.015);
    border: 1px solid rgba(255,255,255,0.05);
    padding: 45px 35px;
    border-radius: 16px;
    transition: transform 0.4s ease, border-color 0.4s ease, background 0.4s ease;
    height: 100%;
}
.service-card:hover {
    transform: translateY(-8px);
    border-color: rgba(0, 210, 255, 0.3);
    background: rgba(255,255,255,0.03);
}

/* Separadores limpios */
.clean-separator {
    height: 1px;
    width: 60px;
    background: var(--vizone-accent);
    margin: 40px auto;
    opacity: 0.5;
}
</style>

<!-- ==============================================
     HERO SECTION (CANVAS BACKGROUND)
=============================================== -->
<!-- Canvas Global -->
<canvas id="network-canvas" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 0; pointer-events: none;"></canvas>

<section id="hero" style="position: relative; width: 100%; height: 100vh; min-height: 800px; display: flex; align-items: center; justify-content: center; overflow: hidden; background-color: transparent;">
    
    <div class="hero-content" style="position: relative; z-index: 2; text-align: center; padding: 0 20px; width: 100%;">
        <div class="fade-in-up">
            <span style="display: inline-block; border: 1px solid rgba(0, 210, 255, 0.3); color: var(--vizone-accent); padding: 8px 16px; border-radius: 50px; font-size: 0.8rem; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 30px;">
                Evolución Digital
            </span>
        </div>
        
        <h1 class="fade-in-up" style="color: #ffffff; font-size: clamp(2.5rem, 6vw, 4.5rem); font-weight: 300; letter-spacing: -1.5px; margin-bottom: 25px; line-height: 1.1; max-width: 1000px; margin-left: auto; margin-right: auto;">
            <?= isset($heroTitle) ? htmlspecialchars($heroTitle) : 'Transformamos el trabajo manual en eficiencia digital.' ?>
        </h1>
        
        <p class="fade-in-up" style="color: rgba(255, 255, 255, 0.6); font-size: clamp(1rem, 2vw, 1.25rem); font-weight: 300; max-width: 650px; margin: 0 auto 50px auto; line-height: 1.7;">
            <?= isset($heroSubtitle) ? htmlspecialchars($heroSubtitle) : 'Desarrollo de software a medida, automatización inteligente e infraestructura IT para escalar operaciones.' ?>
        </p>
        
        <div class="fade-in-up">
            <a href="#auditoria" class="cta-magnetic-btn">
                <span>Agendar Auditoría VIP</span>
                <div class="hover-glow"></div>
            </a>
        </div>
    </div>
</section>

<!-- ==============================================
     NUESTRA ESENCIA (SERVICIOS CORE)
=============================================== -->
<section id="esencia" style="background-color: rgba(10, 10, 10, 0.4); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); padding: 8rem 0; border-top: 1px solid rgba(255,255,255,0.02); position: relative; z-index: 1;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div class="text-center mb-5 fade-in-up">
            <h2 style="font-size: clamp(2.2rem, 4vw, 3.5rem); font-weight: 300; letter-spacing: -1px; margin-bottom: 1rem;">Nuestra Esencia.</h2>
            <p style="color: rgba(255,255,255,0.5); font-weight: 300; max-width: 500px; margin: 0 auto;">No somos una agencia más. Somos tu equipo técnico interno, diseñado para escalar sistemas y resolver problemas complejos.</p>
            <div class="clean-separator"></div>
        </div>
        
        <div class="row g-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px;">
            <?php if (isset($servicios) && !empty($servicios)): ?>
                <?php foreach ($servicios as $servicio): ?>
                    <div class="fade-in-up">
                        <div class="service-card d-flex flex-column">
                            <i class="bi <?= htmlspecialchars($servicio['icono'] ?? 'bi-cpu') ?>" style="font-size: 2.2rem; color: var(--vizone-accent); margin-bottom: 25px;"></i>
                            <h3 style="font-size: 1.4rem; font-weight: 400; margin-bottom: 15px; letter-spacing: -0.5px;"><?= htmlspecialchars($servicio['titulo']) ?></h3>
                            <p style="color: rgba(255,255,255,0.6); font-size: 0.95rem; line-height: 1.6; font-weight: 300; margin-bottom: 20px; flex-grow: 1;">
                                <?= htmlspecialchars($servicio['descripcion']) ?>
                            </p>
                            <!-- Entregables / Tags Minimalistas -->
                            <?php if(isset($servicio['entregables']) && is_array($servicio['entregables'])): ?>
                            <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: auto;">
                                <?php foreach($servicio['entregables'] as $item): ?>
                                    <span style="font-size: 0.75rem; color: rgba(255,255,255,0.4); border: 1px solid rgba(255,255,255,0.1); padding: 4px 10px; border-radius: 4px;">
                                        <?= htmlspecialchars($item) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback en caso de que no haya servicios -->
                <div class="fade-in-up">
                    <div class="service-card text-center">
                        <i class="bi bi-code-slash" style="font-size: 2.5rem; color: var(--vizone-accent); margin-bottom: 20px;"></i>
                        <h3 style="font-size: 1.3rem; margin-bottom: 10px;">Desarrollo Web</h3>
                        <p style="color: rgba(255,255,255,0.6); font-size: 0.95rem; font-weight: 300;">Plataformas escalables creadas desde cero para cumplir las necesidades operativas.</p>
                    </div>
                </div>
                <div class="fade-in-up">
                    <div class="service-card text-center">
                        <i class="bi bi-cpu" style="font-size: 2.5rem; color: var(--vizone-accent); margin-bottom: 20px;"></i>
                        <h3 style="font-size: 1.3rem; margin-bottom: 10px;">Automatización</h3>
                        <p style="color: rgba(255,255,255,0.6); font-size: 0.95rem; font-weight: 300;">Scripts e integración de APIs para conectar sistemas y eliminar trabajo burocrático.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ==============================================
     VIZONE ACADEMY
=============================================== -->
<section id="academy" style="position: relative; background: transparent; padding: 10rem 0; overflow: hidden; z-index: 1;">
    <!-- Resplandor tecnológico sutil -->
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 800px; height: 800px; background: rgba(0, 210, 255, 0.02); border-radius: 50%; filter: blur(100px); pointer-events: none;"></div>
    
    <div class="container" style="max-width: 900px; margin: 0 auto; padding: 0 20px; position: relative; z-index: 2; text-align: center;">
        <div class="fade-in-up">
            <span style="display: inline-block; padding: 6px 20px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 50px; color: #fff; font-size: 0.8rem; letter-spacing: 2px; margin-bottom: 30px;">
                NUEVA DIVISION
            </span>
        </div>
        
        <h2 class="fade-in-up" style="font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 300; letter-spacing: -1px; margin-bottom: 30px;">
            Vizone Academy.
        </h2>
        
        <p class="fade-in-up" style="color: rgba(255, 255, 255, 0.6); font-size: clamp(1rem, 2vw, 1.15rem); font-weight: 300; line-height: 1.8; margin-bottom: 50px; max-width: 750px; margin-left: auto; margin-right: auto;">
            Vizone no solo construye tecnología corporativa, también forma a la próxima generación de desarrolladores. A través de bootcamps intensivos y masterclasses, instruimos en el dominio de herramientas de desarrollo impulsadas por Inteligencia Artificial tales como <span style="color: var(--vizone-accent); font-weight: 500;">Antigravity, Lovable y Stitch</span>.
        </p>

        <div class="fade-in-up" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px;">
            <div style="background: rgba(255,255,255,0.02); padding: 15px 30px; border-radius: 8px; border: 1px solid rgba(0,210,255,0.1); font-size: 0.9rem; font-weight: 300;">
                <i class="bi bi-robot text-info me-2"></i> Desarrollo IA Asistido
            </div>
            <div style="background: rgba(255,255,255,0.02); padding: 15px 30px; border-radius: 8px; border: 1px solid rgba(0,210,255,0.1); font-size: 0.9rem; font-weight: 300;">
                <i class="bi bi-rocket-takeoff text-info me-2"></i> Bootcamps MVP
            </div>
            <div style="background: rgba(255,255,255,0.02); padding: 15px 30px; border-radius: 8px; border: 1px solid rgba(0,210,255,0.1); font-size: 0.9rem; font-weight: 300;">
                <i class="bi bi-journal-code text-info me-2"></i> Masterclasses Tech
            </div>
        </div>
    </div>
</section>

<!-- ==============================================
     CONTACTO Y AUDITORÍA (MANTENIDO)
=============================================== -->
<section id="contacto" class="position-relative overflow-hidden" style="background-color: rgba(5,5,5,0.5); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); padding: 6rem 0; z-index: 1;">
    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-25" style="background: radial-gradient(circle at bottom right, rgba(0,210,255,0.4) 0%, transparent 60%); pointer-events: none;"></div>
    <div class="container position-relative z-1 fade-in-up">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 text-center">
                <div class="mb-4">
                    <span class="badge bg-transparent border border-light text-white px-3 py-2 fw-normal" style="letter-spacing: 1px;">¿LISTO PARA ESCALAR?</span>
                </div>
                <h2 class="display-5 fw-bold text-white mb-4" style="letter-spacing: -1px;">Hablemos de tu próximo proyecto.</h2>
                <p class="lead text-white-50 mb-5 mx-auto" style="max-width: 600px; line-height: 1.6; font-weight: 300;">
                    No esperes a que tu competencia automatice primero. Agenda una consulta sin compromiso o escríbenos
                    directo por WhatsApp para resolver tus dudas al instante.
                </p>
                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                    <a href="#auditoria" class="cta-magnetic-btn" style="border-color: rgba(255,255,255,0.2);">
                        Agendar Auditoría VIP
                    </a>
                    <a href="https://wa.me/525598793460?text=Hola,%20Vizoneweb.%20Me%20interesa%20conocer%20más%20sobre%20sus%20servicios%20de%20tecnología."
                        target="_blank" rel="noopener noreferrer"
                        class="btn px-4 py-3 d-inline-flex align-items-center justify-content-center text-white fw-medium border border-secondary"
                        style="background-color: #25D366; border-color: #25D366 !important; border-radius: 50px; transition: all 0.3s ease;">
                        <i class="bi bi-whatsapp me-2 fs-5"></i> Chat por WhatsApp
                    </a>
                </div>
                <div class="mt-5 pt-4 border-top border-secondary text-white-50 small d-flex flex-column flex-md-row justify-content-center gap-4">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-envelope"></i> contacto@vizoneweb.com
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="auditoria" class="position-relative py-6" style="background-color: transparent; border-top: 1px solid rgba(255,255,255,0.05); padding: 5rem 0; z-index: 1;">
    <div class="container position-relative z-1 fade-in-up">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card bg-transparent border-0 rounded-5 overflow-hidden p-1 shadow-lg"
                    style="background: linear-gradient(145deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0.01) 100%); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.05);">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-5">
                            <span class="badge bg-transparent border text-white px-3 py-2 fw-semibold mb-3 rounded-pill"
                                style="border-color: var(--vizone-accent) !important; color: var(--vizone-accent) !important; font-size: 0.75rem; letter-spacing: 1px;">
                                <i class="bi bi-calendar-check me-1"></i> ESPACIOS LIMITADOS
                            </span>
                            <h2 class="display-6 fw-bold text-white mb-3" style="letter-spacing: -1px; font-weight: 300;">Auditoría Estratégica</h2>
                        </div>

                        <!-- Estado de Éxito y QR -->
                        <div id="audit-success-state" class="text-center d-none transition-all">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                            <h3 class="text-white mt-3 mb-2 fw-bold">¡Auditoría Confirmada!</h3>
                            <p class="text-white-50 mb-4">Escanea o haz click para entrar a nuestra sesión.</p>
                            <div class="bg-white p-3 rounded-4 mx-auto d-inline-block shadow-lg mb-4" id="qr-container"></div>
                            <div>
                                <a id="btn-meet-link" href="#" target="_blank"
                                    class="btn rounded-pill px-4 text-white fw-bold display-inline-flex align-items-center"
                                    style="background-color: #25D366; border: none;">
                                    <i class="bi bi-whatsapp me-2"></i> Enviar a mi WhatsApp
                                </a>
                            </div>
                        </div>

                        <!-- Formulario de Agendamiento -->
                        <form id="auditForm" class="row g-4 needs-validation" novalidate>
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small text-uppercase tracking-wide fw-bold">Nombre Completo</label>
                                <input type="text" class="form-control form-control-lg bg-transparent text-white rounded-3" id="auditName" name="nombre" required style="border: 1px solid rgba(255,255,255,0.1);">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small text-uppercase tracking-wide fw-bold">Empresa</label>
                                <input type="text" class="form-control form-control-lg bg-transparent text-white rounded-3" id="auditCompany" name="empresa" required style="border: 1px solid rgba(255,255,255,0.1);">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-white-50 small text-uppercase tracking-wide fw-bold">WhatsApp</label>
                                <input type="tel" class="form-control form-control-lg bg-transparent text-white rounded-3" id="auditPhone" name="telefono" required style="border: 1px solid rgba(255,255,255,0.1);">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small text-uppercase tracking-wide fw-bold"><i class="bi bi-calendar-event me-1 text-info"></i> Fecha</label>
                                <input type="date" class="form-control form-control-lg bg-transparent text-white rounded-3" id="auditDate" name="fecha" required min="<?= date('Y-m-d') ?>" style="border: 1px solid rgba(255,255,255,0.1);">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small text-uppercase tracking-wide fw-bold"><i class="bi bi-clock me-1 text-info"></i> Hora (CDMX)</label>
                                <select class="form-select form-select-lg bg-transparent text-white rounded-3" id="auditTime" name="hora" required style="border: 1px solid rgba(255,255,255,0.1);">
                                    <option value="" class="text-dark">Selecciona un horario...</option>
                                    <option value="10:00:00" class="text-dark">10:00 AM CDMX</option>
                                    <option value="11:30:00" class="text-dark">11:30 AM CDMX</option>
                                    <option value="13:00:00" class="text-dark">01:00 PM CDMX</option>
                                    <option value="15:00:00" class="text-dark">03:00 PM CDMX</option>
                                    <option value="16:30:00" class="text-dark">04:30 PM CDMX</option>
                                    <option value="18:00:00" class="text-dark">06:00 PM CDMX</option>
                                </select>
                            </div>
                            <div class="col-12 mt-4">
                                <div id="auditAlert" class="alert d-none small" role="alert"></div>
                                <button type="submit" id="btnSubmitAudit" class="btn w-100 py-3 rounded-4 fw-bold text-dark fs-6" style="background: var(--vizone-accent); border: none;">
                                    <i class="bi bi-calendar-plus"></i> Confirmar Sesión
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==============================================
     SCRIPTS DE LOGICA Y ANIMACION
=============================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. Observador Fade In Up ---
    const observerOptions = {
        threshold: 0.15,
        rootMargin: "0px 0px -50px 0px"
    };
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Initial timeout to ensure hero animations trigger if already in view
    setTimeout(() => {
        document.querySelectorAll('.fade-in-up').forEach(el => {
            observer.observe(el);
        });
    }, 100);


    // --- 2. Lógica del Formulario Auditoría ---
    const auditForm = document.getElementById('auditForm');
    if (auditForm) {
        auditForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            if (!this.checkValidity()) {
                this.classList.add('was-validated');
                return;
            }

            const alertBox = document.getElementById('auditAlert');
            const btnSubmit = document.getElementById('btnSubmitAudit');

            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Procesando...';
            alertBox.classList.add('d-none');
            alertBox.classList.remove('alert-danger', 'alert-success');

            const formData = new FormData(this);

            try {
                // Fake API Call logic for demo, fallback to previous if path matches
                const response = await fetch('/vizone/back/api/agendar_auditoria.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.status === 'success') {
                    auditForm.classList.add('d-none');
                    const successState = document.getElementById('audit-success-state');
                    successState.classList.remove('d-none');
                    successState.classList.add('d-block');

                    const calendarUrl = data.calendar_url || '#';
                    const qrContainer = document.getElementById('qr-container');
                    qrContainer.innerHTML = `<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(calendarUrl)}&color=030303" alt="QR Code Add to Calendar" class="img-fluid rounded">`;

                    let rawPhone = document.getElementById('auditPhone').value.replace(/[^0-9]/g, '');
                    const aDate = document.getElementById('auditDate').value;
                    const aTime = document.getElementById('auditTime').options[document.getElementById('auditTime').selectedIndex].text;
                    const whatsappMsg = `¡Hola! Mi Auditoría con Vizone está confirmada para el ${aDate} a las ${aTime}.\n\nPara agendar en mi calendario:\n${calendarUrl}`;

                    document.getElementById('btn-meet-link').href = `https://api.whatsapp.com/send?phone=${rawPhone}&text=${encodeURIComponent(whatsappMsg)}`;
                } else {
                    alertBox.textContent = data.message || 'Error al procesar la solicitud.';
                    alertBox.classList.add('alert-danger', 'd-block');
                    alertBox.classList.remove('d-none');
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = `<i class="bi bi-calendar-plus"></i> Confirmar Sesión`;
                }

            } catch (error) {
                alertBox.textContent = 'Ocurrió un error inesperado al conectar con el servidor.';
                alertBox.classList.add('alert-danger', 'd-block');
                alertBox.classList.remove('d-none');
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = `<i class="bi bi-calendar-plus"></i> Confirmar Sesión`;
            }
        });
    }

    // --- 3. Lógica del Canvas Network ---
    const canvas = document.getElementById('network-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    let particlesArray = [];
    let mouse = {
        x: null,
        y: null,
        radius: 120
    };

    window.addEventListener('mousemove', function(event) {
        // En un canvas fixed, event.x/y ya encajan con el viewport (100vw/vh).
        mouse.x = event.clientX;
        mouse.y = event.clientY;
    });

    window.addEventListener('resize', function() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        init();
    });

    // Restaurar mouse out of bounds
    window.addEventListener('mouseout', function() {
        mouse.x = null;
        mouse.y = null;
    });

    class Particle {
        constructor(x, y, directionX, directionY, size, color) {
            this.x = x;
            this.y = y;
            this.directionX = directionX;
            this.directionY = directionY;
            this.size = size;
            this.color = color;
        }
        draw() {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2, false);
            ctx.fillStyle = this.color;
            ctx.fill();
        }
        update() {
            if (this.x > canvas.width || this.x < 0) {
                this.directionX = -this.directionX;
            }
            if (this.y > canvas.height || this.y < 0) {
                this.directionY = -this.directionY;
            }

            // Interact with mouse
            if(mouse.x != null && mouse.y != null) {
                let dx = mouse.x - this.x;
                let dy = mouse.y - this.y;
                let distance = Math.sqrt(dx * dx + dy * dy);
                if (distance < mouse.radius + this.size) {
                    if (mouse.x < this.x && this.x < canvas.width - this.size * 10) {
                        this.x += 1;
                    }
                    if (mouse.x > this.x && this.x > this.size * 10) {
                        this.x -= 1;
                    }
                    if (mouse.y < this.y && this.y < canvas.height - this.size * 10) {
                        this.y += 1;
                    }
                    if (mouse.y > this.y && this.y > this.size * 10) {
                        this.y -= 1;
                    }
                }
            }
            
            this.x += this.directionX;
            this.y += this.directionY;
            this.draw();
        }
    }

    function init() {
        particlesArray = [];
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        
        let density = window.innerWidth < 768 ? 20000 : 15000;
        let numberOfParticles = (canvas.height * canvas.width) / density;
        
        for (let i = 0; i < numberOfParticles; i++) {
            let size = (Math.random() * 2) + 0.5;
            let x = (Math.random() * ((canvas.width - size * 2) - (size * 2)) + size * 2);
            let y = (Math.random() * ((canvas.height - size * 2) - (size * 2)) + size * 2);
            let directionX = (Math.random() * 0.8) - 0.4;
            let directionY = (Math.random() * 0.8) - 0.4;
            // Un cian muy tenue
            let color = 'rgba(0, 210, 255, 0.6)';

            particlesArray.push(new Particle(x, y, directionX, directionY, size, color));
        }
    }

    let hue = 190; // Comenzar en cian original
    function animate() {
        requestAnimationFrame(animate);
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        hue += 0.3; // Incremento fluido
        if (hue >= 360) hue = 0;

        for (let i = 0; i < particlesArray.length; i++) {
            particlesArray[i].update();
        }
        connect(hue);
    }

    function connect(currentHue) {
        let opacityValue = 1;
        for (let a = 0; a < particlesArray.length; a++) {
            for (let b = a; b < particlesArray.length; b++) {
                let distance = ((particlesArray[a].x - particlesArray[b].x) * (particlesArray[a].x - particlesArray[b].x)) 
                             + ((particlesArray[a].y - particlesArray[b].y) * (particlesArray[a].y - particlesArray[b].y));
                
                let distThreshold = window.innerWidth < 768 ? 10000 : 15000;
                
                if (distance < distThreshold) {
                    opacityValue = 1 - (distance / distThreshold);
                    // Líneas dinámicas mutables por HSL
                    ctx.strokeStyle = `hsla(${currentHue}, 100%, 50%, ${opacityValue * 0.4})`;
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    ctx.moveTo(particlesArray[a].x, particlesArray[a].y);
                    ctx.lineTo(particlesArray[b].x, particlesArray[b].y);
                    ctx.stroke();
                }
            }
        }
    }

    init();
    animate();

});
</script>