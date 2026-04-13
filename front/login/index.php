<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Vizone | Acceso Seguro</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        /* Tipografía Inter (Apple Style) */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            --vizone-bg: #050505;
            --vizone-accent: #00d2ff;
            --vizone-accent-hover: #00b8e6;
        }

        body {
            background-color: var(--vizone-bg);
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* --- Fondos Animados Premium (Orbes Flotantes) --- */
        .glow-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            animation: float 20s infinite ease-in-out alternate;
        }

        .orb-1 {
            width: 500px;
            height: 500px;
            background: rgba(0, 210, 255, 0.15);
            top: -10%;
            left: -10%;
            animation-duration: 25s;
        }

        .orb-2 {
            width: 400px;
            height: 400px;
            background: rgba(50, 50, 255, 0.1);
            bottom: -5%;
            right: -5%;
            animation-duration: 30s;
            animation-delay: -5s;
        }

        @keyframes float {
            0% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(50px, 50px) scale(1.1);
            }

            100% {
                transform: translate(-30px, -20px) scale(0.95);
            }
        }

        /* --- Glassmorphism Login Card --- */
        .login-card {
            background: rgba(20, 20, 20, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 3rem;
            width: 100%;
            max-width: 420px;
            z-index: 10;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: form-appear 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes form-appear {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* --- Inputs --- */
        .form-floating>.form-control {
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .form-floating>.form-control:focus {
            background-color: rgba(255, 255, 255, 0.05);
            border-color: var(--vizone-accent);
            box-shadow: 0 0 0 4px rgba(0, 210, 255, 0.1);
        }

        .form-floating>label {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-floating>.form-control:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 1000px #1a1a1a inset !important;
            -webkit-text-fill-color: white !important;
        }

        /* --- Botón Submit --- */
        .btn-login {
            background: var(--vizone-accent);
            color: #000;
            border: none;
            border-radius: 12px;
            padding: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 210, 255, 0.3);
        }

        .btn-login:hover {
            background: var(--vizone-accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 210, 255, 0.4);
        }

        /* --- Logo --- */
        .brand-logo {
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 2rem;
            text-align: center;
        }

        .brand-logo span {
            color: rgba(255, 255, 255, 0.4);
            font-weight: 400;
        }

        /* Go back link */
        .back-link {
            position: absolute;
            top: 30px;
            left: 40px;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            font-size: 0.9rem;
            z-index: 20;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #fff;
        }
    </style>
</head>

<body>

    <!-- Animaciones de fondo -->
    <div class="glow-orb orb-1"></div>
    <div class="glow-orb orb-2"></div>

    <!-- Botón Volver -->
    <a href="/vizone/" class="back-link"><i class="bi bi-arrow-left me-2"></i>Volver al inicio</a>

    <!-- Tarjeta de Login -->
    <div class="login-card">

        <div class="brand-logo">
            Vizone<span>Portal</span>
        </div>

        <p class="text-center text-white-50 small mb-4">Ingresa tus credenciales para administrar el sistema.</p>

        <?php if (isset($error) && !empty($error)): ?>
            <div class="alert alert-danger border-0 rounded-3 small py-2 text-center"
                style="background-color: rgba(220, 53, 69, 0.1); color: #ff6b6b;" role="alert">
                <i class="bi bi-exclamation-circle me-1"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="/vizone/auth" method="POST">

            <!-- Campo Usuario -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="username" name="username" placeholder="Usuario" required
                    autocomplete="username">
                <label for="username">Usuario</label>
            </div>

            <!-- Campo Contraseña -->
            <div class="form-floating mb-4">
                <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña"
                    required autocomplete="current-password">
                <label for="password">Contraseña</label>
            </div>

            <!-- Submit -->
            <div class="d-grid mt-2">
                <button type="submit" class="btn btn-login d-flex justify-content-center align-items-center gap-2">
                    Ingresar <i class="bi bi-arrow-right-short fs-5"></i>
                </button>
            </div>

        </form>

        <div class="text-center mt-4">
            <a href="#" class="text-decoration-none small text-white-50" style="transition: color 0.3s;"
                onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.5)'">¿Olvidaste
                tu contraseña?</a>
        </div>

    </div>

</body>

</html>