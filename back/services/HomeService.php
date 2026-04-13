<?php

/**
 * HomeService
 * 
 * Servicio encargado de procesar la lógica de negocio y obtener datos para el HomeController.
 * Se comunica con la Base de Datos o APIs externas.
 */
class HomeService
{

    /**
     * Simula la obtención de los pilares principales del negocio de Vizone.
     * En un entorno real, esto haría consultas (queries) a una base de datos.
     * 
     * @return array Lista de servicios con sus detalles
     */
    public function obtenerPilaresNegocio()
    {

        // Copywriting Nivel "Lamborghini" - Vendiendo el Resultado Supremo
        return [
            [
                'id' => 1,
                'titulo' => 'Máquinas de Crecimiento (Desarrollo Web & Apps)',
                'descripcion' => 'Reemplazamos tu caos operativo con ecosistemas digitales precisos que trabajan por ti 24/7.',
                'icono' => 'bi-rocket-takeoff',
                'destacado' => true,
                'resultado_principal' => 'Evoluciona de operar tu negocio a ser dueño de él. Multiplica tu capacidad sin inflar tu nómina.',
                'dolor_resuelto' => 'Estar atado a hojas de cálculo infinitas, procesos manuales absurdos y sistemas lentos está asesinando tu margen de rentabilidad y ahogando a tu mejor talento.',
                'impacto_roi' => 'No creamos "paginitas web". Diseñamos motores de hiper-automatización a tu medida. Cada línea de código está obsesionada con una sola cosa: que tú recuperes tu tiempo y dispares tus ingresos netos.',
                'entregables' => [
                    'Ecosistemas Web High-End',
                    'Apps Nativas Revolucionarias',
                    'Flujos de Automatización Total'
                ]
            ],
            [
                'id' => 2,
                'titulo' => 'Blindaje de Operaciones (Arquitectura IT)',
                'descripcion' => 'Fortalezas digitales invulnerables. Cero caídas, cero estrés, crecimiento ilimitado.',
                'icono' => 'bi-shield-lock',
                'destacado' => false,
                'resultado_principal' => 'Duerme tranquilo sabiendo que tu operación es invencible. Escalabilidad infinita al toque de un botón.',
                'dolor_resuelto' => '¿Un servidor caído en pleno lanzamiento? ¿Datos de clientes vulnerados? Estás construyendo tu imperio sobre arena movediza si tu red no soporta el éxito que tanto buscas.',
                'impacto_roi' => 'Desplegamos una arquitectura Cloud de élite que absorbe cualquier volumen de tráfico. Blindamos tu red y tu información como un búnker de Wall Street. Crecimiento sin techos ni caídas mortales.',
                'entregables' => [
                    'Hiper-Escalabilidad Cloud',
                    'Ciberseguridad de Grado Militar',
                    'Resiliencia y Respaldos Inmutables'
                ]
            ],
            [
                'id' => 3,
                'titulo' => 'Maestros del Caos (CTO As a Service)',
                'descripcion' => 'Dirección técnica implacable que convierte ideas en activos tecnológicos trillados de dinero.',
                'icono' => 'bi-lightning-charge',
                'destacado' => false,
                'resultado_principal' => 'Deja de quemar dinero en tecnología que no entiendes. Convertimos el gasto en código en tracción pura.',
                'dolor_resuelto' => 'Lidiar con desarrolladores que no entregan, proyectos que se estancan por meses y sobrecostos por "apagar fuegos" técnicos te roba la energía creadora que necesita tu empresa.',
                'impacto_roi' => 'Tomamos el control absoluto de tu tecnología. Somos tu élite de operaciones especiales (CTOs). Alineamos cada decisión técnica con tu objetivo financiero más agresivo, asegurando que cada dólar invertido te genere diez.',
                'entregables' => [
                    'Liderazgo Técnico (CTO) Premium',
                    'Rescate de Proyectos Naufragados',
                    'Auditoría y Estrategia de Crecimiento IT'
                ]
            ]
        ];
    }
}
?>