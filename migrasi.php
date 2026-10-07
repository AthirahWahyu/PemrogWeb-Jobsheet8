<?php
require __DIR__ . '/includes/koneksi.php';

// Path ke file data/buku.json
$jsonFile = __DIR__ . '/data/buku.json';

// memeriksa keberadaan file JSON
if (!file_exists($jsonFile)) {
    die("Error: File data/buku.json tidak ditemukan! Pastikan file berada di dalam folder data/.");
}

// membaca isi file dan decode JSON menjadi array PHP
$jsonContent = file_get_contents($jsonFile);
$dataBuku    = json_decode($jsonContent, true);

if (empty($dataBuku)) {
    die("Error: Data JSON kosong atau format tidak valid.");
}

// menyiapkan prepared statement INSERT ke PostgreSQL
$stmt = $pdo->prepare(
    "INSERT INTO buku (judul, pengarang, tahun, isbn, stok, kategori)
     VALUES (:judul, :pengarang, :tahun, :isbn, :stok, :kategori)"
);

$jumlahBerhasil = 0;

// looping seluruh data buku dari JSON dan masukkan ke database
foreach ($dataBuku as $buku) {
    $stmt->execute([
        'judul'     => $buku['judul'] ?? '',
        'pengarang' => $buku['pengarang'] ?? '',
        'tahun'     => (int) ($buku['tahun'] ?? 0),
        'isbn'      => $buku['isbn'] ?? null,
        'stok'      => (int) ($buku['stok'] ?? 0),
        'kategori'  => $buku['kategori'] ?? null,
    ]);
    $jumlahBerhasil++;
}

echo "<h3>Migrasi Selesai!</h3>";
echo "Sebanyak <strong>$jumlahBerhasil</strong> data buku dari file <code>data/buku.json</code> berhasil dipindahkan ke database PostgreSQL.";