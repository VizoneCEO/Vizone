<?php
/* ── Helpers de badge ─────────────────────────────────────── */
function tipoBadge($tipo) {
    $map = [
        'sprint'  => ['bg-primary',   'bi-flag-fill',       'Sprint / Hito'],
        'update'  => ['bg-warning text-dark', 'bi-pencil-square', 'Solicitud'],
        'soporte' => ['bg-danger',     'bi-bug-fill',         'Soporte'],
    ];
    [$cls, $ico, $lbl] = $map[$tipo] ?? ['bg-secondary', 'bi-tag', $tipo];
    return "<span class=\"badge {$cls} rounded-pill px-2 py-1\"><i class=\"bi {$ico} me-1\"></i>{$lbl}</span>";
}

function prioridadBadge($p) {
    $map = [
        'baja'    => 'bg-secondary',
        'media'   => 'bg-info text-dark',
        'alta'    => 'bg-warning text-dark',
        'critica' => 'bg-danger',
    ];
    $cls = $map[$p] ?? 'bg-secondary';
    return "<span class=\"badge {$cls}\">" . ucfirst($p) . "</span>";
}

// Agrupar tickets por estado para Kanban
$kanbanCols = [
    'abierto'     => ['label'=>'Abierto',     'color'=>'#3b82f6','icon'=>'bi-circle',         'tickets'=>[]],
    'en_progreso' => ['label'=>'En Progreso', 'color'=>'#f59e0b','icon'=>'bi-arrow-clockwise','tickets'=>[]],
    'revision'    => ['label'=>'Revisión',    'color'=>'#8b5cf6','icon'=>'bi-eye',            'tickets'=>[]],
    'cerrado'     => ['label'=>'Cerrado',     'color'=>'#10b981','icon'=>'bi-check-circle',   'tickets'=>[]],
];
foreach ($tickets as $t) {
    $est = $t['estado'] ?? 'abierto';
    if (isset($kanbanCols[$est])) $kanbanCols[$est]['tickets'][] = $t;
}
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- ════ HEADER ════ -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1 text-dark">Tickets y Soporte</h1>
        <p class="text-muted small mb-0">Sprints de desarrollo, solicitudes de cambio y tickets de soporte.</p>
    </div>
    <button class="btn btn-dark shadow-sm rounded-3 px-3"
            data-bs-toggle="modal" data-bs-target="#newTicketModal">
        <i class="bi bi-plus-circle me-2"></i>Nuevo Ticket
    </button>
</div>

<!-- ════ KPIs ════ -->
<div class="row g-3 mb-4">
    <?php
    $kpiItems = [
        ['icon'=>'bi-ticket-detailed-fill','label'=>'Abiertos',    'val'=>$kpis['abiertos']??0,    'color'=>'#3b82f6'],
        ['icon'=>'bi-arrow-clockwise',     'label'=>'En Progreso', 'val'=>$kpis['en_progreso']??0, 'color'=>'#f59e0b'],
        ['icon'=>'bi-bug-fill',            'label'=>'Soporte',     'val'=>$kpis['soportes']??0,    'color'=>'#ef4444'],
        ['icon'=>'bi-flag-fill',           'label'=>'Sprints',     'val'=>$kpis['sprints']??0,     'color'=>'#6366f1'],
        ['icon'=>'bi-check-circle-fill',   'label'=>'Cerrados',    'val'=>$kpis['cerrados']??0,    'color'=>'#10b981'],
        ['icon'=>'bi-exclamation-triangle-fill','label'=>'Críticos','val'=>$kpis['criticos']??0,  'color'=>'#dc2626'],
    ];
    foreach ($kpiItems as $k): ?>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 text-center py-3 px-2">
                <i class="bi <?= $k['icon'] ?> fs-4 mb-1" style="color:<?= $k['color'] ?>;"></i>
                <div class="fw-bold fs-5 text-dark"><?= $k['val'] ?></div>
                <div class="small text-muted"><?= $k['label'] ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- ════ FILTROS ════ -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body py-3 px-4">
        <form method="GET" action="/vizone/dashboard/tickets" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted fw-semibold mb-1">Tipo</label>
                <select name="tipo" class="form-select form-select-sm shadow-none">
                    <option value="">Todos los tipos</option>
                    <option value="sprint"  <?= ($filtrosActivos['tipo']==='sprint')  ? 'selected':'' ?>>Sprint / Hito</option>
                    <option value="update"  <?= ($filtrosActivos['tipo']==='update')  ? 'selected':'' ?>>Solicitud de Cambio</option>
                    <option value="soporte" <?= ($filtrosActivos['tipo']==='soporte') ? 'selected':'' ?>>Soporte</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted fw-semibold mb-1">Estado</label>
                <select name="estado" class="form-select form-select-sm shadow-none">
                    <option value="">Todos los estados</option>
                    <option value="abierto"     <?= ($filtrosActivos['estado']==='abierto')     ? 'selected':'' ?>>Abierto</option>
                    <option value="en_progreso" <?= ($filtrosActivos['estado']==='en_progreso') ? 'selected':'' ?>>En Progreso</option>
                    <option value="revision"    <?= ($filtrosActivos['estado']==='revision')    ? 'selected':'' ?>>Revisión</option>
                    <option value="cerrado"     <?= ($filtrosActivos['estado']==='cerrado')     ? 'selected':'' ?>>Cerrado</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted fw-semibold mb-1">Cliente</label>
                <select name="cliente_id" class="form-select form-select-sm shadow-none">
                    <option value="">Todos los clientes</option>
                    <?php foreach ($clientes as $cl): ?>
                        <option value="<?= $cl['id'] ?>" <?= ($filtrosActivos['cliente_id']==$cl['id']) ? 'selected':'' ?>>
                            <?= htmlspecialchars($cl['nombre_empresa']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark btn-sm flex-grow-1 shadow-none">
                    <i class="bi bi-funnel me-1"></i>Filtrar
                </button>
                <a href="/vizone/dashboard/tickets" class="btn btn-outline-secondary btn-sm shadow-none">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- ════ KANBAN ════ -->
<?php if (empty($tickets)): ?>
    <div class="card border-0 shadow-sm rounded-4 text-center p-5 text-muted">
        <i class="bi bi-inbox display-4 opacity-25 d-block mb-3"></i>
        No hay tickets. Crea el primero con el botón "Nuevo Ticket".
    </div>
<?php else: ?>
<div class="row g-3" id="kanbanBoard">
    <?php foreach ($kanbanCols as $estadoKey => $col): ?>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background:#f8fafc;">
            <div class="card-header bg-transparent border-0 pt-3 pb-2 px-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle d-inline-block" style="width:10px;height:10px;background:<?= $col['color'] ?>;"></span>
                    <span class="fw-bold small text-dark"><?= $col['label'] ?></span>
                </div>
                <span class="badge rounded-pill" style="background:<?= $col['color'] ?>22;color:<?= $col['color'] ?>;border:1px solid <?= $col['color'] ?>44;">
                    <?= count($col['tickets']) ?>
                </span>
            </div>
            <div class="card-body px-2 pt-0 pb-3 d-flex flex-column gap-2 kanban-col" data-estado="<?= $estadoKey ?>">
                <?php if (empty($col['tickets'])): ?>
                    <div class="text-center text-muted py-4 small opacity-50">
                        <i class="bi bi-inbox d-block fs-4 mb-1"></i>Sin tickets
                    </div>
                <?php endif; ?>
                <?php foreach ($col['tickets'] as $t):
                    $tipoClr = ['sprint'=>'#3b82f6','update'=>'#f59e0b','soporte'=>'#ef4444'][$t['tipo']] ?? '#6b7280';
                    $priClr  = ['baja'=>'#6b7280','media'=>'#06b6d4','alta'=>'#f59e0b','critica'=>'#ef4444'][$t['prioridad']] ?? '#6b7280';
                    $proyNombre = $t['nombre_proyecto'] ?: ($t['proyecto_externo'] ?: '');
                    $fechaLimite = $t['fecha_limite'] ? date('d M Y', strtotime($t['fecha_limite'])) : null;
                    $isVencido = $t['fecha_limite'] && $t['estado'] !== 'cerrado' && strtotime($t['fecha_limite']) < time();
                ?>
                <div class="card border-0 shadow-sm rounded-3 ticket-card"
                     style="border-left:3px solid <?= $tipoClr ?> !important;cursor:pointer;"
                     data-bs-toggle="modal" data-bs-target="#viewTicketModal"
                     data-id="<?= $t['id'] ?>"
                     data-tipo="<?= $t['tipo'] ?>"
                     data-titulo="<?= htmlspecialchars($t['titulo']) ?>"
                     data-descripcion="<?= htmlspecialchars($t['descripcion'] ?? '') ?>"
                     data-prioridad="<?= $t['prioridad'] ?>"
                     data-estado="<?= $t['estado'] ?>"
                     data-porcentaje="<?= $t['porcentaje'] ?>"
                     data-fecha-inicio="<?= $t['fecha_inicio'] ?? '' ?>"
                     data-fecha-limite="<?= $t['fecha_limite'] ?? '' ?>"
                     data-servicio-id="<?= $t['servicio_id'] ?? '' ?>"
                     data-proyecto-externo="<?= htmlspecialchars($t['proyecto_externo'] ?? '') ?>"
                     data-cliente-id="<?= $t['cliente_id'] ?? '' ?>"
                     data-asignado-a="<?= $t['asignado_a'] ?? '' ?>"
                     data-empresa="<?= htmlspecialchars($t['nombre_empresa'] ?? '') ?>"
                     data-proyecto="<?= htmlspecialchars($proyNombre) ?>"
                     data-asignado-nombre="<?= htmlspecialchars($t['asignado_nombre'] ?? '') ?>">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <?= tipoBadge($t['tipo']) ?>
                            <span class="badge rounded-pill" style="background:<?= $priClr ?>22;color:<?= $priClr ?>;border:1px solid <?= $priClr ?>44;font-size:.7rem;">
                                <?= ucfirst($t['prioridad']) ?>
                            </span>
                        </div>
                        <p class="fw-semibold text-dark mb-1 small lh-sm"><?= htmlspecialchars($t['titulo']) ?></p>
                        <?php if ($t['nombre_empresa'] || $proyNombre): ?>
                        <p class="text-muted mb-2" style="font-size:.72rem;">
                            <i class="bi bi-building me-1"></i><?= htmlspecialchars($t['nombre_empresa'] ?? '') ?>
                            <?php if ($proyNombre): ?> · <i class="bi bi-code-slash me-1"></i><?= htmlspecialchars($proyNombre) ?><?php endif; ?>
                        </p>
                        <?php endif; ?>
                        <?php if ($t['tipo'] === 'sprint'): ?>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1" style="font-size:.7rem;">
                                <span class="text-muted">Avance</span>
                                <span class="fw-semibold"><?= $t['porcentaje'] ?>%</span>
                            </div>
                            <div class="progress" style="height:5px;border-radius:99px;">
                                <div class="progress-bar bg-primary" style="width:<?= $t['porcentaje'] ?>%"></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <?php if ($fechaLimite): ?>
                            <span class="small <?= $isVencido ? 'text-danger fw-bold' : 'text-muted' ?>" style="font-size:.7rem;">
                                <i class="bi bi-calendar<?= $isVencido ? '-x' : '' ?> me-1"></i><?= $fechaLimite ?>
                            </span>
                            <?php else: ?><span></span><?php endif; ?>
                            <?php if ($t['asignado_nombre']): ?>
                            <span class="badge bg-light text-secondary border" style="font-size:.65rem;">
                                <i class="bi bi-person me-1"></i><?= htmlspecialchars($t['asignado_nombre']) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>


<!-- ════ MODAL: NUEVO TICKET ════ -->
<div class="modal fade" id="newTicketModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2 text-primary"></i>Nuevo Ticket</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-2 pt-3">
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-muted text-uppercase">Tipo de Ticket *</label>
                    <div class="d-flex gap-2 flex-wrap" id="tipoSelector">
                        <button type="button" class="btn btn-tipo btn-outline-primary rounded-3 px-3 py-2" data-tipo="sprint">
                            <i class="bi bi-flag-fill me-1"></i> Sprint / Hito
                        </button>
                        <button type="button" class="btn btn-tipo btn-outline-warning rounded-3 px-3 py-2" data-tipo="update">
                            <i class="bi bi-pencil-square me-1"></i> Solicitud de Cambio
                        </button>
                        <button type="button" class="btn btn-tipo btn-outline-danger rounded-3 px-3 py-2" data-tipo="soporte">
                            <i class="bi bi-bug-fill me-1"></i> Soporte
                        </button>
                    </div>
                    <input type="hidden" id="newTipo">
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted">Título *</label>
                        <input type="text" class="form-control shadow-none" id="newTitulo" placeholder="Ej: Hito 1 — Login y Registro">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted">Descripción</label>
                        <textarea class="form-control shadow-none" id="newDescripcion" rows="3" placeholder="Detalla los entregables, el error o el cambio solicitado..."></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted">Proyecto</label>
                        <div class="d-flex gap-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input shadow-none" type="radio" name="proyectoTipo" id="rProyectoVizone" value="vizone" checked>
                                <label class="form-check-label small" for="rProyectoVizone">Proyecto Vizone</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input shadow-none" type="radio" name="proyectoTipo" id="rProyectoExterno" value="externo">
                                <label class="form-check-label small" for="rProyectoExterno">Proyecto externo</label>
                            </div>
                        </div>
                        <div id="divServicio">
                            <select class="form-select shadow-none" id="newServicioId">
                                <option value="">— Sin proyecto vinculado —</option>
                                <?php foreach ($serviciosActivos as $srv): ?>
                                    <option value="<?= $srv['id'] ?>" data-cliente-nombre="<?= htmlspecialchars($srv['nombre_empresa']) ?>" data-cliente-id-srv="<?= htmlspecialchars($srv['id']) ?>">
                                        <?= htmlspecialchars($srv['nombre_empresa']) ?> — <?= htmlspecialchars($srv['nombre_proyecto']) ?>
                                        (<?= htmlspecialchars($srv['tipo_servicio']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="divExterno" class="d-none">
                            <input type="text" class="form-control shadow-none" id="newProyectoExterno" placeholder="Nombre del proyecto externo">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Cliente</label>
                        <select class="form-select shadow-none" id="newClienteId">
                            <option value="">— Sin cliente —</option>
                            <?php foreach ($clientes as $cl): ?>
                                <option value="<?= $cl['id'] ?>"><?= htmlspecialchars($cl['nombre_empresa']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Prioridad</label>
                        <select class="form-select shadow-none" id="newPrioridad">
                            <option value="baja">Baja</option>
                            <option value="media" selected>Media</option>
                            <option value="alta">Alta</option>
                            <option value="critica">Crítica</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Asignado a</label>
                        <select class="form-select shadow-none" id="newAsignadoA">
                            <option value="">— Sin asignar —</option>
                            <?php foreach ($admins as $adm): ?>
                                <option value="<?= $adm['id'] ?>"><?= htmlspecialchars($adm['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="sprintFields" class="col-12 d-none">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-muted">Fecha Inicio</label>
                                <input type="date" class="form-control shadow-none" id="newFechaInicio">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-muted">Fecha Límite</label>
                                <input type="date" class="form-control shadow-none" id="newFechaLimite">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-muted">% Avance</label>
                                <input type="number" min="0" max="100" class="form-control shadow-none" id="newPorcentaje" value="0">
                            </div>
                        </div>
                    </div>
                    <div id="fechaFields" class="col-md-6 d-none">
                        <label class="form-label small fw-semibold text-muted">Fecha Límite</label>
                        <input type="date" class="form-control shadow-none" id="newFechaLimiteSimple">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 bg-light rounded-bottom-4 px-4 py-3 d-flex justify-content-between">
                <button class="btn btn-light text-secondary shadow-none" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-dark px-4 shadow-none" id="btnCrearTicket">
                    <i class="bi bi-check-circle me-1"></i> Crear Ticket
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ════ MODAL: VER TICKET ════ -->
<div class="modal fade" id="viewTicketModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div>
                    <div id="viewTicketBadges" class="d-flex gap-2 mb-2"></div>
                    <h5 class="modal-title fw-bold mb-0" id="viewTicketTitulo">—</h5>
                    <p class="text-muted small mt-1 mb-0" id="viewTicketMeta">—</p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="row g-3">
                    <div class="col-12">
                        <p class="small text-muted fw-semibold mb-1">Descripción</p>
                        <div class="bg-light rounded-3 p-3 small" id="viewTicketDesc" style="white-space:pre-wrap;min-height:50px;"></div>
                    </div>
                    <div class="col-12 d-none" id="viewSprintProgress">
                        <p class="small text-muted fw-semibold mb-1">Avance del Sprint</p>
                        <div class="d-flex align-items-center gap-3">
                            <div class="progress flex-grow-1" style="height:10px;border-radius:99px;">
                                <div class="progress-bar bg-primary" id="viewProgressBar" style="width:0%"></div>
                            </div>
                            <span class="fw-bold text-primary" id="viewProgressPct">0%</span>
                        </div>
                        <div class="d-flex justify-content-between mt-1 small text-muted">
                            <span id="viewFechaInicioLabel"></span>
                            <span id="viewFechaLimiteLabel"></span>
                        </div>
                    </div>
                    <div class="col-12">
                        <p class="small text-muted fw-semibold mb-2">Cambiar Estado</p>
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-sm btn-outline-primary  rounded-pill shadow-none btn-cambiar-estado" data-estado="abierto">Abierto</button>
                            <button class="btn btn-sm btn-outline-warning  rounded-pill shadow-none btn-cambiar-estado" data-estado="en_progreso">En Progreso</button>
                            <button class="btn btn-sm btn-outline-secondary rounded-pill shadow-none btn-cambiar-estado" data-estado="revision">Revisión</button>
                            <button class="btn btn-sm btn-outline-success  rounded-pill shadow-none btn-cambiar-estado" data-estado="cerrado">Cerrado</button>
                        </div>
                    </div>
                    <div class="col-12">
                        <p class="small text-muted fw-semibold mb-2"><i class="bi bi-chat-dots me-1"></i>Comentarios</p>
                        <div id="comentariosList" class="d-flex flex-column gap-2 mb-3" style="max-height:200px;overflow-y:auto;"></div>
                        <textarea class="form-control shadow-none form-control-sm" id="newComentario" rows="2" placeholder="Comentario o nota interna..."></textarea>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input shadow-none" type="checkbox" id="esInterno">
                                <label class="form-check-label small text-muted" for="esInterno">Nota interna</label>
                            </div>
                            <button class="btn btn-sm btn-dark shadow-none" id="btnEnviarComentario">
                                <i class="bi bi-send me-1"></i>Enviar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 bg-light rounded-bottom-4 px-4 py-3 d-flex justify-content-between">
                <button class="btn btn-sm btn-outline-danger shadow-none border-0 bg-danger bg-opacity-10" id="btnDeleteTicket">
                    <i class="bi bi-trash3 me-1"></i> Eliminar
                </button>
                <button class="btn btn-dark px-4 shadow-none btn-sm" id="btnAbrirEdit">
                    <i class="bi bi-pencil me-1"></i> Editar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ════ MODAL: EDITAR TICKET ════ -->
<div class="modal fade" id="editTicketModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil me-2 text-warning"></i>Editar Ticket</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-2 pt-3">
                <input type="hidden" id="editTicketId">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Tipo</label>
                        <select class="form-select shadow-none" id="editTipo">
                            <option value="sprint">Sprint / Hito</option>
                            <option value="update">Solicitud de Cambio</option>
                            <option value="soporte">Soporte</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Prioridad</label>
                        <select class="form-select shadow-none" id="editPrioridad">
                            <option value="baja">Baja</option>
                            <option value="media">Media</option>
                            <option value="alta">Alta</option>
                            <option value="critica">Crítica</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted">Título *</label>
                        <input type="text" class="form-control shadow-none" id="editTitulo">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted">Descripción</label>
                        <textarea class="form-control shadow-none" id="editDescripcion" rows="3"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Estado</label>
                        <select class="form-select shadow-none" id="editEstado">
                            <option value="abierto">Abierto</option>
                            <option value="en_progreso">En Progreso</option>
                            <option value="revision">Revisión</option>
                            <option value="cerrado">Cerrado</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">% Avance (Sprint)</label>
                        <input type="number" min="0" max="100" class="form-control shadow-none" id="editPorcentaje" value="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Fecha Inicio</label>
                        <input type="date" class="form-control shadow-none" id="editFechaInicio">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Fecha Límite</label>
                        <input type="date" class="form-control shadow-none" id="editFechaLimite">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Asignado a</label>
                        <select class="form-select shadow-none" id="editAsignadoA">
                            <option value="">— Sin asignar —</option>
                            <?php foreach ($admins as $adm): ?>
                                <option value="<?= $adm['id'] ?>"><?= htmlspecialchars($adm['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 bg-light rounded-bottom-4 px-4 py-3 d-flex justify-content-between">
                <button class="btn btn-light text-secondary shadow-none" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-dark px-4 shadow-none" id="btnGuardarEdit">
                    <i class="bi bi-check-circle me-1"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.ticket-card { transition:.15s; }
.ticket-card:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,0,0,.1)!important; }
.kanban-col { min-height:120px; }
.btn-tipo.active { filter:brightness(.92); font-weight:700; }
</style>

<script>
(function(){
    let ticketActualId = null;
    let ticketActualData = {};

    /* ── Tipo selector (Nuevo ticket) ── */
    document.querySelectorAll('.btn-tipo').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.btn-tipo').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const tipo = this.getAttribute('data-tipo');
            document.getElementById('newTipo').value = tipo;
            document.getElementById('sprintFields').classList.toggle('d-none', tipo !== 'sprint');
            document.getElementById('fechaFields').classList.toggle('d-none', tipo === 'sprint');
        });
    });

    /* Proyecto: vizone vs externo */
    document.querySelectorAll('input[name="proyectoTipo"]').forEach(r => {
        r.addEventListener('change', function() {
            const esExt = this.value === 'externo';
            document.getElementById('divServicio').classList.toggle('d-none', esExt);
            document.getElementById('divExterno').classList.toggle('d-none', !esExt);
        });
    });

    /* Auto-cliente al elegir servicio */
    document.getElementById('newServicioId').addEventListener('change', function() {
        const servicios = <?= json_encode($serviciosActivos) ?>;
        const sid = parseInt(this.value);
        const srv = servicios.find(s => s.id == sid);
        // encontrar cliente en clientes list
        if (srv) {
            const opts = document.getElementById('newClienteId').options;
            for (let o of opts) { if (o.text.startsWith(srv.nombre_empresa)) { o.selected = true; break; } }
        }
    });

    /* ── Crear Ticket ── */
    document.getElementById('btnCrearTicket').addEventListener('click', function() {
        const tipo   = document.getElementById('newTipo').value;
        const titulo = document.getElementById('newTitulo').value.trim();
        if (!tipo)   { Swal.fire('Atención','Selecciona el tipo de ticket.','warning'); return; }
        if (!titulo) { Swal.fire('Atención','El título es obligatorio.','warning'); return; }

        const esExterno = document.getElementById('rProyectoExterno').checked;
        const btn = this;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        btn.disabled = true;

        const fd = new FormData();
        fd.append('tipo',             tipo);
        fd.append('titulo',           titulo);
        fd.append('descripcion',      document.getElementById('newDescripcion').value);
        fd.append('servicio_id',      esExterno ? '' : document.getElementById('newServicioId').value);
        fd.append('proyecto_externo', esExterno ? document.getElementById('newProyectoExterno').value : '');
        fd.append('cliente_id',       document.getElementById('newClienteId').value);
        fd.append('prioridad',        document.getElementById('newPrioridad').value);
        fd.append('asignado_a',       document.getElementById('newAsignadoA').value);
        fd.append('fecha_inicio',     document.getElementById('newFechaInicio').value);
        fd.append('fecha_limite',     tipo === 'sprint'
            ? document.getElementById('newFechaLimite').value
            : document.getElementById('newFechaLimiteSimple').value);
        fd.append('porcentaje',       tipo === 'sprint' ? document.getElementById('newPorcentaje').value : 0);

        fetch('/vizone/dashboard/tickets/save', { method:'POST', body:fd })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon:'success', title:'Ticket creado', timer:1200, showConfirmButton:false })
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(() => Swal.fire('Error','Sin conexión.','error'))
            .finally(() => { btn.innerHTML='<i class="bi bi-check-circle me-1"></i> Crear Ticket'; btn.disabled=false; });
    });

    /* ── Abrir modal Ver Ticket ── */
    document.querySelectorAll('.ticket-card').forEach(card => {
        card.addEventListener('click', function() {
            const d = this.dataset;
            ticketActualId   = d.id;
            ticketActualData = d;

            const tipoMap = { sprint:['bg-primary','Sprint / Hito'], update:['bg-warning text-dark','Solicitud'], soporte:['bg-danger','Soporte'] };
            const priMap  = { baja:'bg-secondary', media:'bg-info text-dark', alta:'bg-warning text-dark', critica:'bg-danger' };
            const [tc, tl] = tipoMap[d.tipo] || ['bg-secondary', d.tipo];
            document.getElementById('viewTicketBadges').innerHTML =
                `<span class="badge ${tc} rounded-pill">${tl}</span>
                 <span class="badge ${priMap[d.prioridad]||'bg-secondary'} rounded-pill">${d.prioridad}</span>`;

            document.getElementById('viewTicketTitulo').textContent = d.titulo;
            const meta = [d.empresa, d.proyecto, d.asignadoNombre ? '@'+d.asignadoNombre : ''].filter(Boolean).join(' · ');
            document.getElementById('viewTicketMeta').textContent = meta || '—';
            document.getElementById('viewTicketDesc').textContent  = d.descripcion || 'Sin descripción.';

            const sp = document.getElementById('viewSprintProgress');
            if (d.tipo === 'sprint') {
                sp.classList.remove('d-none');
                const pct = parseInt(d.porcentaje)||0;
                document.getElementById('viewProgressBar').style.width = pct+'%';
                document.getElementById('viewProgressPct').textContent  = pct+'%';
                document.getElementById('viewFechaInicioLabel').textContent = d.fechaInicio ? 'Inicio: '+d.fechaInicio : '';
                document.getElementById('viewFechaLimiteLabel').textContent = d.fechaLimite ? 'Límite: '+d.fechaLimite : '';
            } else { sp.classList.add('d-none'); }

            document.querySelectorAll('.btn-cambiar-estado').forEach(b =>
                b.classList.toggle('fw-bold', b.getAttribute('data-estado') === d.estado));

            document.getElementById('comentariosList').innerHTML =
                '<p class="text-muted small text-center py-2 opacity-50">Agrega el primer comentario.</p>';
        });
    });

    /* ── Cambiar estado rápido ── */
    document.querySelectorAll('.btn-cambiar-estado').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!ticketActualId) return;
            const fd = new FormData();
            fd.append('ticket_id', ticketActualId);
            fd.append('estado', this.getAttribute('data-estado'));
            fetch('/vizone/dashboard/tickets/estado', { method:'POST', body:fd })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon:'success', title:'Estado actualizado', timer:900, showConfirmButton:false })
                            .then(() => location.reload());
                    } else { Swal.fire('Error', data.message, 'error'); }
                });
        });
    });

    /* ── Comentario ── */
    document.getElementById('btnEnviarComentario').addEventListener('click', function() {
        const msg = document.getElementById('newComentario').value.trim();
        const esInt = document.getElementById('esInterno').checked ? 1 : 0;
        if (!msg || !ticketActualId) return;
        const fd = new FormData();
        fd.append('ticket_id', ticketActualId);
        fd.append('mensaje', msg);
        fd.append('es_interno', esInt);
        fetch('/vizone/dashboard/tickets/comentario/save', { method:'POST', body:fd })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const list = document.getElementById('comentariosList');
                    if (list.querySelector('.opacity-50')) list.innerHTML = '';
                    list.innerHTML += `<div class="rounded-3 p-2 small ${esInt?'bg-warning bg-opacity-10 border border-warning-subtle':'bg-light'}">
                        ${esInt?'<span class="badge bg-warning text-dark me-1" style="font-size:.6rem;">Interno</span>':''}<span class="fw-semibold">Tú:</span> ${msg}</div>`;
                    document.getElementById('newComentario').value = '';
                    document.getElementById('esInterno').checked = false;
                } else { Swal.fire('Error', data.message, 'error'); }
            });
    });

    /* ── Abrir modal editar ── */
    document.getElementById('btnAbrirEdit').addEventListener('click', function() {
        const d = ticketActualData;
        document.getElementById('editTicketId').value    = d.id        || '';
        document.getElementById('editTipo').value        = d.tipo      || 'soporte';
        document.getElementById('editTitulo').value      = d.titulo    || '';
        document.getElementById('editDescripcion').value = d.descripcion || '';
        document.getElementById('editPrioridad').value   = d.prioridad || 'media';
        document.getElementById('editEstado').value      = d.estado    || 'abierto';
        document.getElementById('editPorcentaje').value  = d.porcentaje || 0;
        document.getElementById('editFechaInicio').value = d.fechaInicio || '';
        document.getElementById('editFechaLimite').value = d.fechaLimite || '';
        document.getElementById('editAsignadoA').value   = d.asignadoA  || '';
        const em = new bootstrap.Modal(document.getElementById('editTicketModal'));
        em.show();
    });

    /* ── Guardar edición ── */
    document.getElementById('btnGuardarEdit').addEventListener('click', function() {
        const btn = this;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        btn.disabled = true;
        const fd = new FormData();
        fd.append('ticket_id',    document.getElementById('editTicketId').value);
        fd.append('tipo',         document.getElementById('editTipo').value);
        fd.append('titulo',       document.getElementById('editTitulo').value);
        fd.append('descripcion',  document.getElementById('editDescripcion').value);
        fd.append('prioridad',    document.getElementById('editPrioridad').value);
        fd.append('estado',       document.getElementById('editEstado').value);
        fd.append('porcentaje',   document.getElementById('editPorcentaje').value);
        fd.append('fecha_inicio', document.getElementById('editFechaInicio').value);
        fd.append('fecha_limite', document.getElementById('editFechaLimite').value);
        fd.append('asignado_a',   document.getElementById('editAsignadoA').value);
        fetch('/vizone/dashboard/tickets/update', { method:'POST', body:fd })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon:'success', title:'Ticket actualizado', timer:1100, showConfirmButton:false })
                        .then(() => location.reload());
                } else { Swal.fire('Error', data.message, 'error'); }
            })
            .catch(() => Swal.fire('Error','Sin conexión.','error'))
            .finally(() => { btn.innerHTML='<i class="bi bi-check-circle me-1"></i> Guardar Cambios'; btn.disabled=false; });
    });

    /* ── Eliminar ── */
    document.getElementById('btnDeleteTicket').addEventListener('click', function() {
        if (!ticketActualId) return;
        Swal.fire({
            title:'¿Eliminar ticket?', text:'Esta acción no se puede deshacer.',
            icon:'warning', showCancelButton:true,
            confirmButtonColor:'#dc2626', cancelButtonColor:'#6c757d',
            confirmButtonText:'Sí, eliminar', cancelButtonText:'Cancelar'
        }).then(r => {
            if (!r.isConfirmed) return;
            const fd = new FormData();
            fd.append('ticket_id', ticketActualId);
            fetch('/vizone/dashboard/tickets/delete', { method:'POST', body:fd })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon:'success', title:'Eliminado', timer:900, showConfirmButton:false })
                            .then(() => location.reload());
                    } else { Swal.fire('Error', data.message, 'error'); }
                });
        });
    });
})();
</script>