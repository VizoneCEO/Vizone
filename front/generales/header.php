<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Vizone Web | Resultados Tangibles' ?></title>

    <!-- Bootstrap 5 CSS (kept for grid system) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- AOS (Animate On Scroll) CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Custom styles -->
    <style>
        :root {
            --bs-font-sans-serif: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            --vizone-bg: #050505;
            --vizone-dark: #0a0a0a;
            --vizone-accent: #00d2ff;
        }

        body {
            background-color: var(--vizone-bg);
            color: #ffffff;
            font-family: var(--bs-font-sans-serif);
            -webkit-font-smoothing: antialiased;
        }

        /* Nav Glassmorphism Premium (Fijo en el Footer) */
        .glass-nav {
            position: fixed;
            bottom: 0;
            top: auto;
            left: 0;
            width: 100%;
            z-index: 1000;
            background: rgba(5, 5, 5, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-top: 1px solid rgba(255, 255, 255, 0.05); /* Borde superior en lugar de inferior */
            transition: all 0.3s ease;
        }

        .glass-nav-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .glass-nav .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            letter-spacing: -0.5px;
        }
        .glass-nav .logo span {
            color: rgba(255, 255, 255, 0.5);
            font-weight: 400;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2.5rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-link-item {
            position: relative;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 400;
            transition: color 0.3s ease;
        }

        .nav-link-item:hover {
            color: #fff;
        }

        /* Hover animation Stripe/Apple style (Desktop only effectively) */
        .nav-link-item::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 1px;
            background-color: #fff;
            transform: scaleX(0);
            transform-origin: center;
            transition: transform 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .nav-link-item:hover::after {
            transform: scaleX(1);
        }
        
        .portal-link {
            color: var(--vizone-accent);
            font-weight: 500;
        }
        .portal-link::after {
            background-color: var(--vizone-accent);
        }

        /* CTA Botón Nav (Desktop) */
        .nav-cta {
            background: rgba(0, 210, 255, 0.1);
            border: 1px solid rgba(0, 210, 255, 0.3);
            color: #fff;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 210, 255, 0);
        }

        .nav-cta:hover {
            background: rgba(0, 210, 255, 0.2);
            border-color: var(--vizone-accent);
            color: #fff;
            box-shadow: 0 4px 15px rgba(0, 210, 255, 0.2);
            transform: translateY(-1px);
        }

        /* Helpers for Mobile/Desktop Elements */
        @media (min-width: 769px) {
            .mobile-icon { display: none !important; }
            .mobile-text { display: none !important; }
        }

        /* =======================================
           MOBILE BOTTOM FOOTER NAVIGATION
           ======================================= */
        @media (max-width: 768px) {
            .desktop-icon { display: none !important; }
            .desktop-text { display: none !important; }
            
            .text-accent { color: var(--vizone-accent) !important; }
            
            .glass-nav {
                /* El logo sube de regreso al top en móvil para mantenerlo visible */
                bottom: auto;
                top: 0;
                background: transparent;
                backdrop-filter: none;
                -webkit-backdrop-filter: none;
                border-top: none;
                pointer-events: none;
            }
            .glass-nav .logo {
                pointer-events: auto;
            }
            .glass-nav-container {
                justify-content: center;
                padding-top: 20px;
            }

            .nav-links {
                position: fixed;
                bottom: 0; /* Fijo al ras del footer */
                left: 0;
                transform: none;
                width: 100%;
                max-width: none;
                background: rgba(8, 8, 8, 0.9); /* Más sólido para mejor lectura en footer */
                backdrop-filter: blur(25px);
                -webkit-backdrop-filter: blur(25px);
                border-radius: 0; /* Pierde la cápsula, se vuelve barra total */
                border-top: 1px solid rgba(255, 255, 255, 0.08); /* Línea divisoria */
                display: flex;
                flex-direction: row;
                justify-content: space-evenly;
                align-items: center;
                /* Soporte para el notch/safe area en iOS */
                padding: 10px 0 calc(10px + env(safe-area-inset-bottom)); 
                box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.5); /* Sombra hacia arriba */
                z-index: 1001;
                pointer-events: auto;
            }
            .nav-links li {
                flex: 1;
                display: flex;
                justify-content: center;
            }
            .nav-link-item, .nav-cta {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                font-size: 0.65rem !important;
                padding: 0 !important;
                background: transparent !important;
                border: none !important;
                box-shadow: none !important;
                line-height: 1.2;
                letter-spacing: 0.3px;
                font-weight: 300 !important;
                opacity: 0.6;
                transition: opacity 0.2s ease;
            }
            .nav-cta {
                opacity: 0.9;
                font-weight: 500 !important;
            }
            .nav-link-item:hover, .nav-link-item:active, .nav-cta:hover, .nav-cta:active {
                opacity: 1;
            }
            .mobile-icon {
                font-size: 1.3rem;
                margin-bottom: 4px;
                display: block !important;
                transition: transform 0.2s ease;
            }
            .nav-link-item:active .mobile-icon, .nav-cta:active .mobile-icon {
                transform: scale(0.9); /* Micro-interacción touch */
            }
            .nav-link-item::after { display: none !important; }
        }

        /* Ajuste de Whatsapp para el Footer Fijo */
        .whatsapp-float {
            position: fixed;
            width: 50px;
            height: 50px;
            bottom: 90px; /* Separado del desktop footer nav */
            right: 30px;
            background-color: rgba(37, 211, 102, 0.8);
            backdrop-filter: blur(5px);
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 24px;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none !important;
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .whatsapp-float:hover {
            color: white;
            background-color: rgba(37, 211, 102, 1);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(37, 211, 102, 0.3);
        }

        /* En móvil alzamos whastapp un poco más para librar el nuevo footer fijo */
        @media (max-width: 768px) {
            .whatsapp-float {
                bottom: calc(90px + env(safe-area-inset-bottom));
                right: 20px;
                width: 45px;
                height: 45px;
                font-size: 20px;
            }
        }

        /* General sections spacing */
        section {
            padding: 6rem 0;
        }
    </style>
</head>

<body>

    <!-- Header Glassmorphism Premium -->
    <nav class="glass-nav">
        <div class="glass-nav-container">
            <a class="logo" href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>">
                Vizone<span>Web</span>
            </a>

            <!-- Dock Navigation -->
            <ul class="nav-links" id="nav-links">
                <li>
                    <a class="nav-link-item" href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>#esencia">
                        <i class="bi bi-layers mobile-icon"></i>
                        <span class="nav-item-text desktop-text">Nuestra Esencia</span>
                        <span class="nav-item-text mobile-text">Esencia</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link-item" href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>#academy">
                        <i class="bi bi-journal-code mobile-icon"></i>
                        <span class="nav-item-text desktop-text">Academy</span>
                        <span class="nav-item-text mobile-text">Academy</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link-item" href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>proyectos">
                        <i class="bi bi-briefcase mobile-icon"></i>
                        <span class="nav-item-text desktop-text">Proyectos</span>
                        <span class="nav-item-text mobile-text">Proyectos</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link-item portal-link" href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>login">
                        <i class="bi bi-person-circle mobile-icon"></i>
                        <i class="bi bi-person-fill desktop-icon"></i>
                        <span class="nav-item-text">Portal</span>
                    </a>
                </li>
                <li>
                    <a class="nav-cta" href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>#auditoria">
                        <i class="bi bi-calendar-check-fill mobile-icon text-accent" style="filter: drop-shadow(0 0 5px rgba(0,210,255,0.4));"></i>
                        <span class="nav-item-text desktop-text">Empezar ahora</span>
                        <span class="nav-item-text mobile-text text-accent">Agendar</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Botón Whatsapp Opcional Reducido -->
    <a href="https://wa.me/525598793460?text=Hola,%20Vizoneweb." class="whatsapp-float" target="_blank" rel="noopener noreferrer">
        <i class="bi bi-whatsapp"></i>
    </a>

    <!-- Inicia Contenido Principal -->
    <main>