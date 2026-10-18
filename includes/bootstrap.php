<?php
declare(strict_types=1);

/* bootstrap.php — dimuat paling atas di setiap halaman/API */
session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Menu.php';
require_once __DIR__ . '/../models/Cart.php';

/** Format rupiah: 22000 -> "Rp 22.000" */
function rp(int $n): string
{
    return 'Rp ' . number_format($n, 0, ',', '.');
}

/** Escape output HTML */
function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/** Nomor meja dari ?meja=07 (hanya angka, 2 digit); tersimpan di session */
function meja(): string
{
    if (isset($_GET['meja']) && preg_match('/^\d{1,3}$/', (string) $_GET['meja'])) {
        $_SESSION['meja'] = str_pad((string) $_GET['meja'], 2, '0', STR_PAD_LEFT);
    }
    return $_SESSION['meja'] ?? MEJA_DEFAULT;
}

/** Token CSRF untuk request API */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

Cart::seedDemo();
