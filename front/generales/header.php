<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Vizone Web | Resultados Tangibles' ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- AOS (Animate On Scroll) CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Custom styles -->
    <style>
        /* Tipografía Sans-serif limpia estilo Apple */
        :root {
            --bs-font-sans-serif: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            --vizone-bg: #fafafa;
            --vizone-dark: #0a0a0a;
            /* Aún más oscuro para el theme premium */
            --vizone-accent: #00d2ff;
            /* Azul eléctrico / cyber para contraste */
            --vizone-accent-hover: #00b8e6;
        }

        body {
            background-color: var(--vizone-bg);
            color: var(--vizone-dark);
            font-family: var(--bs-font-sans-serif);
            -webkit-font-smoothing: antialiased;
        }

        /* Navbar para el tema oscuro (Hero) */
        .navbar-dark-custom {
            background-color: rgba(10, 10, 10, 0.85);
            /* Coincide con el fondo oscuro */
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .navbar-dark-custom .navbar-brand {
            color: #ffffff;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .navbar-dark-custom .navbar-brand span {
            color: rgba(255, 255, 255, 0.5);
        }

        .navbar-dark-custom .nav-link {
            font-weight: 500;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7) !important;
            transition: color 0.3s ease;
        }

        .navbar-dark-custom .nav-link:hover {
            color: #ffffff !important;
        }

        .navbar-dark-custom .navbar-toggler-icon {
            filter: invert(1);
        }

        /* Botón Primario Custom (Acento) */
        .btn-custom-primary {
            border-radius: 50px;
            padding: 0.8rem 2rem;
            font-weight: 600;
            font-size: 0.95rem;
            background-color: var(--vizone-accent);
            color: var(--vizone-dark);
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 210, 255, 0.3);
        }

        .btn-custom-primary:hover {
            background-color: var(--vizone-accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 210, 255, 0.4);
            color: var(--vizone-dark);
        }

        /* Botón Secundario Oscuro (Para el Hero) */
        .btn-outline-custom {
            border-radius: 50px;
            padding: 0.8rem 2rem;
            font-weight: 500;
            font-size: 0.95rem;
            background-color: rgba(255, 255, 255, 0.05);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .btn-outline-custom:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-color: rgba(255, 255, 255, 0.4);
        }

        /* Hero Glow Effect */
        .hero-glow-bg {
            background-color: var(--vizone-dark);
            position: relative;
            overflow: hidden;
        }

        .hero-glow-bg::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80vw;
            height: 80vw;
            max-width: 800px;
            max-height: 800px;
            background: radial-gradient(circle, rgba(0, 210, 255, 0.15) 0%, rgba(10, 10, 10, 0) 70%);
            pointer-events: none;
            z-index: 0;
        }

        /* Servicio Card Hover Effect */
        .service-card {
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08) !important;
            border-color: rgba(0, 210, 255, 0.2);
        }

        .service-card.highlight {
            border: 1px solid rgba(0, 210, 255, 0.3);
            background: linear-gradient(to bottom, #ffffff, #fdfdfd);
        }

        .service-card.highlight:hover {
            border-color: var(--vizone-accent);
        }

        /* Botón Flotante de WhatsApp */
        .whatsapp-float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 30px;
            right: 30px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 0px 4px 15px rgba(37, 211, 102, 0.4);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none !important;
            transition: all 0.3s ease;
            animation: pulse-whatsapp 2s infinite;
        }

        .whatsapp-float:hover {
            color: white;
            transform: scale(1.1);
            background-color: #20b858;
            box-shadow: 0px 6px 20px rgba(37, 211, 102, 0.6);
            animation: none;
            /* Se detiene al hacer hover */
        }

        .whatsapp-float i {
            margin-top: 2px;
        }

        @keyframes pulse-whatsapp {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 15px rgba(37, 211, 102, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
            }
        }

        /* Ocultar en móviles muy pequeños para no estorbar (opcional, ajustado a <=350px) */
        @media screen and (max-width: 350px) {
            .whatsapp-float {
                width: 50px;
                height: 50px;
                bottom: 15px;
                right: 15px;
                font-size: 25px;
            }
        }

        /* Espaciado General */
        section {
            padding: 6rem 0;
        }
    </style>
</head>

<body>

    <!-- Header / Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top navbar-dark-custom py-3">
        <div class="container">
            <a class="navbar-brand" href="/vizone/">
                Vizone<span class="fw-normal">Web</span>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-lg-center gap-3">
                    <li class="nav-item">
                        <a class="nav-link" href="#enfoque">Enfoque</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#servicios">Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold d-flex align-items-center gap-1" href="/vizone/login"
                            style="color: var(--vizone-accent) !important;">
                            <i class="bi bi-person-fill"></i> Portal Vizone
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2 mt-3 mt-lg-0">
                        <!-- En el navbar usamos un botón acorde al header oscuro -->
                        <a class="btn btn-custom-primary btn-sm w-100 px-4 py-2" style="font-size: 0.85rem;"
                            href="#auditoria">Empezar ahora</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Inicia Contenido Principal -->
    <main>