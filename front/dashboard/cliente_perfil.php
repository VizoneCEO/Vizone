<!-- Header de Página - Regreso -->
<div class="mb-4">
    <a href="/vizone/dashboard/clientes"
        class="text-decoration-none text-muted small header-menu d-inline-flex align-items-center mb-2 shadow-hover-md transition-all">
        <i class="bi bi-arrow-left me-1"></i> Volver al Directorio
    </a>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 fw-bold mb-1 text-dark"><?= htmlspecialchars($cliente['nombre_empresa']) ?></h1>
            <p class="text-muted small mb-0">
                <i class="bi bi-person-badge text-primary me-1"></i> Contacto:
                <?= htmlspecialchars($cliente['contacto_principal'] ?: 'Sin asignar') ?>
                <span class="mx-2 text-light">|</span>
                <i class="bi bi-envelope text-primary me-1"></i>
                <?= htmlspecialchars($cliente['email'] ?: 'Sin email') ?>
            </p>
        </div>
        <div>
            <!-- Botón para Editar Info General (Pendiente de implementar) -->
            <button class="btn btn-light border text-secondary shadow-sm"><i class="bi bi-gear"></i> Ajustes</button>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- COLUMNA IZQUIERDA: Tarjetas Finanzas y Servicios -->
    <div class="col-lg-8">

        <!-- Header Seccion Proyectos -->
        <div class="d-flex justify-content-between align-items-end mb-3 mt-2">
            <h5 class="fw-bold text-dark mb-0">Servicios Contratados</h5>
            <button class="btn btn-sm btn-custom text-white shadow-sm" data-bs-toggle="modal"
                data-bs-target="#newServiceModal">
                <i class="bi bi-plus-lg"></i> Añadir Servicio
            </button>
        </div>

        <?php if (empty($cliente['servicios'])): ?>
            <div class="card border border-dashed rounded-4 bg-transparent text-center p-5 mb-4">
                <i class="bi bi-rocket-takeoff text-muted opacity-50 display-6 d-block mb-2"></i>
                <p class="text-muted mb-0">Este cliente aún no tiene servicios o proyectos asignados.</p>
            </div>
        <?php else: ?>
            <?php foreach ($cliente['servicios'] as $srv): ?>
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div
                        class="card-header bg-white border-bottom-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div class="pe-3 flex-grow-1">
                            <h6 class="fw-bold mb-1 text-dark text-break">
                                <i
                                    class="bi bi-briefcase text-primary me-2"></i><?= htmlspecialchars($srv['nombre_proyecto']) ?>
                                <span
                                    class="badge bg-light text-secondary border d-inline-block ms-1 fw-normal"><?= htmlspecialchars($srv['tipo_servicio'] ?? 'Otro') ?></span>
                            </h6>
                            <span
                                class="badge <?= $srv['estado'] == 'activo' ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' ?> border mt-1">
                                <?= ucfirst($srv['estado']) ?>
                            </span>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border text-secondary shadow-sm" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <?php
                                $svcDataRaw = htmlspecialchars(json_encode([
                                    'id' => $srv['id'],
                                    'tipo_servicio' => $srv['tipo_servicio'] ?? 'Otro',
                                    'nombre_proyecto' => $srv['nombre_proyecto'],
                                    'tipo_pago' => $srv['tipo_pago'] ?? 'unico',
                                    'costo_total' => $srv['costo_total'] ?? 0,
                                    'pago_inicial' => $srv['pago_inicial'] ?? 0,
                                    'mensualidad_financiamiento' => $srv['mensualidad_financiamiento'] ?? 0,
                                    'es_recurrente' => $srv['es_recurrente'] ?? 0,
                                    'frecuencia_pago' => $srv['frecuencia_pago'] ?? 'ninguno',
                                    'fecha_proximo_pago' => $srv['fecha_proximo_pago'] ?? ''
                                ]), ENT_QUOTES, 'UTF-8');
                                ?>
                                <li><a class="dropdown-item small text-secondary btn-edit-service" href="#" data-raw="<?= $svcDataRaw ?>"><i
                                            class="bi bi-pencil me-2 text-primary"></i>Editar</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item small text-success btn-add-pago" href="#" 
                                        data-servicio-id="<?= $srv['id'] ?>"
                                        data-nombre="<?= htmlspecialchars($srv['nombre_proyecto']) ?>">
                                    <i class="bi bi-cash-coin me-2"></i>Registrar Pago
                                </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item small text-danger btn-delete-service" href="#"
                                        data-id="<?= $srv['id'] ?>"><i class="bi bi-trash me-2"></i>Eliminar</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="bg-light rounded-3 p-3 h-100">
                                    <p class="text-primary small fw-bold text-uppercase mb-2">
                                        <?= htmlspecialchars($srv['tipo_servicio'] ?? 'Servicio') ?>
                                    </p>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-secondary small">Costo Total:</span>
                                        <span class="fw-semibold text-dark">$<?= number_format($srv['costo_total'], 2) ?></span>
                                    </div>

                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-secondary small">Anticipo Acordado:</span>
                                        <span class="text-secondary fw-medium">$<?= number_format($srv['pago_inicial'], 2) ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-secondary small">Total Pagado:</span>
                                        <span
                                            class="text-success fw-medium">$<?= number_format($srv['total_pagado'], 2) ?></span>
                                    </div>
                                    <hr class="my-2 border-secondary opacity-25">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-secondary small">Restante a Liquidar:</span>
                                        <span
                                            class="<?= ($srv['costo_total'] - $srv['total_pagado']) > 0 ? 'text-danger' : 'text-success' ?> fw-medium">
                                            $<?= number_format(max(0, $srv['costo_total'] - $srv['total_pagado']), 2) ?>
                                        </span>
                                    </div>

                                    <?php if (($srv['costo_total'] - $srv['total_pagado']) <= 0): ?>
                                        <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                                            <span class="text-secondary small"><i
                                                    class="bi bi-check-circle-fill text-success me-1"></i>Totalmente
                                                Liquidado</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Esquema de Pagos -->
                            <div class="col-md-6">
                                <div class="bg-light rounded-3 p-3 h-100 border-start border-4 border-primary">
                                    <p class="text-primary small fw-bold text-uppercase mb-2">Esquema de Pagos</p>

                                    <?php if ($srv['tipo_pago'] === 'varios' && $srv['mensualidad_financiamiento'] > 0): ?>
                                        <div class="mb-2">
                                            <span class="d-block text-secondary small mb-1">Financiamiento (<span
                                                    class="fw-bold"><?= $srv['meses_financiamiento'] ?> meses
                                                    previstos</span>)</span>
                                            <span
                                                class="h5 fw-bold text-dark mb-0">$<?= number_format($srv['mensualidad_financiamiento'], 2) ?>
                                                <span class="text-muted fs-6 fw-normal">/ mes</span>
                                            </span>
                                            <?php if (!empty($srv['fecha_proximo_pago'])): ?>
                                                <span class="d-block text-primary small mt-1"><i class="bi bi-calendar me-1"></i>Próximo Pago: <?= htmlspecialchars($srv['fecha_proximo_pago']) ?></span>
                                            <?php endif; ?>
                                            
                                            <!-- Boton para Amortizacion Card -->
                                            <button class="btn btn-sm btn-outline-primary mt-2 shadow-sm w-100 btn-view-amortization" 
                                                    data-costo="<?= $srv['costo_total'] ?>"
                                                    data-anticipo="<?= $srv['pago_inicial'] ?>"
                                                    data-mensualidad="<?= $srv['mensualidad_financiamiento'] ?>">
                                                <i class="bi bi-table me-1"></i> Ver Tabla de Amortización
                                            </button>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($srv['es_recurrente'] && $srv['frecuencia_pago'] !== 'ninguno'): ?>
                                        <div class="mt-3">
                                            <span class="d-block text-secondary small mb-1">Renovación de Servicio</span>
                                            <span class="h6 fw-bold text-dark mb-0"><i
                                                    class="bi bi-arrow-repeat text-primary me-1"></i>Cobro
                                                <?= ucfirst($srv['frecuencia_pago']) ?></span>
                                            <?php if (!empty($srv['fecha_proximo_pago'])): ?>
                                                <span class="d-block text-primary small mt-1"><i class="bi bi-calendar me-1"></i>Próxima Renovación: <?= htmlspecialchars($srv['fecha_proximo_pago']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($srv['tipo_pago'] === 'unico' && !$srv['es_recurrente']): ?>
                                        <div class="d-flex align-items-center h-100 pb-4">
                                            <span class="text-muted small"><i class="bi bi-info-circle me-1"></i> No tiene cobros recurrentes activos.</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Historial de Pagos (Accordion) -->
                            <div class="col-12 mt-3">
                                <div class="accordion accordion-flush bg-light rounded-3 border" id="accordionPagos_<?= $srv['id'] ?>">
                                    <div class="accordion-item bg-transparent border-0">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed bg-transparent shadow-none py-2 text-dark fw-medium small" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapsePagos_<?= $srv['id'] ?>">
                                                <i class="bi bi-receipt me-2 text-secondary"></i> Historial de Pagos (<span class="text-primary mx-1"><?= count($srv['pagos_historial'] ?? []) ?></span>)
                                            </button>
                                        </h2>
                                        <div id="flush-collapsePagos_<?= $srv['id'] ?>" class="accordion-collapse collapse" data-bs-parent="#accordionPagos_<?= $srv['id'] ?>">
                                            <div class="accordion-body pt-0 pb-3">
                                                <?php if (empty($srv['pagos_historial'])): ?>
                                                    <div class="text-center py-3">
                                                        <span class="text-muted small">No se han registrado pagos para este servicio.</span>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="table-responsive bg-white border rounded shadow-sm mt-2">
                                                        <table class="table table-sm table-hover mb-0 text-center">
                                                            <thead class="table-light text-muted small">
                                                                <tr>
                                                                    <th>Fecha</th>
                                                                    <th>Monto</th>
                                                                    <th>Método</th>
                                                                    <th>Ref.</th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="small align-middle">
                                                                <?php foreach ($srv['pagos_historial'] as $pago): ?>
                                                                    <tr>
                                                                        <td><?= htmlspecialchars($pago['fecha_pago']) ?></td>
                                                                        <td class="text-success fw-medium">$<?= number_format($pago['monto_pagado'], 2) ?></td>
                                                                        <td><?= htmlspecialchars($pago['metodo_pago']) ?></td>
                                                                        <td class="text-muted"><?= htmlspecialchars($pago['referencia'] ?: '--') ?></td>
                                                                        <td>
                                                                            <button class="btn btn-sm text-danger shadow-none btn-delete-pago" data-id="<?= $pago['id'] ?>" title="Revocar Pago">
                                                                                <i class="bi bi-x-circle-fill"></i>
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fin Historial -->

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- COLUMNA DERECHA: Documentos y Accesos -->
    <div class="col-lg-4">
        <!-- Credenciales Portald del Cliente -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-dark text-white text-center p-4"
            style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
            <i class="bi bi-shield-lock display-5 text-primary mb-2"></i>
            <h6 class="fw-bold">Acceso Visión</h6>
            <p class="small text-white-50 mb-3">Las credenciales que el cliente usa para entrar aquí.</p>
            <div class="bg-black bg-opacity-25 rounded-3 p-2 font-monospace text-info">
                User: <?= htmlspecialchars($cliente['username']) ?>
            </div>
            <button class="btn btn-sm btn-outline-light mt-3 rounded-pill px-3 opacity-75">Reestablecer
                Password</button>
        </div>

        <!-- Archivos y Manuales -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-richtext text-primary me-2"></i>Archivos
                    y Manuales</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if (empty($cliente['documentos'])): ?>
                        <li class="list-group-item text-center p-4 text-muted small border-bottom-0 rounded-bottom-4">
                            No hay manuales cargados.
                        </li>
                    <?php else: ?>
                        <?php foreach ($cliente['documentos'] as $doc): ?>
                            <li
                                class="list-group-item px-4 py-3 d-flex justify-content-between align-items-center header-menu shadow-hover-sm transition-all">
                                <div class="text-truncate flex-grow-1 me-3">
                                    <h6 class="mb-0 text-dark fs-6 text-truncate"><i
                                            class="bi bi-file-pdf text-danger me-2"></i><?= htmlspecialchars($doc['nombre_original']) ?>
                                    </h6>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($doc['uploaded_at'])) ?></small>
                                </div>
                                <a href="#" class="btn btn-sm btn-light border rounded-circle"><i
                                        class="bi bi-download text-primary"></i></a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="card-footer bg-light border-top-0 p-3 rounded-bottom-4 text-center">
                <button class="btn btn-sm btn-secondary w-100 shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#newDocumentModal"><i class="bi bi-cloud-arrow-up me-1"></i> Subir Archivo</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Servicio -->
<div class="modal fade" id="newServiceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Añadir Nuevo Servicio a
                    <?= htmlspecialchars($cliente['nombre_empresa']) ?>
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="serviceForm">
                <div class="modal-body">
                    <input type="hidden" name="cliente_id" value="<?= $cliente['id'] ?>">

                    <!-- Paso 1: Tipo de Servicio -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">1. Pormenores del Proyecto</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-medium text-muted">A. Tipo de Servicio *</label>
                                <select class="form-select shadow-none" name="tipo_servicio" id="tipoServicio" required>
                                    <option value="" selected disabled>Selecciona una opción</option>
                                    <option value="Desarrollo">Desarrollo (Web/Software)</option>
                                    <option value="Asesoría">Asesoría / Consultoría</option>
                                    <option value="Soporte Técnico">Soporte Técnico</option>
                                    <option value="Hosting">Hosting</option>
                                    <option value="Dominio">Dominio</option>
                                    <option value="SSL">Certificado SSL</option>
                                    <option value="Marketing Digital">Marketing Digital</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium text-muted">B. Nombre del Proyecto *</label>
                                <input type="text" class="form-control shadow-none" name="nombre_proyecto" required
                                    placeholder="Ej. Tienda Online v2">
                            </div>
                        </div>
                    </div>

                    <!-- Paso 2: Esquema de Pago -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">2. Finanzas y Pagos</h6>
                        <label class="form-label small fw-medium text-muted">A. ¿Cómo se pagará este servicio? *</label>
                        <div class="d-flex gap-3 mb-3">
                            <div class="form-check">
                                <input class="form-check-input shadow-none" type="radio" name="tipo_pago"
                                    id="tipoPagoUnico" value="unico" checked>
                                <label class="form-check-label" for="tipoPagoUnico">Un solo pago</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input shadow-none" type="radio" name="tipo_pago"
                                    id="tipoPagoVarios" value="varios">
                                <label class="form-check-label" for="tipoPagoVarios">Varios pagos (Financieros)</label>
                            </div>
                        </div>

                        <!-- Contenedores Dinámicos -->
                        <!-- Contenedor: Un Solo Pago -->
                        <div id="containerUnico" class="row g-3 p-3 bg-light rounded-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-medium text-muted">Costo Total ($) *</label>
                                <input type="number" step="0.01" class="form-control shadow-none"
                                    name="costo_total_unico" value="0.00">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-medium text-muted">Abono Inicial ($)</label>
                                <input type="number" step="0.01" class="form-control shadow-none"
                                    name="pago_inicial_unico" value="0.00">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-medium text-muted">¿Es recurrente?</label>
                                <select class="form-select shadow-none" name="es_recurrente" id="esRecurrenteUnico">
                                    <option value="0">No, es único</option>
                                    <option value="1">Sí, recurrente</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="boxFrecuenciaUnico" style="display: none;">
                                <label class="form-label small fw-medium text-muted">Frecuencia de Cobro</label>
                                <select class="form-select shadow-none" name="frecuencia_pago_unico">
                                    <option value="quincenal">Quincenal</option>
                                    <option value="mensual">Mensual</option>
                                    <option value="semestral">Semestral</option>
                                    <option value="anual">Anual</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="boxDateUnico" style="display: none;">
                                <label class="form-label small fw-medium text-muted">Próximo Pago</label>
                                <input type="date" class="form-control shadow-none" name="fecha_proximo_pago_unico">
                            </div>
                        </div>

                        <!-- Contenedor: Varios Pagos -->
                        <div id="containerVarios" class="row g-3 p-3 bg-light rounded-3" style="display: none;">
                            <div class="col-md-4">
                                <label class="form-label small fw-medium text-muted">Costo Total ($) *</label>
                                <input type="number" step="0.01" class="form-control shadow-none"
                                    name="costo_total_varios" id="costoTotalVarios" value="0.00">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-medium text-muted">Anticipo ($) *</label>
                                <input type="number" step="0.01" class="form-control shadow-none" name="pago_inicial"
                                    id="anticipoVarios" value="0.00">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-medium text-muted">Mensualidad ($) *</label>
                                <input type="number" step="0.01" class="form-control shadow-none"
                                    name="mensualidad_financiamiento" id="mensualidadVarios" value="0.00">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-medium text-muted">Fecha 1er Pago (Financiado)</label>
                                <input type="date" class="form-control shadow-none" name="fecha_proximo_pago_varios">
                            </div>

                            <div class="col-12 mt-3 d-flex justify-content-between align-items-center border-top pt-3">
                                <div>
                                    <span class="text-secondary small d-block">Total de meses previstos:</span>
                                    <span class="h5 fw-bold text-dark mb-0" id="labelMesesVarios">0 meses</span>
                                    <input type="hidden" name="meses_financiamiento" id="inputMesesVarios" value="0">
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary shadow-sm"
                                    id="btnAmortizacion">Ver Tabla de Amortización</button>
                            </div>
                        </div>

                        <!-- Tabla Amortizacion -->
                        <div id="boxAmortizacion" class="mt-3 table-responsive bg-white border rounded shadow-sm"
                            style="display:none;">
                            <table class="table table-sm table-hover mb-0 text-center">
                                <thead class="table-light text-muted small">
                                    <tr>
                                        <th># Pago</th>
                                        <th>Monto a Pagar</th>
                                        <th>Saldo Restante</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyAmortizacion">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 mt-3 bg-light rounded-bottom-4 p-3">
                    <button type="button" class="btn btn-light shadow-none text-secondary fw-medium"
                        data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-custom px-4" id="btnSaveService">Guardar Proyecto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Servicio -->
<div class="modal fade" id="editServiceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Editar Servicio</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editServiceForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="editServiceId">
                    <input type="hidden" name="cliente_id" id="editServiceClientId" value="<?= $cliente['id'] ?>">

                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">1. Pormenores del Proyecto</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-medium text-muted">A. Tipo de Servicio *</label>
                                <select class="form-select shadow-none" name="tipo_servicio" id="editTipoServicio"
                                    required>
                                    <option value="" disabled>Selecciona una opción</option>
                                    <option value="Desarrollo">Desarrollo (Web/Software)</option>
                                    <option value="Asesoría">Asesoría / Consultoría</option>
                                    <option value="Soporte Técnico">Soporte Técnico</option>
                                    <option value="Hosting">Hosting</option>
                                    <option value="Dominio">Dominio</option>
                                    <option value="SSL">Certificado SSL</option>
                                    <option value="Marketing Digital">Marketing Digital</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium text-muted">B. Nombre del Proyecto *</label>
                                <input type="text" class="form-control shadow-none" name="nombre_proyecto"
                                    id="editNombreProyecto" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">2. Finanzas y Pagos</h6>
                        <label class="form-label small fw-medium text-muted">A. ¿Cómo se pagará este servicio? *</label>
                        <div class="d-flex gap-3 mb-3">
                            <div class="form-check">
                                <input class="form-check-input shadow-none" type="radio" name="tipo_pago"
                                    id="editTipoPagoUnico" value="unico">
                                <label class="form-check-label" for="editTipoPagoUnico">Un solo pago</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input shadow-none" type="radio" name="tipo_pago"
                                    id="editTipoPagoVarios" value="varios">
                                <label class="form-check-label" for="editTipoPagoVarios">Varios pagos
                                    (Financieros)</label>
                            </div>
                        </div>

                        <!-- Contenedor Unico -->
                        <div id="editContainerUnico" class="row g-3 p-3 bg-light rounded-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-medium text-muted">Costo Total ($) *</label>
                                <input type="number" step="0.01" class="form-control shadow-none"
                                    name="costo_total_unico" id="editCostoTotalUnico">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-medium text-muted">Abono Inicial ($)</label>
                                <input type="number" step="0.01" class="form-control shadow-none"
                                    name="pago_inicial_unico" id="editPagoInicialUnico">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-medium text-muted">¿Es recurrente?</label>
                                <select class="form-select shadow-none" name="es_recurrente" id="editEsRecurrenteUnico">
                                    <option value="0">No, es único</option>
                                    <option value="1">Sí, recurrente</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="editBoxFrecuenciaUnico" style="display: none;">
                                <label class="form-label small fw-medium text-muted">Frecuencia de Cobro</label>
                                <select class="form-select shadow-none" name="frecuencia_pago_unico"
                                    id="editFrecuenciaUnico">
                                    <option value="quincenal">Quincenal</option>
                                    <option value="mensual">Mensual</option>
                                    <option value="semestral">Semestral</option>
                                    <option value="anual">Anual</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="editBoxDateUnico" style="display: none;">
                                <label class="form-label small fw-medium text-muted">Próximo Pago</label>
                                <input type="date" class="form-control shadow-none" name="fecha_proximo_pago_unico"
                                    id="editFechaUnico">
                            </div>
                        </div>

                        <!-- Contenedor Varios -->
                        <div id="editContainerVarios" class="row g-3 p-3 bg-light rounded-3" style="display: none;">
                            <div class="col-md-4">
                                <label class="form-label small fw-medium text-muted">Costo Total ($) *</label>
                                <input type="number" step="0.01" class="form-control shadow-none"
                                    name="costo_total_varios" id="editCostoTotalVarios">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-medium text-muted">Anticipo ($) *</label>
                                <input type="number" step="0.01" class="form-control shadow-none" name="pago_inicial"
                                    id="editAnticipoVarios">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-medium text-muted">Mensualidad ($) *</label>
                                <input type="number" step="0.01" class="form-control shadow-none"
                                    name="mensualidad_financiamiento" id="editMensualidadVarios">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-medium text-muted">Fecha Pago</label>
                                <input type="date" class="form-control shadow-none" name="fecha_proximo_pago_varios"
                                    id="editFechaVarios">
                            </div>

                            <div class="col-12 mt-3 d-flex justify-content-between align-items-center border-top pt-3">
                                <div>
                                    <span class="text-secondary small d-block">Total de meses previstos:</span>
                                    <span class="h5 fw-bold text-dark mb-0" id="editLabelMesesVarios">0 meses</span>
                                    <input type="hidden" name="meses_financiamiento" id="editInputMesesVarios"
                                        value="0">
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary shadow-sm"
                                    id="editBtnAmortizacion">Ver Tabla de Amortización</button>
                            </div>
                        </div>

                        <!-- Tabla Amortizacion -->
                        <div id="editBoxAmortizacion" class="mt-3 table-responsive bg-white border rounded shadow-sm"
                            style="display:none;">
                            <table class="table table-sm table-hover mb-0 text-center">
                                <thead class="table-light text-muted small">
                                    <tr>
                                        <th># Pago</th>
                                        <th>Monto a Pagar</th>
                                        <th>Saldo Restante</th>
                                    </tr>
                                </thead>
                                <tbody id="editBodyAmortizacion">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 mt-3 bg-light rounded-bottom-4 p-3">
                    <button type="button" class="btn btn-light shadow-none text-secondary fw-medium"
                        data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-custom px-4" id="btnUpdateService">Actualizar Proyecto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Registrar Pago -->
<div class="modal fade" id="newPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Registrar Pago a <span id="paymentServiceName" class="text-primary"></span></h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="paymentForm">
                <div class="modal-body p-4 pt-3">
                    <input type="hidden" name="servicio_id" id="paymentServicioId">

                    <div class="mb-3">
                        <label class="form-label small fw-medium text-muted">Monto Pagado ($) *</label>
                        <input type="number" step="0.01" class="form-control form-control-lg shadow-none text-success fw-bold" name="monto_pagado" id="paymentMonto" required placeholder="0.00">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-muted">Fecha del Pago *</label>
                            <input type="date" class="form-control shadow-none" name="fecha_pago" required value="<?= date('Y-m-d') ?>">
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
                            <input type="text" class="form-control shadow-none" name="referencia" placeholder="Ej: SPEI 12345609">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 mt-2 bg-light rounded-bottom-4 p-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-light shadow-none text-secondary fw-medium" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4" id="btnSavePayment"><i class="bi bi-check-circle me-1"></i> Confirmar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Subir Documento -->
<div class="modal fade" id="newDocumentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Subir Documento o Manual</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="documentForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="cliente_id" value="<?= $cliente['id'] ?>">

                    <div class="bg-light p-4 rounded-4 text-center border border-dashed mb-3 position-relative">
                        <i class="bi bi-cloud-arrow-up display-4 text-primary opacity-50 mb-3 d-block"></i>
                        <h6 class="fw-bold mb-1">Selecciona un archivo</h6>
                        <p class="small text-muted mb-3">PDF, DOCX, TXT, Excel o Markdown permitidos.</p>
                        <input class="form-control shadow-none" type="file" name="documento" id="documentFile" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 mt-3">
                    <button type="button" class="btn btn-light shadow-none text-secondary fw-medium"
                        data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" id="btnSaveDocument">Subir
                        Archivo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Amortizacion (Cards) -->
<div class="modal fade" id="viewAmortizationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Tabla de Amortización</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-3">
                <div class="d-flex justify-content-between mb-3 bg-light p-3 rounded-3 border">
                    <div class="text-center w-100 border-end">
                        <span class="d-block text-secondary small fw-medium">Costo Total</span>
                        <span class="h6 fw-bold text-dark mb-0" id="viewAmortCosto">$0.00</span>
                    </div>
                    <div class="text-center w-100 border-end">
                        <span class="d-block text-secondary small fw-medium">Anticipo</span>
                        <span class="h6 fw-bold text-success mb-0" id="viewAmortAnticipo">$0.00</span>
                    </div>
                    <div class="text-center w-100">
                        <span class="d-block text-secondary small fw-medium">Mensualidad</span>
                        <span class="h6 fw-bold text-primary mb-0" id="viewAmortMensualidad">$0.00</span>
                    </div>
                </div>

                <div class="table-responsive bg-white border rounded shadow-sm" style="max-height: 350px;">
                    <table class="table table-sm table-hover mb-0 text-center sticky-top-table">
                        <thead class="table-light text-muted small" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th># Pago</th>
                                <th>Monto a Pagar</th>
                                <th>Saldo Restante</th>
                            </tr>
                        </thead>
                        <tbody id="viewAmortBody">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 mt-3 p-3 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-secondary shadow-none w-100 fw-medium" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const serviceModal = new bootstrap.Modal(document.getElementById('newServiceModal'));
        const serviceForm = document.getElementById('serviceForm');

        const documentModal = new bootstrap.Modal(document.getElementById('newDocumentModal'));
        const documentForm = document.getElementById('documentForm');

        // UI Dynamics Logic
        const radioUnico = document.getElementById('tipoPagoUnico');
        const radioVarios = document.getElementById('tipoPagoVarios');
        const containerUnico = document.getElementById('containerUnico');
        const containerVarios = document.getElementById('containerVarios');

        const esRecurrenteUnico = document.getElementById('esRecurrenteUnico');
        const boxFrecuenciaUnico = document.getElementById('boxFrecuenciaUnico');

        const inputCostoTotalVarios = document.getElementById('costoTotalVarios');
        const inputAnticipo = document.getElementById('anticipoVarios');
        const inputMensualidad = document.getElementById('mensualidadVarios');
        const labelMeses = document.getElementById('labelMesesVarios');
        const inputMeses = document.getElementById('inputMesesVarios');

        const btnAmortizacion = document.getElementById('btnAmortizacion');
        const boxAmortizacion = document.getElementById('boxAmortizacion');
        const bodyAmortizacion = document.getElementById('bodyAmortizacion');

        function toggleMainContainers() {
            if (radioUnico.checked) {
                containerUnico.style.display = 'flex';
                containerVarios.style.display = 'none';
                boxAmortizacion.style.display = 'none';
            } else {
                containerUnico.style.display = 'none';
                containerVarios.style.display = 'flex';
            }
        }

        radioUnico.addEventListener('change', toggleMainContainers);
        radioVarios.addEventListener('change', toggleMainContainers);

        esRecurrenteUnico.addEventListener('change', (e) => {
            boxFrecuenciaUnico.style.display = e.target.value === '1' ? 'block' : 'none';
        });

        function calcularMeses() {
            const total = parseFloat(inputCostoTotalVarios.value) || 0;
            const anticipo = parseFloat(inputAnticipo.value) || 0;
            const mensualidad = parseFloat(inputMensualidad.value) || 0;

            let meses = 0;
            if (total > anticipo && mensualidad > 0) {
                meses = Math.ceil((total - anticipo) / mensualidad);
            }

            labelMeses.innerText = meses + " meses";
            inputMeses.value = meses;
            boxAmortizacion.style.display = 'none';
        }

        inputCostoTotalVarios.addEventListener('input', calcularMeses);
        inputAnticipo.addEventListener('input', calcularMeses);
        inputMensualidad.addEventListener('input', calcularMeses);

        btnAmortizacion.addEventListener('click', () => {
            const total = parseFloat(inputCostoTotalVarios.value) || 0;
            const anticipo = parseFloat(inputAnticipo.value) || 0;
            const mensualidad = parseFloat(inputMensualidad.value) || 0;

            let restante = total - anticipo;
            // Solo permite hacer click si hay logica sana.
            if (restante <= 0 || mensualidad <= 0) {
                Swal.fire('Atención', 'Asegúrate de configurar el Costo, Anticipo (menor al costo) y Mensualidad.', 'warning');
                return;
            }

            // Primer pago
            bodyAmortizacion.innerHTML = `
                <tr class="table-success">
                    <td>Anticipo (Día 0)</td>
                    <td>$${anticipo.toFixed(2)}</td>
                    <td>$${restante.toFixed(2)}</td>
                </tr>
            `;

            let pagoNum = 1;
            while (restante > 0) {
                let montoPago = mensualidad;
                if (restante < mensualidad) {
                    montoPago = restante;
                }
                restante -= montoPago;
                if (restante < 0.01) restante = 0;

                bodyAmortizacion.innerHTML += `
                    <tr>
                        <td>Mes ${pagoNum}</td>
                        <td>$${montoPago.toFixed(2)}</td>
                        <td>$${restante.toFixed(2)}</td>
                    </tr>
                `;
                pagoNum++;
            }
            // Toggle view
            boxAmortizacion.style.display = boxAmortizacion.style.display === 'none' ? 'block' : 'none';
        });


        serviceForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            // Consolidate 'costo_total' and 'frecuencia_pago' from active schema
            if (radioUnico.checked) {
                formData.set('costo_total', formData.get('costo_total_unico'));
                if (formData.get('es_recurrente') === '1') {
                    formData.set('frecuencia_pago', formData.get('frecuencia_pago_unico') || 'ninguno');
                    formData.set('fecha_proximo_pago', formData.get('fecha_proximo_pago_unico') || '');
                } else {
                    formData.set('frecuencia_pago', 'ninguno');
                    formData.set('fecha_proximo_pago', '');
                }
                formData.set('pago_inicial', formData.get('pago_inicial_unico') || '0');
                formData.set('mensualidad_financiamiento', '0');
                formData.set('meses_financiamiento', '0');
            } else {
                formData.set('costo_total', formData.get('costo_total_varios'));
                formData.set('es_recurrente', '0');
                formData.set('frecuencia_pago', 'ninguno');
                formData.set('fecha_proximo_pago', formData.get('fecha_proximo_pago_varios') || '');
                // The meses_financiamiento is already in the hidden input
            }

            const btnSave = document.getElementById('btnSaveService');
            const originalText = btnSave.innerText;

            btnSave.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';
            btnSave.disabled = true;

            fetch('/vizone/dashboard/clientes/servicio/save', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        serviceModal.hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Servicio Añadido',
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
                    Swal.fire('Error', 'Problema al comunicarse con el servidor.', 'error');
                })
                .finally(() => {
                    btnSave.innerHTML = originalText;
                    btnSave.disabled = false;
                });
        });

        // Document Upload Logic
        documentForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const btnSave = document.getElementById('btnSaveDocument');
            const originalText = btnSave.innerText;

            btnSave.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Subiendo...';
            btnSave.disabled = true;

            fetch('/vizone/dashboard/clientes/documento/save', {
                method: 'POST',
                body: formData // FormData automatically sets the correct multipart/form-data headers
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        documentModal.hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Documento Subido',
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
                    Swal.fire('Error', 'Problema al comunicarse con el servidor.', 'error');
                })
                .finally(() => {
                    btnSave.innerHTML = originalText;
                    btnSave.disabled = false;
                });
        });

        // View Amortization from Cards
        const viewAmortizationModalEl = document.getElementById('viewAmortizationModal');
        if (viewAmortizationModalEl) {
            const viewAmortizationModal = new bootstrap.Modal(viewAmortizationModalEl);
            document.querySelectorAll('.btn-view-amortization').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const costo = parseFloat(this.getAttribute('data-costo')) || 0;
                    const anticipo = parseFloat(this.getAttribute('data-anticipo')) || 0;
                    const mensualidad = parseFloat(this.getAttribute('data-mensualidad')) || 0;

                    document.getElementById('viewAmortCosto').textContent = '$' + costo.toFixed(2);
                    document.getElementById('viewAmortAnticipo').textContent = '$' + anticipo.toFixed(2);
                    document.getElementById('viewAmortMensualidad').textContent = '$' + mensualidad.toFixed(2);

                    const tbody = document.getElementById('viewAmortBody');
                    let restante = costo - anticipo;

                    tbody.innerHTML = `
                        <tr class="table-success">
                            <td>Anticipo Acordado (Día 0)</td>
                            <td>$${anticipo.toFixed(2)}</td>
                            <td>$${restante.toFixed(2)}</td>
                        </tr>
                    `;

                    let pagoNum = 1;
                    while (restante > 0) {
                        let montoPago = mensualidad;
                        if (restante < mensualidad) {
                            montoPago = restante;
                        }
                        restante -= montoPago;
                        if (restante < 0.01) restante = 0;

                        tbody.innerHTML += `
                            <tr>
                                <td>Mes ${pagoNum}</td>
                                <td>$${montoPago.toFixed(2)}</td>
                                <td class="${restante === 0 ? 'text-success fw-bold' : ''}">$${restante.toFixed(2)}</td>
                            </tr>
                        `;
                        pagoNum++;
                    }
                    viewAmortizationModal.show();
                });
            });
        }

        // Payments Logic: Add Payment
        const newPaymentModalEl = document.getElementById('newPaymentModal');
        let newPaymentModal;
        if (newPaymentModalEl) {
            newPaymentModal = new bootstrap.Modal(newPaymentModalEl);
            const paymentForm = document.getElementById('paymentForm');

            document.querySelectorAll('.btn-add-pago').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.getElementById('paymentServicioId').value = this.getAttribute('data-servicio-id');
                    document.getElementById('paymentServiceName').textContent = this.getAttribute('data-nombre');
                    document.getElementById('paymentMonto').value = '';
                    newPaymentModal.show();
                });
            });

            paymentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btnSave = document.getElementById('btnSavePayment');
                const originalText = btnSave.innerHTML;
                btnSave.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';
                btnSave.disabled = true;

                const formData = new FormData(this);

                fetch('/vizone/dashboard/clientes/pagos/save', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        newPaymentModal.hide();
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
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                })
                .finally(() => {
                    btnSave.innerHTML = originalText;
                    btnSave.disabled = false;
                });
            });
        }

        // Payments Logic: Delete Payment
        document.querySelectorAll('.btn-delete-pago').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const pagoId = this.getAttribute('data-id');

                Swal.fire({
                    title: '¿Revocar este pago?',
                    text: 'Este pago se descontará del saldo liquidado. Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, revocar pago',
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
                                Swal.fire('Revocado', data.message, 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        })
                        .catch(err => Swal.fire('Error', 'No se pudo revocar el pago', 'error'));
                    }
                });
            });
        });

        // Edit Service Logic
        const editServiceModal = new bootstrap.Modal(document.getElementById('editServiceModal'));
        const editServiceForm = document.getElementById('editServiceForm');

        const editRadioUnico = document.getElementById('editTipoPagoUnico');
        const editRadioVarios = document.getElementById('editTipoPagoVarios');
        const editContainerUnico = document.getElementById('editContainerUnico');
        const editContainerVarios = document.getElementById('editContainerVarios');
        const editEsRecurrenteUnico = document.getElementById('editEsRecurrenteUnico');
        const editBoxFrecuenciaUnico = document.getElementById('editBoxFrecuenciaUnico');
        const editBoxDateUnico = document.getElementById('editBoxDateUnico');

        const editInputCostoTotalVarios = document.getElementById('editCostoTotalVarios');
        const editInputAnticipo = document.getElementById('editAnticipoVarios');
        const editInputMensualidad = document.getElementById('editMensualidadVarios');
        const editLabelMeses = document.getElementById('editLabelMesesVarios');
        const editInputMeses = document.getElementById('editInputMesesVarios');

        const editBtnAmortizacion = document.getElementById('editBtnAmortizacion');
        const editBoxAmortizacion = document.getElementById('editBoxAmortizacion');
        const editBodyAmortizacion = document.getElementById('editBodyAmortizacion');

        function toggleEditMainContainers() {
            if (editRadioUnico.checked) {
                editContainerUnico.style.display = 'flex';
                editContainerVarios.style.display = 'none';
                editBoxAmortizacion.style.display = 'none';
            } else {
                editContainerUnico.style.display = 'none';
                editContainerVarios.style.display = 'flex';
            }
        }

        editRadioUnico.addEventListener('change', toggleEditMainContainers);
        editRadioVarios.addEventListener('change', toggleEditMainContainers);

        editEsRecurrenteUnico.addEventListener('change', (e) => {
            editBoxFrecuenciaUnico.style.display = e.target.value === '1' ? 'block' : 'none';
            editBoxDateUnico.style.display = e.target.value === '1' ? 'block' : 'none';
        });

        function editCalcularMeses() {
            const total = parseFloat(editInputCostoTotalVarios.value) || 0;
            const anticipo = parseFloat(editInputAnticipo.value) || 0;
            const mensualidad = parseFloat(editInputMensualidad.value) || 0;

            let meses = 0;
            if (total > anticipo && mensualidad > 0) {
                meses = Math.ceil((total - anticipo) / mensualidad);
            }

            editLabelMeses.innerText = meses + " meses";
            editInputMeses.value = meses;
            editBoxAmortizacion.style.display = 'none';
        }

        editInputCostoTotalVarios.addEventListener('input', editCalcularMeses);
        editInputAnticipo.addEventListener('input', editCalcularMeses);
        editInputMensualidad.addEventListener('input', editCalcularMeses);

        editBtnAmortizacion.addEventListener('click', () => {
            const total = parseFloat(editInputCostoTotalVarios.value) || 0;
            const anticipo = parseFloat(editInputAnticipo.value) || 0;
            const mensualidad = parseFloat(editInputMensualidad.value) || 0;

            let restante = total - anticipo;
            if (restante <= 0 || mensualidad <= 0) {
                Swal.fire('Atención', 'Asegúrate de configurar el Costo, Anticipo (menor al costo) y Mensualidad.', 'warning');
                return;
            }

            editBodyAmortizacion.innerHTML = `
                <tr class="table-success">
                    <td>Anticipo (Día 0)</td>
                    <td>$${anticipo.toFixed(2)}</td>
                    <td>$${restante.toFixed(2)}</td>
                </tr>
            `;

            let pagoNum = 1;
            while (restante > 0) {
                let montoPago = mensualidad;
                if (restante < mensualidad) {
                    montoPago = restante;
                }
                restante -= montoPago;
                if (restante < 0.01) restante = 0;

                editBodyAmortizacion.innerHTML += `
                    <tr>
                        <td>Mes ${pagoNum}</td>
                        <td>$${montoPago.toFixed(2)}</td>
                        <td>$${restante.toFixed(2)}</td>
                    </tr>
                `;
                pagoNum++;
            }
            editBoxAmortizacion.style.display = editBoxAmortizacion.style.display === 'none' ? 'block' : 'none';
        });

        document.querySelectorAll('.btn-edit-service').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                try {
                    // 1. Get raw data
                    const rawData = JSON.parse(this.getAttribute('data-raw'));

                    // 2. Populate standard fields
                    document.getElementById('editServiceId').value = rawData.id;
                    document.getElementById('editTipoServicio').value = rawData.tipo_servicio;
                    document.getElementById('editNombreProyecto').value = rawData.nombre_proyecto;

                    // 3. Populate conditional fields
                    if (rawData.tipo_pago === 'unico') {
                        editRadioUnico.checked = true;
                        document.getElementById('editCostoTotalUnico').value = rawData.costo_total;
                        document.getElementById('editPagoInicialUnico').value = rawData.pago_inicial;

                        if (rawData.es_recurrente == 1) {
                            editEsRecurrenteUnico.value = "1";
                            document.getElementById('editFrecuenciaUnico').value = rawData.frecuencia_pago;
                            document.getElementById('editFechaUnico').value = rawData.fecha_proximo_pago;
                            editBoxFrecuenciaUnico.style.display = 'block';
                            editBoxDateUnico.style.display = 'block';
                        } else {
                            editEsRecurrenteUnico.value = "0";
                            document.getElementById('editFrecuenciaUnico').value = "mensual"; // default
                            document.getElementById('editFechaUnico').value = "";
                            editBoxFrecuenciaUnico.style.display = 'none';
                            editBoxDateUnico.style.display = 'none';
                        }

                    } else {
                        editRadioVarios.checked = true;
                        editInputCostoTotalVarios.value = rawData.costo_total;
                        editInputAnticipo.value = rawData.pago_inicial;
                        editInputMensualidad.value = rawData.mensualidad_financiamiento;
                        document.getElementById('editFechaVarios').value = rawData.fecha_proximo_pago;
                        editCalcularMeses();
                    }

                    toggleEditMainContainers();
                    editServiceModal.show();
                } catch (err) {
                    console.error("Error parsing service data: ", err);
                    Swal.fire('Error', 'No se pudieron cargar los datos de este servicio.', 'error');
                }
            });
        });

        editServiceForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            if (editRadioUnico.checked) {
                formData.set('costo_total', formData.get('costo_total_unico'));
                if (formData.get('es_recurrente') === '1') {
                    formData.set('frecuencia_pago', formData.get('frecuencia_pago_unico') || 'ninguno');
                    formData.set('fecha_proximo_pago', formData.get('fecha_proximo_pago_unico') || '');
                } else {
                    formData.set('frecuencia_pago', 'ninguno');
                    formData.set('fecha_proximo_pago', '');
                }
                formData.set('pago_inicial', formData.get('pago_inicial_unico') || '0');
                formData.set('mensualidad_financiamiento', '0');
                formData.set('meses_financiamiento', '0');
            } else {
                formData.set('costo_total', formData.get('costo_total_varios'));
                formData.set('es_recurrente', '0');
                formData.set('frecuencia_pago', 'ninguno');
                formData.set('fecha_proximo_pago', formData.get('fecha_proximo_pago_varios') || '');
            }

            const btnSave = document.getElementById('btnUpdateService');
            const originalText = btnSave.innerText;

            btnSave.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';
            btnSave.disabled = true;

            fetch('/vizone/dashboard/clientes/servicio/update', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        editServiceModal.hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Servicio Actualizado',
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
                    Swal.fire('Error', 'Problema al comunicarse con el servidor.', 'error');
                })
                .finally(() => {
                    btnSave.innerHTML = originalText;
                    btnSave.disabled = false;
                });
        });

        // Delete Service Logic
        const deleteButtons = document.querySelectorAll('.btn-delete-service');
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const serviceId = this.getAttribute('data-id');

                Swal.fire({
                    title: '¿Eliminar este servicio?',
                    text: 'Esta acción no se puede deshacer y eliminará sus finanzas e historial asociado.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData();
                        formData.append('id', serviceId);

                        fetch('/vizone/dashboard/clientes/servicio/delete', {
                            method: 'POST',
                            body: formData
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '¡Eliminado!',
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
                                Swal.fire('Error', 'Problema al comunicarse con el servidor.', 'error');
                            });
                    }
                });
            });
        });

    });
</script>