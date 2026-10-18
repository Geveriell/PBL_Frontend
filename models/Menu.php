<?php
declare(strict_types=1);

/* Menu.php — sumber data menu (database atau data contoh) */
class Menu
{
    /** @return array<int,array<string,mixed>> */
    public static function all(): array
    {
        if (DB_ENABLED) {
            try {
                return db()->query(
                    'SELECT id, nama, deskripsi, harga, harga_coret, favorit, foto, opsi, ikon_opsi
                       FROM menu WHERE tersedia = 1 ORDER BY urutan, id'
                )->fetchAll();
            } catch (PDOException $ex) {
                error_log('Menu::all — ' . $ex->getMessage()); // jatuh ke data contoh
            }
        }
        return self::sample();
    }

    /** @return array<string,mixed>|null */
    public static function find(int $id): ?array
    {
        foreach (self::all() as $m) {
            if ((int) $m['id'] === $id) {
                return $m;
            }
        }
        return null;
    }

    /** Data contoh (sama dengan database/schema.sql) */
    private static function sample(): array
    {
        return [
            ['id' => 1, 'nama' => 'Kopi Susu Surya Gula Aren',  'deskripsi' => 'Signature espresso gilingan segar, susu murni pasteurisasi, dan sirup aren organik',               'harga' => 22000, 'harga_coret' => 25000, 'favorit' => 1, 'foto' => '', 'opsi' => 'Opsi: Less Sugar (50%) • Oat Milk (+5k) • Extra Shot',      'ikon_opsi' => 'sliders'],
            ['id' => 2, 'nama' => 'Nasi Goreng Bromo Rempah',   'deskripsi' => 'Nasi goreng gurih dengan bumbu rempah khas lereng Bromo, telur mata sapi renyah,',                   'harga' => 34000, 'harga_coret' => null,  'favorit' => 0, 'foto' => '', 'opsi' => 'Tingkat Pedas: Sedang • Telur Setengah Matang',           'ikon_opsi' => 'utensils'],
            ['id' => 3, 'nama' => 'Cireng Krispi Sambal Rujak', 'deskripsi' => 'Snack renyah di luar kenyal di dalam khas Jawa Timur dengan racikan bumbu rujak...',                 'harga' => 18000, 'harga_coret' => null,  'favorit' => 0, 'foto' => '', 'opsi' => 'Porsi Berbagi: Isi 8 pcs • Sambal Rujak Ekstra',         'ikon_opsi' => 'flame'],
            ['id' => 4, 'nama' => 'Surya Citrus Cold Brew',     'deskripsi' => 'Cold brew 16 jam biji arabika dampit dengan perasan jeruk lokal Malang yang...',                    'harga' => 26000, 'harga_coret' => null,  'favorit' => 0, 'foto' => '', 'opsi' => 'Es Batu Normal • Bulir Jeruk Segar Asli',                'ikon_opsi' => 'snow'],
            ['id' => 5, 'nama' => 'Croissant Butter Warm',      'deskripsi' => 'Flaky golden butter croissant dipanggang ulang hangat, beraroma harum mentega...',                  'harga' => 24000, 'harga_coret' => null,  'favorit' => 0, 'foto' => '', 'opsi' => '',                                                          'ikon_opsi' => ''],
        ];
    }
}
