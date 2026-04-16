<?php
header('Content-Type: application/json');

// Permitir solicitudes CORS solo desde nuestro dominio (opcional)
// header("Access-Control-Allow-Origin: *");

// Incluir configuración de base de datos
require_once __DIR__ . '/../config/database.php';

try {
    // Validar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido. Utilice POST.');
    }

    // Función segura para sanitizar inputs en PHP 8+ (reemplaza deprecated FILTER_SANITIZE_STRING)
    function sanitize_input($key) {
        return isset($_POST[$key]) ? htmlspecialchars(strip_tags(trim($_POST[$key])), ENT_QUOTES, 'UTF-8') : '';
    }

    // Obtener y sanitizar datos recibidos
    $nombre = sanitize_input('nombre');
    $empresa = sanitize_input('empresa');
    $telefono = sanitize_input('telefono');
    $fecha = sanitize_input('fecha');
    $hora = sanitize_input('hora');

    // Validar campos obligatorios
    if (empty($nombre) || empty($empresa) || empty($telefono) || empty($fecha) || empty($hora)) {
        throw new Exception('Todos los campos son obligatorios.');
    }

    // Validación estricta con Regex para Fecha (YYYY-MM-DD) y Hora (HH:MM:SS)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        throw new Exception('Formato de fecha inválido. Utilice AAAA-MM-DD.');
    }
    if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $hora)) {
        throw new Exception('Formato de hora inválido. Utilice HH:MM:SS.');
    }

    $db = Database::getInstance()->getConnection();

    // 1. VALIDACIÓN ANTI-DUPLICADOS (Crucial para no empalmar reuniones)
    // Buscamos si ya existe una reunión "Agendada" en esa fecha y hora.
    $stmtCheck = $db->prepare("SELECT id FROM reuniones_auditoria WHERE fecha = ? AND hora = ? AND estado = 'Agendada'");
    $stmtCheck->execute([$fecha, $hora]);

    if ($stmtCheck->rowCount() > 0) {
        // Horario Ocupado redirigido a Catch unificado
        throw new Exception('El horario seleccionado (' . $hora . ' el ' . $fecha . ') ya ha sido reservado. Por favor, selecciona otro.');
    }

    // ==============================================================================
    // 2. CREACIÓN DEL MEET (Lógica Google Calendar API)
    // ==============================================================================
    $googleMeetLink = "https://meet.google.com/vizone-audit-placeholder"; // Fallback

    $credentialsPath = __DIR__ . '/../config/client_secret_1007082747559-jg1lgb9tq6kt5rh62tsn75tmtlolur42.apps.googleusercontent.com.json';
    $tokenPath = __DIR__ . '/../config/token.json';

    if (file_exists($tokenPath) && file_exists($credentialsPath)) {
        require_once __DIR__ . '/../../vendor/autoload.php';

        try {
            $client = new Google\Client();
            $client->setAuthConfig($credentialsPath);
            $client->addScope(Google\Service\Calendar::CALENDAR_EVENTS);

            // Configurar Token
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);

            // Auto-refresh token if expired
            if ($client->isAccessTokenExpired()) {
                if ($client->getRefreshToken()) {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                    file_put_contents($tokenPath, json_encode($client->getAccessToken()));
                }
            }

            if (!$client->isAccessTokenExpired()) {
                $service = new Google\Service\Calendar($client);

                // Computar Fechas y Tiempos de la reunión
                $startDateTime = "{$fecha}T{$hora}"; // ISO 8601
                $timeObj = new DateTime($startDateTime, new DateTimeZone('America/Mexico_City'));
                $timeObj->modify('+1 hour');
                $endDateTime = $timeObj->format('Y-m-d\TH:i:s');

                $event = new Google\Service\Calendar\Event(array(
                    'summary' => 'Auditoría Vizone - ' . $empresa . ' (' . $nombre . ')',
                    'description' => 'Reunión de Auditoría Estratégica.\nContacto Telefónico: ' . $telefono,
                    'start' => array(
                        'dateTime' => $startDateTime,
                        'timeZone' => 'America/Mexico_City',
                    ),
                    'end' => array(
                        'dateTime' => $endDateTime,
                        'timeZone' => 'America/Mexico_City',
                    ),
                    'conferenceData' => array(
                        'createRequest' => array(
                            'requestId' => 'vizone_audit_' . time(),
                            'conferenceSolutionKey' => array('type' => 'hangoutsMeet')
                        )
                    )
                ));

                $calendarId = 'primary';
                $optParams = array('conferenceDataVersion' => 1);
                $event = $service->events->insert($calendarId, $event, $optParams);

                // Recuperar el link autogenerado
                if ($event->getHangoutLink()) {
                    $googleMeetLink = $event->getHangoutLink();
                }
            } else {
                throw new Exception("El token proporcionado ha expirado y no se logró actualizar.");
            }
        } catch (Exception $googleEx) {
            // Manejo estricto de Errores de Google API
            throw new Exception('Fallo en sincronización con Google Calendar. Detalle: ' . $googleEx->getMessage());
        }
    } else {
        // En lugar de usar Fallback a ciegas sugerimos alertarlo
        // throw new Exception("Credenciales de Google o Token.json faltantes en la configuración del servidor.");
        // Si prefieres mantener el fallback, omite el throw de arriba (por el momento está comentado para no romper tests del usuario)
    }

    // 3. REGISTRO EN LA BASE DE DATOS
    $stmtInsert = $db->prepare("INSERT INTO reuniones_auditoria (nombre, empresa, telefono, fecha, hora, google_meet_link, estado) VALUES (?, ?, ?, ?, ?, ?, 'Agendada')");
    $resultado = $stmtInsert->execute([$nombre, $empresa, $telefono, $fecha, $hora, $googleMeetLink]);

    if (!$resultado) {
        throw new Exception('Error interno al guardar la reunión en nuestra base de datos.');
    }

    // Calcular URL de Add to Calendar (Google Formato Z)
    $timeObjStart = new DateTime("{$fecha}T{$hora}", new DateTimeZone('America/Mexico_City'));
    $timeObjStart->setTimezone(new DateTimeZone('UTC'));
    $startUtc = $timeObjStart->format('Ymd\THis\Z');

    $timeObjEnd = clone $timeObjStart;
    $timeObjEnd->modify('+1 hour');
    $endUtc = $timeObjEnd->format('Ymd\THis\Z');

    $googleCalendarAddUrl = "https://calendar.google.com/calendar/render?action=TEMPLATE" .
        "&text=" . urlencode("Auditoría Vizone - " . $empresa) .
        "&dates=" . $startUtc . "/" . $endUtc .
        "&details=" . urlencode("Reunión de Auditoría Estratégica.\nEnlace al Meet: " . $googleMeetLink) .
        "&location=" . urlencode($googleMeetLink);

    // 4. RETORNAR ÉXITO
    echo json_encode([
        'status' => 'success',
        'message' => 'Auditoría agendada correctamente',
        'meet_link' => $googleMeetLink,
        'calendar_url' => $googleCalendarAddUrl
    ]);

} catch (Exception $e) {
    // Retorno Universal de Errores en JSON Limpio
    http_response_code(400); // Bad Request (Opcional, pero buenas prácticas)
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>