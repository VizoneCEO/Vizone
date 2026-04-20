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
            <!-- Botón para Editar Info General -->
            <button class="btn btn-light border text-secondary shadow-sm" data-bs-toggle="modal" data-bs-target="#editClientProfileModal"><i class="bi bi-gear"></i> Ajustes</button>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- COLUMNA PRINCIPAL: Servicios y Pagos (Pantalla Completa) -->
    <div class="col-12">

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
                                    'fecha_proximo_pago' => $srv['fecha_proximo_pago'] ?? '',
                                    'incluye_iva' => $srv['incluye_iva'] ?? 0
                                ]), ENT_QUOTES, 'UTF-8');
                                ?>
                                <li><a class="dropdown-item small text-secondary btn-edit-service" href="#" data-raw="<?= $svcDataRaw ?>"><i
                                            class="bi bi-pencil me-2 text-primary"></i>Editar</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item small text-success btn-add-pago" href="#" 
                                        data-servicio-id="<?= $srv['id'] ?>"
                                        data-nombre="<?= htmlspecialchars($srv['nombre_proyecto']) ?>"
                                        data-tipo="<?= $srv['tipo_pago'] ?>"
                                        data-anticipo="<?= $srv['pago_inicial'] * (($srv['incluye_iva'] ?? 0) ? 1.16 : 1) ?>"
                                        data-iva="<?= $srv['incluye_iva'] ?? 0 ?>"
                                        data-amortizaciones='<?= htmlspecialchars(json_encode($srv['amortizaciones'] ?? []), ENT_QUOTES, 'UTF-8') ?>'>
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
                                    <?php 
                                        $costo_base = $srv['costo_total'];
                                        $iva_monto = 0;
                                        if (($srv['incluye_iva'] ?? 0) == 1) {
                                            $iva_monto = $costo_base * 0.16;
                                        }
                                        $total_general = $costo_base + $iva_monto;
                                        $restante = max(0, $total_general - $srv['total_pagado']);
                                    ?>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-secondary small">Subtotal:</span>
                                        <span class="fw-semibold text-dark">$<?= number_format($costo_base, 2) ?></span>
                                    </div>
                                    <?php if ($iva_monto > 0): ?>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-secondary small">I.V.A (16%):</span>
                                        <span class="fw-semibold text-primary">+$<?= number_format($iva_monto, 2) ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-secondary small fw-bold">Costo Neto:</span>
                                        <span class="fw-bold text-dark">$<?= number_format($total_general, 2) ?></span>
                                    </div>

                                    <div class="d-flex justify-content-between mb-1 mt-2">
                                        <span class="text-secondary small">Anticipo Original:</span>
                                        <span class="text-secondary fw-medium">$<?= number_format($srv['pago_inicial'] * ($srv['incluye_iva'] ? 1.16 : 1), 2) ?></span>
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
                                            class="<?= $restante > 0 ? 'text-danger' : 'text-success' ?> fw-medium">
                                            $<?= number_format($restante, 2) ?>
                                        </span>
                                    </div>

                                    <?php if ($restante <= 0): ?>
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
                                                class="h5 fw-bold text-dark mb-0">$<?= number_format($srv['mensualidad_financiamiento'] * ($srv['incluye_iva'] ? 1.16 : 1), 2) ?>
                                                <span class="text-muted fs-6 fw-normal">/ mes <?= $srv['incluye_iva'] ? '(IVA incl.)' : '' ?></span>
                                            </span>
                                            
                                            <?php 
                                            // Vencimientos alert
                                            $vencidas = 0;
                                            if (!empty($srv['amortizaciones'])) {
                                                foreach($srv['amortizaciones'] as $am) {
                                                    if($am['es_vencido']) $vencidas++;
                                                }
                                            }
                                            if ($vencidas > 0): ?>
                                                <div class="alert alert-danger py-1 px-2 mt-2 mb-1 small d-inline-block shadow-sm">
                                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> <strong><?= $vencidas ?> pago(s) vencido(s)</strong>
                                                </div>
                                            <?php endif; ?>

                                            <?php
                                            // Calcular dinámicamente el próximo pago pendiente o vencido
                                            $proximo_pago_fecha = null;
                                            $proximo_pago_vencido = false;
                                            if (!empty($srv['amortizaciones'])) {
                                                foreach ($srv['amortizaciones'] as $am) {
                                                    if ($am['estado'] !== 'pagado') {
                                                        $proximo_pago_fecha = $am['fecha_esperada'];
                                                        $proximo_pago_vencido = (bool)$am['es_vencido'];
                                                        break; // primera pendiente/vencida
                                                    }
                                                }
                                            }
                                            // fallback al campo estático si no hay amortizaciones
                                            if (!$proximo_pago_fecha && !empty($srv['fecha_proximo_pago'])) {
                                                $proximo_pago_fecha = $srv['fecha_proximo_pago'];
                                            }
                                            ?>
                                            <?php if ($proximo_pago_fecha): ?>
                                                <span class="d-block <?= $proximo_pago_vencido ? 'text-danger fw-bold' : 'text-primary' ?> small mt-1">
                                                    <i class="bi bi-calendar<?= $proximo_pago_vencido ? '-x' : '' ?> me-1"></i>
                                                    <?= $proximo_pago_vencido ? 'Vencido: ' : 'Próximo Pago: ' ?>
                                                    <?= htmlspecialchars($proximo_pago_fecha) ?>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <!-- Boton para Amortizacion Card -->
                                            <button class="btn btn-sm btn-outline-primary mt-2 shadow-sm w-100 btn-view-amortization" 
                                                    data-costo="<?= $srv['costo_total'] ?>"
                                                    data-anticipo="<?= $srv['pago_inicial'] ?>"
                                                    data-mensualidad="<?= $srv['mensualidad_financiamiento'] ?>"
                                                    data-iva="<?= $srv['incluye_iva'] ?>"
                                                    data-fecha="<?= $srv['fecha_proximo_pago'] ?>"
                                                    data-amortizaciones="<?= htmlspecialchars(json_encode($srv['amortizaciones'] ?? [])) ?>">
                                                <i class="bi bi-table me-1"></i> Ver Tabla de Amortización
                                            </button>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($srv['es_recurrente'] && $srv['frecuencia_pago'] !== 'ninguno'): ?>
                                        <div class="mt-3">
                                            <span class="d-block text-secondary small mb-1">Cobros Recurrentes Programados</span>
                                            <span class="h6 fw-bold text-dark mb-0"><i
                                                    class="bi bi-arrow-repeat text-primary me-1"></i>Cobro
                                                <?= ucfirst($srv['frecuencia_pago']) ?></span>
                                            <?php
                                            // Calcular vencidos y próximo pendiente desde amortizaciones
                                            $rec_vencidos = 0;
                                            $rec_proximo_fecha = null;
                                            $rec_proximo_vencido = false;
                                            if (!empty($srv['amortizaciones'])) {
                                                foreach ($srv['amortizaciones'] as $am) {
                                                    if ($am['es_vencido']) $rec_vencidos++;
                                                    if ($am['estado'] !== 'pagado' && !$rec_proximo_fecha) {
                                                        $rec_proximo_fecha = $am['fecha_esperada'];
                                                        $rec_proximo_vencido = (bool)$am['es_vencido'];
                                                    }
                                                }
                                            }
                                            if (!$rec_proximo_fecha && !empty($srv['fecha_proximo_pago'])) {
                                                $rec_proximo_fecha = $srv['fecha_proximo_pago'];
                                            }
                                            ?>
                                            <?php if ($rec_vencidos > 0): ?>
                                                <div class="alert alert-danger py-1 px-2 mt-2 mb-1 small d-inline-block shadow-sm">
                                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> <strong><?= $rec_vencidos ?> cobro(s) vencido(s)</strong>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($rec_proximo_fecha): ?>
                                                <span class="d-block <?= $rec_proximo_vencido ? 'text-danger fw-bold' : 'text-primary' ?> small mt-1">
                                                    <i class="bi bi-calendar<?= $rec_proximo_vencido ? '-x' : '' ?> me-1"></i>
                                                    <?= $rec_proximo_vencido ? 'Vencido: ' : 'Próximo Cobro: ' ?>
                                                    <?= htmlspecialchars($rec_proximo_fecha) ?>
                                                </span>
                                            <?php endif; ?>
                                            <!-- Botón para Ver Cobros Programados -->
                                            <button class="btn btn-sm btn-outline-primary mt-2 shadow-sm w-100 btn-view-amortization"
                                                    data-costo="<?= $srv['costo_total'] ?>"
                                                    data-anticipo="0"
                                                    data-mensualidad="<?= $srv['costo_total'] ?>"
                                                    data-iva="<?= $srv['incluye_iva'] ?>"
                                                    data-fecha="<?= $srv['fecha_proximo_pago'] ?>"
                                                    data-recurrente="1"
                                                    data-frecuencia="<?= htmlspecialchars($srv['frecuencia_pago']) ?>"
                                                    data-amortizaciones="<?= htmlspecialchars(json_encode($srv['amortizaciones'] ?? [])) ?>">
                                                <i class="bi bi-calendar-range me-1"></i> Ver Cobros Programados
                                            </button>
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
                                                                    <th>Concepto</th>
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
                                                                        <td class="text-primary fw-medium"><?= htmlspecialchars($pago['concepto'] ?? 'Abono General') ?></td>
                                                                        <td class="text-success fw-medium">$<?= number_format($pago['monto_pagado'], 2) ?></td>
                                                                        <td><?= htmlspecialchars($pago['metodo_pago']) ?></td>
                                                                        <td class="text-muted"><?= htmlspecialchars($pago['referencia'] ?: '--') ?></td>
                                                                        <td class="text-center">
                                                                            <div class="d-flex gap-1 justify-content-center align-items-center flex-nowrap">
                                                                                <!-- Editar Pago -->
                                                                                <button class="btn btn-sm btn-outline-warning py-0 px-2 shadow-none btn-edit-pago"
                                                                                        title="Editar Pago"
                                                                                        data-id="<?= $pago['id'] ?>"
                                                                                        data-monto="<?= $pago['monto_pagado'] ?>"
                                                                                        data-concepto="<?= htmlspecialchars($pago['concepto'] ?? '') ?>"
                                                                                        data-fecha="<?= $pago['fecha_pago'] ?>"
                                                                                        data-metodo="<?= htmlspecialchars($pago['metodo_pago'] ?? '') ?>"
                                                                                        data-referencia="<?= htmlspecialchars($pago['referencia'] ?? '') ?>">
                                                                                    <i class="bi bi-pencil-fill"></i>
                                                                                </button>
                                                                                <!-- Comprobante (siempre visible) -->
                                                                                <?php if (!empty($pago['comprobante_url'])): ?>
                                                                                    <a href="<?= '/vizone/back/uploads/' . $pago['comprobante_url'] ?>" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2 shadow-none" title="Ver Comprobante">
                                                                                        <i class="bi bi-file-earmark-image"></i>
                                                                                    </a>
                                                                                <?php else: ?>
                                                                                    <button class="btn btn-sm btn-outline-secondary py-0 px-2 shadow-none btn-upload-comprobante"
                                                                                            data-pago-id="<?= $pago['id'] ?>" title="Subir Comprobante de Pago">
                                                                                        <i class="bi bi-file-earmark-image text-primary opacity-50"></i>
                                                                                    </button>
                                                                                <?php endif; ?>
                                                                                <!-- Factura PDF -->
                                                                                <?php if (!empty($pago['factura_pdf_url'])): ?>
                                                                                    <a href="<?= '/vizone/back/uploads/' . $pago['factura_pdf_url'] ?>" target="_blank" class="btn btn-sm btn-outline-danger py-0 px-2 shadow-none" title="Ver Factura PDF">
                                                                                        <i class="bi bi-file-earmark-pdf"></i>
                                                                                    </a>
                                                                                <?php else: ?>
                                                                                    <button class="btn btn-sm btn-outline-secondary py-0 px-2 shadow-none btn-upload-factura" 
                                                                                            data-pago-id="<?= $pago['id'] ?>" data-tipo="pdf" title="Subir Factura PDF">
                                                                                        <i class="bi bi-file-earmark-pdf text-danger opacity-50"></i>
                                                                                    </button>
                                                                                <?php endif; ?>
                                                                                <!-- Factura XML -->
                                                                                <?php if (!empty($pago['factura_xml_url'])): ?>
                                                                                    <a href="<?= '/vizone/back/uploads/' . $pago['factura_xml_url'] ?>" target="_blank" class="btn btn-sm btn-outline-success py-0 px-2 shadow-none" title="Ver XML CFDI">
                                                                                        <i class="bi bi-filetype-xml"></i>
                                                                                    </a>
                                                                                <?php else: ?>
                                                                                    <button class="btn btn-sm btn-outline-secondary py-0 px-2 shadow-none btn-upload-factura" 
                                                                                            data-pago-id="<?= $pago['id'] ?>" data-tipo="xml" title="Subir XML CFDI">
                                                                                        <i class="bi bi-filetype-xml text-success opacity-50"></i>
                                                                                    </button>
                                                                                <?php endif; ?>
                                                                                <!-- Eliminar -->
                                                                                <button class="btn btn-sm text-danger shadow-none btn-delete-pago py-0 px-2" data-id="<?= $pago['id'] ?>" title="Revocar Pago">
                                                                                    <i class="bi bi-x-circle-fill"></i>
                                                                                </button>
                                                                            </div>
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

    <!-- FILA INFERIOR: Credenciales y Documentos -->
    <div class="col-12">
        <div class="row g-4">
            <!-- Credenciales Portal del Cliente -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 bg-dark text-white text-center p-4 h-100"
                    style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                    <i class="bi bi-shield-lock display-5 text-primary mb-2"></i>
                    <h6 class="fw-bold">Acceso Visión</h6>
                    <p class="small text-white-50 mb-3">Las credenciales que el cliente usa para entrar aquí.</p>
                    <div class="bg-black bg-opacity-25 rounded-3 p-2 font-monospace text-info">
                        User: <?= htmlspecialchars($cliente['username']) ?>
                    </div>
                    <button class="btn btn-sm btn-outline-light mt-3 rounded-pill px-3 opacity-75" id="btnResetPassword">Reestablecer Password</button>
                </div>
            </div>

            <!-- Archivos y Manuales -->
            <div class="col-md-9">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-richtext text-primary me-2"></i>Archivos y Manuales</h6>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php if (empty($cliente['documentos'])): ?>
                                <li class="list-group-item text-center p-4 text-muted small border-bottom-0 rounded-bottom-4">
                                    No hay manuales cargados.
                                </li>
                            <?php else: ?>
                                <?php foreach ($cliente['documentos'] as $doc): ?>
                                    <li class="list-group-item px-4 py-3 d-flex justify-content-between align-items-center header-menu shadow-hover-sm transition-all">
                                        <div class="text-truncate flex-grow-1 me-3">
                                            <h6 class="mb-0 text-dark fs-6 text-truncate"><i class="bi bi-file-pdf text-danger me-2"></i><?= htmlspecialchars($doc['nombre_original']) ?></h6>
                                            <small class="text-muted"><?= date('d/m/Y', strtotime($doc['uploaded_at'])) ?></small>
                                        </div>
                                        <a href="#" class="btn btn-sm btn-light border rounded-circle"><i class="bi bi-download text-primary"></i></a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="card-footer bg-light border-top-0 p-3 rounded-bottom-4 text-center">
                        <button class="btn btn-sm btn-secondary w-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#newDocumentModal"><i class="bi bi-cloud-arrow-up me-1"></i> Subir Archivo</button>
                    </div>
                </div>
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
                        <div class="d-flex justify-content-between align-items-center border-bottom mb-3 pb-2">
                            <h6 class="fw-bold text-primary mb-0">2. Finanzas y Pagos</h6>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input shadow-none" type="checkbox" name="incluye_iva" id="incluyeIva" value="1">
                                <label class="form-check-label small fw-bold text-primary" for="incluyeIva">Aplicar +16% I.V.A.</label>
                            </div>
                        </div>
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
                            <div class="col-12 mt-2 pt-2 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-secondary small fw-medium">Desglose (Subtotal vs IVA)</span>
                                    <div class="text-end">
                                        <span class="d-block small text-muted" id="breakdownUnicoSubtotal">Subtotal: $0.00</span>
                                        <span class="d-block small text-muted text-primary" id="breakdownUnicoIva" style="display:none;">+ I.V.A. (16%): $0.00</span>
                                        <span class="fw-bold text-dark h6 mb-0" id="breakdownUnicoTotal">Costo Neto: $0.00</span>
                                    </div>
                                </div>
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
                            <div class="col-12 mt-2 pt-2 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-secondary small fw-medium">Desglose general del Costo:</span>
                                    <div class="text-end">
                                        <span class="d-block small text-muted" id="breakdownVariosSubtotal">Subtotal: $0.00</span>
                                        <span class="d-block small text-muted text-primary" id="breakdownVariosIva" style="display:none;">+ I.V.A. (16%): $0.00</span>
                                        <span class="fw-bold text-dark h6 mb-0" id="breakdownVariosTotal">Costo Neto: $0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla Amortizacion -->
                        <div id="boxAmortizacion" class="mt-3 table-responsive bg-white border rounded shadow-sm"
                            style="display:none;">
                            <table class="table table-sm table-hover mb-0 text-center">
                                <thead class="table-light text-muted small">
                                    <tr>
                                        <th># Pago</th>
                                        <th>Subtotal</th>
                                        <th>I.V.A (16%)</th>
                                        <th>Total a Pagar</th>
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
                        <div class="d-flex justify-content-between align-items-center border-bottom mb-3 pb-2">
                            <h6 class="fw-bold text-primary mb-0">2. Finanzas y Pagos</h6>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input shadow-none" type="checkbox" name="incluye_iva" id="editIncluyeIva" value="1">
                                <label class="form-check-label small fw-bold text-primary" for="editIncluyeIva">Aplicar +16% I.V.A.</label>
                            </div>
                        </div>
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
                            <div class="col-12 mt-2 pt-2 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-secondary small fw-medium">Desglose (Subtotal vs IVA)</span>
                                    <div class="text-end">
                                        <span class="d-block small text-muted" id="editBreakdownUnicoSubtotal">Subtotal: $0.00</span>
                                        <span class="d-block small text-muted text-primary" id="editBreakdownUnicoIva" style="display:none;">+ I.V.A. (16%): $0.00</span>
                                        <span class="fw-bold text-dark h6 mb-0" id="editBreakdownUnicoTotal">Costo Neto: $0.00</span>
                                    </div>
                                </div>
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
                            <div class="col-12 mt-2 pt-2 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-secondary small fw-medium">Desglose general del Costo:</span>
                                    <div class="text-end">
                                        <span class="d-block small text-muted" id="editBreakdownVariosSubtotal">Subtotal: $0.00</span>
                                        <span class="d-block small text-muted text-primary" id="editBreakdownVariosIva" style="display:none;">+ I.V.A. (16%): $0.00</span>
                                        <span class="fw-bold text-dark h6 mb-0" id="editBreakdownVariosTotal">Costo Neto: $0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla Amortizacion -->
                        <div id="editBoxAmortizacion" class="mt-3 table-responsive bg-white border rounded shadow-sm"
                            style="display:none;">
                            <table class="table table-sm table-hover mb-0 text-center">
                                <thead class="table-light text-muted small">
                                    <tr>
                                        <th># Pago</th>
                                        <th>Subtotal</th>
                                        <th>I.V.A (16%)</th>
                                        <th>Total a Pagar</th>
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

<!-- Modal Editar Pago -->
<div class="modal fade" id="editPagoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2 text-warning"></i>Editar Pago</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-3">
                <input type="hidden" id="editPagoId">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-medium text-muted">Concepto</label>
                        <input type="text" class="form-control shadow-none" id="editPagoConcepto" placeholder="Ej: Pago de Mensualidad">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-medium text-muted">Monto ($)</label>
                        <input type="number" step="0.01" class="form-control shadow-none text-success fw-bold" id="editPagoMonto">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-medium text-muted">Fecha del Pago</label>
                        <input type="date" class="form-control shadow-none" id="editPagoFecha">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-medium text-muted">Método de Pago</label>
                        <select class="form-select shadow-none" id="editPagoMetodo">
                            <option value="Transferencia">Transferencia</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Tarjeta">Tarjeta / Link</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-medium text-muted">Referencia / Folio</label>
                        <input type="text" class="form-control shadow-none" id="editPagoReferencia" placeholder="Ej: SPEI 12345">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 mt-2 bg-light rounded-bottom-4 p-3 d-flex justify-content-between">
                <button type="button" class="btn btn-light shadow-none text-secondary fw-medium" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning px-4" id="btnGuardarEditPago"><i class="bi bi-save me-1"></i> Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cargar Factura / Comprobante -->
<div class="modal fade" id="uploadFacturaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="facturaModalTitle"><i class="bi bi-receipt me-2 text-primary"></i>Cargar Factura al Pago</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-3">
                <input type="hidden" id="facturaPagoId">
                <input type="hidden" id="facturaTipo">

                <div class="mb-3">
                    <label class="form-label fw-medium text-muted small" id="facturaFileLabel"></label>
                    <input type="file" class="form-control shadow-none" id="facturaFileInput">
                    <div class="form-text text-muted" id="facturaFileHelp"></div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 mt-2 bg-light rounded-bottom-4 p-3 d-flex justify-content-between">
                <button type="button" class="btn btn-light shadow-none text-secondary fw-medium" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4" id="btnSaveFactura"><i class="bi bi-cloud-upload me-1"></i> Subir Documento</button>
            </div>
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
                        <label class="form-label small fw-medium text-muted">Abono / Cuota a Pagar *</label>
                        <select class="form-select shadow-none list-group-select" name="amortizacion_concepto" id="paymentConcepto" required>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-medium text-muted">Monto Registrado ($) *</label>
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
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-muted">Referencia (Folio)</label>
                            <input type="text" class="form-control shadow-none" name="referencia" placeholder="Ej: SPEI 12345609">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-muted">Subir Comprobante (Opcional)</label>
                            <input type="file" class="form-control shadow-none form-control-sm mt-1" name="comprobante_file" accept=".pdf, image/*">
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

<!-- Modal Ajustes de Perfil -->
<div class="modal fade" id="editClientProfileModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Ajustes de Perfil Comercial</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editProfileForm">
                <div class="modal-body">
                    <input type="hidden" name="cliente_id" value="<?= $cliente['id'] ?>">
                    <div class="mb-3">
                        <label class="form-label small fw-medium text-muted">Nombre de la Empresa *</label>
                        <input type="text" class="form-control shadow-none" name="nombre_empresa" required value="<?= htmlspecialchars($cliente['nombre_empresa']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium text-muted">Usuario para el Portal * <small>(Si cambia, debe reiniciar sesión)</small></label>
                        <input type="text" class="form-control shadow-none" name="username" required value="<?= htmlspecialchars($cliente['username']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium text-muted">Nombre del Contacto Directo</label>
                        <input type="text" class="form-control shadow-none" name="contacto_principal" value="<?= htmlspecialchars($cliente['contacto_principal'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium text-muted">Teléfono Móvil</label>
                        <input type="text" class="form-control shadow-none" name="telefono" value="<?= htmlspecialchars($cliente['telefono'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium text-muted">Correo Electrónico</label>
                        <input type="email" class="form-control shadow-none" name="email" value="<?= htmlspecialchars($cliente['email'] ?? '') ?>">
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 mt-3 p-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-light shadow-none text-secondary fw-medium" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-custom px-4" id="btnUpdateProfile">Guardar Cambios</button>
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
                                <th>Subtotal</th>
                                <th>I.V.A (16%)</th>
                                <th>Total a Pagar</th>
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
            // NEW FIX
            boxDateUnico.style.display = e.target.value === '1' ? 'block' : 'none';
        });

        const incluyeIva = document.getElementById('incluyeIva');
        
        function actualizarDesgloses() {
            const isChecked = incluyeIva.checked;
            
            // Unico
            let costoUnico = parseFloat(document.querySelector('input[name="costo_total_unico"]').value) || 0;
            let ivaUnico = isChecked ? costoUnico * 0.16 : 0;
            let totalUnico = costoUnico + ivaUnico;
            
            document.getElementById('breakdownUnicoSubtotal').innerText = `Subtotal: $${costoUnico.toFixed(2)}`;
            const labelIvaUnico = document.getElementById('breakdownUnicoIva');
            labelIvaUnico.style.display = isChecked ? 'block' : 'none';
            labelIvaUnico.innerText = `+ I.V.A. (16%): $${ivaUnico.toFixed(2)}`;
            document.getElementById('breakdownUnicoTotal').innerText = `Total: $${totalUnico.toFixed(2)}`;
            
            // Varios
            let costoVarios = parseFloat(document.getElementById('costoTotalVarios').value) || 0;
            let ivaVarios = isChecked ? costoVarios * 0.16 : 0;
            let totalVarios = costoVarios + ivaVarios;
            
            document.getElementById('breakdownVariosSubtotal').innerText = `Subtotal: $${costoVarios.toFixed(2)}`;
            const labelIvaVarios = document.getElementById('breakdownVariosIva');
            labelIvaVarios.style.display = isChecked ? 'block' : 'none';
            labelIvaVarios.innerText = `+ I.V.A. (16%): $${ivaVarios.toFixed(2)}`;
            document.getElementById('breakdownVariosTotal').innerText = `Total: $${totalVarios.toFixed(2)}`;
        }
        
        incluyeIva.addEventListener('change', () => {
            actualizarDesgloses();
        });

        document.querySelector('input[name="costo_total_unico"]').addEventListener('input', actualizarDesgloses);

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
            actualizarDesgloses();
        }

        inputCostoTotalVarios.addEventListener('input', calcularMeses);
        inputAnticipo.addEventListener('input', calcularMeses);
        inputMensualidad.addEventListener('input', calcularMeses);

        btnAmortizacion.addEventListener('click', () => {
            const total = parseFloat(inputCostoTotalVarios.value) || 0;
            const anticipo = parseFloat(inputAnticipo.value) || 0;
            const mensualidad = parseFloat(inputMensualidad.value) || 0;
            const isChecked = incluyeIva.checked;
            const factor_iva = isChecked ? 1.16 : 1.0;
            let fechaStr = document.querySelector('input[name="fecha_proximo_pago_varios"]').value;

            let restante = total - anticipo;
            if (restante <= 0 || mensualidad <= 0) {
                Swal.fire('Atención', 'Asegúrate de configurar el Costo, Anticipo (menor al costo) y Mensualidad.', 'warning');
                return;
            }

            // Primer pago
            bodyAmortizacion.innerHTML = `
                <tr class="table-success">
                    <td>Anticipo (Día 0)</td>
                    <td>$${anticipo.toFixed(2)}</td>
                    <td>$${(isChecked ? anticipo * 0.16 : 0).toFixed(2)}</td>
                    <td>$${(anticipo * factor_iva).toFixed(2)}</td>
                    <td>$${(restante * factor_iva).toFixed(2)}</td>
                </tr>
            `;

            let pagoNum = 1;
            let currentRestante = restante;
            
            // Generate sequence dates
            let fechaObj = fechaStr ? new Date(fechaStr + 'T12:00:00') : new Date();

            while (currentRestante > 0) {
                let montoPago = mensualidad;
                if (currentRestante < mensualidad) {
                    montoPago = currentRestante;
                }
                currentRestante -= montoPago;
                if (currentRestante < 0.01) currentRestante = 0;
                
                let fechaDisplay = fechaObj.toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' });

                bodyAmortizacion.innerHTML += `
                    <tr>
                        <td>Mes ${pagoNum}<br><small class="text-muted">${fechaDisplay}</small></td>
                        <td>$${montoPago.toFixed(2)}</td>
                        <td>$${(isChecked ? montoPago * 0.16 : 0).toFixed(2)}</td>
                        <td>$${(montoPago * factor_iva).toFixed(2)}</td>
                        <td>$${(currentRestante * factor_iva).toFixed(2)}</td>
                    </tr>
                `;
                pagoNum++;
                fechaObj.setMonth(fechaObj.getMonth() + 1);
            }
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
                    const incluyeIva = parseInt(this.getAttribute('data-iva')) === 1;
                    const factor_iva = incluyeIva ? 1.16 : 1.0;
                    const amortRaw = this.getAttribute('data-amortizaciones') || '[]';
                    const esRecurrente = this.getAttribute('data-recurrente') === '1';
                    const frecuencia = this.getAttribute('data-frecuencia') || 'mensual';
                    let amortizaciones = [];
                    try { amortizaciones = JSON.parse(amortRaw); } catch(ex) {}

                    const modalTitle = viewAmortizationModalEl.querySelector('.modal-title');
                    const anticipoRow = viewAmortizationModalEl.querySelector('[id="viewAmortAnticipo"]').closest('.text-center');

                    if (esRecurrente) {
                        if (modalTitle) modalTitle.innerHTML = '<i class="bi bi-arrow-repeat me-2 text-primary"></i>Cobros Recurrentes Programados';
                        if (anticipoRow) anticipoRow.style.display = 'none';
                        document.getElementById('viewAmortCosto').textContent = '$' + (costo * factor_iva).toFixed(2);
                        document.getElementById('viewAmortMensualidad').textContent = frecuencia.charAt(0).toUpperCase() + frecuencia.slice(1);
                    } else {
                        if (modalTitle) modalTitle.textContent = 'Tabla de Amortización';
                        if (anticipoRow) anticipoRow.style.display = '';
                        document.getElementById('viewAmortCosto').textContent = '$' + (costo * factor_iva).toFixed(2);
                        document.getElementById('viewAmortAnticipo').textContent = '$' + (anticipo * factor_iva).toFixed(2);
                        document.getElementById('viewAmortMensualidad').textContent = '$' + (mensualidad * factor_iva).toFixed(2);
                    }

                    const tbody = document.getElementById('viewAmortBody');
                    const restante = costo - anticipo;

                    if (!esRecurrente) {
                        // Fila del anticipo (solo para financiamiento)
                        tbody.innerHTML = `
                            <tr class="table-success">
                                <td><strong>Anticipo Acordado</strong><br><small class="text-muted">Día 0</small></td>
                                <td>$${anticipo.toFixed(2)}</td>
                                <td>$${(incluyeIva ? anticipo * 0.16 : 0).toFixed(2)}</td>
                                <td class="fw-bold">$${(anticipo * factor_iva).toFixed(2)}</td>
                                <td>$${(restante * factor_iva).toFixed(2)}</td>
                            </tr>
                        `;
                    } else {
                        tbody.innerHTML = '';
                    }

                    if (amortizaciones.length > 0) {
                        amortizaciones.forEach(am => {
                            const isPagado  = am.estado === 'pagado';
                            const isVencido = am.es_vencido == 1 || am.estado === 'vencido';
                            const monto     = parseFloat(am.monto_esperado) || mensualidad;
                            const iva       = incluyeIva ? monto * 0.16 : 0;
                            const total     = monto + iva;
                            const saldoRest = parseFloat(am.saldo_restante) || 0;

                            let fechaDisp = '';
                            if (am.fecha_esperada) {
                                fechaDisp = new Date(am.fecha_esperada + 'T12:00:00').toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' });
                            }

                            let rowClass = '';
                            let badge = '';
                            if (isPagado) {
                                rowClass = 'table-success';
                                badge = '<br><span class="badge bg-success mt-1"><i class="bi bi-check-circle me-1"></i>Pagado</span>';
                            } else if (isVencido) {
                                rowClass = 'table-danger';
                                badge = '<br><span class="badge bg-danger mt-1">Cobro Vencido</span>';
                            } else {
                                badge = '<br><span class="badge bg-warning text-dark mt-1">Pendiente</span>';
                            }

                            const rowLabel = esRecurrente ? `Cobro ${am.numero_pago}` : `Mes ${am.numero_pago}`;
                            const saldoCell = esRecurrente
                                ? '<td class="text-muted small text-center">—</td>'
                                : `<td>$${(saldoRest * factor_iva).toFixed(2)}</td>`;

                            tbody.innerHTML += `
                                <tr class="${rowClass}">
                                    <td>${rowLabel}<br><small class="${isVencido && !isPagado ? 'text-danger fw-bold' : 'text-muted'}">${fechaDisp}</small>${badge}</td>
                                    <td>$${monto.toFixed(2)}</td>
                                    <td>$${iva.toFixed(2)}</td>
                                    <td class="fw-bold">$${total.toFixed(2)}</td>
                                    ${saldoCell}
                                </tr>
                            `;
                        });
                    } else if (!esRecurrente) {
                        // Fallback solo para financiamiento si no hay amortizaciones en BD
                        const fechaStr = this.getAttribute('data-fecha') || '';
                        let pagoNum = 1;
                        let currentRestante = restante;
                        let fechaObj = fechaStr ? new Date(fechaStr + 'T12:00:00') : new Date();
                        const now = new Date();

                        while (currentRestante > 0) {
                            let montoPago = mensualidad;
                            if (currentRestante < mensualidad) montoPago = currentRestante;
                            currentRestante -= montoPago;
                            if (currentRestante < 0.01) currentRestante = 0;

                            const isVencido = fechaObj < now;
                            const fechaDisplay = fechaObj.toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' });
                            const vencidoBadge = isVencido ? '<br><span class="badge bg-danger mt-1">Pago Vencido</span>' : '';

                            tbody.innerHTML += `
                                <tr class="${isVencido ? 'table-danger' : ''}">
                                    <td>Mes ${pagoNum}<br><small class="${isVencido ? 'text-danger fw-bold' : 'text-muted'}">${fechaDisplay}</small>${vencidoBadge}</td>
                                    <td>$${montoPago.toFixed(2)}</td>
                                    <td>$${(incluyeIva ? montoPago * 0.16 : 0).toFixed(2)}</td>
                                    <td class="fw-bold">$${(montoPago * factor_iva).toFixed(2)}</td>
                                    <td>$${(currentRestante * factor_iva).toFixed(2)}</td>
                                </tr>
                            `;
                            pagoNum++;
                            fechaObj.setMonth(fechaObj.getMonth() + 1);
                        }
                    } else {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No hay cobros generados aún.</td></tr>';
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
            const selectConcepto = document.getElementById('paymentConcepto');
            const inputMonto = document.getElementById('paymentMonto');

            document.querySelectorAll('.btn-add-pago').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.getElementById('paymentServicioId').value = this.getAttribute('data-servicio-id');
                    document.getElementById('paymentServiceName').textContent = this.getAttribute('data-nombre');
                    inputMonto.value = '';

                    const tipo_pago = this.getAttribute('data-tipo');
                    const anticipo = parseFloat(this.getAttribute('data-anticipo')) || 0;
                    const amortizacionesRaw = this.getAttribute('data-amortizaciones');
                    const has_iva = parseInt(this.getAttribute('data-iva')) === 1;
                    
                    let amortizaciones = [];
                    try { amortizaciones = JSON.parse(amortizacionesRaw); } catch(ex){}

                    selectConcepto.innerHTML = '<option value="" data-monto="">-- Seleccionar Concepto --</option>';

                    if (tipo_pago === 'unico') {
                        selectConcepto.innerHTML += `<option value="unico_0" data-monto="">Pago Único de Servicio</option>`;
                    } else {
                        if (anticipo > 0) {
                            selectConcepto.innerHTML += `<option value="anticipo_0" data-monto="${anticipo.toFixed(2)}">Anticipo Pactado ($${anticipo.toFixed(2)})</option>`;
                        }
                        if (amortizaciones && amortizaciones.length > 0) {
                            amortizaciones.forEach(am => {
                                const m_base = parseFloat(am.monto_esperado);
                                const m_total = m_base * (has_iva ? 1.16 : 1.0);
                                const m_str = m_total.toFixed(2);
                                
                                let fechaDisp = '';
                                if (am.fecha_esperada) {
                                    fechaDisp = new Date(am.fecha_esperada + 'T12:00:00').toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' });
                                }
                                
                                if (am.estado === 'pendiente' || am.estado === 'vencido') {
                                    let label = am.estado === 'vencido' ? `(VENCIDA)` : '';
                                    selectConcepto.innerHTML += `<option value="mes_${am.id}" data-monto="${m_str}">Mensualidad ${am.numero_pago} del ${fechaDisp} por $${m_str} ${label}</option>`;
                                } else {
                                    selectConcepto.innerHTML += `<option value="disabled" disabled>Mensualidad ${am.numero_pago} del ${fechaDisp} por $${m_str} (PAGADA)</option>`;
                                }
                            });
                        }
                    }
                    selectConcepto.innerHTML += `<option value="otro" data-monto="">Abono Libre / Otro Monto</option>`;

                    newPaymentModal.show();
                });
            });

            selectConcepto.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const montoAsociado = selectedOption.getAttribute('data-monto');
                if (montoAsociado) {
                    inputMonto.value = montoAsociado;
                } else {
                    inputMonto.value = '';
                }
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

        // Factura Upload Logic
        const uploadFacturaModalEl = document.getElementById('uploadFacturaModal');
        let uploadFacturaModal;
        if (uploadFacturaModalEl) {
            uploadFacturaModal = new bootstrap.Modal(uploadFacturaModalEl);

            document.querySelectorAll('.btn-upload-factura').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const pagoId = this.getAttribute('data-pago-id');
                    const tipo = this.getAttribute('data-tipo');

                    document.getElementById('facturaPagoId').value = pagoId;
                    document.getElementById('facturaTipo').value = tipo;

                    const fileInput = document.getElementById('facturaFileInput');
                    const label = document.getElementById('facturaFileLabel');
                    const help = document.getElementById('facturaFileHelp');

                    fileInput.value = ''; // reset
                    if (tipo === 'pdf') {
                        fileInput.accept = '.pdf';
                        label.innerHTML = '<i class="bi bi-file-earmark-pdf text-danger me-1"></i>Factura en PDF';
                        help.textContent = 'Solo archivos .PDF de la factura timbrada.';
                    } else {
                        fileInput.accept = '.xml';
                        label.innerHTML = '<i class="bi bi-filetype-xml text-success me-1"></i>CFDI en XML';
                        help.textContent = 'Solo archivos .XML del CFDI timbrado.';
                    }

                    uploadFacturaModal.show();
                });
            });

            document.getElementById('btnSaveFactura').addEventListener('click', function() {
                const pagoId = document.getElementById('facturaPagoId').value;
                const tipo = document.getElementById('facturaTipo').value;
                const fileInput = document.getElementById('facturaFileInput');

                if (!fileInput.files[0]) {
                    Swal.fire('Atención', 'Por favor selecciona un archivo antes de continuar.', 'warning');
                    return;
                }

                const btnSave = this;
                const originalText = btnSave.innerHTML;
                btnSave.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Subiendo...';
                btnSave.disabled = true;

                const formData = new FormData();
                formData.append('pago_id', pagoId);
                formData.append('tipo', tipo);
                formData.append('factura_file', fileInput.files[0]);

                fetch('/vizone/dashboard/clientes/pagos/facturas/save', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        uploadFacturaModal.hide();
                        Swal.fire({
                            icon: 'success',
                            title: '¡Documento Cargado!',
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

            // Comprobante upload (same modal, tipo = 'comprobante')
            document.querySelectorAll('.btn-upload-comprobante').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const pagoId = this.getAttribute('data-pago-id');
                    document.getElementById('facturaPagoId').value = pagoId;
                    document.getElementById('facturaTipo').value = 'comprobante';

                    const fileInput = document.getElementById('facturaFileInput');
                    const label = document.getElementById('facturaFileLabel');
                    const help = document.getElementById('facturaFileHelp');

                    fileInput.value = '';
                    fileInput.accept = '.pdf, image/*';
                    label.innerHTML = '<i class="bi bi-file-earmark-image text-primary me-1"></i>Comprobante de Pago';
                    help.textContent = 'Puedes subir una captura de pantalla o PDF del comprobante.';
                    document.getElementById('facturaModalTitle').innerHTML = '<i class="bi bi-file-earmark-image me-2 text-primary"></i>Subir Comprobante de Pago';

                    uploadFacturaModal.show();
                });
            });
        }

        // Edit Pago Logic
        const editPagoModalEl = document.getElementById('editPagoModal');
        let editPagoModal;
        if (editPagoModalEl) {
            editPagoModal = new bootstrap.Modal(editPagoModalEl);

            document.querySelectorAll('.btn-edit-pago').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.getElementById('editPagoId').value = this.getAttribute('data-id');
                    document.getElementById('editPagoConcepto').value = this.getAttribute('data-concepto');
                    document.getElementById('editPagoMonto').value = this.getAttribute('data-monto');
                    document.getElementById('editPagoFecha').value = this.getAttribute('data-fecha');
                    document.getElementById('editPagoReferencia').value = this.getAttribute('data-referencia');
                    document.getElementById('editPagoMetodo').value = this.getAttribute('data-metodo');
                    editPagoModal.show();
                });
            });

            document.getElementById('btnGuardarEditPago').addEventListener('click', function() {
                const btn = this;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';
                btn.disabled = true;

                const formData = new FormData();
                formData.append('pago_id', document.getElementById('editPagoId').value);
                formData.append('concepto', document.getElementById('editPagoConcepto').value);
                formData.append('monto_pagado', document.getElementById('editPagoMonto').value);
                formData.append('fecha_pago', document.getElementById('editPagoFecha').value);
                formData.append('metodo_pago', document.getElementById('editPagoMetodo').value);
                formData.append('referencia', document.getElementById('editPagoReferencia').value);

                fetch('/vizone/dashboard/clientes/pagos/update', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        editPagoModal.hide();
                        Swal.fire({ icon: 'success', title: 'Pago actualizado', timer: 1200, showConfirmButton: false })
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(() => Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error'))
                .finally(() => { btn.innerHTML = originalText; btn.disabled = false; });
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

        const editIncluyeIva = document.getElementById('editIncluyeIva');
        
        function editActualizarDesgloses() {
            const isChecked = editIncluyeIva.checked;
            
            // Unico
            let costoUnico = parseFloat(document.getElementById('editCostoTotalUnico').value) || 0;
            let ivaUnico = isChecked ? costoUnico * 0.16 : 0;
            let totalUnico = costoUnico + ivaUnico;
            
            document.getElementById('editBreakdownUnicoSubtotal').innerText = `Subtotal: $${costoUnico.toFixed(2)}`;
            const labelIvaUnico = document.getElementById('editBreakdownUnicoIva');
            labelIvaUnico.style.display = isChecked ? 'block' : 'none';
            labelIvaUnico.innerText = `+ I.V.A. (16%): $${ivaUnico.toFixed(2)}`;
            document.getElementById('editBreakdownUnicoTotal').innerText = `Total: $${totalUnico.toFixed(2)}`;
            
            // Varios
            let costoVarios = parseFloat(document.getElementById('editCostoTotalVarios').value) || 0;
            let ivaVarios = isChecked ? costoVarios * 0.16 : 0;
            let totalVarios = costoVarios + ivaVarios;
            
            document.getElementById('editBreakdownVariosSubtotal').innerText = `Subtotal: $${costoVarios.toFixed(2)}`;
            const labelIvaVarios = document.getElementById('editBreakdownVariosIva');
            labelIvaVarios.style.display = isChecked ? 'block' : 'none';
            labelIvaVarios.innerText = `+ I.V.A. (16%): $${ivaVarios.toFixed(2)}`;
            document.getElementById('editBreakdownVariosTotal').innerText = `Total: $${totalVarios.toFixed(2)}`;
        }
        
        editIncluyeIva.addEventListener('change', () => {
            editActualizarDesgloses();
        });

        document.getElementById('editCostoTotalUnico').addEventListener('input', editActualizarDesgloses);


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
            editActualizarDesgloses();
        }

        editInputCostoTotalVarios.addEventListener('input', editCalcularMeses);
        editInputAnticipo.addEventListener('input', editCalcularMeses);
        editInputMensualidad.addEventListener('input', editCalcularMeses);

        editBtnAmortizacion.addEventListener('click', () => {
            const total = parseFloat(editInputCostoTotalVarios.value) || 0;
            const anticipo = parseFloat(editInputAnticipo.value) || 0;
            const mensualidad = parseFloat(editInputMensualidad.value) || 0;
            const isChecked = editIncluyeIva.checked;
            const factor_iva = isChecked ? 1.16 : 1.0;
            let fechaStr = document.getElementById('editFechaVarios').value;

            let restante = total - anticipo;
            if (restante <= 0 || mensualidad <= 0) {
                Swal.fire('Atención', 'Asegúrate de configurar el Costo, Anticipo (menor al costo) y Mensualidad.', 'warning');
                return;
            }

            editBodyAmortizacion.innerHTML = `
                <tr class="table-success">
                    <td>Anticipo (Día 0)</td>
                    <td>$${anticipo.toFixed(2)}</td>
                    <td>$${(isChecked ? anticipo * 0.16 : 0).toFixed(2)}</td>
                    <td>$${(anticipo * factor_iva).toFixed(2)}</td>
                    <td>$${(restante * factor_iva).toFixed(2)}</td>
                </tr>
            `;

            let pagoNum = 1;
            let currentRestante = restante;
            let fechaObj = fechaStr ? new Date(fechaStr + 'T12:00:00') : new Date();

            while (currentRestante > 0) {
                let montoPago = mensualidad;
                if (currentRestante < mensualidad) {
                    montoPago = currentRestante;
                }
                currentRestante -= montoPago;
                if (currentRestante < 0.01) currentRestante = 0;
                
                let fechaDisplay = fechaObj.toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' });

                editBodyAmortizacion.innerHTML += `
                    <tr>
                        <td>Mes ${pagoNum}<br><small class="text-muted">${fechaDisplay}</small></td>
                        <td>$${montoPago.toFixed(2)}</td>
                        <td>$${(isChecked ? montoPago * 0.16 : 0).toFixed(2)}</td>
                        <td>$${(montoPago * factor_iva).toFixed(2)}</td>
                        <td>$${(currentRestante * factor_iva).toFixed(2)}</td>
                    </tr>
                `;
                pagoNum++;
                fechaObj.setMonth(fechaObj.getMonth() + 1);
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
                    if (rawData.incluye_iva == 1) {
                        editIncluyeIva.checked = true;
                    } else {
                        editIncluyeIva.checked = false;
                    }

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

        // Update Profile Logic
        const editProfileForm = document.getElementById('editProfileForm');
        if (editProfileForm) {
            editProfileForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const btnSave = document.getElementById('btnUpdateProfile');
                const originalText = btnSave.innerHTML;
                btnSave.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';
                btnSave.disabled = true;

                fetch('/vizone/dashboard/clientes/update-profile', {
                    method: 'POST',
                    body: new FormData(this)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success', title: 'Perfil Actualizado', text: data.message, showConfirmButton: false, timer: 1500
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(err => Swal.fire('Error', 'Problema al procesar la solicitud.', 'error'))
                .finally(() => { btnSave.innerHTML = originalText; btnSave.disabled = false; });
            });
        }

        // Reset Password Logic
        const btnResetPassword = document.getElementById('btnResetPassword');
        if (btnResetPassword) {
            btnResetPassword.addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Reestablecer Contraseña?',
                    text: 'Se le asignará temporalmente la contraseña "password123". Al intentar acceder, el sistema obligará a cambiarla.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, reestablecer',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData();
                        formData.append('cliente_id', '<?= $cliente['id'] ?>');

                        fetch('/vizone/dashboard/clientes/reset-password', {
                            method: 'POST', body: formData
                        }).then(res => res.json()).then(data => {
                            if (data.success) {
                                Swal.fire('Restablecido', data.message, 'success');
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        }).catch(err => Swal.fire('Error', 'No se pudo conectar', 'error'));
                    }
                });
            });
        }

    });
</script>