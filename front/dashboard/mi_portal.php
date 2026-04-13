<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 fw-bold mb-1 text-dark">Bienvenido, <?= htmlspecialchars($cliente['nombre_empresa']) ?></h1>
            <p class="text-muted small mb-0">Revisa el estado de tus proyectos, finanzas y descargas de archivos.</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- COLUMNA IZQUIERDA: Tarjetas Finanzas y Servicios -->
    <div class="col-lg-8">

        <!-- Header Seccion Proyectos -->
        <div class="d-flex justify-content-between align-items-end mb-3 mt-2">
            <h5 class="fw-bold text-dark mb-0">Servicios Contratados</h5>
        </div>

        <?php if (empty($cliente['servicios'])): ?>
            <div class="card border border-dashed rounded-4 bg-transparent text-center p-5 mb-4">
                <i class="bi bi-rocket-takeoff text-muted opacity-50 display-6 d-block mb-2"></i>
                <p class="text-muted mb-0">Aún no tienes servicios o proyectos activos.</p>
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

                                    <div class="d-flex justify-content-between">
                                        <span class="text-secondary small">Pagado / Anticipo:</span>
                                        <span
                                            class="text-success fw-medium">$<?= number_format($srv['pago_inicial'], 2) ?></span>
                                    </div>
                                    <hr class="my-2 border-secondary opacity-25">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-secondary small">Anticipo Acordado:</span>
                                        <span
                                            class="text-secondary fw-medium">$<?= number_format($srv['pago_inicial'], 2) ?></span>
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
                                                <span class="d-block text-primary small mt-1"><i class="bi bi-calendar me-1"></i>Próximo
                                                    Pago: <?= htmlspecialchars($srv['fecha_proximo_pago']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($srv['es_recurrente'] && $srv['frecuencia_pago'] !== 'ninguno'): ?>
                                        <div class="mt-3">
                                            <span class="d-block text-secondary small mb-1">Renovación de Servicio</span>
                                            <span class="h6 fw-bold text-dark mb-0"><i
                                                    class="bi bi-arrow-repeat text-primary me-1"></i>Cobro
                                                <?= ucfirst($srv['frecuencia_pago']) ?></span>
                                            <?php if (!empty($srv['fecha_proximo_pago'])): ?>
                                                <span class="d-block text-primary small mt-1"><i class="bi bi-calendar me-1"></i>Próxima
                                                    Renovación: <?= htmlspecialchars($srv['fecha_proximo_pago']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($srv['tipo_pago'] === 'unico' && !$srv['es_recurrente']): ?>
                                    <div class="d-flex align-items-center h-100 pb-4">
                                        <span class="text-muted small"><i class="bi bi-info-circle me-1"></i> No tiene cobros
                                            recurrentes activos.</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Historial de Pagos (Accordion) -->
                        <div class="col-12 mt-3">
                            <div class="accordion accordion-flush bg-light rounded-3 border"
                                id="accordionPagos_<?= $srv['id'] ?>">
                                <div class="accordion-item bg-transparent border-0">
                                    <h2 class="accordion-header">
                                        <button
                                            class="accordion-button collapsed bg-transparent shadow-none py-2 text-dark fw-medium small"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#flush-collapsePagos_<?= $srv['id'] ?>">
                                            <i class="bi bi-receipt me-2 text-secondary"></i> Historial de Pagos (<span
                                                class="text-primary mx-1"><?= count($srv['pagos_historial'] ?? []) ?></span>)
                                        </button>
                                    </h2>
                                    <div id="flush-collapsePagos_<?= $srv['id'] ?>" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionPagos_<?= $srv['id'] ?>">
                                        <div class="accordion-body pt-0 pb-3">
                                            <?php if (empty($srv['pagos_historial'])): ?>
                                                <div class="text-center py-3">
                                                    <span class="text-muted small">No se han registrado pagos para este
                                                        servicio.</span>
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
                                                            </tr>
                                                        </thead>
                                                        <tbody class="small align-middle">
                                                            <?php foreach ($srv['pagos_historial'] as $pago): ?>
                                                                <tr>
                                                                    <td><?= htmlspecialchars($pago['fecha_pago']) ?></td>
                                                                    <td class="text-success fw-medium">
                                                                        $<?= number_format($pago['monto_pagado'], 2) ?></td>
                                                                    <td><?= htmlspecialchars($pago['metodo_pago']) ?></td>
                                                                    <td class="text-muted">
                                                                        <?= htmlspecialchars($pago['referencia'] ?: '--') ?></td>
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
        </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>

<!-- COLUMNA DERECHA: Documentos y Accesos -->
<div class="col-lg-4">
    <!-- Info de Soporte -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-dark text-white text-center p-4"
        style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <i class="bi bi-headset display-5 text-primary mb-2"></i>
        <h6 class="fw-bold">Soporte Técnico</h6>
        <p class="small text-white-50 mb-3">¿Tienes algún problema o necesitas asistencia con tu proyecto?</p>
        <a href="/vizone/dashboard/tickets" class="btn btn-sm btn-outline-light rounded-pill px-4 opacity-100">Abrir
            Ticket de Soporte</a>
    </div>

    <!-- Archivos y Manuales -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-richtext text-primary me-2"></i>Tus
                Archivos y Manuales</h6>
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                <?php if (empty($cliente['documentos'])): ?>
                    <li class="list-group-item text-center p-4 text-muted small border-bottom-0 rounded-bottom-4">
                        No hay archivos disponibles en este momento.
                    </li>
                <?php else: ?>
                    <?php foreach ($cliente['documentos'] as $doc): ?>
                        <li
                            class="list-group-item px-4 py-3 d-flex justify-content-between align-items-center header-menu shadow-hover-sm transition-all">
                            <div class="text-truncate flex-grow-1 me-3">
                                <h6 class="mb-0 text-dark fs-6 text-truncate"><i
                                        class="bi bi-file-earmark-check text-success me-2"></i><?= htmlspecialchars($doc['nombre_original']) ?>
                                </h6>
                                <small class="text-muted"><?= date('d/m/Y', strtotime($doc['uploaded_at'])) ?></small>
                            </div>
                            <a href="/vizone/back/uploads/<?= htmlspecialchars($doc['ruta_fisica']) ?>" target="_blank"
                                class="btn btn-sm btn-light border rounded-circle"><i
                                    class="bi bi-download text-primary"></i></a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
        <?php if (!empty($cliente['documentos'])): ?>
            <div class="card-footer bg-light border-top-0 p-3 rounded-bottom-4 text-center">
                <small class="text-muted">Si no puedes descargar un archivo, contáctanos.</small>
            </div>
        <?php endif; ?>
    </div>
</div>
</div>