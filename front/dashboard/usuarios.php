<!-- SweetAlert2 para alertas bonitas -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Header de página -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1 text-dark">Gestión de Usuarios</h1>
        <p class="text-muted small mb-0">Administra los accesos de tu equipo interno al sistema.</p>
    </div>
    <div>
        <button class="btn btn-custom shadow-sm" onclick="openUserModal()"><i class="bi bi-person-plus-fill me-1"></i>
            Nuevo Usuario</button>
    </div>
</div>

<!-- Panel / Tabla de Usuarios -->
<div class="panel">
    <div class="p-3 border-bottom border-light d-flex justify-content-between align-items-center bg-white">
        <h2 class="h6 fw-semibold mb-0 text-dark">Lista de Usuarios</h2>
        <div class="input-group input-group-sm" style="width: 250px;">
            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control border-start-0 ps-0 shadow-none" placeholder="Buscar por nombre...">
        </div>
    </div>

    <div class="table-responsive bg-white">
        <table class="table table-hover table-custom mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Rol Funcional</th>
                    <th>Fecha de Alta</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($users) && !empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="text-muted fw-medium">#
                                <?= htmlspecialchars($user['id']) ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div
                                        style="width:32px; height:32px; border-radius:50%; background: #f1f5f9; color: #475569; display:flex; align-items:center; justify-content:center; font-size:14px;">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <span class="fw-medium text-dark">
                                        <?= htmlspecialchars($user['username']) ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <?php 
                                    $rolMap = [
                                        'admin' => 'Administrador',
                                        'apoyo' => 'Apoyo de sistema',
                                        'cliente' => 'Cliente',
                                        'estudiante' => 'Estudiante'
                                    ];
                                    $rolName = $rolMap[$user['role']] ?? ucfirst($user['role']);
                                ?>
                                <span class="badge"
                                    style="background-color: rgba(0, 210, 255, 0.1); color: #008eb3; border-radius: 50px; padding: 0.4em 0.8em; font-weight: 500;">
                                    <i class="bi bi-shield-lock-fill me-1"></i>
                                    <?= htmlspecialchars($rolName) ?>
                                </span>
                            </td>
                            <td>
                                <?= htmlspecialchars(date('d M, Y', strtotime($user['created_at']))) ?>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm text-secondary bg-light rounded-circle p-2 mx-1 border-0"
                                    title="Editar"
                                    onclick="openUserModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>', '<?= htmlspecialchars($user['role']) ?>')">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <button class="btn btn-sm text-danger bg-light rounded-circle p-2 border-0" title="Eliminar"
                                        onclick="deleteUser(<?= $user['id'] ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <div class="mb-3"><i class="bi bi-inboxes fs-1" style="color:#dee2e6;"></i></div>
                            No hay usuarios registrados.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Crear/Editar Usuario -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="userModalLabel">Nuevo Usuario</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="userForm">
                <div class="modal-body">
                    <input type="hidden" id="user_id" name="user_id" value="">

                    <div class="mb-3">
                        <label for="username" class="form-label small fw-medium text-muted">Nombre de Usuario</label>
                        <input type="text" class="form-control shadow-none" id="username" name="username" required>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label small fw-medium text-muted">Rol en el Sistema</label>
                        <select class="form-select shadow-none" id="role" name="role" required>
                            <option value="admin">Administrador</option>
                            <option value="apoyo">Apoyo de sistema</option>
                            <option value="cliente">Cliente</option>
                            <option value="estudiante">Estudiante</option>
                        </select>
                    </div>

                    <div class="mb-2" id="passwordContainer">
                        <label for="password" class="form-label small fw-medium text-muted">Contraseña <span
                                id="passHelp" class="text-secondary fw-normal"></span></label>
                        <input type="password" class="form-control shadow-none" id="password" name="password">
                    </div>
                </div>

                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light shadow-none text-secondary fw-medium"
                        data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-custom px-4" id="btnSaveUser">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let userModal;
    let userForm;

    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar el modal una vez que Bootstrap JS haya cargado
        userModal = new bootstrap.Modal(document.getElementById('userModal'));
        userForm = document.getElementById('userForm');

        // Maneja el envío del formulario por AJAX
        userForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const btnSave = document.getElementById('btnSaveUser');
            const originalText = btnSave.innerText;
            
            btnSave.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
            btnSave.disabled = true;

            fetch('/vizone/dashboard/usuarios/save', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    userModal.hide();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload()); // Recargamos para ver la tabla fresca
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Hubo un problema de conexión con el servidor.', 'error');
            })
            .finally(() => {
                btnSave.innerHTML = originalText;
                btnSave.disabled = false;
            });
        });
    });

    // Abre el modal para Crear o Editar
    function openUserModal(id = '', username = '', role = 'admin') {
        document.getElementById('user_id').value = id;
        document.getElementById('username').value = username;
        document.getElementById('role').value = role;
        document.getElementById('password').value = '';
        
        const isEditing = id !== '';
        document.getElementById('userModalLabel').innerText = isEditing ? 'Editar Usuario' : 'Nuevo Usuario';
        
        // Si edita, password es opcional
        const passHelp = document.getElementById('passHelp');
        const passInput = document.getElementById('password');
        const pwdContainer = document.getElementById('passwordContainer');
        
        // Reset container display first
        pwdContainer.style.display = 'block';
        
        if (isEditing) {
            passHelp.innerText = '(Dejar en blanco para mantener la actual)';
            passInput.removeAttribute('required');
            // If editing and role is not admin nor apoyo, hide it completely
            if (role === 'cliente' || role === 'estudiante') {
                pwdContainer.style.display = 'none';
            }
        } else {
            passHelp.innerText = '*';
            passInput.setAttribute('required', 'required');
        }
        
        if(userModal) userModal.show();
    }

    // Función para eliminar usuario
    function deleteUser(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás revertir esta acción de borrado.",
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

                fetch('/vizone/dashboard/usuarios/delete', {
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
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Problema al intentar eliminar.', 'error');
                });
            }
        });
    }
</script>