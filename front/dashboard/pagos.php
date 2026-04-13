<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Header de página -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1 text-dark">Módulo de Pagos Globales</h1>
        <p class="text-muted small mb-0">Vista general de todos los pagos registrados en la plataforma.</p>
    </div>
    <div>
        <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#globalPaymentModal">
            <i class="bi bi-plus-circle me-1"></i> Capturar Pago
        </button>
    </div>
</div>

<?php
$totalIngresos = 0;
foreach ($pagos as $p) {
    $totalIngresos += $p['monto_pagado'];
}
?>

<!-- Tarjetas de Resumen -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden"
            style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
            <div class="card-body p-4 position-relative text-white">
                <i class="bi bi-wallet2 position-absolute"
                    style="font-size: 5rem; right: -10px; bottom: -20px; opacity: 0.1;"></i>
                <h6 class="text-white-50 text-uppercase fw-bold small mb-3">Total Ingresos Registrados</h6>
                <h2 class="display-6 fw-bold mb-0 text-success">$
                    <?= number_format($totalIngresos, 2) ?>
                </h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4 d-flex flex-column justify-content-center">
                <h6 class="text-muted text-uppercase fw-bold small mb-3">Transacciones Exitosas</h6>
                <h2 class="display-6 fw-bold mb-0 text-dark">
                    <?= count($pagos) ?> <span class="fs-6 fw-normal text-muted">pagos</span>
                </h2>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Pagos -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h6 class="fw-bold mb-0"><i class="bi bi-receipt me-2 text-primary"></i>Historial Global de Pagos</h6>
            </div>
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control bg-light border-0 shadow-none ps-0"
                        placeholder="Buscar por cliente, proyecto o referencia..." id="searchPagos">
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0 mt-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center" id="pagosTable">
                <thead class="table-light text-muted small fw-medium text-uppercase">
                    <tr>
                        <th class="ps-4 text-start">Cliente</th>
                        <th>Proyecto</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Referencia</th>
                        <th>Fecha</th>
                        <th class="pe-4 text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (empty($pagos)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-inbox display-4 opacity-25 d-block mb-3"></i>
                                No hay pagos registrados en el sistema.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pagos as $pago): ?>
                            <tr>
                                <td class="ps-4 text-start">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 35px; height: 35px;">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <p class="mb-0 fw-semibold text-dark">
                                            <?= htmlspecialchars($pago['nombre_empresa']) ?>
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border px-2 py-1">
                                        <?= htmlspecialchars($pago['nombre_proyecto']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-success fw-bold fs-6">
                                        +$
                                        <?= number_format($pago['monto_pagado'], 2) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $icon = 'cash';
                                    if (strtolower($pago['metodo_pago']) == 'transferencia')
                                        $icon = 'bank';
                                    if (strtolower($pago['metodo_pago']) == 'tarjeta')
                                        $icon = 'credit-card';
                                    if (strtolower($pago['metodo_pago']) == 'cheque')
                                        $icon = 'journal-check';
                                    ?>
                                    <i class="bi bi-<?= $icon ?> text-muted me-1"></i>
                                    <?= htmlspecialchars($pago['metodo_pago']) ?>
                                </td>
                                <td>
                                    <span class="text-muted small font-monospace">
                                        <?= htmlspecialchars($pago['referencia'] ?: '--') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-dark">
                                        <?= htmlspecialchars(date('d M, Y', strtotime($pago['fecha_pago']))) ?>
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button
                                            class="btn btn-sm btn-outline-primary border-0 bg-primary bg-opacity-10 text-primary shadow-none btn-view-ticket"
                                            data-bs-toggle="modal" data-bs-target="#ticketModal"
                                            data-cliente="<?= htmlspecialchars($pago['nombre_empresa']) ?>"
                                            data-proyecto="<?= htmlspecialchars($pago['nombre_proyecto']) ?>"
                                            data-monto="<?= number_format($pago['monto_pagado'], 2) ?>"
                                            data-fecha="<?= date('d M, Y', strtotime($pago['fecha_pago'])) ?>"
                                            data-metodo="<?= htmlspecialchars($pago['metodo_pago']) ?>"
                                            data-referencia="<?= htmlspecialchars($pago['referencia'] ?: 'N/A') ?>"
                                            title="Ver Ticket Pro">
                                            <i class="bi bi-receipt"></i> Ticket
                                        </button>
                                        <button
                                            class="btn btn-sm btn-outline-danger border-0 bg-danger bg-opacity-10 text-danger shadow-none btn-delete-pago"
                                            data-id="<?= $pago['pago_id'] ?>" title="Eliminar/Revocar Pago">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Capturar Pago -->
<div class="modal fade" id="globalPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Capturar Pago Manual</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="globalPaymentForm">
                <div class="modal-body p-4 pt-3">

                    <div class="mb-3">
                        <label class="form-label small fw-medium text-muted">Cliente y Proyecto Activo *</label>
                        <select class="form-select shadow-none" name="servicio_id" required>
                            <option value="">-- Selecciona un proyecto --</option>
                            <?php foreach ($serviciosActivos as $srv): ?>
                                <option value="<?= $srv['id'] ?>">
                                    <?= htmlspecialchars($srv['nombre_empresa']) ?> -
                                    <?= htmlspecialchars($srv['nombre_proyecto']) ?>
                                    (Resta: $<?= number_format($srv['restante'], 2) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-medium text-muted">Monto Pagado ($) *</label>
                        <input type="number" step="0.01"
                            class="form-control form-control-lg shadow-none text-success fw-bold" name="monto_pagado"
                            id="paymentMonto" required placeholder="0.00">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-muted">Fecha del Pago *</label>
                            <input type="date" class="form-control shadow-none" name="fecha_pago" required
                                value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-muted">Método de Pago</label>
                            <select class="form-select shadow-none" name="metodo_pago">
                                <option value="Transferencia">Transferencia</option>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Tarjeta">Tarjeta / Link</option>
                                <option value="Cheque">Cheque</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium text-muted">Referencia / Comprobante</label>
                            <input type="text" class="form-control shadow-none" name="referencia"
                                placeholder="Ej: SPEI 12345609">
                        </div>
                    </div>
                </div>
                <div
                    class="modal-footer border-top-0 pt-0 mt-2 bg-light rounded-bottom-4 p-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-light shadow-none text-secondary fw-medium"
                        data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4" id="btnSaveGlobalPayment"><i
                            class="bi bi-check-circle me-1"></i> Confirmar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ticket Super Pro -->
<div class="modal fade" id="ticketModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden; background: #fff;">
            <!-- Decoración superior (estilo ticket rasgado o color) -->
            <div class="bg-success text-center py-4 text-white position-relative"
                style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="bi bi-check-circle-fill display-5 mb-2 shadow-sm rounded-circle"></i>
                <h5 class="fw-bold mb-0 text-uppercase tracking-wide">Pago Recibido</h5>
            </div>
            <div class="modal-body p-4 pb-2">
                <div class="text-center mb-4">
                    <h2 class="display-6 fw-bold text-dark mb-0" id="ticketMonto">$0.00</h2>
                    <span class="text-muted small text-uppercase fw-medium" id="ticketFecha">--/--/----</span>
                </div>

                <div class="border-top border-bottom border-dashed py-3 mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Cliente</span>
                        <span class="fw-semibold text-dark text-end" id="ticketCliente">---</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Proyecto</span>
                        <span class="fw-semibold text-dark text-end" id="ticketProyecto">---</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Método</span>
                        <span class="fw-semibold text-dark text-end" id="ticketMetodo">---</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Ref.</span>
                        <span class="fw-semibold text-dark text-end font-monospace" id="ticketReferencia">---</span>
                    </div>
                </div>

                <div class="text-center">
                    <p class="small text-muted mb-0">Comprobante oficial de pago.</p>
                    <p class="small fw-bold mb-0" style="color: var(--vizone-accent);">Vizone</p>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 pt-0 bg-transparent d-flex justify-content-center">
                <button class="btn btn-dark rounded-pill px-4 shadow-sm w-100" data-bs-dismiss="modal">
                    Cerrar Ticket
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .border-dashed {
        border-style: dashed !important;
        border-width: 2px !important;
        border-color: rgba(0, 0, 0, 0.08) !important;
    }

    .tracking-wide {
        letter-spacing: 1px;
    }
</style>

<script>
    // Simple table filter
    document.getElementById('searchPagos').addEventListener('keyup', function () {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#pagosTable tbody tr');

        rows.forEach(row => {
            if (row.innerText.toLowerCase().includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Form submission logic
    const globalPaymentForm = document.getElementById('globalPaymentForm');
    if (globalPaymentForm) {
        globalPaymentForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const btnSave = document.getElementById('btnSaveGlobalPayment');
            const originalText = btnSave.innerHTML;
            btnSave.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';
            btnSave.disabled = true;

            const formData = new FormData(this);

            fetch('/vizone/dashboard/clientes/pagos/save', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Pago Registrado',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    Swal.fire('Error', 'No se pudo registrar el pago.', 'error');
                })
                .finally(() => {
                    btnSave.innerHTML = originalText;
                    btnSave.disabled = false;
                });
        });
    }

    // Lógica para visualizar el Ticket Super Pro
    document.querySelectorAll('.btn-view-ticket').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('ticketCliente').textContent = this.getAttribute('data-cliente');
            document.getElementById('ticketProyecto').textContent = this.getAttribute('data-proyecto');
            document.getElementById('ticketMonto').textContent = '$' + this.getAttribute('data-monto');
            document.getElementById('ticketFecha').textContent = this.getAttribute('data-fecha');
            document.getElementById('ticketMetodo').textContent = this.getAttribute('data-metodo');
            document.getElementById('ticketReferencia').textContent = this.getAttribute('data-referencia');
        });
    });

    // Lógica para eliminar/revocar un pago global
    document.querySelectorAll('.btn-delete-pago').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const pagoId = this.getAttribute('data-id');

            Swal.fire({
                title: '¿Revocar este pago?',
                text: 'Esta acción eliminará el registro y restaurará el saldo adeudado del proyecto. No se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar pago',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('pago_id', pagoId);

                    fetch('/vizone/dashboard/clientes/pagos/delete', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Eliminado', 'Pago restablecido con éxito.', 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(err => Swal.fire('Error', 'Error de red al intentar revocar.', 'error'));
                }
            });
        });
    });
</script>