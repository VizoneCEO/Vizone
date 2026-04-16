<!-- SweetAlert2 + Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<?php
/* ─── Pre-cómputo PHP ─────────────────────────────────────────────── */
$mesActual  = (int)date('n');
$anioActual = (int)date('Y');

$mesesNombres = [
    1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',
    5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',
    9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'
];
$mesesCortos = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

// Estructura: dataByAnio[anio][mes] = { total, count }
$dataByAnio   = [];   // [anio][mes] => ['total'=>0,'count'=>0]
$historialJs  = [];   // para filtrar el historial desde JS
$totalIngresos = 0;

foreach ($pagos as $p) {
    $pAnio = (int)date('Y', strtotime($p['fecha_pago']));
    $pMes  = (int)date('n', strtotime($p['fecha_pago']));
    $totalIngresos += $p['monto_pagado'];

    if (!isset($dataByAnio[$pAnio])) {
        $dataByAnio[$pAnio] = array_fill(1, 12, ['total'=>0,'count'=>0]);
    }
    $dataByAnio[$pAnio][$pMes]['total'] += $p['monto_pagado'];
    $dataByAnio[$pAnio][$pMes]['count']++;

    $historialJs[] = [
        'anio'    => $pAnio,
        'mes'     => $pMes,
        'empresa' => $p['nombre_empresa'],
        'proyecto'=> $p['nombre_proyecto'],
        'concepto'=> $p['concepto'] ?? 'Abono General',
        'monto'   => (float)$p['monto_pagado'],
        'fecha'   => date('d M, Y', strtotime($p['fecha_pago'])),
        'clienteId' => $p['cliente_id'],
    ];
}

krsort($dataByAnio);
$aniosDisponibles = array_keys($dataByAnio);
?>

<!-- ════ SELECTOR DE AÑO (top) ════ -->
<div class="d-flex align-items-center gap-2 mb-4" id="anioSelector">
    <span class="text-muted small fw-semibold me-1"><i class="bi bi-calendar2-range me-1"></i>Año:</span>
    <?php foreach ($aniosDisponibles as $a): ?>
        <button class="btn btn-anio rounded-pill px-4 py-2 fw-bold
            <?= $a === $anioActual ? 'btn-dark text-white' : 'btn-outline-secondary text-muted' ?>"
            data-anio="<?= $a ?>"><?= $a ?></button>
    <?php endforeach; ?>
</div>

<!-- ════ HEADER ════ -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h3 fw-bold mb-1 text-dark">Módulo de Pagos Globales</h1>
        <p class="text-muted small mb-0">Ingresos registrados en la plataforma.</p>
    </div>
</div>

<!-- ════ KPI TOTAL DEL AÑO ════ -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden"
     style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);">
    <div class="card-body p-4 position-relative text-white">
        <i class="bi bi-wallet2 position-absolute" style="font-size:6rem;right:0;bottom:-20px;opacity:0.07;"></i>
        <div class="row align-items-center">
            <div class="col">
                <p class="text-white-50 text-uppercase fw-bold small mb-1">
                    <i class="bi bi-calendar2-check me-1"></i>
                    Total Ingresos · <span id="kpiAnioLabel"><?= $anioActual ?></span>
                </p>
                <h2 class="display-5 fw-bold mb-0 text-success" id="kpiAnioTotal">$0.00</h2>
                <p class="small text-white-50 mt-1 mb-0" id="kpiAnioCount">0 pagos</p>
            </div>
        </div>
    </div>
</div>


<!-- ════ KPIs MES A MES (scroll horizontal) ════ -->
<div class="mb-4">
    <h6 class="fw-bold mb-3 text-muted small text-uppercase">
        <i class="bi bi-calendar3 me-1 text-primary"></i>
        Ingresos mes a mes · <span id="labelAnioMeses"><?= $anioActual ?></span>
    </h6>
    <div class="d-flex gap-3 overflow-auto pb-2" id="mesKpiStrip" style="scrollbar-width:thin;">
        <!-- Generado por JS -->
    </div>
</div>

<!-- ════ GRÁFICA ════ -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
        <h6 class="fw-bold mb-0">
            <i class="bi bi-bar-chart-line text-primary me-2"></i>
            Ingresos mensuales · <span id="labelAnioChart"><?= $anioActual ?></span>
        </h6>
    </div>
    <div class="card-body px-4 pb-4 pt-2">
        <canvas id="ingresoChart" height="90"></canvas>
    </div>
</div>

<!-- ════ BUSCADOR ════ -->
<div class="mb-3">
    <div class="input-group shadow-sm">
        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
        <input type="text" class="form-control bg-white border-start-0 shadow-none ps-0"
            placeholder="Buscar por cliente, proyecto o concepto..." id="searchPagos">
    </div>
</div>

<!-- ════ HISTORIAL DINÁMICO ════ -->
<div id="historialContainer"></div>

<style>
.btn-anio          { transition:.15s; }
.btn-anio.active   { background:#0f172a!important; color:#fff!important; border-color:#0f172a!important; }
.mes-kpi-card      { min-width:130px; border-radius:14px; padding:14px 16px; background:#fff;
                     box-shadow:0 1px 6px rgba(0,0,0,.07); border:2px solid transparent;
                     transition:.15s; flex-shrink:0; cursor:default; }
.mes-kpi-card.active-mes { border-color:#10b981; background:#f0fdf4; }
.mes-kpi-card.has-data:hover { border-color:#6366f1; }
.border-dashed { border-style:dashed!important; border-width:2px!important; border-color:rgba(0,0,0,.08)!important; }
</style>

<script>
(function () {
    /* ── Datos desde PHP ── */
    const DATA_BY_ANIO = <?= json_encode($dataByAnio) ?>;  // {anio:{1:{total,count},...},...}
    const HISTORIAL    = <?= json_encode($historialJs) ?>;
    const MESES_CORTOS = <?= json_encode($mesesCortos) ?>;
    const MESES_NOMBRES = <?= json_encode(array_values($mesesNombres)) ?>; // 0-indexed
    const MES_ACTUAL   = <?= $mesActual ?>;
    const ANIO_ACTUAL  = <?= $anioActual ?>;

    let anioSeleccionado = ANIO_ACTUAL.toString();
    let chartInstance    = null;

    /* ── Chart.js setup ── */
    const ctx = document.getElementById('ingresoChart').getContext('2d');

    function getMonthlyTotals(anio) {
        const anioData = DATA_BY_ANIO[anio] || {};
        return MESES_CORTOS.map((_, i) => {
            const m = i + 1;
            return anioData[m] ? anioData[m].total : 0;
        });
    }
    function getMonthlyCount(anio, mes) {
        const anioData = DATA_BY_ANIO[anio] || {};
        return anioData[mes] ? anioData[mes].count : 0;
    }
    function getMonthlyTotal(anio, mes) {
        const anioData = DATA_BY_ANIO[anio] || {};
        return anioData[mes] ? anioData[mes].total : 0;
    }

    function buildChart(anio) {
        const datos = getMonthlyTotals(anio);
        const cfg = {
            type: 'bar',
            data: {
                labels: MESES_CORTOS,
                datasets: [{
                    label: 'Ingresos ' + anio,
                    data: datos,
                    backgroundColor: datos.map((_, i) => {
                        const m = i + 1;
                        return (anio == ANIO_ACTUAL && m == MES_ACTUAL)
                            ? 'rgba(16,185,129,0.9)' : 'rgba(99,102,241,0.55)';
                    }),
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: c => ' $' + c.parsed.y.toLocaleString('es-MX', { minimumFractionDigits: 2 })
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: { font: { size: 11 }, callback: v => '$' + v.toLocaleString('es-MX') }
                    }
                }
            }
        };
        if (chartInstance) chartInstance.destroy();
        chartInstance = new Chart(ctx, cfg);
    }

    /* ── KPI strip mes a mes ── */
    function buildMesStrip(anio) {
        const strip = document.getElementById('mesKpiStrip');
        strip.innerHTML = '';
        document.getElementById('labelAnioMeses').textContent = anio;

        for (let m = 1; m <= 12; m++) {
            const total    = getMonthlyTotal(anio, m);
            const count    = getMonthlyCount(anio, m);
            const isActual = (parseInt(anio) === ANIO_ACTUAL && m === MES_ACTUAL);
            const hasData  = total > 0;

            // Delta vs mes anterior
            const mPrev = m === 1 ? 12 : m - 1;
            const anioPrev = m === 1 ? parseInt(anio) - 1 : parseInt(anio);
            const totalPrev = getMonthlyTotal(anioPrev, mPrev);
            let deltaHtml = '';
            if (hasData && totalPrev > 0) {
                const delta = ((total - totalPrev) / totalPrev * 100).toFixed(1);
                const upDown = delta >= 0 ? 'up' : 'down';
                const color  = delta >= 0 ? 'text-success' : 'text-danger';
                deltaHtml = `<span class="small ${color}"><i class="bi bi-arrow-${upDown}-short"></i>${Math.abs(delta)}%</span>`;
            }

            const card = document.createElement('div');
            card.className = 'mes-kpi-card' + (isActual ? ' active-mes' : '') + (hasData ? ' has-data' : ' opacity-50');
            card.innerHTML = `
                <p class="fw-bold small mb-1 text-muted text-uppercase" style="font-size:.7rem;">${MESES_NOMBRES[m-1]}</p>
                <p class="fw-bold mb-0 ${hasData ? 'text-dark' : 'text-muted'}" style="font-size:.95rem;">
                    $${total.toLocaleString('es-MX', {minimumFractionDigits:2})}
                </p>
                <p class="small text-muted mb-1">${count} pago${count !== 1 ? 's' : ''}</p>
                ${deltaHtml}
                ${isActual ? '<span class="badge bg-success bg-opacity-10 text-success border border-success-subtle d-block mt-1" style="font-size:.65rem;">Mes actual</span>' : ''}
            `;
            strip.appendChild(card);
        }
    }

    /* ── KPIs del año seleccionado ── */
    function updateAnioKpi(anio) {
        const anioData = DATA_BY_ANIO[anio] || {};
        let totalAnio = 0, countAnio = 0;
        for (let m = 1; m <= 12; m++) {
            if (anioData[m]) { totalAnio += anioData[m].total; countAnio += anioData[m].count; }
        }
        document.getElementById('kpiAnioLabel').textContent  = anio;
        document.getElementById('kpiAnioTotal').textContent  = '$' + totalAnio.toLocaleString('es-MX', {minimumFractionDigits:2});
        document.getElementById('kpiAnioCount').textContent  = countAnio + ' pago' + (countAnio !== 1 ? 's' : '');
        document.getElementById('labelAnioChart').textContent = anio;
    }

    /* ── Historial por mes filtrado por año ── */
    function buildHistorial(anio, searchFilter = '') {
        const container = document.getElementById('historialContainer');
        container.innerHTML = '';
        const filtered = HISTORIAL.filter(p => p.anio == anio);
        if (filtered.length === 0) {
            container.innerHTML = `<div class="card border-0 shadow-sm rounded-4 text-center p-5 text-muted">
                <i class="bi bi-inbox display-4 opacity-25 d-block mb-3"></i>No hay pagos en ${anio}.</div>`;
            return;
        }

        // Agrupar por mes
        const byMes = {};
        filtered.forEach(p => {
            if (!byMes[p.mes]) byMes[p.mes] = [];
            byMes[p.mes].push(p);
        });

        // Ordenar meses descendente
        const mesesOrdenados = Object.keys(byMes).map(Number).sort((a,b) => b - a);

        mesesOrdenados.forEach(mes => {
            const pagosMes = byMes[mes].filter(p => {
                if (!searchFilter) return true;
                const txt = `${p.empresa} ${p.proyecto} ${p.concepto}`.toLowerCase();
                return txt.includes(searchFilter);
            });
            if (pagosMes.length === 0) return;

            const montoMes = pagosMes.reduce((s, p) => s + p.monto, 0);
            const mesNombre = MESES_NOMBRES[mes - 1];

            const rows = pagosMes.map(p => `
                <tr>
                    <td class="ps-4 text-start">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width:34px;height:34px;min-width:34px;"><i class="bi bi-building"></i></div>
                            <span class="fw-semibold text-dark">${p.empresa}</span>
                        </div>
                    </td>
                    <td><span class="badge bg-light text-secondary border px-2 py-1">${p.proyecto}</span></td>
                    <td class="text-primary fw-medium small">${p.concepto}</td>
                    <td><span class="text-success fw-bold fs-6">+$${p.monto.toLocaleString('es-MX', {minimumFractionDigits:2})}</span></td>
                    <td><span class="text-dark">${p.fecha}</span></td>
                    <td class="pe-4">
                        <a href="/vizone/dashboard/cliente/detalles?id=${p.clienteId}"
                           class="btn btn-sm btn-outline-primary border-0 bg-primary bg-opacity-10 text-primary shadow-none"
                           title="Ver Servicios Contratados">
                            <i class="bi bi-briefcase me-1"></i> Servicios
                        </a>
                    </td>
                </tr>`).join('');

            container.innerHTML += `
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-calendar3 text-primary me-2"></i>${mesNombre} ${anio}
                        </h6>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-2 fw-semibold">
                            ${pagosMes.length} pago${pagosMes.length!==1?'s':''} · $${montoMes.toLocaleString('es-MX',{minimumFractionDigits:2})}
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-center">
                                <thead class="table-light text-muted small fw-medium text-uppercase">
                                    <tr>
                                        <th class="ps-4 text-start">Cliente</th>
                                        <th>Proyecto</th>
                                        <th>Concepto</th>
                                        <th>Monto</th>
                                        <th>Fecha</th>
                                        <th class="pe-4">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">${rows}</tbody>
                            </table>
                        </div>
                    </div>
                </div>`;
        });

        if (container.innerHTML === '') {
            container.innerHTML = `<p class="text-center text-muted py-4">Sin resultados para "${searchFilter}".</p>`;
        }
    }

    /* ── Render inicial ── */
    function renderAnio(anio) {
        anioSeleccionado = anio.toString();
        updateAnioKpi(anio);
        buildMesStrip(anio);
        buildChart(anio);
        buildHistorial(anio, document.getElementById('searchPagos').value.toLowerCase().trim());

        // Botones activos
        document.querySelectorAll('.btn-anio').forEach(b => {
            b.classList.toggle('active', b.getAttribute('data-anio') == anio);
            if (b.getAttribute('data-anio') == anio) {
                b.classList.add('btn-dark','text-white');
                b.classList.remove('btn-outline-secondary','text-muted');
            } else {
                b.classList.remove('btn-dark','text-white');
                b.classList.add('btn-outline-secondary','text-muted');
            }
        });
    }

    // Eventos botones de año
    document.querySelectorAll('.btn-anio').forEach(btn => {
        btn.addEventListener('click', () => renderAnio(parseInt(btn.getAttribute('data-anio'))));
    });

    // Buscador
    document.getElementById('searchPagos').addEventListener('keyup', function() {
        buildHistorial(anioSeleccionado, this.value.toLowerCase().trim());
    });

    // Inicial
    renderAnio(ANIO_ACTUAL);

})();
</script>