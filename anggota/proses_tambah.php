<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

// Ambil data dari form
$nama       = trim($_POST['nama'] ?? '');
$no_anggota = trim($_POST['no_anggota'] ?? '');
$alamat     = trim($_POST['alamat'] ?? '');
$no_hp      = trim($_POST['no_hp'] ?? '');

// Validasi input sederhana
if ($nama === '' || $no_anggota === '') {
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Nama dan No. Anggota wajib diisi.'];
    header('Location: tambah.php');
    exit;
}

try {
    // Siapkan prepared statement untuk INSERT
    $stmt = $pdo->prepare(
        "INSERT INTO anggota (nama, no_anggota, alamat, no_hp)
         VALUES (:nama, :no_anggota, :alamat, :no_hp)"
    );

    // Jalankan query
    $stmt->execute([
        'nama'       => $nama,
        'no_anggota' => $no_anggota,
        'alamat'     => $alamat,
        'no_hp'      => $no_hp,
    ]);

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Anggota berhasil ditambahkan.'];
    header('Location: list.php');
    exit;

} catch (PDOException $e) {
    // Error 23505 adalah kode PostgreSQL untuk pelanggaran UNIQUE constraint
    if ($e->getCode() === '23505') {
        $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'No. Anggota sudah dipakai, gunakan nomor lain.'];
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Gagal menyimpan data: ' . $e->getMessage()];
    }

    header('Location: tambah.php');
    exit;
}