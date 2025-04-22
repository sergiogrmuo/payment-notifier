<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Jwt\FirebaseJwtValidator;
use App\Infrastructure\Jwt\FirebaseJwtSigner;
use App\Infrastructure\Http\RequestParser;

$secret = 'my-secret';
$logFile = __DIR__ . '/../notificacion.log';

// 🛠️ Crear el log si no existe
if (!file_exists($logFile)) {
    file_put_contents($logFile, "📄 Log iniciado: " . date('Y-m-d H:i:s') . "\n\n");
}

// Mostrar log como HTML si se accede por navegador (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $contenido = htmlspecialchars(file_get_contents($logFile));

    echo <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Log de notificaciones</title>
    <style>
        body {
            background-color: #f9f9f9;
            font-family: monospace;
            padding: 2rem;
        }
        h2 {
            color: #333;
        }
        pre {
            background: #fff;
            border: 1px solid #ccc;
            padding: 1rem;
            overflow: auto;
            max-height: 80vh;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <h2>📋 Log de notificaciones recibidas</h2>
    <pre>{$contenido}</pre>
</body>
</html>
HTML;
    exit;
}

// Procesar notificación POST
try {
    $rawBody = file_get_contents('php://input');
    $headers = getallheaders();

    $validator = new FirebaseJwtValidator($secret);
    $parser = new RequestParser($validator);

    $payment = $parser->parse($rawBody, $headers);


$payloadArray = (array) $payment;
$signer = new FirebaseJwtSigner($secret);
$recalculatedSignature = $signer->sign($payloadArray);

// Obtener la firma original de los headers
$originalSignature = $headers['Signature'] ?? null;

// Comparar firmas
if ($recalculatedSignature !== $originalSignature) {
    http_response_code(400);
    $error = "❌ Error: la firma recalculada no coincide con la original. Posible manipulación.\n\n";
    file_put_contents($logFile, $error, FILE_APPEND);
    echo $error;
    exit;
}

    $logEntry = "✅ Notificación recibida y verificada correctamente\n";
    $logEntry .= "🔐 Firma JWT válida y segundo factor (reverificación) pasada\n";
    $logEntry .= "📦 Datos del pago:\n" . json_encode($payment, JSON_PRETTY_PRINT) . "\n\n";


    file_put_contents($logFile, $logEntry, FILE_APPEND);

    http_response_code(200);
    echo "✅ Notificación recibida";
} catch (Throwable $e) {
    http_response_code(400);
    $error = "❌ Error al procesar la notificación: " . $e->getMessage() . "\n\n";
    file_put_contents($logFile, $error, FILE_APPEND);
    echo $error;
}


