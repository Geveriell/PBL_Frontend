<?php
declare(strict_types=1);

/* config.php — pengaturan aplikasi */

const APP_NAME     = 'Bon Surya';
const APP_ADDRESS  = 'Jl. Bromo No.5A, Malang';
const PAJAK_SERVIS = 0.10;          // PB1 & servis 10%
const BATAS_BAYAR  = 15 * 60;       // detik
const MEJA_DEFAULT = '08';
const AREA_MEJA    = 'Area Indoor AC';
const AREA_DETAIL  = 'Semi Outdoor';

/* true  = isi keranjang awal dengan 3 menu (supaya sama dengan desain)
   false = keranjang mulai kosong */
const DEMO_CART = true;

/* Database MySQL. Biarkan false untuk memakai data contoh di models/Menu.php */
const DB_ENABLED = false;
const DB_HOST    = '127.0.0.1';
const DB_NAME    = 'bon_surya';
const DB_USER    = 'root';
const DB_PASS    = '';
