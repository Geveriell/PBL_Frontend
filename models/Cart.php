<?php
declare(strict_types=1);

/* Cart.php — keranjang di $_SESSION; harga SELALU diambil dari Menu (server) */
class Cart
{
    /** Isi awal 3 menu bila DEMO_CART aktif (hanya sekali per session) */
    public static function seedDemo(): void
    {
        if (DEMO_CART && !isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [1 => 1, 2 => 1, 3 => 1];
        }
    }

    public static function qty(int $id): int
    {
        return (int) ($_SESSION['cart'][$id] ?? 0);
    }

    /** Ubah jumlah item. Return jumlah baru, atau null bila menu tidak ada. */
    public static function change(int $id, string $action): ?int
    {
        if (Menu::find($id) === null) {
            return null;
        }
        $q = self::qty($id) + ($action === 'inc' ? 1 : -1);
        $q = max(0, min(99, $q));
        if ($q === 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id] = $q;
        }
        return $q;
    }

    /** @return array{items:int,subtotal:int,pajak:int,total:int} */
    public static function summary(): array
    {
        $items = 0;
        $subtotal = 0;
        foreach (Menu::all() as $m) {
            $q = self::qty((int) $m['id']);
            $items += $q;
            $subtotal += $q * (int) $m['harga'];
        }
        $pajak = (int) round($subtotal * PAJAK_SERVIS);
        return ['items' => $items, 'subtotal' => $subtotal, 'pajak' => $pajak, 'total' => $subtotal + $pajak];
    }

    /**
     * Baris pesanan (hanya menu yang qty-nya > 0), untuk split bill & status.
     * @return array<int,array{id:int,nama:string,qty:int,harga:int,opsi:string,ikon:string,foto:string}>
     */
    public static function lines(): array
    {
        $out = [];
        foreach (Menu::all() as $m) {
            $q = self::qty((int) $m['id']);
            if ($q > 0) {
                $out[] = [
                    'id'    => (int) $m['id'],
                    'nama'  => (string) $m['nama'],
                    'qty'   => $q,
                    'harga' => (int) $m['harga'],
                    'opsi'  => trim((string) preg_replace('/^Opsi:\s*/u', '', (string) $m['opsi'])),
                    'ikon'  => (string) ($m['ikon_opsi'] ?: 'utensils'),
                    'foto'  => (string) $m['foto'],
                ];
            }
        }
        return $out;
    }

    /** Nomor referensi pesanan, tetap selama session */
    public static function ref(): string
    {
        if (empty($_SESSION['ref'])) {
            $_SESSION['ref'] = 'A' . random_int(1000, 9999);
        }
        return '#' . $_SESSION['ref'];
    }
}
