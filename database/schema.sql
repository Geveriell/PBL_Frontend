-- schema.sql — aktifkan dengan DB_ENABLED = true di config/config.php
CREATE DATABASE IF NOT EXISTS bon_surya CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bon_surya;

CREATE TABLE IF NOT EXISTS menu (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama         VARCHAR(120) NOT NULL,
  deskripsi    VARCHAR(255) NOT NULL DEFAULT '',
  harga        INT UNSIGNED NOT NULL,
  harga_coret  INT UNSIGNED NULL,
  favorit      TINYINT(1)   NOT NULL DEFAULT 0,
  foto         VARCHAR(120) NOT NULL DEFAULT '',   -- nama file di assets/img/
  opsi         VARCHAR(255) NOT NULL DEFAULT '',
  ikon_opsi    VARCHAR(30)  NOT NULL DEFAULT '',   -- sliders | utensils | flame | snow
  tersedia     TINYINT(1)   NOT NULL DEFAULT 1,
  urutan       INT          NOT NULL DEFAULT 0
);

INSERT INTO menu (nama, deskripsi, harga, harga_coret, favorit, opsi, ikon_opsi, urutan) VALUES
('Kopi Susu Surya Gula Aren',  'Signature espresso gilingan segar, susu murni pasteurisasi, dan sirup aren organik', 22000, 25000, 1, 'Opsi: Less Sugar (50%) • Oat Milk (+5k) • Extra Shot', 'sliders',  1),
('Nasi Goreng Bromo Rempah',   'Nasi goreng gurih dengan bumbu rempah khas lereng Bromo, telur mata sapi renyah,',   34000, NULL,  0, 'Tingkat Pedas: Sedang • Telur Setengah Matang',        'utensils', 2),
('Cireng Krispi Sambal Rujak', 'Snack renyah di luar kenyal di dalam khas Jawa Timur dengan racikan bumbu rujak...', 18000, NULL,  0, 'Porsi Berbagi: Isi 8 pcs • Sambal Rujak Ekstra',        'flame',    3),
('Surya Citrus Cold Brew',     'Cold brew 16 jam biji arabika dampit dengan perasan jeruk lokal Malang yang...',    26000, NULL,  0, 'Es Batu Normal • Bulir Jeruk Segar Asli',               'snow',     4),
('Croissant Butter Warm',      'Flaky golden butter croissant dipanggang ulang hangat, beraroma harum mentega...',  24000, NULL,  0, '',                                                      '',         5);
