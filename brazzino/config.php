<?php
// ============================================================
//  config.php — Brazzino Casino · Conexión PDO
// ============================================================

define('DB_HOST',    'localhost');
define('DB_NAME',    'casino_db');
define('DB_USER',    'root');
define('DB_PASS',    '');         // ← Ajusta tu contraseña
define('DB_CHARSET', 'utf8mb4');
define('SITE_NAME',  'Brazzino Casino');

function pdo(): PDO {
    static $conn = null;
    if ($conn !== null) return $conn;
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $conn = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    return $conn;
}

function json_response(array $data, int $status = 200): void {
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function get_json_body(): array {
    $raw = file_get_contents('php://input');
    if (!$raw) return [];
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

// Genera folio correlativo FAC-YYYY-NNN
function generar_folio(PDO $db): string {
    $year = date('Y');
    $stmt = $db->prepare('SELECT COUNT(*) FROM facturas WHERE YEAR(fecha_emision) = ?');
    $stmt->execute([$year]);
    $seq = (int)$stmt->fetchColumn() + 1;
    return 'FAC-' . $year . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
}

if (session_status() === PHP_SESSION_NONE) session_start();
