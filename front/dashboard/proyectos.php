<!-- SweetAlert2 para alertas bonitas -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Header de página -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1 text-dark">Gestión de Proyectos</h1>
        <p class="text-muted small mb-0">Administra el portafolio público de casos de éxito.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary shadow-sm" onclick="refreshScreenshots()" id="btnRefreshScreenshots">
            <i class="bi bi-camera me-1"></i> Extraer Screenshots
        </button>
        <button class="btn btn-custom shadow-sm" onclick="openProyectoModal()"><i class="bi bi-plus-lg me-1"></i>
            Nuevo Proyecto</button>
    </div>
</div>

<!-- Panel / Tabla de Proyectos -->
<div class="panel">
    <div class="p-3 border-bottom border-light bg-white">
        <h2 class="h6 fw-semibold mb-0 text-dark">Lista de Proyectos</h2>
    </div>

    <div class="table-responsive bg-white">
        <table class="table table-hover table-custom mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título del Proyecto</th>
                    <th>Terminado En</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($proyectos) && !empty($proyectos)): ?>
                    <?php foreach ($proyectos as $proyecto): ?>
                        <tr>
                            <td class="text-muted fw-medium">#<?= htmlspecialchars($proyecto['id']) ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:40px; height:40px; border-radius:8px; overflow:hidden; background: #f1f5f9; display:flex; align-items:center; justify-content:center;">
                                        <?php if(!empty($proyecto['link_proyecto'])): ?>
                                            <i class="bi bi-browser-chrome text-primary"></i>
                                        <?php else: ?>
                                            <i class="bi bi-image text-muted"></i>
                                        <?php endif; ?>
                                    </div>
                                    <span class="fw-bold text-dark">
                                        <?= htmlspecialchars($proyecto['titulo']) ?>
                                    </span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars(date('d M, Y', strtotime($proyecto['fecha_terminado']))) ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm text-secondary bg-light rounded-circle p-2 mx-1 border-0"
                                    title="Editar"
                                    onclick="openProyectoModal(<?= $proyecto['id'] ?>, <?= htmlspecialchars(json_encode($proyecto['titulo'])) ?>, <?= htmlspecialchars(json_encode($proyecto['descripcion'])) ?>, <?= htmlspecialchars(json_encode($proyecto['imagen_url'])) ?>, <?= htmlspecialchars(json_encode($proyecto['link_proyecto'])) ?>, <?= htmlspecialchars(json_encode($proyecto['fecha_terminado'])) ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm text-danger bg-light rounded-circle p-2 border-0" title="Eliminar"
                                    onclick="deleteProyecto(<?= $proyecto['id'] ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <div class="mb-3"><i class="bi bi-inboxes fs-1" style="color:#dee2e6;"></i></div>
                            Aún no hay proyectos en el portafolio.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Crear/Editar Proyecto -->
<div class="modal fade" id="proyectoModal" tabindex="-1" aria-labelledby="proyectoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="proyectoModalLabel">Nuevo Proyecto</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="proyectoForm">
                <div class="modal-body">
                    <input type="hidden" id="proyecto_id" name="id" value="">

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="titulo" class="form-label small fw-medium text-muted">Título del Proyecto</label>
                            <input type="text" class="form-control shadow-none" id="titulo" name="titulo" required>
                        </div>
                        <div class="col-md-4">
                            <label for="fecha_terminado" class="form-label small fw-medium text-muted">Fecha Finalizado</label>
                            <input type="date" class="form-control shadow-none" id="fecha_terminado" name="fecha_terminado" required>
                        </div>
                        <div class="col-md-12">
                            <label for="descripcion" class="form-label small fw-medium text-muted">Descripción Corta / Resumen</label>
                            <textarea class="form-control shadow-none" id="descripcion" name="descripcion" rows="3" required></textarea>
                        </div>
                        <div class="col-md-12">
                            <label for="link_proyecto" class="form-label small fw-medium text-muted">Enlace del Proyecto (Esta URL se renderizará automáticamente en la pantalla)</label>
                            <input type="url" class="form-control shadow-none" id="link_proyecto" name="link_proyecto" placeholder="https://..." required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light shadow-none text-secondary fw-medium"
                        data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-custom px-4" id="btnSaveProyecto">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let proyectoModal;
    let proyectoForm;

    document.addEventListener('DOMContentLoaded', function() {
        proyectoModal = new bootstrap.Modal(document.getElementById('proyectoModal'));
        proyectoForm = document.getElementById('proyectoForm');

        proyectoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const btnSave = document.getElementById('btnSaveProyecto');
            const originalText = btnSave.innerText;
            
            btnSave.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
            btnSave.disabled = true;

            fetch('<?= defined('BASE_URL') ? BASE_URL : '/' ?>dashboard/proyectos/save', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    proyectoModal.hide();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload()); 
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Hubo un problema.', 'error');
            })
            .finally(() => {
                btnSave.innerHTML = originalText;
                btnSave.disabled = false;
            });
        });
    });

    function openProyectoModal(id = '', titulo = '', descripcion = '', imagen_url = '', link_proyecto = '', fecha_terminado = '') {
        document.getElementById('proyecto_id').value = id;
        document.getElementById('titulo').value = titulo;
        document.getElementById('descripcion').value = descripcion;
        document.getElementById('link_proyecto').value = link_proyecto;
        document.getElementById('fecha_terminado').value = fecha_terminado ? fecha_terminado : new Date().toISOString().split('T')[0];
        
        document.getElementById('proyectoModalLabel').innerText = (id !== '') ? 'Editar Proyecto' : 'Nuevo Proyecto';
        
        if(proyectoModal) proyectoModal.show();
    }

    function deleteProyecto(id) {
        Swal.fire({
            title: '¿Eliminar Proyecto?',
            text: "No podrás deshacer esta acción.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('id', id);

                fetch('<?= defined('BASE_URL') ? BASE_URL : '/' ?>dashboard/proyectos/delete', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success', title: 'Eliminado', text: data.message, timer: 1500, showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
            }
        });
    }

    function refreshScreenshots() {
        Swal.fire({
            title: '¿Actualizar Capturas?',
            text: "Se analizarán los enlaces y se descargarán screenshots nuevos (esto reemplaza los iframes por imágenes limpias cacheables). Puede tardar un poco.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Sí, procesar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const btn = document.getElementById('btnRefreshScreenshots');
                const orig = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';
                btn.disabled = true;

                fetch('<?= defined('BASE_URL') ? BASE_URL : '/' ?>dashboard/proyectos/refresh-screenshots', {
                    method: 'POST'
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('¡Listo!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Aviso', data.message, 'warning');
                        btn.innerHTML = orig;
                        btn.disabled = false;
                    }
                })
                .catch(err => {
                    Swal.fire('Error', 'Fallo conexión con el servidor', 'error');
                    btn.innerHTML = orig;
                    btn.disabled = false;
                });
            }
        });
    }
</script>
