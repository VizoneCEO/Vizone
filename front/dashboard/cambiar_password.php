<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Cambiar Contraseña | Vizone') ?></title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            color: #334155;
        }

        .auth-container {
            width: 100%;
            max-width: 440px;
            padding: 2.5rem;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .auth-logo {
            font-size: 2rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2rem;
            letter-spacing: -0.5px;
            text-align: center;
        }

        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            font-size: 0.95rem;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(0, 210, 255, 0.15);
            border-color: #00d2ff;
        }

        .btn-custom {
            background-color: #00d2ff;
            border: none;
            color: #fff;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 8px;
            width: 100%;
            transition: all 0.2s ease;
        }

        .btn-custom:hover {
            background-color: #008eb3;
            transform: translateY(-1px);
        }
    </style>
</head>

<body>

    <div class="auth-container">
        <div class="auth-logo">Vizone <span style="color:#00d2ff;">Portal</span></div>
        <h4 class="mb-3 fw-bold text-center">Cambio de Contraseña Requerido</h4>
        <p class="text-muted text-center mb-4 small">Por motivos de seguridad, detectamos que estás usando una contraseña temporal. Por favor, ingresa una nueva contraseña para continuar.</p>

        <form id="formCambiarPassword">
            <div class="mb-3">
                <label for="new_password" class="form-label small fw-medium">Nueva Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-key"></i></span>
                    <input type="password" class="form-control border-start-0 ps-0 shadow-none" id="new_password" name="new_password" required placeholder="Mínimo 6 caracteres">
                </div>
            </div>

            <div class="mb-4">
                <label for="confirm_password" class="form-label small fw-medium">Confirmar Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-key-fill"></i></span>
                    <input type="password" class="form-control border-start-0 ps-0 shadow-none" id="confirm_password" name="confirm_password" required placeholder="Repite la contraseña">
                </div>
            </div>

            <button type="submit" class="btn btn-custom" id="btnSubmit">Actualizar Contraseña y Continuar</button>
        </form>
    </div>

    <!-- Bootstrap JS (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('formCambiarPassword');

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const newPw = document.getElementById('new_password').value;
                const confPw = document.getElementById('confirm_password').value;

                if (newPw !== confPw) {
                    Swal.fire('Error', 'Las contraseñas no coinciden. Intenta de nuevo.', 'error');
                    return;
                }

                if (newPw.length < 6) {
                    Swal.fire('Atención', 'Tu contraseña debe tener al menos 6 caracteres.', 'warning');
                    return;
                }

                const btn = document.getElementById('btnSubmit');
                const originalText = btn.innerText;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Actualizando...';
                btn.disabled = true;

                const formData = new FormData(this);

                fetch('/vizone/dashboard/usuarios/update-password', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Contraseña actualizada!',
                            text: 'Redirigiendo a tu portal...',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.href = '/vizone/dashboard';
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Hubo un problema de conexión.', 'error');
                })
                .finally(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
            });
        });
    </script>
</body>
</html>
