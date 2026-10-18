<?php
declare(strict_types=1);

/* api/cart.php — POST JSON {id, action: "inc"|"dec"} → keranjang terbaru */
require_once __DIR__ . '/../includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

function fail(int $code, string $msg): never
{
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $msg]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    fail(405, 'Metode tidak diizinkan');
}
if (!hash_equals(csrf_token(), $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '')) {
    fail(403, 'Token tidak valid');
}

$in     = json_decode((string) file_get_contents('php://input'), true) ?: [];
$id     = (int) ($in['id'] ?? 0);
$action = (string) ($in['action'] ?? '');

if ($id <= 0 || !in_array($action, ['inc', 'dec'], true)) {
    fail(422, 'Data tidak valid');
}

$qty = Cart::change($id, $action);
if ($qty === null) {
    fail(404, 'Menu tidak ditemukan');
}

$s = Cart::summary();
echo json_encode([
    'ok'       => true,
    'id'       => $id,
    'qty'      => $qty,
    'items'    => $s['items'],
    'total'    => $s['subtotal'],
    'totalFmt' => rp($s['subtotal']),
]);
