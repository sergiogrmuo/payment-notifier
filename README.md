# Payment Notifier - Arquitectura Hexatonial

Proyecto de demostración en PHP 8.2 con arquitectura hexatonial, sin frameworks, utilizando puertos y adaptadores. Envía notificaciones de pagos firmadas con JWT a través de HTTP.

---

## ¿Qué hace?

1. Crea un objeto `Payment` con datos de prueba.
2. Firma el contenido con JWT usando una clave secreta.
3. Envia el `Payment` como JSON vía HTTP POST.
4. El receptor valida la firma y guarda el resultado en un log.
5. Todo orquestado a través de clases desacopladas por capas.

---

## Estructura del proyecto

```
payment-notifier/
├── src/
│   ├── Domain/
│   │   ├── Entity/            # Entidades puras (Payment)
│   │   └── Port/              # Interfaces (puertos)
│   ├── Application/           # Casos de uso
│   └── Infrastructure/
│       ├── Http/              # Adaptadores de salida HTTP
│       └── Jwt/               # Firma y validación JWT
├── public/
│   └── receive.php            # Receptor de notificaciones
├── run.php                    # Cliente CLI para enviar una notificación
├── config.php                 # Configuración de endpoint y secreto
├── notificacion.log           # Log generado por receive.php
└── composer.json
```

---

## Requisitos

- Docker instalado  
(No necesitas instalar PHP ni Composer en tu sistema)

---

## Instalación de dependencias

```bash
docker run --rm -v $(pwd):/app -w /app composer install
```

---

## Levantar el receptor

Esto inicia el servidor en `http://localhost:8009`:

```bash
docker run --rm -v $(pwd):/app -w /app -p 8009:8000 php:8.2-cli php -S 0.0.0.0:8000 -t public

```

> Dejá esta terminal abierta para que el receptor escuche.

---

## Enviar una notificación

Desde otra terminal, ejecutá:

```bash
docker run --rm -v $(pwd):/app -w /app php:8.2-cli php run.php
```

---

## Resultado esperado

### En la terminal del cliente (run.php):

```
✅ Notificación enviada correctamente.
```

### En el log (`notificacion.log`):

```
✅ Recibido:
{
  "amount": 120.75,
  ...
}
```

---

## Cómo probarlo manualmente

- Abre `http://localhost:8009/receive.php` en el navegador para ver el log acumulado.

---

## Seguridad

La cabecera `Signature` contiene el JWT firmado.  
El receptor valida que el cuerpo del `POST` coincida exactamente con lo firmado.

---

## Test 

```bash
docker run --rm -v $(pwd):/app -w /app php:8.2-cli ./vendor/bin/phpunit
```


















🔍 Esto es lo que ocurre:

    run.php se ejecuta

        Se hace require config.php para obtener la secret y el endpoint.

        Se crea un objeto Payment (entidad del dominio) con los datos de prueba.

    Se firma el contenido

        Se instancia FirebaseJwtSigner, que usa firebase/php-jwt para firmar el array del Payment.

        Se construye un GuzzlePaymentNotifier, que es un adaptador que implementa la interfaz del puerto para enviar la notificación vía HTTP.

    Se ejecuta el caso de uso SendPaymentNotification

        Este recibe el Payment y lo pasa al Notifier.

        GuzzlePaymentNotifier firma el JSON del Payment y envía la petición HTTP POST al endpoint configurado con:

            Cuerpo JSON

            Cabecera Signature con el JWT firmado

📥 En el receptor (public/receive.php):

    Se recibe el POST /receive.php y se recoge el cuerpo (php://input) y los headers.

    Se valida:

        Primera verificación: El JWT es válido (FirebaseJwtValidator)

        Segunda verificación: Se recalcula el JWT desde los datos del cuerpo y se compara con la cabecera Signature.

    Si todo es correcto:

        Se guarda un log bonito con ✅ y los datos del pago

        Se imprime en el navegador en GET /receive.php
