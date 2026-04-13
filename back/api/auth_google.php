<?php
require_once __DIR__ . '/../../vendor/autoload.php';

session_start();

// Ruta de tus credenciales
$credentialsPath = __DIR__ . '/../config/client_secret_1007082747559-jg1lgb9tq6kt5rh62tsn75tmtlolur42.apps.googleusercontent.com.json';
$tokenPath = __DIR__ . '/../config/token.json';

// La URL a la que Google te redireccionará de vuelta tras autorizar.
// IMPORTANTE: Esta URL exacta debe estar registrada en Google Cloud Console bajo "Authorized redirect URIs"
$redirectUri = 'http://localhost/vizone/back/api/auth_google.php';

$client = new Google\Client();
$client->setAuthConfig($credentialsPath);
$client->setRedirectUri($redirectUri);
$client->addScope(Google\Service\Calendar::CALENDAR_EVENTS);
$client->setAccessType('offline');        // Requerido para recibir un Refresh Token y que funcione solo
$client->setPrompt('select_account consent');

// 1. Si regresamos de Google con un código, lo intercambiamos por un Token
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    // Si hay error en el token recibido
    if (array_key_exists('error', $token)) {
        die("Error obteniendo el token de Google: " . json_encode($token));
    }

    $client->setAccessToken($token);

    // Guardar el token de forma permanente en un archivo
    $bytesWritten = file_put_contents($tokenPath, json_encode($client->getAccessToken()));

    if ($bytesWritten === false) {
        echo "<h1 style='color:red;'>Error Crítico de Permisos</h1>";
        echo "<p>PHP no tiene permisos para escribir el archivo <code>$tokenPath</code>.</p>";
        echo "<p>Por favor, ejecuta el siguiente comando en tu terminal de Mac para darle permisos a la carpeta temporalmente:</p>";
        echo "<pre>chmod 777 /Applications/XAMPP/xamppfiles/htdocs/vizone/back/config</pre>";
        echo "<p>Luego intenta recargar esta página nuevamente.</p>";
        exit;
    }

    echo "<h1>¡Autenticación Exitosa!</h1>";
    echo "<p>El sistema Vizone ahora puede generar reuniones de Google Meet y calendarizarlas automáticamente.</p>";
    echo "<p>El archivo <code>token.json</code> se ha guardado correctamente. Ya puedes cerrar esta ventana y probar el formulario de agendamiento.</p>";
    exit;
}

// 2. Si ya existe un Token, mostrar un mensaje.
if (file_exists($tokenPath)) {
    $accessToken = json_decode(file_get_contents($tokenPath), true);
    $client->setAccessToken($accessToken);
    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
            echo "<h1>Token Refrescado</h1>";
        } else {
            echo "El token expiró y no hay Refresh Token. Elimina token.json y vuelve a autorizar.";
            file_put_contents($tokenPath, null); // Forzar re-auth
            exit;
        }
    } else {
        echo "<h1>Validación Completa</h1>";
        echo "<p>Tu conexión con Google Calendar ya está activa y configurada.</p>";
        echo "<p>Si quieres volver a vincular la cuenta o cambiar de correo, elimina el archivo <code>back/config/token.json</code> y entra de nuevo aquí.</p>";
        exit;
    }
}

// 3. Generar la URL para que el usuario Inicie Sesión en Google y autorice la App.
$authUrl = $client->createAuthUrl();

echo "<h1>Vincular Vizone con Google Calendar</h1>";
echo '<p><strong style="color:red;">ATENCIÓN:</strong> Antes de continuar, asegúrate de haber añadido exactamente esta URL (<code>http://localhost/vizone/back/api/auth_google.php</code>) como "Authorized redirect URI" dentro de tu Google Cloud Console para ese Cliente OAuth 2.0.</p>';
echo "<p><a href='" . filter_var($authUrl, FILTER_SANITIZE_URL) . "'>Da click aquí para iniciar sesión con Google y autorizar el calendario</a></p>";
?>