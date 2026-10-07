<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Daftar Buku";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// 1. Ambil keyword dari pencarian (metode GET)
$keyword = trim($_GET['keyword'] ?? '');

// 2. Query ke PostgreSQL dengan ILIKE
if ($keyword !== '') {
    $stmt = $pdo->prepare("SELECT * FROM buku WHERE judul ILIKE :keyword ORDER BY id DESC");
    $stmt->execute(['keyword' => "%$keyword%"]);
    $daftarBuku = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $daftarBuku = $pdo->query("SELECT * FROM buku ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
}
?>
        <section>
            <h2>Daftar Buku</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
            <?php endif; ?>

            <form method="GET" action="list.php" class="search-box" style="margin-bottom: 20px;">
                <label for="search-input">Cari Judul Buku</label>
                <input type="text" id="search-input" name="keyword" placeholder="Ketik judul buku..." value="<?php echo htmlspecialchars($keyword); ?>">
                <button type="submit">Cari</button>
                <?php if ($keyword !== ''): ?>
                    <a href="list.php" style="margin-left: 10px; font-size: 14px;">Reset</a>
                <?php endif; ?>
            </form>

            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Pengarang</th>
                        <th>Tahun</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                        <th>Tanggal Ditambahkan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftarBuku)): ?>
                    <tr>
                        <td colspan="6">Data buku tidak ditemukan.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($daftarBuku as $buku): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($buku['judul']); ?></td>
                            <td><?php echo htmlspecialchars($buku['pengarang']); ?></td>
                            <td><?php echo htmlspecialchars($buku['tahun']); ?></td>
                            <td><?php echo htmlspecialchars($buku['stok']); ?></td>
                            <td>
                                <button type="button">Edit</button>
                                <button type="button" class="btn-hapus">Hapus</button>
                            </td>
                            <td>
                            <?php 
                            if (!empty($buku['tanggal_ditambahkan'])) {
                                echo date('d-m-Y H:i', strtotime($buku['tanggal_ditambahkan']));
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>