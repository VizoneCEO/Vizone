<?php
header('Content-Type: application/json');

// Permitir solicitudes CORS solo desde nuestro dominio (opcional para extra seguridad en producciones)
// header("Access-Control-Allow-Origin: *");

// Incluir configuración de base de datos
require_once __DIR__ . '/../config/database.php';

try {
    // Validar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido. Utilice POST.');
    }

    // Obtener y sanitizar datos recibidos
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $empresa = filter_input(INPUT_POST, 'empresa', FILTER_SANITIZE_STRING);
    $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_STRING);
    $fecha = filter_input(INPUT_POST, 'fecha', FILTER_SANITIZE_STRING);
    $hora = filter_input(INPUT_POST, 'hora', FILTER_SANITIZE_STRING);

    // Validar campos obligatorios
    if (empty($nombre) || empty($empresa) || empty($telefono) || empty($fecha) || empty($hora)) {
        throw new Exception('Todos los campos son obligatorios.');
    }

    $db = Database::getInstance()->getConnection();

    // 1. VALIDACIÓN ANTI-DUPLICADOS (Crucial para no empalmar reuniones)
    // Buscamos si ya existe una reunión "Agendada" en esa fecha y hora.
    $stmtCheck = $db->prepare("SELECT id FROM reuniones_auditoria WHERE fecha = ? AND hora = ? AND estado = 'Agendada'");
    $stmtCheck->execute([$fecha, $hora]);

    if ($stmtCheck->rowCount() > 0) {
        // Horario Ocupado
        echo json_encode([
            'status' => 'error',
            'message' => 'El horario seleccionado (' . $hora . ' el ' . $fecha . ') ya ha sido reservado por otra persona. Por favor, selecciona otro espacio.'
        ]);
        exit;
    }

    // ==============================================================================
    // 2. CREACIÓN DEL MEET (Lógica Google Calendar API real)
    // ==============================================================================
    $googleMeetLink = "https://meet.google.com/vizone-audit-placeholder"; // Fallback por defecto

    // Rutas a credenciales y token
    $credentialsPath = __DIR__ . '/../config/client_secret_1007082747559-jg1lgb9tq6kt5rh62tsn75tmtlolur42.apps.googleusercontent.com.json';
    $tokenPath = __DIR__ . '/../config/token.json';

    if (file_exists($tokenPath) && file_exists($credentialsPath)) {
        require_once __DIR__ . '/../../vendor/autoload.php';

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
            // Auditoría es de 1 hora
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
                ),
                'attendees' => array(
                    // Opcionalmente agregar el email de contacto del cliente aquí
                )
            ));

            $calendarId = 'primary';
            $optParams = array('conferenceDataVersion' => 1);
            $event = $service->events->insert($calendarId, $event, $optParams);

            // Recuperar el link autogenerado de Google Meet
            if ($event->getHangoutLink()) {
                $googleMeetLink = $event->getHangoutLink();
            }
        }
    }

    // 3. REGISTRO EN LA BASE DE DATOS
    $stmtInsert = $db->prepare("INSERT INTO reuniones_auditoria (nombre, empresa, telefono, fecha, hora, google_meet_link, estado) VALUES (?, ?, ?, ?, ?, ?, 'Agendada')");
    $resultado = $stmtInsert->execute([$nombre, $empresa, $telefono, $fecha, $hora, $googleMeetLink]);

    if (!$resultado) {
        throw new Exception('Error al guardar la reunión en la base de datos.');
    }

    // Calcular URL de Add to Calendar
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

    // 4. RETORNAR ÉXITO Y LINK (Para generar el QR)
    echo json_encode([
        'status' => 'success',
        'message' => 'Auditoría agendada correctamente',
        'meet_link' => $googleMeetLink,
        'calendar_url' => $googleCalendarAddUrl
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>