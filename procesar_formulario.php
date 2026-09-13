<?php


// Sección: conexión a la base de datos
$servidor = '127.0.0.1';
$usuario = 'root';
$contrasena = '';
$base_datos = 'uriona_taller';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

// Sección: datos del formulario
$nombre = trim((string) ($_POST['nombre'] ?? ''));
$celular = trim((string) ($_POST['celular'] ?? ''));
$tipoVehiculo = trim((string) ($_POST['tipo_vehiculo'] ?? ''));
$servicio = trim((string) ($_POST['servicio'] ?? ''));
$descripcion = trim((string) ($_POST['descripcion'] ?? ''));

$preciosServicios = [
    'Mantenimiento del motor' => 350.00,
    'Reparación de caja' => 350.00,
    'Mantenimiento de frenos' => 180.00,
    'Sistema de aire' => 150.00,
    'Cambio de piezas' => 120.00,
    'Compra de productos' => 35.00,
];

// Sección: validar datos y calcular precio
if ($nombre === '' || $celular === '' || $tipoVehiculo === '' || $servicio === '' || $descripcion === '') {
    mostrar_mensaje('Faltan datos', 'Completa todos los campos del formulario.', false);
}

if (strlen($nombre) > 100 || strlen($celular) > 20 || strlen($tipoVehiculo) > 100 || strlen($servicio) > 100) {
    mostrar_mensaje('Datos demasiado largos', 'Revisa la longitud de los datos ingresados.', false);
}

if (!array_key_exists($servicio, $preciosServicios)) {
    mostrar_mensaje('Servicio no válido', 'Selecciona uno de los servicios disponibles.', false);
}

$precioReferencial = $preciosServicios[$servicio];
$adelanto25 = round($precioReferencial * 0.25, 2);

// Sección: guardar la solicitud
try {
    $conexion = new PDO(
        "mysql:host={$servidor};dbname={$base_datos};charset=utf8mb4",
        $usuario,
        $contrasena,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    $consulta = $conexion->prepare(
        'INSERT INTO solicitudes_servicio
        (nombre, celular, tipo_vehiculo, servicio, descripcion, precio_referencial, adelanto_25, estado_pago)
        VALUES (:nombre, :celular, :tipo_vehiculo, :servicio, :descripcion, :precio_referencial, :adelanto_25, :estado_pago)'
    );

    $consulta->execute([
        ':nombre' => $nombre,
        ':celular' => $celular,
        ':tipo_vehiculo' => $tipoVehiculo,
        ':servicio' => $servicio,
        ':descripcion' => $descripcion,
        ':precio_referencial' => $precioReferencial,
        ':adelanto_25' => $adelanto25,
        ':estado_pago' => 'Pendiente',
    ]);

    mostrar_mensaje(
        'Solicitud recibida',
        'Gracias, ' . $nombre . '. Registramos tu solicitud de ' . $servicio . '. El adelanto referencial es Bs ' . number_format($adelanto25, 2, ',', '.') . '. Te contactaremos al ' . $celular . '.',
        true
    );
} catch (PDOException $error) {
    error_log('FrenosUriona - error de base de datos: ' . $error->getMessage());
    mostrar_mensaje(
        'No se pudo guardar',
        'Activa Apache y MySQL en XAMPP y confirma que importaste SOLO_IMPORTAR_EN_PHPMYADMIN.sql en phpMyAdmin.',
        false
    );
}

// Sección: mostrar resultado
function mostrar_mensaje($titulo, $texto, $correcto)
{
    $color = $correcto ? '#e32635' : '#a31424';
    $tituloSeguro = htmlspecialchars($titulo, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $textoSeguro = htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    echo "<!DOCTYPE html>
<html lang='es'>
<head>
<meta charset='UTF-8'>
<meta name='viewport' content='width=device-width, initial-scale=1'>
<title>{$tituloSeguro} | Uriona</title>
<style>
:root{--azul:#07182b;--gris:#eef1f4;--blanco:#fff;--azul-intenso:#0f5ea8;--rojo:#e32635;--texto:#53616f}
*{box-sizing:border-box}
body{margin:0;padding:70px 20px;background:var(--gris);color:var(--azul);font-family:Arial,sans-serif}
.caja{max-width:560px;margin:auto;padding:38px;background:var(--blanco);border-top:8px solid {$color};box-shadow:0 10px 28px #07182b22;text-align:center}
h1{font-size:28px}
p{color:var(--texto);line-height:1.7}
.boton{display:inline-block;margin-top:18px;padding:12px 18px;background:var(--azul);color:white;text-decoration:none;font-weight:bold}
.boton:hover{background:var(--azul-intenso)}
</style>
</head>
<body>
<div class='caja'>
<h1>{$tituloSeguro}</h1>
<p>{$textoSeguro}</p>
<a class='boton' href='index.html'>Volver al inicio</a>
</div>
</body>
</html>";
    exit;
}
