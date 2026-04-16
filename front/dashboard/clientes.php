<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Header de página -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1 text-dark">Directorio de Clientes</h1>
        <p class="text-muted small mb-0">Gestiona los accesos, finanzas y proyectos de tus clientes.</p>
    </div>
    <div>
        <button class="btn btn-custom shadow-sm" onclick="openClienteModal()"><i class="bi bi-briefcase-fill me-1"></i>
            Nuevo Cliente</button>
    </div>
</div>

<!-- Tabla de Clientes -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h6 class="fw-bold mb-0">Cartera de Clientes</h6>
            </div>
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control bg-light border-0 shadow-none ps-0"
                        placeholder="Buscar cliente o usuario...">
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0 mt-3">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted small fw-medium uppercase">
                <tr>
                    <th class="ps-4">Empresa</th>
                    <th>Contacto</th>
                    <th>Usuario Portal</th>
                    <th>Alta</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                <?php if (empty($clientes)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="bi bi-folder2-open display-4 opacity-25 d-block mb-3"></i>
                            No hay clientes registrados en el directorio.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($clientes as $cli): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style="width: 40px; height: 40px;">
                                        <i class="bi bi-building fw-bold"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 fw-semibold text-dark">
                                            <?= htmlspecialchars($cli['nombre_empresa']) ?>
                                        </p>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($cli['email'] ?? 'Sin email') ?>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="mb-0 text-dark">
                                    <?= htmlspecialchars($cli['contacto_principal'] ?? 'N/A') ?>
                                </p>
                                <small class="text-muted">
                                    <?= htmlspecialchars($cli['telefono'] ?? '') ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border d-inline-flex align-items-center px-2 py-1">
                                    <i class="bi bi-person-fill me-1 opacity-50"></i>
                                    <?= htmlspecialchars($cli['username'] ?? 'No vinculado') ?>
                                </span>
                            </td>
                            <td class="text-muted small">
                                <?= htmlspecialchars(date('d M, Y', strtotime($cli['created_at']))) ?>
                            </td>
                            <td class="text-end pe-4">
                                <a href="/vizone/dashboard/cliente/detalles?id=<?= $cli['id'] ?>"
                                    class="btn btn-sm btn-light text-primary border shadow-sm px-3 rounded-pill fw-medium header-menu shadow-hover-md transition-all">
                                    Ver Perfil <i class="bi bi-arrow-right-short"></i>
                                </a>
                                <button onclick="deleteCliente(<?= $cli['id'] ?>)" class="btn btn-sm btn-outline-danger ms-2 rounded-pill shadow-sm" title="Eliminar Cliente">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Nuevo Cliente -->
<div class="modal fade" id="clienteModal" tabindex="-1" aria-labelledby="clienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="clienteModalLabel">Dar de Alta Cliente</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="clienteForm">
                <div class="modal-body">

                    <div class="bg-primary bg-opacity-10 rounded-3 p-3 mb-4 border border-primary border-opacity-25">
                        <p class="small text-primary mb-0 fw-medium">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            Al crear el cliente, se generará en automático un acceso al portal para ellos usando el
                            "Usuario Portal" con la contraseña <code>password123</code>.
                        </p>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-muted">Nombre de la Empresa Comercial
                                *</label>
                            <input type="text" class="form-control shadow-none" name="nombre_empresa" required
                                placeholder="Ej. ACME Corp">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-muted">Usuario para el Portal *</label>
                            <input type="text" class="form-control shadow-none" name="username" required
                                placeholder="Ej. acme_admin">
                        </div>

                        <div class="col-md-12 mt-4">
                            <h6 class="fw-bold mb-3 border-bottom pb-2">Datos de Contacto (Opcional)</h6>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-medium text-muted">Nombre del Contacto</label>
                            <input type="text" class="form-control shadow-none" name="contacto_principal"
                                placeholder="Ej. Juan Pérez">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium text-muted">Teléfono</label>
                            <input type="text" class="form-control shadow-none" name="telefono" placeholder="+52 ...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium text-muted">Correo Electrónico</label>
                            <input type="email" class="form-control shadow-none" name="email"
                                placeholder="contacto@empresa.com">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0 pt-0 mt-3">
                    <button type="button" class="btn btn-light shadow-none text-secondary fw-medium"
                        data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-custom px-4" id="btnSaveCliente">Registrar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let clienteModal;
    let clienteForm;

    document.addEventListener('DOMContentLoaded', function () {
        clienteModal = new bootstrap.Modal(document.getElementById('clienteModal'));
        clienteForm = document.getElementById('clienteForm');

        // Maneja el envío del formulario
        clienteForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const btnSave = document.getElementById('btnSaveCliente');
            const originalText = btnSave.innerText;

            btnSave.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Registrando...';
            btnSave.disabled = true;

            fetch('/vizone/dashboard/clientes/save', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        clienteModal.hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Cliente Registrado',
                            html: data.message, // Muestra el mensaje que incluye la contraseña
                            showConfirmButton: true,
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#0ea5e9' // Cyan de vizone
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Problema al comunicarse con el servidor.', 'error');
                })
                .finally(() => {
                    btnSave.innerHTML = originalText;
                    btnSave.disabled = false;
                });
        });
    });

    function openClienteModal() {
        clienteForm.reset();
        if (clienteModal) clienteModal.show();
    }

    function deleteCliente(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se eliminará el cliente, sus servicios asociados, y su usuario de portal. ¡Esta acción es irreversible!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('id', id);

                fetch('/vizone/dashboard/clientes/delete', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Problema al comunicarse con el servidor.', 'error');
                });
            }
        });
    }
</script>