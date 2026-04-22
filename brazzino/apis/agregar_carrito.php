<?php
// apis/agregar_carrito.php — Brazzino Casino
require_once dirname(__DIR__) . '/config.php';

$id_producto = (int)($_POST['id_producto'] ?? 0);
$cantidad    = max(1, (int)($_POST['cantidad'] ?? 1));

if ($id_producto <= 0) {
    $_SESSION['cart_msg'] = ['tipo'=>'error','texto'=>'Paquete inválido.'];
    header('Location: ../index.php'); exit;
}

try {
    $db   = pdo();
    $stmt = $db->prepare("SELECT id,nombre_paquete,precio FROM productos WHERE id=? AND activo=1");
    $stmt->execute([$id_producto]);
    $prod = $stmt->fetch();
} catch (Exception $e) {
    $_SESSION['cart_msg'] = ['tipo'=>'error','texto'=>'Error de base de datos.'];
    header('Location: ../index.php'); exit;
}

if (!$prod) {
    $_SESSION['cart_msg'] = ['tipo'=>'error','texto'=>'Paquete no disponible.'];
    header('Location: ../index.php'); exit;
}

if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];

$pid = $prod['id'];
if (isset($_SESSION['carrito'][$pid])) {
    $_SESSION['carrito'][$pid]['cantidad'] += $cantidad;
} else {
    $_SESSION['carrito'][$pid] = [
        'id'             => $prod['id'],
        'nombre_paquete' => $prod['nombre_paquete'],
        'precio'         => (float)$prod['precio'],
        'cantidad'       => $cantidad,
    ];
}

$_SESSION['cart_msg'] = [
    'tipo'  => 'ok',
    'texto' => '✓ "' . $prod['nombre_paquete'] . '" agregado al carrito.'
];

header('Location: ../index.php#creditos'); exit;
