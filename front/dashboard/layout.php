<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= htmlspecialchars($pageTitle ?? 'Portal Vizone') ?>
    </title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            --bs-font-sans-serif: 'Inter', sans-serif;
            --dashboard-bg: #f5f6f8;
            --sidebar-width: 250px;
            --vizone-accent: #00d2ff;
            --vizone-sidebar-bg: #0f172a;
            /* Slate 900 */
        }

        body {
            background-color: var(--dashboard-bg);
            font-family: var(--bs-font-sans-serif);
            color: #333;
            overflow-x: hidden;
        }

        /* --- Layout Structure --- */
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
            min-height: 100vh;
        }

        /* --- Sidebar --- */
        #sidebar {
            min-width: var(--sidebar-width);
            max-width: var(--sidebar-width);
            background: var(--vizone-sidebar-bg);
            color: #fff;
            transition: all 0.3s;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 20px;
            background: rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .brand-text {
            font-weight: 800;
            letter-spacing: -0.5px;
            font-size: 1.4rem;
            color: #fff;
        }

        .brand-text span {
            color: rgba(255, 255, 255, 0.5);
            font-weight: 400;
        }

        .sidebar-menu {
            padding: 20px 0;
            flex-grow: 1;
        }

        .menu-title {
            padding: 0 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.4);
            margin-bottom: 10px;
            font-weight: 600;
        }

        .menu-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .menu-item {
            padding: 0 10px;
            margin-bottom: 5px;
        }

        .menu-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .menu-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
        }

        .menu-link i {
            margin-right: 12px;
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.5);
            transition: all 0.3s;
        }

        /* Active State */
        .menu-link.active {
            color: #fff;
            background: linear-gradient(90deg, rgba(0, 210, 255, 0.15) 0%, rgba(0, 210, 255, 0) 100%);
            border-left: 3px solid var(--vizone-accent);
        }

        .menu-link.active i {
            color: var(--vizone-accent);
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* --- Main Content Area --- */
        #content {
            width: 100%;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        /* --- Top Navbar --- */
        .top-navbar {
            background: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            height: 70px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .avatar-circle {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #00d2ff 0%, #3a7bd5 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* --- View Container --- */
        .view-container {
            padding: 2rem;
            flex-grow: 1;
        }

        /* --- Paneles / Componentes Compartidos --- */
        .panel {
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }

        .btn-custom {
            background-color: #111;
            color: white;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.2s;
        }

        .btn-custom:hover {
            background-color: #333;
            color: white;
        }

        /* Helpers comunes */
        .table-custom th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #888;
            background: #fafafa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1rem 1.5rem;
        }

        .table-custom td {
            padding: 1rem 1.5rem;
            vertical-align: middle;
            color: #444;
            font-size: 0.9rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        }

        /* Responsive Mobile */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: calc(var(--sidebar-width) * -1);
                position: fixed;
                height: 100vh;
            }

            #sidebar.active {
                margin-left: 0;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                width: 100vw;
                height: 100vh;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }

            .sidebar-overlay.active {
                display: block;
            }
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <!-- Sidebar Overlay para móviles -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <a href="/vizone/" class="text-decoration-none">
                    <div class="brand-text">Vizone<span>Portal</span></div>
                </a>
            </div>

            <div class="sidebar-menu">
                <div class="menu-title">Módulos Principales</div>
                <ul class="menu-list">
                    <?php if (isset($activeRole) && $activeRole === 'admin'): ?>
                        <!-- Dashboard / Usuarios -->
                        <li class="menu-item">
                            <a href="/vizone/dashboard/usuarios"
                                class="menu-link <?= (isset($activeModule) && $activeModule === 'usuarios') ? 'active' : '' ?>">
                                <i class="bi bi-people-fill"></i>
                                Usuarios Internos
                            </a>
                        </li>

                        <!-- Clientes -->
                        <li class="menu-item">
                            <a href="/vizone/dashboard/clientes"
                                class="menu-link <?= (isset($activeModule) && $activeModule === 'clientes') ? 'active' : '' ?>">
                                <i class="bi bi-briefcase-fill"></i>
                                Directorio de Clientes
                            </a>
                        </li>

                        <!-- Pagos Globales -->
                        <li class="menu-item">
                            <a href="/vizone/dashboard/pagos"
                                class="menu-link <?= (isset($activeModule) && $activeModule === 'pagos') ? 'active' : '' ?>">
                                <i class="bi bi-receipt"></i>
                                Módulo de Pagos
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (isset($activeRole) && $activeRole === 'cliente'): ?>
                        <!-- Mi Portal -->
                        <li class="menu-item">
                            <a href="/vizone/dashboard/mi-portal"
                                class="menu-link <?= (isset($activeModule) && $activeModule === 'mi_portal') ? 'active' : '' ?>">
                                <i class="bi bi-person-badge-fill"></i>
                                Mi Portal
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Tickets -->
                    <li class="menu-item">
                        <a href="/vizone/dashboard/tickets"
                            class="menu-link <?= (isset($activeModule) && $activeModule === 'tickets') ? 'active' : '' ?>">
                            <i class="bi bi-ticket-detailed-fill"></i>
                            Soporte y Tickets
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sidebar-footer">
                <a href="/vizone/logout" class="menu-link text-danger"
                    style="color: #ff6b6b !important; padding: 10px;">
                    <i class="bi bi-box-arrow-right text-danger"></i>
                    Cerrar Sesión
                </a>
            </div>
        </nav>

        <!-- Contenido Central -->
        <div id="content">
            <!-- Top Navbar -->
            <nav class="top-navbar">
                <div>
                    <!-- Botón Hamburguesa Móvil (Simulado por ahora) -->
                    <button type="button" id="sidebarCollapse" class="btn btn-light d-md-none shadow-sm">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <!-- Título de sección opcional -->
                    <span class="ms-3 fw-medium text-muted d-none d-md-inline">Empresa Tech Solutions S.A.</span>
                </div>

                <div class="d-flex align-items-center gap-4">
                    <div class="d-none d-md-block text-end">
                        <div class="fw-semibold lh-1" style="font-size:0.9rem; color: #111;">
                            <?= htmlspecialchars($activeUsername ?? 'User') ?>
                        </div>
                        <small class="text-muted" style="font-size:0.75rem; text-transform: uppercase;">
                            <?= htmlspecialchars($activeRole ?? 'Admin') ?>
                        </small>
                    </div>
                    <div class="avatar-circle shadow-sm">
                        <?= strtoupper(substr($activeUsername ?? 'U', 0, 1)) ?>
                    </div>
                </div>
            </nav>

            <!-- Carga Dinámica de Vista -->
            <div class="view-container">
                <?php
                // Incluimos el contenido de la vista solicitada por el Controlador
                if (isset($viewContent) && file_exists($viewContent)) {
                    require_once $viewContent;
                } else {
                    echo "<div class='alert alert-warning'>Vista no encontrada: " . htmlspecialchars($viewContent) . "</div>";
                }
                ?>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Lógica simple para abrir/cerrar sidebar en móviles
        document.addEventListener("DOMContentLoaded", () => {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggleBtn = document.getElementById('sidebarCollapse');

            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    sidebar.classList.toggle('active');
                    overlay.classList.toggle('active');
                });
            }

            if (overlay) {
                overlay.addEventListener('click', () => {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                });
            }
        });
    </script>
</body>

</html>