<style>
/* =========================================================
   ESTÉTICA PREMIUM MINIMALISTA - PORTAFOLIO VIZONE
   ========================================================= */
body {
    background-color: var(--vizone-bg) !important;
}

.portfolio-header {
    padding: 12rem 0 6rem 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.portfolio-header h1 {
    font-size: clamp(3rem, 6vw, 4.5rem);
    font-weight: 300;
    letter-spacing: -1.5px;
    margin-bottom: 20px;
    color: #fff;
    line-height: 1.1;
}

.portfolio-header p {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.5);
    font-weight: 300;
    max-width: 600px;
    margin: 0 auto;
    letter-spacing: 0.5px;
}

/* Espacio en blanco y flow */
.portfolio-grid {
    padding: 2rem 0 10rem 0;
    display: flex;
    flex-direction: column;
    gap: 8rem; /* Gran uso de espacio en blanco literal */
    max-width: 1100px;
    margin: 0 auto;
}

/* Sistema de Cards "Limpias" */
.project-card {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 4rem;
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 0.8s ease forwards;
}

.project-card:nth-child(even) {
    flex-direction: row-reverse;
}

.project-info {
    flex: 1;
    min-width: 300px;
}

.project-info h2 {
    font-size: 2.2rem;
    font-weight: 400;
    letter-spacing: -0.5px;
    margin-bottom: 15px;
    color: #fff;
}

.project-info p {
    color: rgba(255, 255, 255, 0.6);
    font-size: 1rem;
    line-height: 1.7;
    font-weight: 300;
    margin-bottom: 30px;
}

.visit-link {
    display: inline-flex;
    align-items: center;
    font-size: 0.9rem;
    color: var(--vizone-accent);
    text-decoration: none;
    font-weight: 500;
    letter-spacing: 1px;
    text-transform: uppercase;
    transition: all 0.3s ease;
}

.visit-link i {
    margin-left: 8px;
    transition: transform 0.3s ease;
}

.visit-link:hover i {
    transform: translateX(5px);
}

/* =========================================================
   MOCKUP CSS (Efecto MacBook) - Cero imágenes extra pesadas
   ========================================================= */
.project-visual {
    flex: 1.2;
    min-width: 300px;
    perspective: 1000px;
}

.macbook-mockup {
    background: #111; /* Aluminio Oscuro */
    border-radius: 12px 12px 0 0;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0,0,0,0.5);
    border: 1px solid rgba(255,255,255,0.08);
    position: relative;
    transition: transform 0.5s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.5s ease;
}

.macbook-mockup:hover {
    transform: scale(1.02) translateY(-5px);
    box-shadow: 0 30px 60px rgba(0, 210, 255, 0.1);
}

.macbook-topbar {
    height: 24px;
    background: rgba(255, 255, 255, 0.03);
    display: flex;
    align-items: center;
    padding: 0 12px;
    gap: 6px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.03);
}

.dot {
    width: 10px; height: 10px; border-radius: 50%;
}
.dot.red { background: #ff5f56; }
.dot.yellow { background: #ffbd2e; }
.dot.green { background: #27c93f; }

.macbook-screen {
    position: relative;
    width: 100%;
    padding-top: 60%; /* Ratio aproximado de pantalla */
    background: #000;
    overflow: hidden;
}

.macbook-screen img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: top;
    transition: transform 0.5s ease;
}

.macbook-mockup:hover .macbook-screen img {
    transform: scale(1.03);
}

.macbook-body {
    height: 8px;
    background: #222;
    border-radius: 0 0 12px 12px;
    border-top: 1px solid rgba(255,255,255,0.05);
    position: relative;
}
.macbook-body::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 20%;
    height: 4px;
    background: #111;
    border-radius: 0 0 4px 4px;
}

/* =========================================================
   ACADEMY BADGE
   ========================================================= */
.academy-badge {
    display: inline-flex;
    align-items: center;
    background: linear-gradient(90deg, rgba(162, 0, 255, 0.1) 0%, rgba(0, 210, 255, 0.1) 100%);
    border: 1px solid rgba(0, 210, 255, 0.3);
    color: #fff;
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 500;
    margin-bottom: 15px;
    letter-spacing: 0.5px;
}
.academy-badge i {
    color: var(--vizone-accent);
    margin-right: 6px;
    font-size: 0.85rem;
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Retraso progresivo en la animación si hubiera múltiples estáticos (Opcional) */
<?php if(isset($proyectos) && is_array($proyectos)): ?>
<?php foreach($proyectos as $index => $p): ?>
.project-card:nth-child(<?= $index + 1 ?>) {
    animation-delay: <?= $index * 0.15 ?>s;
}
<?php endforeach; ?>
<?php endif; ?>

@media (max-width: 768px) {
    .project-card, .project-card:nth-child(even) {
        flex-direction: column-reverse;
        gap: 2.5rem;
    }
    .portfolio-grid {
        gap: 6rem;
    }
}
</style>

<div class="container-fluid px-0">
    <!-- HERO DE PORTAFOLIO -->
    <header class="portfolio-header">
        <h1>Lo que Construimos.</h1>
        <p>Interfaces cristalinas, ecosistemas automatizados y portafolios resilientes de última generación.</p>
    </header>

    <!-- GRID DE PROYECTOS -->
    <section class="container px-4">
        <div class="portfolio-grid">
            
            <?php if (isset($proyectos) && !empty($proyectos)): ?>
                <?php foreach ($proyectos as $proyecto): 
                    // Truco Visual: Si la descripción contiene la etiqueta [academy], la mostramos como Badge y la borramos del texto
                    $showsAcademy = false;
                    $descLimpia = $proyecto['descripcion'];
                    if (strpos(strtolower($descLimpia), '[academy]') !== false) {
                        $showsAcademy = true;
                        $descLimpia = str_ireplace('[academy]', '', $descLimpia);
                    }
                ?>
                <article class="project-card">
                    <div class="project-info">
                        <?php if($showsAcademy): ?>
                            <div class="academy-badge">
                                <i class="bi bi-stars"></i> Caso de estudio en Academy
                            </div>
                        <?php endif; ?>
                        
                        <h2><?= htmlspecialchars($proyecto['titulo']) ?></h2>
                        <p><?= nl2br(htmlspecialchars(trim($descLimpia))) ?></p>
                        
                        <?php if(!empty($proyecto['link_proyecto'])): ?>
                            <a href="<?= htmlspecialchars($proyecto['link_proyecto']) ?>" target="_blank" rel="noopener noreferrer" class="visit-link">
                                Ver Proyecto en Vivo <i class="bi bi-arrow-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="project-visual">
                        <!-- MOCKUP RE-UTILIZABLE CSS -->
                        <div class="macbook-mockup">
                            <div class="macbook-topbar">
                                <span class="dot red"></span>
                                <span class="dot yellow"></span>
                                <span class="dot green"></span>
                            </div>
                            <div class="macbook-screen">
                                <?php if(!empty($proyecto['imagen_url'])): ?>
                                    <!-- Screenshot Dinámico Cacheado -->
                                    <img src="<?= htmlspecialchars($proyecto['imagen_url']) ?>" alt="Screenshot de <?= htmlspecialchars($proyecto['titulo']) ?>">
                                <?php elseif(!empty($proyecto['link_proyecto'])): ?>
                                    <!-- Iframe embebido escalado para que actúe y parezca un screenshot integrado interactivo (Zoom Out) -->
                                    <iframe src="<?= htmlspecialchars($proyecto['link_proyecto']) ?>" 
                                            style="position:absolute; top:0; left:0; width:400%; height:400%; transform: scale(0.25); transform-origin: top left; border:none; z-index:1;" 
                                            loading="lazy" 
                                            title="Vista en vivo del proyecto"></iframe>
                                    <!-- Overlap invisible para permitir scrolling sin quedar atrapado, pero clics pasan al iframe -->
                                    <div style="position: absolute; top:0; left:0; width:100%; height:100%; z-index:2; pointer-events: none;"></div>
                                <?php else: ?>
                                    <!-- Fallback Minimalista -->
                                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#000; position:absolute; top:0; left:0;">
                                        <i class="bi bi-infinity" style="font-size:3rem; color:rgba(255,255,255,0.1);"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="macbook-body"></div>
                    </div>
                </article>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- EMPTY STATE ELEGANTE -->
                <div class="text-center" style="opacity:0.5; padding: 4rem 0;">
                    <i class="bi bi-grid font-monospace text-muted" style="font-size:3rem;"></i>
                    <h3 class="mt-4 fw-light text-white">Refinanzando Arquitecturas...</h3>
                    <p class="text-white-50 font-monospace small">Pronto publicaremos nuestros últimos desarrollos.</p>
                </div>
            <?php endif; ?>

        </div>
    </section>
</div>
